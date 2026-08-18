<?php

namespace App\Http\Controllers;

use App\Exports\GuestPassReportExport;
use App\Models\GuestPass;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class GuestPassReportController extends Controller
{
    public function index(Request $request)
    {
        $report = $this->buildReport($request);

        $records = $report['records'];
        $summary = $report['summary'];

        return view(
            'guest-pass-reports.index',
            compact('records', 'summary')
        );
    }

    public function exportExcel(Request $request)
    {
        $report = $this->buildReport($request);

        $filename = 'reporte-pases-invitado-'
            .now()->format('Ymd-His')
            .'.xlsx';

        return Excel::download(
            new GuestPassReportExport($report['records']),
            $filename,
            \Maatwebsite\Excel\Excel::XLSX
        );
    }

    public function exportPdf(Request $request)
    {
        $report = $this->buildReport($request);

        $pdf = Pdf::loadView(
            'guest-pass-reports.pdf',
            [
                'records' => $report['records'],
                'summary' => $report['summary'],
                'filters' => $report['filters'],
            ]
        )->setPaper('a4', 'landscape');

        $filename = 'reporte-pases-invitado-'
            .now()->format('Ymd-His')
            .'.pdf';

        return $pdf->download($filename);
    }

    private function buildReport(Request $request): array
    {
        $validated = $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $dateFrom = isset($validated['date_from'])
            ? Carbon::parse($validated['date_from'])->startOfDay()
            : null;

        $dateTo = isset($validated['date_to'])
            ? Carbon::parse($validated['date_to'])->endOfDay()
            : null;

        $query = GuestPass::with('registeredBy');

        if ($dateFrom) {
            $query->whereDate('visit_date', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('visit_date', '<=', $dateTo);
        }

        $records = $query
            ->orderByDesc('visit_date')
            ->orderByDesc('id')
            ->get();

        // dpi es unico por diseno (un pase de invitado es una visita de prueba unica por persona),
        // asi que el total de registros ya equivale a invitados distintos, no hace falta contarlos aparte
        $total = $records->count();
        $activeDays = $records->pluck('visit_date')->map(fn ($date) => $date->toDateString())->unique()->count();
        $averagePerDay = $activeDays > 0 ? round($total / $activeDays, 1) : 0;

        return [
            'records' => $records,

            'summary' => [
                'total' => $total,
                'active_days' => $activeDays,
                'average_per_day' => $averagePerDay,
            ],

            'filters' => [
                'date_from' => $validated['date_from'] ?? null,
                'date_to' => $validated['date_to'] ?? null,
            ],
        ];
    }
}
