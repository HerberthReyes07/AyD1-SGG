<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Member;
use App\Services\PeriodicMeasurementService;
use App\Services\TrainerAssignmentService;
use App\Exports\PhysicalProgressExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PhysicalProgressController extends Controller
{
    public function __construct(
        private PeriodicMeasurementService $measurementService,
        private TrainerAssignmentService $assignmentService,
    ) {}

    public function index(Request $request)
    {
        $members = Member::with('user')->get();
        $report = null;

        if ($request->filled('member_id')) {
            $report = $this->buildReport($request);
        }

        return view('admin.reports.physical-progress.index', [
            'members' => $members,
            'report' => $report,
            'filters' => [
                'member_id' => $request->input('member_id'),
                'start_date' => $request->input('start_date'),
                'end_date' => $request->input('end_date'),
            ],
        ]);
    }

    public function exportExcel(Request $request)
    {
        $report = $this->buildReport($request);

        $filename = 'progreso-fisico-' . str($report['member']->user->first_name . '-' . $report['member']->user->last_name)->slug() . '.xlsx';

        return Excel::download(new PhysicalProgressExport($report['measurements']), $filename);
    }

    public function exportPdf(Request $request)
    {
        $report = $this->buildReport($request);

        $pdf = Pdf::loadView('admin.reports.physical-progress.pdf', [
            'member' => $report['member'],
            'measurements' => $report['measurements'],
            'assignmentPeriods' => $report['assignmentPeriods'],
            'startDate' => $request->input('start_date'),
            'endDate' => $request->input('end_date'),
            'chartImage' => $request->input('chart_image'),
        ]);

        $filename = 'progreso-fisico-' . str($report['member']->user->first_name . '-' . $report['member']->user->last_name)->slug() . '.pdf';

        return $pdf->download($filename);
    }

    private function buildReport(Request $request): array
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,user_id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $member = Member::with('user')->findOrFail($validated['member_id']);

        $measurements = $this->measurementService->getFullHistoryForMember(
            $member->user_id,
            $validated['start_date'] ?? null,
            $validated['end_date'] ?? null
        );

        $assignmentPeriods = $this->assignmentService->getAllForMemberWithRatings($member->user_id);

        // dd($assignmentPeriods);

        return compact('member', 'measurements', 'assignmentPeriods');
    }
}
