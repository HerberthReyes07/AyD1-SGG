<?php

namespace App\Services;

use App\Enums\MembershipStatus;
use App\Models\Attendance;
use App\Models\Member;
use App\Models\MemberMembership;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class AttendanceService
{
    // membresia cuyo rango cubre hoy, sin filtrar por status, para poder explicar el motivo si esta mal
    public function getMembershipForToday(Member $member): ?MemberMembership
    {
        $today = today();

        return $member->memberships()
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->with('plan')
            ->orderByDesc('start_date')
            ->first();
    }

    public function getOpenAttendanceForMember(Member $member): ?Attendance
    {
        return Attendance::where('member_id', $member->user_id)
            ->whereDate('check_in_at', today())
            ->whereNull('check_out_at')
            ->first();
    }

    public function getTodaysAttendances(): Collection
    {
        return Attendance::whereDate('check_in_at', today())
            ->with('member.user')
            ->orderByDesc('check_in_at')
            ->get();
    }

    public function checkIn(Member $member): Attendance
    {
        $membership = $this->getMembershipForToday($member);

        if (! $membership) {
            throw ValidationException::withMessages([
                'member_id' => 'El socio no tiene una membresia vigente. Debe renovar en recepcion.',
            ]);
        }

        if ($membership->status !== MembershipStatus::Active) {
            throw ValidationException::withMessages([
                'member_id' => 'La membresia del socio esta '.mb_strtolower($membership->status->label()).'. Debe regularizarla en recepcion.',
            ]);
        }

        if ($this->getOpenAttendanceForMember($member)) {
            throw ValidationException::withMessages([
                'member_id' => 'El socio ya tiene un check-in abierto hoy.',
            ]);
        }

        return Attendance::create([
            'member_id' => $member->user_id,
            'check_in_at' => now(),
        ]);
    }

    public function checkOut(Attendance $attendance): Attendance
    {
        if ($attendance->check_out_at) {
            throw ValidationException::withMessages([
                'attendance' => 'Esta asistencia ya tiene un check-out registrado.',
            ]);
        }

        $attendance->update([
            'check_out_at' => now(),
        ]);

        return $attendance;
    }
}
