<?php

namespace App\Http\Controllers;

use App\Exports\MembershipExpirationExport;
use App\Services\ReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class MembershipExpirationReportController extends Controller
{
    public function __construct(
        protected ReportService $reportService
    ) {}

    public function index(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $startDate = $validated['start_date'] ?? null;
        $endDate = $validated['end_date'] ?? null;

        $report = $this->reportService->getMembershipExpirationReport($startDate, $endDate);

        return view('admin.reports.membership-expiration.index', compact('report'));
    }

    public function exportExcel(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $report = $this->reportService->getMembershipExpirationReport($startDate, $endDate);

        $filename = 'reporte-vencimiento-membresias-' . now()->format('Ymd-His') . '.xlsx';

        return Excel::download(new MembershipExpirationExport($report), $filename, \Maatwebsite\Excel\Excel::XLSX);
    }

    public function exportCsv(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $report = $this->reportService->getMembershipExpirationReport($startDate, $endDate);

        $filename = 'reporte-vencimiento-membresias-' . now()->format('Ymd-His') . '.csv';

        return Excel::download(new MembershipExpirationExport($report), $filename, \Maatwebsite\Excel\Excel::CSV);
    }

    public function exportPdf(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $chartImage = $request->input('chart_image');

        $report = $this->reportService->getMembershipExpirationReport($startDate, $endDate);

        $pdf = Pdf::loadView('admin.reports.membership-expiration.pdf', compact('report', 'chartImage'));

        $filename = 'reporte-vencimiento-membresias-' . now()->format('Ymd-His') . '.pdf';

        return $pdf->download($filename);
    }
}
