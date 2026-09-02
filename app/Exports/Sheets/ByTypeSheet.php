<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class ByTypeSheet implements FromArray, WithHeadings, WithTitle
{
    public function __construct(protected array $report, protected array $sections = [])
    {
    }

    public function array(): array
    {
        $withComparison = in_array('comparison', $this->sections, true);
        $comparison = $this->report['comparison']['by_type'];
        $rows = [];

        foreach ($this->report['current']['by_type'] as $type => $count) {
            if (! $withComparison) {
                $rows[] = [$type, $count];

                continue;
            }

            $change = $comparison[$type]['change_percent'] ?? null;

            $rows[] = [
                $type,
                $count,
                $comparison[$type]['previous'] ?? 0,
                $change === null ? 'new' : $change,
            ];
        }

        return $rows;
    }

    public function headings(): array
    {
        return in_array('comparison', $this->sections, true)
            ? ['Incident type', 'Reports (current period)', 'Reports (previous period)', 'Change (%)']
            : ['Incident type', 'Reports'];
    }

    public function title(): string
    {
        return 'By type';
    }
}
