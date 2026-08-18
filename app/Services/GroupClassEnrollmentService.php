<?php

namespace App\Services;

use App\Enums\ClassEnrollmentStatus;
use App\Enums\ClassSessionStatus;
use App\Enums\MembershipStatus;
use App\Enums\WaitlistStatus;
use App\Models\ClassEnrollment;
use App\Models\ClassSession;
use App\Models\ClassWaitlist;
use App\Models\Member;
use App\Models\MemberMembership;
use App\Models\MembershipPlan;
use App\Notifications\ClassWaitlistNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GroupClassEnrollmentService
{
    public function enroll(Member $member, ClassSession $session): array
    {
        return DB::transaction(function () use ($member, $session) {

            $session = ClassSession::with('groupClass')
                ->lockForUpdate()
                ->findOrFail($session->id);

            $this->validateSessionForEnrollment($session);

            $membership = $this->getValidMembership(
                $member,
                $session
            );

            $plan = $membership->plan;

            if (! $plan->includes_group_classes) {
                throw ValidationException::withMessages([
                    'member_id' => 'El plan del socio no incluye clases grupales.',
                ]);
            }

            $this->validateWeeklyLimit(
                $member,
                $session,
                $plan
            );

            $existingEnrollment = ClassEnrollment::where(
                'member_id',
                $member->user_id
            )
                ->where(
                    'class_session_id',
                    $session->id
                )
                ->first();

            if (
                $existingEnrollment &&
                $existingEnrollment->status !== ClassEnrollmentStatus::Cancelled
            ) {
                throw ValidationException::withMessages([
                    'member_id' => 'El socio ya esta inscrito en esta sesion.',
                ]);
            }

            $existingWaitlist = ClassWaitlist::where(
                'member_id',
                $member->user_id
            )
                ->where(
                    'class_session_id',
                    $session->id
                )
                ->where(
                    'status',
                    WaitlistStatus::Waiting->value
                )
                ->exists();

            if ($existingWaitlist) {
                throw ValidationException::withMessages([
                    'member_id' => 'El socio ya se encuentra en la lista de espera.',
                ]);
            }

            $enrolledCount = $this->getEnrolledCount(
                $session
            );

            if (
                $enrolledCount <
                $session->groupClass->max_participants
            ) {
                if ($existingEnrollment) {
                    $existingEnrollment->update([
                        'status' => ClassEnrollmentStatus::Enrolled,
                        'enrollment_date' => today(),
                    ]);
                } else {
                    ClassEnrollment::create([
                        'member_id' => $member->user_id,
                        'class_session_id' => $session->id,
                        'enrollment_date' => today(),
                        'status' => ClassEnrollmentStatus::Enrolled,
                    ]);
                }

                return [
                    'type' => 'enrolled',
                    'message' => 'Socio inscrito correctamente.',
                ];
            }

            $waitlist = ClassWaitlist::where(
                'member_id',
                $member->user_id
            )
                ->where(
                    'class_session_id',
                    $session->id
                )
                ->first();

            if ($waitlist) {
                $waitlist->update([
                    'requested_date' => today(),
                    'status' => WaitlistStatus::Waiting,
                ]);
            } else {
                ClassWaitlist::create([
                    'member_id' => $member->user_id,
                    'class_session_id' => $session->id,
                    'requested_date' => today(),
                    'status' => WaitlistStatus::Waiting,
                ]);
            }

            return [
                'type' => 'waitlist',
                'message' => 'La clase esta llena. El socio fue agregado a la lista de espera.',
            ];
        });
    }

    public function cancel(
        Member $member,
        ClassSession $session
    ): array {
        return DB::transaction(function () use (
            $member,
            $session
        ) {

            $session = ClassSession::with('groupClass')
                ->lockForUpdate()
                ->findOrFail($session->id);

            $enrollment = ClassEnrollment::where(
                'member_id',
                $member->user_id
            )
                ->where(
                    'class_session_id',
                    $session->id
                )
                ->where(
                    'status',
                    ClassEnrollmentStatus::Enrolled->value
                )
                ->first();

            if (! $enrollment) {
                throw ValidationException::withMessages([
                    'member_id' => 'El socio no tiene una inscripcion activa en esta sesion.',
                ]);
            }

            if (now()->greaterThanOrEqualTo($session->starts_at)) {
                throw ValidationException::withMessages([
                    'member_id' => 'La sesion ya inicio y la inscripcion no puede cancelarse.',
                ]);
            }

            $canPromoteWaitlist = now()->lte(
                $session->starts_at
                    ->copy()
                    ->subHours(2)
            );

            $enrollment->update([
                'status' => ClassEnrollmentStatus::Cancelled,
            ]);

            $promoted = null;

            if ($canPromoteWaitlist) {
                $promoted = $this->promoteNextMember(
                    $session
                );
            }

            return [
                'type' => 'cancelled',
                'promoted' => $promoted,
                'message' => $promoted
                    ? 'Inscripcion cancelada y se promovio un socio de la lista de espera.'
                    : 'Inscripcion cancelada correctamente.',
            ];
        });
    }

    public function cancelWaitlist(
    Member $member,
    ClassSession $session
    ): array {
        return DB::transaction(function () use (
            $member,
            $session
        ) {
            $waitlist = ClassWaitlist::where(
                'member_id',
                $member->user_id
            )
                ->where(
                    'class_session_id',
                    $session->id
                )
                ->where(
                    'status',
                    WaitlistStatus::Waiting->value
                )
                ->first();

            if (! $waitlist) {
                throw ValidationException::withMessages([
                    'session' => 'El socio no se encuentra en la lista de espera.',
                ]);
            }

            $waitlist->update([
                'status' => WaitlistStatus::Expired,
            ]);

            return [
                'type' => 'waitlist_cancelled',
                'message' => 'El socio fue retirado de la lista de espera.',
            ];
        });
    }

    private function validateSessionForEnrollment(
        ClassSession $session
    ): void {
        if (! in_array($session->status, [
            ClassSessionStatus::Scheduled,
            ClassSessionStatus::Rescheduled,
        ], true)) {
            throw ValidationException::withMessages([
                'session' => 'Esta sesion no acepta nuevas inscripciones.',
            ]);
        }

        if ($session->starts_at->isPast()) {
            throw ValidationException::withMessages([
                'session' => 'No es posible inscribirse en una sesion que ya inicio.',
            ]);
        }

        if (! $session->groupClass->is_active) {
            throw ValidationException::withMessages([
                'session' => 'La clase grupal se encuentra inactiva.',
            ]);
        }
    }

    private function getValidMembership(
        Member $member,
        ClassSession $session
    ): MemberMembership {
        $membership = $this->findValidMembership(
            $member,
            $session
        );

        if (! $membership) {
            throw ValidationException::withMessages([
                'member_id' => 'El socio no posee una membresia activa y vigente para la fecha de la sesion.',
            ]);
        }

        return $membership;
    }

    private function findValidMembership(
        Member $member,
        ClassSession $session
    ): ?MemberMembership {
        $sessionDate = $session
            ->starts_at
            ->toDateString();

        return $member->memberships()
            ->with('plan')
            ->where(
                'status',
                MembershipStatus::Active->value
            )
            ->whereDate(
                'start_date',
                '<=',
                $sessionDate
            )
            ->whereDate(
                'end_date',
                '>=',
                $sessionDate
            )
            ->orderByDesc('start_date')
            ->first();
    }

    private function validateWeeklyLimit(
        Member $member,
        ClassSession $session,
        MembershipPlan $plan
    ): void {
        if ($plan->weekly_class_limit === null) {
            return;
        }

        $count = $this->getWeeklyEnrollmentCount(
            $member,
            $session
        );

        if ($count >= $plan->weekly_class_limit) {
            throw ValidationException::withMessages([
                'member_id' => 'El socio ya alcanzo el limite semanal de clases de su plan.',
            ]);
        }
    }

    private function getWeeklyEnrollmentCount(
        Member $member,
        ClassSession $session
    ): int {
        $weekStart = $session
            ->starts_at
            ->copy()
            ->startOfWeek(Carbon::MONDAY)
            ->startOfDay();

        $weekEnd = $session
            ->starts_at
            ->copy()
            ->endOfWeek(Carbon::SUNDAY)
            ->endOfDay();

        return ClassEnrollment::where(
            'member_id',
            $member->user_id
        )
            ->whereIn('status', [
                ClassEnrollmentStatus::Enrolled->value,
                ClassEnrollmentStatus::Attended->value,
                ClassEnrollmentStatus::NoShow->value,
            ])
            ->whereHas(
                'classSession',
                function ($query) use (
                    $weekStart,
                    $weekEnd
                ) {
                    $query
                        ->whereBetween(
                            'starts_at',
                            [$weekStart, $weekEnd]
                        )
                        ->whereIn('status', [
                            ClassSessionStatus::Scheduled->value,
                            ClassSessionStatus::Rescheduled->value,
                            ClassSessionStatus::InProgress->value,
                            ClassSessionStatus::Completed->value,
                        ]);
                }
            )
            ->count();
    }

    private function getEnrolledCount(
        ClassSession $session
    ): int {
        return ClassEnrollment::where(
            'class_session_id',
            $session->id
        )
            ->where(
                'status',
                ClassEnrollmentStatus::Enrolled->value
            )
            ->count();
    }

    private function promoteNextMember(
        ClassSession $session
    ): ?ClassWaitlist {
        if (
            $this->getEnrolledCount($session) >=
            $session->groupClass->max_participants
        ) {
            return null;
        }

        $waitlists = ClassWaitlist::with([
            'member.memberships.plan',
        ])
            ->where(
                'class_session_id',
                $session->id
            )
            ->where(
                'status',
                WaitlistStatus::Waiting->value
            )
            ->orderBy('created_at')
            ->get();

        $eligible = [];

        foreach ($waitlists as $waitlist) {
            $membership = $this->findValidMembership(
                $waitlist->member,
                $session
            );

            if (! $membership) {
                continue;
            }

            if (! $membership->plan->includes_group_classes) {
                continue;
            }

            if (
                $membership->plan->weekly_class_limit !== null &&
                $this->getWeeklyEnrollmentCount(
                    $waitlist->member,
                    $session
                ) >= $membership->plan->weekly_class_limit
            ) {
                continue;
            }

            $eligible[] = [
                'waitlist' => $waitlist,
                'priority' => $membership
                    ->plan
                    ->has_waitlist_priority
                    ? 1
                    : 0,
            ];
        }

        if (empty($eligible)) {
            return null;
        }

        usort(
            $eligible,
            function ($a, $b) {
                if ($a['priority'] !== $b['priority']) {
                    return $b['priority'] <=> $a['priority'];
                }

                return $a['waitlist']->created_at
                    <=> $b['waitlist']->created_at;
            }
        );

        $next = $eligible[0]['waitlist'];

        // Mark as notified and send email — do NOT auto-enroll.
        // The member must claim the spot through the normal enrollment flow.
        $next->update([
            'status' => WaitlistStatus::Notified,
        ]);

        $next->member->user->notify(
            new ClassWaitlistNotification($session)
        );

        return $next;
    }
}