<?php

namespace App\Services;

use App\Enums\MembershipStatus;
use App\Models\Member;
use App\Models\Trainer;
use App\Models\TrainerAssignment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TrainerAssignmentService
{
    public function getActiveAssignments()
    {
        return TrainerAssignment::with(['member.user', 'trainer.user', 'assignedBy'])
            ->whereNull('end_date')
            ->orderByDesc('created_at')
            ->get();
    }

    public function getCreateData(): array
    {
        return [
            'eligibleMembers' => $this->getEligibleMembers(),
            'trainers' => $this->getTrainers(),
        ];
    }

    public function getEligibleMembers()
    {
        return Member::whereDoesntHave('trainerAssignments', function ($query) {
            $query->whereNull('end_date');
        })
            ->whereHas('memberships', function ($query) {
                $query->where('status', MembershipStatus::Active)
                    ->whereHas('plan', fn($planQuery) => $planQuery->where('includes_trainer', true));
            })
            ->with('user')
            ->get();
    }

    public function getTrainers()
    {
        return Trainer::with(['user', 'specialty'])
            ->withCount(['trainerAssignments as active_members_count' => function ($query) {
                $query->whereNull('end_date');
            }])
            ->get();
    }

    public function createAssignment(array $validated): bool
    {
        $alreadyAssigned = TrainerAssignment::where('member_id', $validated['member_id'])
            ->whereNull('end_date')
            ->exists();

        if ($alreadyAssigned) {
            return false;
        }

        TrainerAssignment::create([
            'member_id' => $validated['member_id'],
            'trainer_id' => $validated['trainer_id'],
            'goal' => $validated['goal'] ?? null,
            'assignment_date' => now(),
            'assigned_by' => Auth::id(),
        ]);

        return true;
    }

    public function isActive(TrainerAssignment $trainerAssignment): bool
    {
        return $trainerAssignment->end_date === null;
    }

    public function getReassignData(TrainerAssignment $trainerAssignment): array
    {
        $trainerAssignment->load(['member.user', 'trainer.user', 'trainer.specialty']);

        $availableTrainers = Trainer::with(['user', 'specialty'])
            ->where('user_id', '!=', $trainerAssignment->trainer_id)
            ->withCount(['trainerAssignments as active_members_count' => function ($query) {
                $query->whereNull('end_date');
            }])
            ->get();

        return [
            'trainerAssignment' => $trainerAssignment,
            'availableTrainers' => $availableTrainers,
        ];
    }

    public function reassign(TrainerAssignment $trainerAssignment, array $validated): void
    {
        DB::transaction(function () use ($trainerAssignment, $validated) {
            $trainerAssignment->update([
                'end_date' => now(),
                'reassignment_reason' => $validated['reassignment_reason'],
            ]);

            TrainerAssignment::create([
                'member_id' => $trainerAssignment->member_id,
                'trainer_id' => $validated['new_trainer_id'],
                'assignment_date' => now(),
                'goal' => $validated['goal'] ?? null,
                'assigned_by' => Auth::id(),
            ]);
        });
    }

    public function getHistoryAssignments()
    {
        return TrainerAssignment::with(['member.user', 'trainer.user', 'assignedBy'])
            ->orderByDesc('created_at')
            ->get();
    }

    public function getActiveForTrainer(int $trainerId)
    {
        return TrainerAssignment::where('trainer_id', $trainerId)
            ->whereNull('end_date')
            ->with('member.user')
            ->get();
    }

    public function getHistoryForTrainer(int $trainerId)
    {
        return TrainerAssignment::where('trainer_id', $trainerId)
            ->with(['member.user', 'assignedBy'])
            ->orderByDesc('created_at')
            ->get();
    }

}
