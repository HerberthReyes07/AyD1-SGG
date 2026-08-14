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
     * Process membership state change or extension based on payment.
     */
    public function processMembershipPayment(int|string $memberId, int|string $planId, int|string $registeredById): MemberMembership
    {
        $plan = MembershipPlan::findOrFail($planId);
        $currentMembership = $this->membershipRepository->findCurrentByMemberId($memberId);

        return DB::transaction(function () use ($memberId, $planId, $registeredById, $plan, $currentMembership) {
            // No previous active/frozen membership
            if (!$currentMembership) {
                return $this->createNewMembership($memberId, $plan, $registeredById);
            }

            // Active + Same Plan
            if ($currentMembership->status === MembershipStatus::Active && $currentMembership->plan_id == $planId) {
                $oldEndDate = $currentMembership->end_date;
                $newEndDate = $oldEndDate->copy()->addMonths($plan->duration_months);

                $this->membershipRepository->update($currentMembership->id, [
                    'end_date' => $newEndDate,
                ]);

                // Log status history (status remains Active, but we record the extension)
                MembershipStatusHistory::create([
                    'previous_status' => MembershipStatus::Active,
                    'new_status' => MembershipStatus::Active,
                    'change_date' => now(),
                    'reason' => 'Extensión de membresía por pago (mismo plan)',
                    'changed_by' => $registeredById,
                    'member_membership_id' => $currentMembership->id,
                ]);

                return $currentMembership->fresh();
            }

            // Active + Different Plan
            if ($currentMembership->status === MembershipStatus::Active && $currentMembership->plan_id != $planId) {
                // Cancel current membership
                $this->membershipRepository->update($currentMembership->id, [
                    'status' => MembershipStatus::Cancelled,
                    'cancellation_reason' => 'Cancelada automáticamente por cambio de plan',
                    'cancellation_date' => now(),
                ]);

                MembershipStatusHistory::create([
                    'previous_status' => MembershipStatus::Active,
                    'new_status' => MembershipStatus::Cancelled,
                    'change_date' => now(),
                    'reason' => 'Cancelación automática por cambio de plan',
                    'changed_by' => $registeredById,
                    'member_membership_id' => $currentMembership->id,
                ]);

                // Create new membership starting today
                return $this->createNewMembership($memberId, $plan, $registeredById);
            }

            // Frozen + Same Plan
            if ($currentMembership->status === MembershipStatus::Frozen && $currentMembership->plan_id == $planId) {
                // Calculate frozen days
                $activeFreeze = $currentMembership->freezes()->whereNull('reactivation_date')->first();
                $frozenDays = 0;
                if ($activeFreeze) {
                    $reactivationDate = now()->startOfDay();
                    $frozenDays = $activeFreeze->start_date->diffInDays($reactivationDate);

                    $activeFreeze->update([
                        'reactivation_date' => $reactivationDate,
                        'frozen_days' => $frozenDays,
                    ]);
                }

                // Recalculate end_date: original end_date + frozen days + plan duration
                $newEndDate = $currentMembership->end_date->copy()->addDays($frozenDays)->addMonths($plan->duration_months);

                $this->membershipRepository->update($currentMembership->id, [
                    'status' => MembershipStatus::Active,
                    'end_date' => $newEndDate,
                ]);

                // Log status history for reactivation
                MembershipStatusHistory::create([
                    'previous_status' => MembershipStatus::Frozen,
                    'new_status' => MembershipStatus::Active,
                    'change_date' => now(),
                    'reason' => 'Reactivación de membresía y pago (mismo plan)',
                    'changed_by' => $registeredById,
                    'member_membership_id' => $currentMembership->id,
                ]);

                return $currentMembership->fresh();
            }

            // Frozen + Different Plan
            if ($currentMembership->status === MembershipStatus::Frozen && $currentMembership->plan_id != $planId) {
                // Close any active freeze record if exists
                $activeFreeze = $currentMembership->freezes()->whereNull('reactivation_date')->first();
                if ($activeFreeze) {
                    $reactivationDate = now()->startOfDay();
                    $frozenDays = $activeFreeze->start_date->diffInDays($reactivationDate);
                    $activeFreeze->update([
                        'reactivation_date' => $reactivationDate,
                        'frozen_days' => $frozenDays,
                    ]);
                }

                // Cancel current membership
                $this->membershipRepository->update($currentMembership->id, [
                    'status' => MembershipStatus::Cancelled,
                    'cancellation_reason' => 'Cancelada automáticamente por cambio de plan desde estado congelado',
                    'cancellation_date' => now(),
                ]);

                MembershipStatusHistory::create([
                    'previous_status' => MembershipStatus::Frozen,
                    'new_status' => MembershipStatus::Cancelled,
                    'change_date' => now(),
                    'reason' => 'Cancelación automática por cambio de plan (desde congelado)',
                    'changed_by' => $registeredById,
                    'member_membership_id' => $currentMembership->id,
                ]);

                // Create new membership starting today
                return $this->createNewMembership($memberId, $plan, $registeredById);
            }

            throw new Exception('Estado de membresía no compatible.');
        });
    }

    /**
     * Helper to create a new membership starting today.
     */
    protected function createNewMembership(int|string $memberId, MembershipPlan $plan, int|string $registeredById): MemberMembership
    {
        $startDate = now()->startOfDay();
        $endDate = $startDate->copy()->addMonths($plan->duration_months);

        $membership = $this->membershipRepository->create([
            'member_id' => $memberId,
            'plan_id' => $plan->id,
            'status' => MembershipStatus::Active,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        MembershipStatusHistory::create([
            'previous_status' => null,
            'new_status' => MembershipStatus::Active,
            'change_date' => now(),
            'reason' => 'Compra de membresía',
            'changed_by' => $registeredById,
            'member_membership_id' => $membership->id,
        ]);

        return $membership;
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

    /**
     * Get all memberships for a specific member.
     */
    public function getMemberMemberships(int|string $memberId)
    {
        return $this->membershipRepository->findByMemberId($memberId);
    }
}
