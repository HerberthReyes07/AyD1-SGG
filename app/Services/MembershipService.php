<?php

namespace App\Services;

use App\Repositories\MembershipRepository;
use App\Models\MemberMembership;
use App\Models\MembershipPlan;
use App\Models\MembershipStatusHistory;
use App\Enums\MembershipStatus;
use Illuminate\Support\Facades\DB;
use Exception;

class MembershipService
{
    protected MembershipRepository $membershipRepository;

    public function __construct(MembershipRepository $membershipRepository)
    {
        $this->membershipRepository = $membershipRepository;
    }

    /**
     * Create a new membership for a member.
     */
    public function createMembership(int|string $memberId, int|string $planId, int|string $registeredById): MemberMembership
    {
        // Reject if member already has an active membership
        $activeMembership = $this->membershipRepository->findActiveByMemberId($memberId);
        if ($activeMembership) {
            throw new Exception('El miembro ya tiene una membresía activa.');
        }

        $plan = MembershipPlan::findOrFail($planId);
        $startDate = now()->startOfDay();
        $endDate = $startDate->copy()->addMonths($plan->duration_months);

        return DB::transaction(function () use ($memberId, $planId, $startDate, $endDate, $registeredById) {
            // Create membership
            $membership = $this->membershipRepository->create([
                'member_id' => $memberId,
                'plan_id' => $planId,
                'status' => MembershipStatus::Active,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);

            // Create status history log
            MembershipStatusHistory::create([
                'previous_status' => null,
                'new_status' => MembershipStatus::Active,
                'change_date' => now(),
                'reason' => 'Alta de membresía',
                'changed_by' => $registeredById,
                'member_membership_id' => $membership->id,
            ]);

            return $membership;
        });
    }

    /**
     * Renew a membership for a member.
     */
    public function renewMembership(int|string $memberId, int|string $planId, int|string $registeredById): MemberMembership
    {
        $plan = MembershipPlan::findOrFail($planId);
        $activeMembership = $this->membershipRepository->findActiveByMemberId($memberId);

        return DB::transaction(function () use ($memberId, $planId, $registeredById, $plan, $activeMembership) {
            // If active membership exists, expire it first to keep history
            if ($activeMembership) {
                $this->membershipRepository->update($activeMembership->id, [
                    'status' => MembershipStatus::Expired,
                ]);

                // Create status history log for the expired one
                MembershipStatusHistory::create([
                    'previous_status' => MembershipStatus::Active,
                    'new_status' => MembershipStatus::Expired,
                    'change_date' => now(),
                    'reason' => 'Renovación de membresía',
                    'changed_by' => $registeredById,
                    'member_membership_id' => $activeMembership->id,
                ]);
            }

            $startDate = now()->startOfDay();
            $endDate = $startDate->copy()->addMonths($plan->duration_months);

            // Create new membership record
            $newMembership = $this->membershipRepository->create([
                'member_id' => $memberId,
                'plan_id' => $planId,
                'status' => MembershipStatus::Active,
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);

            // Create status history log for new membership
            MembershipStatusHistory::create([
                'previous_status' => null,
                'new_status' => MembershipStatus::Active,
                'change_date' => now(),
                'reason' => 'Renovación de membresía',
                'changed_by' => $registeredById,
                'member_membership_id' => $newMembership->id,
            ]);

            return $newMembership;
        });
    }

    /**
     * Cancel a membership.
     */
    public function cancelMembership(int|string $membershipId, string $reason, int|string $changedById): bool
    {
        $membership = $this->membershipRepository->findById($membershipId);
        if (!$membership) {
            throw new Exception('Membresía no encontrada.');
        }

        if ($membership->status === MembershipStatus::Cancelled) {
            throw new Exception('La membresía ya está cancelada.');
        }

        return DB::transaction(function () use ($membership, $reason, $changedById) {
            $previousStatus = $membership->status;

            // Update membership fields
            $updated = $this->membershipRepository->update($membership->id, [
                'status' => MembershipStatus::Cancelled,
                'cancellation_reason' => $reason,
                'cancellation_date' => now(),
            ]);

            if ($updated) {
                // Log status history
                MembershipStatusHistory::create([
                    'previous_status' => $previousStatus,
                    'new_status' => MembershipStatus::Cancelled,
                    'change_date' => now(),
                    'reason' => $reason,
                    'changed_by' => $changedById,
                    'member_membership_id' => $membership->id,
                ]);
            }

            return $updated;
        });
    }
}

