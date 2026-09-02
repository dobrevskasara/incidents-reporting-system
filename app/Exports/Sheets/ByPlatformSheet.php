<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class ByPlatformSheet implements FromArray, WithHeadings, WithTitle
{
    public function __construct(protected array $report, protected array $sections = [])
    {
    }

    public function array(): array
    {
        $withComparison = in_array('comparison', $this->sections, true);
        $total = max($this->report['current']['total'], 1);
        $previous = $this->report['previous']['by_platform'];
        $rows = [];

        foreach ($this->report['current']['by_platform'] as $platform => $count) {
            $share = round(($count / $total) * 100, 1);

            $rows[] = $withComparison
                ? [$platform, $count, $share, $previous[$platform] ?? 0]
                : [$platform, $count, $share];
        }

        return $rows;
    }

    public function headings(): array
    {
        return in_array('comparison', $this->sections, true)
            ? ['Platform', 'Reports (current period)', 'Share of total (%)', 'Reports (previous period)']
            : ['Platform', 'Reports', 'Share of total (%)'];
    }

    public function title(): string
    {
        return 'By platform';
    }
}
