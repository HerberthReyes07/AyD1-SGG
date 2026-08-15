<?php

namespace App\Http\Controllers;

use App\Exports\IncomeReportExport;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class IncomeReportController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    public function index(Request $request)
    {
        $validated = $request->validate([
            'group_by' => 'nullable|in:week,month',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $groupBy = $validated['group_by'] ?? 'month';
        $startDate = $validated['start_date'] ?? null;
        $endDate = $validated['end_date'] ?? null;

        $report = $this->reportService->getIncomeReport($groupBy, $startDate, $endDate);

        return view('admin.reports.income.index', compact('report'));
    }

    public function exportExcel(Request $request)
    {
        $groupBy = $request->query('group_by', 'month');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $report = $this->reportService->getIncomeReport($groupBy, $startDate, $endDate);

        $filename = 'reporte-ingresos-' . now()->format('Ymd-His') . '.xlsx';

        return Excel::download(new IncomeReportExport($report), $filename, \Maatwebsite\Excel\Excel::XLSX);
    }

    public function exportCsv(Request $request)
    {
        $groupBy = $request->query('group_by', 'month');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $report = $this->reportService->getIncomeReport($groupBy, $startDate, $endDate);

        $filename = 'reporte-ingresos-' . now()->format('Ymd-His') . '.csv';

        return Excel::download(new IncomeReportExport($report), $filename, \Maatwebsite\Excel\Excel::CSV);
    }

    public function exportPdf(Request $request)
    {
        $groupBy = $request->input('group_by', 'month');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $chartImage = $request->input('chart_image');

        $report = $this->reportService->getIncomeReport($groupBy, $startDate, $endDate);

        $pdf = Pdf::loadView('admin.reports.income.pdf', compact('report', 'chartImage'));

        $filename = 'reporte-ingresos-' . now()->format('Ymd-His') . '.pdf';

        return $pdf->download($filename);
    }
}
