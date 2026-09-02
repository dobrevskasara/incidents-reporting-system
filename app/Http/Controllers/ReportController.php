<?php

namespace App\Http\Controllers;

use App\Exports\IncidentReportExport;
use App\Http\Requests\ReportFilterRequest;
use App\Models\Incident;
use App\Services\IncidentReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    /** @var array<int, string> */
    protected const ALL_SECTIONS = ['type', 'age', 'platform', 'status', 'trend', 'comparison'];

    public function __construct(protected IncidentReportService $reports)
    {
    }

    /**
     * Displays the dashboard with filters and the generated report on screen.
     */
    public function index(ReportFilterRequest $request)
    {
        $built = $this->buildReport($request);

        return view('reports.index', [
            'report' => $built['report'],
            'period' => $built['period'],
            'filters' => $built['filters'],
            'sections' => $built['sections'],
            'compareMode' => $built['compareMode'],
            'types' => Incident::types(),
            'platforms' => Incident::platforms(),
            'ageGroups' => Incident::ageGroups(),
        ]);
    }

    /**
     * Generates and downloads a PDF version of the report.
     */
    public function exportPdf(ReportFilterRequest $request): Response
    {
        $built = $this->buildReport($request);

        $pdf = Pdf::loadView('reports.pdf', [
            'report' => $built['report'],
            'sections' => $built['sections'],
        ])->setPaper('a4', 'portrait');

        return $pdf->download($this->filename($built['report'], 'pdf'));
    }

    /**
     * Generates and downloads an Excel (.xlsx) version of the report with sheets only for the selected sections.
     */
    public function exportExcel(ReportFilterRequest $request)
    {
        $built = $this->buildReport($request);

        return Excel::download(
            new IncidentReportExport($built['report'], $built['sections']),
            $this->filename($built['report'], 'xlsx')
        );
    }

    /**
     * @return array{report: array, period: string, filters: array, sections: array<int, string>, compareMode: string}
     */
    protected function buildReport(ReportFilterRequest $request): array
    {
        $period = $request->input('period', 'month');

        [$from, $to] = $this->reports->resolvePeriod(
            $period,
            $request->input('from'),
            $request->input('to')
        );

        $filters = $request->only(['type', 'platform', 'age_group']);

        // Unchecked checkboxes aren't sent in the HTTP request at all, so we can't
        // tell "the form was never submitted" apart from "everything was deliberately
        // deselected". That's why the form also sends a hidden 'sections_submitted' field.
        $sections = $request->boolean('sections_submitted')
            ? array_values(array_intersect(self::ALL_SECTIONS, $request->input('sections', [])))
            : self::ALL_SECTIONS;

        $compareMode = $request->input('compare_mode', 'auto');
        $previousFrom = $previousTo = null;

        if ($compareMode === 'custom' && $request->filled('compare_from') && $request->filled('compare_to')) {
            $previousFrom = Carbon::parse($request->input('compare_from'))->startOfDay();
            $previousTo = Carbon::parse($request->input('compare_to'))->endOfDay();
        }

        return [
            'report' => $this->reports->generate($from, $to, $filters, $previousFrom, $previousTo),
            'period' => $period,
            'filters' => $filters,
            'sections' => $sections,
            'compareMode' => $compareMode,
        ];
    }

    protected function filename(array $report, string $extension): string
    {
        return sprintf(
            'incident-report_%s_to_%s.%s',
            $report['from']->format('Y-m-d'),
            $report['to']->format('Y-m-d'),
            $extension
        );
    }
}
