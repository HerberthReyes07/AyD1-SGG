<?php

namespace App\Http\Controllers;

use App\Enums\ClassEnrollmentStatus;
use App\Models\ClassEnrollment;
use App\Models\GroupClass;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ClassAttendanceReportController extends Controller
{
    public function index(Request $request)
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
            ? Carbon::parse($validated['date_from'])->startOfDay()
            : null;

        $dateTo = isset($validated['date_to'])
            ? Carbon::parse($validated['date_to'])->endOfDay()
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
            ->whereHas('classSession', function ($query) use (
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

                if (! empty($validated['group_class_id'])) {
                    $query->where(
                        'group_class_id',
                        $validated['group_class_id']
                    );
                }
            });

        $records = $query
            ->get()
            ->sortByDesc(function ($enrollment) {
                return $enrollment
                    ->classSession
                    ->starts_at;
            })
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
            ? round(($attended / $total) * 100, 2)
            : 0;

        $summary = [
            'total' => $total,
            'attended' => $attended,
            'no_show' => $noShow,
            'percentage' => $attendancePercentage,
        ];

        $groupClasses = GroupClass::query()
            ->orderBy('name')
            ->get();

        return view(
            'class-attendance-reports.index',
            compact(
                'records',
                'summary',
                'groupClasses'
            )
        );
    }
}