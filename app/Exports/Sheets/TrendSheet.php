<?php

namespace App\Exports\Sheets;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class TrendSheet implements FromArray, WithHeadings, WithTitle
{
    public function __construct(protected array $report)
    {
    }

    public function array(): array
    {
        return collect($this->report['current']['trend'])
            ->map(fn (array $bucket) => [$bucket['label'], $bucket['count']])
            ->all();
    }

    public function headings(): array
    {
        return ['Period', 'Number of reports'];
    }

    public function title(): string
    {
        return 'Trend';
    }
}
