<?php

namespace App\Http\Controllers;

use App\Enums\ClassEnrollmentStatus;
use App\Exports\ClassAttendanceReportExport;
use App\Models\ClassEnrollment;
use App\Models\GroupClass;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ClassAttendanceReportController extends Controller
{
    public function index(Request $request)
    {
        $report = $this->buildReport($request);

        $groupClasses = GroupClass::query()
            ->orderBy('name')
            ->get();

        $records = $report['records'];
        $summary = $report['summary'];

        return view(
            'class-attendance-reports.index',
            compact(
                'records',
                'summary',
                'groupClasses'
            )
        );
    }

    public function exportExcel(Request $request)
    {
        $report = $this->buildReport($request);

        $filename = 'reporte-asistencia-clases-'
            . now()->format('Ymd-His')
            . '.xlsx';

        return Excel::download(
            new ClassAttendanceReportExport(
                $report['records']
            ),
            $filename,
            \Maatwebsite\Excel\Excel::XLSX
        );
    }

    public function exportPdf(Request $request)
    {
        $report = $this->buildReport($request);

        $pdf = Pdf::loadView(
            'class-attendance-reports.pdf',
            [
                'records' => $report['records'],
                'summary' => $report['summary'],
                'filters' => $report['filters'],
            ]
        )->setPaper('a4', 'landscape');

        $filename = 'reporte-asistencia-clases-'
            . now()->format('Ymd-His')
            . '.pdf';

        return $pdf->download($filename);
    }

    

    private function buildReport(Request $request): array
    {
        $validated = $request->validate([
            'date_from' => [
                'nullable',
                'date',
            ],
            'date_to' => [
                'nullable',
                'date',
                'after_or_equal:date_from',
            ],
            'group_class_id' => [
                'nullable',
                'integer',
                'exists:group_classes,id',
            ],
        ]);

        $dateFrom = isset($validated['date_from'])
            ? Carbon::parse(
                $validated['date_from']
            )->startOfDay()
            : null;

        $dateTo = isset($validated['date_to'])
            ? Carbon::parse(
                $validated['date_to']
            )->endOfDay()
            : null;

        $query = ClassEnrollment::with([
            'member.user',
            'classSession.groupClass.category',
            'classAttendance',
        ])
            ->whereIn('status', [
                ClassEnrollmentStatus::Attended->value,
                ClassEnrollmentStatus::NoShow->value,
            ])
            ->whereHas(
                'classSession',
                function ($query) use (
                    $dateFrom,
                    $dateTo,
                    $validated
                ) {
                    if ($dateFrom) {
                        $query->where(
                            'starts_at',
                            '>=',
                            $dateFrom
                        );
                    }

                    if ($dateTo) {
                        $query->where(
                            'starts_at',
                            '<=',
                            $dateTo
                        );
                    }

                    if (
                        ! empty(
                            $validated['group_class_id']
                        )
                    ) {
                        $query->where(
                            'group_class_id',
                            $validated[
                                'group_class_id'
                            ]
                        );
                    }
                }
            );

        $records = $query
            ->get()
            ->sortByDesc(
                fn ($enrollment) =>
                    $enrollment
                        ->classSession
                        ->starts_at
            )
            ->values();

        $attended = $records
            ->where(
                'status',
                ClassEnrollmentStatus::Attended
            )
            ->count();

        $noShow = $records
            ->where(
                'status',
                ClassEnrollmentStatus::NoShow
            )
            ->count();

        $total = $records->count();

        $attendancePercentage = $total > 0
            ? round(
                ($attended / $total) * 100,
                2
            )
            : 0;

            $selectedClass = null;

            if (! empty($validated['group_class_id'])) {
                $selectedClass = GroupClass::find(
                    $validated['group_class_id']
                );
            }

        return [
            'records' => $records,

            'summary' => [
                'total' => $total,
                'attended' => $attended,
                'no_show' => $noShow,
                'percentage' => $attendancePercentage,
            ],

            'filters' => [
                'date_from' => $validated['date_from'] ?? null,
                'date_to' => $validated['date_to'] ?? null,
                'group_class_id' =>
                    $validated['group_class_id'] ?? null,
                'group_class_name' =>
                    $selectedClass?->name,
            ],
        ];
    }
}