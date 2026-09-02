<?php

namespace App\Exports;

use App\Exports\Sheets\ByAgeGroupSheet;
use App\Exports\Sheets\ByPlatformSheet;
use App\Exports\Sheets\ByTypeSheet;
use App\Exports\Sheets\StatusComparisonSheet;
use App\Exports\Sheets\SummarySheet;
use App\Exports\Sheets\TrendSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class IncidentReportExport implements WithMultipleSheets
{
    /**
     * @param  array<int, string>  $sections
     */
    public function __construct(protected array $report, protected array $sections)
    {
    }

    /**
     * @return array<int, object>
     */
    public function sheets(): array
    {
        $sheets = [new SummarySheet($this->report, $this->sections)];

        if (in_array('type', $this->sections, true)) {
            $sheets[] = new ByTypeSheet($this->report, $this->sections);
        }

        if (in_array('age', $this->sections, true)) {
            $sheets[] = new ByAgeGroupSheet($this->report, $this->sections);
        }

        if (in_array('platform', $this->sections, true)) {
            $sheets[] = new ByPlatformSheet($this->report, $this->sections);
        }

        if (in_array('trend', $this->sections, true)) {
            $sheets[] = new TrendSheet($this->report);
        }

        if (in_array('status', $this->sections, true)) {
            $sheets[] = new StatusComparisonSheet($this->report, $this->sections);
        }

        return $sheets;
    }
}
