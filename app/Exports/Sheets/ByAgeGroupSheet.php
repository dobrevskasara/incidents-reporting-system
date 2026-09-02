<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class ByAgeGroupSheet implements FromArray, WithHeadings, WithTitle
{
    public function __construct(protected array $report, protected array $sections = [])
    {
    }

    public function array(): array
    {
        $withComparison = in_array('comparison', $this->sections, true);
        $total = max($this->report['current']['total'], 1);
        $previous = $this->report['previous']['by_age_group'];
        $rows = [];

        foreach ($this->report['current']['by_age_group'] as $group => $count) {
            $share = round(($count / $total) * 100, 1);

            $rows[] = $withComparison
                ? [$group, $count, $share, $previous[$group] ?? 0]
                : [$group, $count, $share];
        }

        return $rows;
    }

    public function headings(): array
    {
        return in_array('comparison', $this->sections, true)
            ? ['Age group', 'Reports (current period)', 'Share of total (%)', 'Reports (previous period)']
            : ['Age group', 'Reports', 'Share of total (%)'];
    }

    public function title(): string
    {
        return 'By age';
    }
}
