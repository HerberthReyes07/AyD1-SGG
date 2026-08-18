<?php

namespace App\Http\Controllers;

use App\Enums\ClassEnrollmentStatus;
use App\Enums\ClassSessionStatus;
use App\Enums\WaitlistStatus;
use App\Models\ClassSession;
use Carbon\Carbon;
use Illuminate\Http\Request;

class GroupClassReportController extends Controller
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
        ]);

        $dateFrom = isset($validated['date_from'])
            ? Carbon::parse($validated['date_from'])->startOfDay()
            : null;

        $dateTo = isset($validated['date_to'])
            ? Carbon::parse($validated['date_to'])->endOfDay()
            : null;

        $sessions = ClassSession::with([
            'groupClass.category',
            'enrollments.classRating',
            'waitlists',
        ])
            ->when(
                $dateFrom,
                fn ($query) =>
                    $query->where('starts_at', '>=', $dateFrom)
            )
            ->when(
                $dateTo,
                fn ($query) =>
                    $query->where('starts_at', '<=', $dateTo)
            )
            ->orderByDesc('starts_at')
            ->get();

        $summary = [
            'total' => $sessions->count(),

            'scheduled' => $sessions
                ->where(
                    'status',
                    ClassSessionStatus::Scheduled
                )
                ->count(),

            'completed' => $sessions
                ->where(
                    'status',
                    ClassSessionStatus::Completed
                )
                ->count(),

            'cancelled' => $sessions
                ->where(
                    'status',
                    ClassSessionStatus::Cancelled
                )
                ->count(),

            'rescheduled' => $sessions
                ->where(
                    'status',
                    ClassSessionStatus::Rescheduled
                )
                ->count(),
        ];

        $classes = $sessions
            ->groupBy('group_class_id')
            ->map(function ($classSessions) {

                $groupClass = $classSessions
                    ->first()
                    ->groupClass;

                $enrollments = $classSessions
                    ->flatMap(
                        fn ($session) =>
                            $session->enrollments
                    );

                $waitlists = $classSessions
                    ->flatMap(
                        fn ($session) =>
                            $session->waitlists
                    );

                $activeEnrollments = $enrollments
                    ->filter(
                        fn ($enrollment) =>
                            in_array(
                                $enrollment->status,
                                [
                                    ClassEnrollmentStatus::Enrolled,
                                    ClassEnrollmentStatus::Attended,
                                    ClassEnrollmentStatus::NoShow,
                                ],
                                true
                            )
                    );

                $attended = $enrollments
                    ->where(
                        'status',
                        ClassEnrollmentStatus::Attended
                    )
                    ->count();

                $noShow = $enrollments
                    ->where(
                        'status',
                        ClassEnrollmentStatus::NoShow
                    )
                    ->count();

                $ratings = $enrollments
                    ->map(
                        fn ($enrollment) =>
                            $enrollment->classRating?->rating
                    )
                    ->filter();

                return [
                    'name' => $groupClass->name,

                    'category' =>
                        $groupClass->category?->name
                        ?? 'Sin categoria',

                    'sessions' =>
                        $classSessions->count(),

                    'enrollments' =>
                        $activeEnrollments->count(),

                    'waitlist_requests' =>
                        $waitlists->count(),

                    'waitlist_promotions' =>
                        $waitlists
                            ->where(
                                'status',
                                WaitlistStatus::Notified
                            )
                            ->count(),

                    'attended' => $attended,

                    'no_show' => $noShow,

                    'average_rating' =>
                        $ratings->isNotEmpty()
                            ? round(
                                $ratings->avg(),
                                2
                            )
                            : null,

                    'ratings_count' =>
                        $ratings->count(),
                ];
            })
            ->sortByDesc(function ($class) {
                return
                    $class['enrollments']
                    + $class['waitlist_requests'];
            })
            ->values();

        $changedSessions = $sessions
            ->filter(
                fn ($session) =>
                    in_array(
                        $session->status,
                        [
                            ClassSessionStatus::Cancelled,
                            ClassSessionStatus::Rescheduled,
                        ],
                        true
                    )
            )
            ->values();

        return view(
            'group-class-reports.index',
            compact(
                'summary',
                'classes',
                'changedSessions',
                'dateFrom',
                'dateTo'
            )
        );
    }
}