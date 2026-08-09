<?php

namespace App\Http\Controllers;

use App\Enums\ClassEnrollmentStatus;
use App\Enums\WaitlistStatus;
use App\Models\ClassSession;
use App\Models\Member;
use App\Services\GroupClassEnrollmentService;
use Illuminate\Http\Request;

class ClassEnrollmentController extends Controller
{
    public function __construct(
        private readonly GroupClassEnrollmentService $enrollmentService
    ) {
    }

    public function index(ClassSession $session)
    {
        $session->load([
            'groupClass.category',
            'groupClass.trainer.user',
        ]);

        $enrollments = $session->enrollments()
            ->with('member.user')
            ->where(
                'status',
                ClassEnrollmentStatus::Enrolled->value
            )
            ->orderBy('enrollment_date')
            ->get();

        $waitlists = $session->waitlists()
            ->with([
                'member.user',
                'member.memberships.plan',
            ])
            ->where(
                'status',
                WaitlistStatus::Waiting->value
            )
            ->orderBy('created_at')
            ->get();

        $members = Member::with('user')
            ->whereHas('user', function ($query) {
                $query->where('is_active', true);
            })
            ->orderBy('user_id')
            ->get();

        return view('class-enrollments.index', compact(
            'session',
            'enrollments',
            'waitlists',
            'members'
        ));
    }

    public function store(
        Request $request,
        ClassSession $session
    ) {
        $validated = $request->validate([
            'member_id' => [
                'required',
                'integer',
                'exists:members,user_id',
            ],
        ]);

        $member = Member::findOrFail(
            $validated['member_id']
        );

        $result = $this->enrollmentService
            ->enroll($member, $session);

        return redirect()
            ->route(
                'class-enrollments.index',
                $session
            )
            ->with(
                'success',
                $result['message']
            );
    }

    public function cancel(
        ClassSession $session,
        Member $member
    ) {
        $result = $this->enrollmentService
            ->cancel($member, $session);

        return redirect()
            ->route(
                'class-enrollments.index',
                $session
            )
            ->with(
                'success',
                $result['message']
            );
    }
}