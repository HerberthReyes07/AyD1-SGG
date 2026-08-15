<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Member;
use App\Services\AttendanceService;
use App\Services\MemberService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AttendanceController extends Controller
{
    public function __construct(
        private readonly AttendanceService $attendanceService,
        private readonly MemberService $memberService
    ) {}

    public function index(Request $request)
    {
        $members = collect();

        if ($request->filled('search')) {
            $members = $this->memberService
                ->getAllMembersFiltering($request->search)
                ->map(function ($user) {
                    $membership = $this->attendanceService->getMembershipForToday($user->member);
                    $openAttendance = $this->attendanceService->getOpenAttendanceForMember($user->member);

                    return [
                        'user' => $user,
                        'membership' => $membership,
                        'openAttendance' => $openAttendance,
                    ];
                });
        }

        $todaysAttendances = $this->attendanceService->getTodaysAttendances();

        return view('attendance.index', compact('members', 'todaysAttendances'));
    }

    public function checkIn(Request $request)
    {
        $validated = $request->validate([
            'member_id' => ['required', Rule::exists('members', 'user_id')],
        ]);

        $member = Member::findOrFail($validated['member_id']);

        try {
            $this->attendanceService->checkIn($member);
        } catch (ValidationException $e) {
            return back()->with('error', collect($e->errors())->flatten()->first());
        }

        return back()->with('success', 'Check-in registrado correctamente.');
    }

    public function checkOut(Attendance $attendance)
    {
        try {
            $this->attendanceService->checkOut($attendance);
        } catch (ValidationException $e) {
            return back()->with('error', collect($e->errors())->flatten()->first());
        }

        return back()->with('success', 'Check-out registrado correctamente.');
    }
}
