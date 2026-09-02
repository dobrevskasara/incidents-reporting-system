<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class SummarySheet implements FromArray, WithHeadings, WithTitle
{
    public function __construct(protected array $report, protected array $sections = [])
    {
    }

    public function array(): array
    {
        $current = $this->report['current'];
        $rows = [
            ['Report period', $this->report['from']->format('d.m.Y').' - '.$this->report['to']->format('d.m.Y')],
            ['Filter: type', $this->report['filters']['type'] ?? 'all'],
            ['Filter: platform', $this->report['filters']['platform'] ?? 'all'],
            ['Filter: age group', $this->report['filters']['age_group'] ?? 'all'],
            ['Total reports in current period', $current['total']],
        ];

        if (in_array('comparison', $this->sections, true)) {
            $previous = $this->report['previous'];
            $changePercent = $this->report['comparison']['total_change_percent'];

            array_splice($rows, 1, 0, [
                ['Previous period (for comparison)', $this->report['previous_from']->format('d.m.Y').' - '.$this->report['previous_to']->format('d.m.Y')],
            ]);

            $rows[] = ['Total reports in previous period', $previous['total']];
            $rows[] = ['Change vs. previous period (%)', $changePercent === null ? 'no previous baseline' : $changePercent];
        }

        return $rows;
    }

    public function headings(): array
    {
        return ['Metric', 'Value'];
    }

    public function title(): string
    {
        return 'Summary';
    }
}
