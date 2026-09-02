<?php

namespace App\Services;

use App\Models\Incident;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class IncidentReportService
{
    public const GRANULARITY_DAY = 'day';
    public const GRANULARITY_WEEK = 'week';
    public const GRANULARITY_MONTH = 'month';

    /** @var array<int, string> Month names (kept as a constant so we don't depend on installed locale files). */
    protected const MONTHS = [
        1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
        5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
        9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December',
    ];

    /**
     * Resolves a chosen period preset (or a custom date range) into [from, to].
     *
     * @return array{0: Carbon, 1: Carbon}
     */
    public function resolvePeriod(string $preset, ?string $from = null, ?string $to = null): array
    {
        $now = Carbon::now();

        return match ($preset) {
            'week' => [$now->copy()->startOfWeek(), $now->copy()->endOfWeek()],
            'quarter' => [$now->copy()->firstOfQuarter()->startOfDay(), $now->copy()->lastOfQuarter()->endOfDay()],
            'year' => [$now->copy()->startOfYear(), $now->copy()->endOfYear()],
            'custom' => [
                $from ? Carbon::parse($from)->startOfDay() : $now->copy()->startOfMonth(),
                $to ? Carbon::parse($to)->endOfDay() : $now->copy()->endOfDay(),
            ],
            default => [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()],
        };
    }

    /**
     * Determines the trend granularity (day/week/month) based on the period length.
     */
    public function granularityFor(Carbon $from, Carbon $to): string
    {
        $days = $from->diffInDays($to) + 1;

        return match (true) {
            $days <= 31 => self::GRANULARITY_DAY,
            $days <= 180 => self::GRANULARITY_WEEK,
            default => self::GRANULARITY_MONTH,
        };
    }

    /**
     * Generates a full report (current period + comparison period + comparison) for the given filters.
     *
     * If $previousFrom/$previousTo are not given, a comparison period with the identical
     * length, immediately preceding the current one, is computed automatically.
     *
     * @param  array{type?: ?string, platform?: ?string, age_group?: ?string}  $filters
     */
    public function generate(Carbon $from, Carbon $to, array $filters = [], ?Carbon $previousFrom = null, ?Carbon $previousTo = null): array
    {
        $type = $filters['type'] ?? null;
        $platform = $filters['platform'] ?? null;
        $ageGroup = $filters['age_group'] ?? null;

        $current = $this->summarize($from, $to, $type, $platform, $ageGroup);

        $comparisonIsAuto = $previousFrom === null || $previousTo === null;

        if ($comparisonIsAuto) {
            $periodLengthDays = $from->diffInDays($to) + 1;
            $previousTo = $from->copy()->subSecond();
            $previousFrom = $previousTo->copy()->subDays($periodLengthDays - 1)->startOfDay();
        }

        $previous = $this->summarize($previousFrom, $previousTo, $type, $platform, $ageGroup);

        return [
            'from' => $from,
            'to' => $to,
            'previous_from' => $previousFrom,
            'previous_to' => $previousTo,
            'comparison_is_auto' => $comparisonIsAuto,
            'filters' => [
                'type' => $type,
                'platform' => $platform,
                'age_group' => $ageGroup,
            ],
            'current' => $current,
            'previous' => $previous,
            'comparison' => $this->compare($current, $previous),
        ];
    }

    /**
     * Computes the figures for a single period (no comparison).
     */
    protected function summarize(Carbon $from, Carbon $to, ?string $type, ?string $platform, ?string $ageGroup): array
    {
        $incidents = Incident::query()
            ->reportedBetween($from, $to)
            ->ofType($type)
            ->onPlatform($platform)
            ->ofAgeGroup($ageGroup)
            ->get(['id', 'type', 'age_group', 'platform', 'status', 'reported_at']);

        // When a filter is active for a given dimension (e.g. a single type),
        // there's no point listing every other value at 0 - we only show the
        // selected value in that breakdown.
        $typeValues = $type ? [$type] : Incident::types();
        $ageValues = $ageGroup ? [$ageGroup] : Incident::ageGroups();
        $platformValues = $platform ? [$platform] : Incident::platforms();

        return [
            'total' => $incidents->count(),
            'by_type' => $this->countBy($incidents, 'type', $typeValues),
            'by_age_group' => $this->countBy($incidents, 'age_group', $ageValues),
            'by_platform' => $this->countBy($incidents, 'platform', $platformValues),
            'by_status' => $this->countBy($incidents, 'status', Incident::statuses()),
            'trend' => $this->trend($incidents, $from, $to),
        ];
    }

    /**
     * Groups a collection of incidents by a given field, including values with 0 reports,
     * sorted by report count (highest first).
     */
    protected function countBy(Collection $incidents, string $field, array $allValues): Collection
    {
        $grouped = $incidents->countBy($field);

        return collect($allValues)
            ->mapWithKeys(fn ($value) => [$value => $grouped->get($value, 0)])
            ->sortDesc();
    }

    /**
     * Builds a time series (trend) with evenly spaced buckets (including ones with no reports).
     */
    protected function trend(Collection $incidents, Carbon $from, Carbon $to): Collection
    {
        $granularity = $this->granularityFor($from, $to);

        $buckets = collect();
        $cursor = $from->copy();
        $safetyLimit = 5000;

        while ($cursor->lte($to) && $safetyLimit-- > 0) {
            $key = $this->bucketKey($cursor, $granularity);

            if (! $buckets->has($key)) {
                $buckets->put($key, ['label' => $this->bucketLabel($cursor, $granularity), 'count' => 0]);
            }

            $cursor = match ($granularity) {
                self::GRANULARITY_DAY => $cursor->addDay(),
                self::GRANULARITY_WEEK => $cursor->addWeek(),
                self::GRANULARITY_MONTH => $cursor->addMonthNoOverflow(),
            };
        }

        foreach ($incidents as $incident) {
            $key = $this->bucketKey($incident->reported_at, $granularity);

            if ($buckets->has($key)) {
                $bucket = $buckets->get($key);
                $bucket['count']++;
                $buckets->put($key, $bucket);
            }
        }

        return $buckets->values();
    }

    protected function bucketKey(Carbon $date, string $granularity): string
    {
        return match ($granularity) {
            self::GRANULARITY_DAY => $date->format('Y-m-d'),
            self::GRANULARITY_WEEK => $date->format('o-\WW'),
            self::GRANULARITY_MONTH => $date->format('Y-m'),
        };
    }

    protected function bucketLabel(Carbon $date, string $granularity): string
    {
        return match ($granularity) {
            self::GRANULARITY_DAY => $date->format('d.m.Y'),
            self::GRANULARITY_WEEK => $date->copy()->startOfWeek()->format('d.m').' - '.$date->copy()->endOfWeek()->format('d.m.Y'),
            self::GRANULARITY_MONTH => self::MONTHS[(int) $date->format('n')].' '.$date->format('Y'),
        };
    }

    /**
     * Compares the current period with the previous one: overall change (%) and change per incident type.
     */
    protected function compare(array $current, array $previous): array
    {
        $byType = collect($current['by_type'])->mapWithKeys(function ($count, $type) use ($previous) {
            $prevCount = $previous['by_type'][$type] ?? 0;

            return [$type => [
                'current' => $count,
                'previous' => $prevCount,
                'change_percent' => $this->percentChange($prevCount, $count),
            ]];
        });

        return [
            'total_change_percent' => $this->percentChange($previous['total'], $current['total']),
            'by_type' => $byType,
        ];
    }

    /**
     * Percentage change from $previous to $current.
     * Returns null when there's no previous baseline to compare against (0 -> >0), which
     * should be displayed as "new".
     */
    protected function percentChange(int $previous, int $current): ?float
    {
        if ($previous === 0) {
            return $current === 0 ? 0.0 : null;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }
}
