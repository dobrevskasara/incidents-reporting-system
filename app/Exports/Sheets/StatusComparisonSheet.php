<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class StatusComparisonSheet implements FromArray, WithHeadings, WithTitle
{
    public function __construct(protected array $report, protected array $sections = [])
    {
    }

    public function array(): array
    {
        $withComparison = in_array('comparison', $this->sections, true);
        $previous = $this->report['previous']['by_status'];
        $rows = [];

        foreach ($this->report['current']['by_status'] as $status => $count) {
            $rows[] = $withComparison
                ? [$status, $count, $previous[$status] ?? 0]
                : [$status, $count];
        }

        return $rows;
    }

    public function headings(): array
    {
        return in_array('comparison', $this->sections, true)
            ? ['Status', 'Current period', 'Previous period']
            : ['Status', 'Reports'];
    }

    public function title(): string
    {
        return 'Status';
    }
}
