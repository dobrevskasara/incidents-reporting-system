<?php

namespace Database\Seeders;

use App\Models\Incident;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class IncidentSeeder extends Seeder
{
    /**
     * Generates demo data for reported incidents over the last 24 months,
     * with a mild upward trend, so the reports (trend and comparison with
     * the previous period) show meaningful results.
     */
    public function run(): void
    {
        $months = collect(range(23, 0))->map(
            fn (int $monthsAgo) => Carbon::now()->subMonths($monthsAgo)->startOfMonth()
        );

        foreach ($months as $index => $monthStart) {
            // Mild upward trend: starts around ~25 reports, up to ~70 in the latest month.
            $baseCount = 25 + (int) round($index * 2.2);
            $count = random_int(
                max(5, $baseCount - 8),
                $baseCount + 8
            );

            Incident::factory()
                ->count($count)
                ->make()
                ->each(function (Incident $incident) use ($monthStart) {
                    $reportedAt = $monthStart->copy()
                        ->addDays(random_int(0, $monthStart->daysInMonth - 1))
                        ->addHours(random_int(0, 23))
                        ->addMinutes(random_int(0, 59));

                    $incident->reported_at = $reportedAt;
                    $incident->created_at = $reportedAt;
                    $incident->updated_at = $reportedAt;
                    $incident->save();
                });
        }
    }
}
