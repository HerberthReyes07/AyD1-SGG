<?php

namespace App\Http\Controllers;

use App\Enums\ClassEnrollmentStatus;
use App\Enums\ClassSessionStatus;
use App\Enums\WaitlistStatus;
use App\Models\ClassSession;
use App\Services\GroupClassEnrollmentService;
use Illuminate\Http\Request;

class MemberClassController extends Controller
{
    public function __construct(
        private readonly GroupClassEnrollmentService $enrollmentService
    ) {
    }

    public function index(Request $request)
    {
        $member = $request->user()->member;

        abort_if(! $member, 403);

        $query = ClassSession::with([
            'groupClass.category',
            'groupClass.trainer.user',
        ])
            ->withCount([
                'enrollments as enrolled_count' => function ($query) {
                    $query->where(
                        'status',
                        ClassEnrollmentStatus::Enrolled->value
                    );
                },

                'waitlists as waiting_count' => function ($query) {
                    $query->where(
                        'status',
                        WaitlistStatus::Waiting->value
                    );
                },
            ])
            ->whereIn('status', [
                ClassSessionStatus::Scheduled->value,
                ClassSessionStatus::Rescheduled->value,
            ])
            ->where('starts_at', '>', now())
            ->whereHas('groupClass', function ($query) {
                $query->where('is_active', true);
            });

        if ($request->filled('search')) {
            $search = $request->search;

            $query->whereHas(
                'groupClass',
                function ($query) use ($search) {
                    $query->where(
                        'name',
                        'like',
                        '%' . $search . '%'
                    );
                }
            );
        }

        if ($request->filled('category_id')) {
            $query->whereHas(
                'groupClass',
                function ($query) use ($request) {
                    $query->where(
                        'category_id',
                        $request->category_id
                    );
                }
            );
        }

        $sessions = $query
            ->orderBy('starts_at')
            ->get();

        $categories = \App\Models\ClassCategory::orderBy('name')
            ->get();

        $enrollments = $member->classEnrollments()
            ->whereIn(
                'class_session_id',
                $sessions->pluck('id')
            )
            ->get()
            ->keyBy('class_session_id');

        $waitlists = $member->classWaitlists()
            ->whereIn(
                'class_session_id',
                $sessions->pluck('id')
            )
            ->get()
            ->keyBy('class_session_id');

        return view('member-classes.index', compact(
            'member',
            'sessions',
            'categories',
            'enrollments',
            'waitlists'
        ));
    }

    public function enroll(
        Request $request,
        ClassSession $session
    ) {
        $member = $request->user()->member;

        abort_if(! $member, 403);

        $result = $this->enrollmentService
            ->enroll($member, $session);

        return redirect()
            ->route('member-classes.index')
            ->with('success', $result['message']);
    }

    public function cancel(
        Request $request,
        ClassSession $session
    ) {
        $member = $request->user()->member;

        abort_if(! $member, 403);

        $result = $this->enrollmentService
            ->cancel($member, $session);

        return redirect()
            ->route('member-classes.index')
            ->with('success', $result['message']);
    }

    public function cancelWaitlist(
        Request $request,
        ClassSession $session
    ) {
        $member = $request->user()->member;

        abort_if(! $member, 403);

        $result = $this->enrollmentService
            ->cancelWaitlist($member, $session);

        return redirect()
            ->route('member-classes.index')
            ->with('success', $result['message']);
    }

    public function history(Request $request)
    {
        $member = $request->user()->member;

        abort_if(! $member, 403);

        $enrollments = $member->classEnrollments()
            ->with([
                'classSession.groupClass.category',
                'classSession.groupClass.trainer.user',
                'classRating',
            ])
            ->whereIn('status', [
                ClassEnrollmentStatus::Attended->value,
                ClassEnrollmentStatus::NoShow->value,
            ])
            ->whereHas('classSession', function ($query) {
                $query->where(
                    'status',
                    ClassSessionStatus::Completed->value
                );
            })
            ->whereHas('classSession')
            ->get()
            ->sortByDesc(function ($enrollment) {
                return $enrollment->classSession->starts_at;
            });

        return view(
            'member-classes.history',
            compact('enrollments')
        );
    }
}