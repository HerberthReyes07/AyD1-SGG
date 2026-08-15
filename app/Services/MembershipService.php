<?php

namespace App\Services;

use App\Repositories\MembershipRepository;
use App\Models\MemberMembership;
use App\Models\MembershipFreeze;
use App\Models\MembershipPlan;
use App\Models\MembershipStatusHistory;
use App\Enums\MembershipStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
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

            // If the membership is currently Frozen, we must close its active freeze record first.
            if ($previousStatus === MembershipStatus::Frozen) {
                $activeFreeze = $membership->freezes()->whereNull('reactivation_date')->first();
                if ($activeFreeze) {
                    $reactivationDate = now()->startOfDay();
                    $frozenDays = (int) $activeFreeze->start_date->diffInDays($reactivationDate);

                    $activeFreeze->update([
                        'reactivation_date' => $reactivationDate,
                        'frozen_days'       => $frozenDays,
                    ]);
                }
            }

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

    /**
     * Freeze an active membership upon member request.
     *
     * Business rules enforced:
     *  - Membership must belong to the requesting member.
     *  - Membership must be Active (not already Frozen, Cancelled, or Expired).
     *  - No open freeze may already exist for this membership.
     *  - Accumulated frozen days in the rolling 90-day window must not exceed 15.
     *  - estimated_reactivation_date defaults to start_date + 15 days when not provided.
     *  - frozen_days is intentionally left null; it will be set when the freeze closes.
     */
    public function freezeMembership(
        int|string $membershipId,
        int|string $requestingMemberId,
        string     $reason,
        ?string    $estimatedReactivationDate = null
    ): MembershipFreeze {
        $membership = $this->membershipRepository->findById($membershipId);

        if (!$membership) {
            throw new Exception('Membresía no encontrada.');
        }

        // The requesting member must own this membership.
        if ((string) $membership->member_id !== (string) $requestingMemberId) {
            throw new Exception('No tienes permiso para congelar esta membresía.');
        }

        if ($membership->status !== MembershipStatus::Active) {
            throw new Exception('Solo se puede congelar una membresía activa.');
        }

        // Guard: no open freeze already exists.
        $openFreeze = $this->membershipRepository->findOpenFreezeByMembershipId($membershipId);
        if ($openFreeze) {
            throw new Exception('La membresía ya tiene un congelamiento activo.');
        }

        // Quota check: maximum 15 accumulated frozen days in the rolling 90-day window.
        $accumulatedDays = $this->membershipRepository->getAccumulatedFrozenDaysInQuarter($membershipId);
        $remainingQuota = 15 - $accumulatedDays;

        if ($remainingQuota <= 0) {
            throw new Exception(
                'Has alcanzado el máximo de 15 días de congelamiento acumulados para este trimestre.'
            );
        }

        $startDate = now()->startOfDay();

        // Calculate requested duration
        if ($estimatedReactivationDate) {
            $estimatedDate = Carbon::parse($estimatedReactivationDate)->startOfDay();
            if ($estimatedDate->lessThanOrEqualTo($startDate)) {
                throw new Exception('La fecha estimada de reactivación debe ser posterior a la fecha de inicio.');
            }
            $requestedDuration = (int) $startDate->diffInDays($estimatedDate);
        } else {
            $requestedDuration = 15;
            $estimatedDate = $startDate->copy()->addDays(15)->startOfDay();
        }

        // Reject if requested duration exceeds remaining quota
        if ($requestedDuration > $remainingQuota) {
            throw new Exception(
                "El período de congelamiento solicitado ({$requestedDuration} días) supera tu cupo disponible de congelamiento restante ({$remainingQuota} días) para este trimestre."
            );
        }

        return DB::transaction(function () use ($membership, $requestingMemberId, $reason, $estimatedDate) {
            $startDate = now()->startOfDay();

            // Create the freeze record. frozen_days is left null intentionally.
            $freeze = MembershipFreeze::create([
                'member_membership_id'        => $membership->id,
                'start_date'                  => $startDate,
                'estimated_reactivation_date' => $estimatedDate->toDateString(),
                'reactivation_date'           => null,
                'reason'                      => $reason,
                'frozen_days'                 => null,
                'registered_by'               => $requestingMemberId,
            ]);

            // Flip membership status to Frozen.
            $this->membershipRepository->update($membership->id, [
                'status' => MembershipStatus::Frozen,
            ]);

            // Record the status change using the existing history mechanism.
            MembershipStatusHistory::create([
                'previous_status'      => MembershipStatus::Active,
                'new_status'           => MembershipStatus::Frozen,
                'change_date'          => now(),
                'reason'               => $reason,
                'changed_by'           => $requestingMemberId,
                'member_membership_id' => $membership->id,
            ]);

            return $freeze;
        });
    }

    /**
     * Reactivate a frozen membership by closing its open freeze record.
     *
     * This is the single shared reactivation method used by:
     *  - The daily scheduler (automatic reactivation when estimated_reactivation_date is reached).
     *  - Future admin/receptionist manual reactivation (pass a descriptive $reason).
     *
     * Steps performed inside a transaction:
     *  1. Locate and close the open freeze: set reactivation_date and calculate frozen_days.
     *  2. Extend the membership end_date by the actual number of frozen days.
     *  3. Flip status back to Active.
     *  4. Record the transition in the existing status-history mechanism.
     *
     * @param  int|string  $membershipId      The membership to reactivate.
     * @param  int|string  $changedById       The user ID responsible (system user or admin).
     * @param  string      $reason            Human-readable reason recorded in status history.
     * @throws Exception   When the membership is not frozen or has no open freeze.
     */
    public function reactivateFrozenMembership(
        int|string $membershipId,
        int|string $changedById,
        string     $reason
    ): MemberMembership {
        $membership = $this->membershipRepository->findById($membershipId);

        if (!$membership) {
            throw new Exception('Membresía no encontrada.');
        }

        if ($membership->status !== MembershipStatus::Frozen) {
            throw new Exception('La membresía no está congelada.');
        }

        $openFreeze = $this->membershipRepository->findOpenFreezeByMembershipId($membershipId);
        if (!$openFreeze) {
            throw new Exception('No se encontró un congelamiento activo para esta membresía.');
        }

        return DB::transaction(function () use ($membership, $openFreeze, $changedById, $reason) {
            $reactivationDate = now()->startOfDay();
            $frozenDays       = (int) $openFreeze->start_date->diffInDays($reactivationDate);

            // Close the freeze record with the actual reactivation date and frozen_days.
            $openFreeze->update([
                'reactivation_date' => $reactivationDate,
                'frozen_days'       => $frozenDays,
            ]);

            // Extend end_date by the actual frozen days so no membership time is consumed.
            $newEndDate = $membership->end_date->copy()->addDays($frozenDays);

            $this->membershipRepository->update($membership->id, [
                'status'   => MembershipStatus::Active,
                'end_date' => $newEndDate,
            ]);

            // Record the status transition using the existing history mechanism.
            MembershipStatusHistory::create([
                'previous_status'      => MembershipStatus::Frozen,
                'new_status'           => MembershipStatus::Active,
                'change_date'          => now(),
                'reason'               => $reason,
                'changed_by'           => $changedById,
                'member_membership_id' => $membership->id,
            ]);

            return $membership->fresh();
        });
    }

    // -------------------------------------------------------------------------
    // Automatic status transitions
    // -------------------------------------------------------------------------

    /**
     * Transition an active membership to Expired when its end_date has been reached.
     *
     * Handles source state:
     *  - Active → Expired  (end_date reached while membership was active)
     *
     * Frozen memberships must not expire while frozen; they reactivate first
     * (extending end_date) and can only expire afterwards if the extended end_date is reached.
     *
     * Already-expired and cancelled memberships are rejected to prevent duplicate
     * status-history records.
     *
     * @param  MemberMembership  $membership   Eager-loaded membership model.
     * @param  int|string        $changedById  User ID attributed to the transition.
     * @throws Exception  When the membership is not in an Active state.
     */
    public function expireMembership(MemberMembership $membership, int|string $changedById): void
    {
        if ($membership->status !== MembershipStatus::Active) {
            throw new Exception(
                "La membresía #{$membership->id} no puede ser vencida desde el estado '{$membership->status->value}'."
            );
        }

        DB::transaction(function () use ($membership, $changedById) {
            $previousStatus = $membership->status;

            // Flip status to Expired.
            $this->membershipRepository->update($membership->id, [
                'status' => MembershipStatus::Expired,
            ]);

            // Record the transition using the existing status-history mechanism.
            MembershipStatusHistory::create([
                'previous_status'      => $previousStatus,
                'new_status'           => MembershipStatus::Expired,
                'change_date'          => now(),
                'reason'               => 'Vencimiento automático de membresía',
                'changed_by'           => $changedById,
                'member_membership_id' => $membership->id,
            ]);
        });
    }

    // -------------------------------------------------------------------------
    // Expiration notifications
    // -------------------------------------------------------------------------

    /**
     * Send the 5-day expiration warning email to the member who owns the
     * given membership and mark the flag so it is never sent again.
     *
     * @throws Exception If the membership is not in a state that allows a warning.
     */
    public function sendExpirationWarning(MemberMembership $membership): void
    {
        if ($membership->expiration_warning_sent) {
            throw new Exception('La advertencia de vencimiento ya fue enviada para esta membresía.');
        }

        $user = $membership->member?->user;
        if (!$user) {
            throw new Exception("No se encontró el usuario para la membresía #{$membership->id}.");
        }

        $user->notify(new \App\Notifications\MembershipExpiringNotification($membership));

        $this->membershipRepository->update($membership->id, [
            'expiration_warning_sent' => true,
        ]);
    }

    /**
     * Send the expiration-day notification email to the member who owns the
     * given membership and mark the flag so it is never sent again.
     *
     * @throws Exception If the membership is not in a state that allows the notification.
     */
    public function sendExpirationNotification(MemberMembership $membership): void
    {
        if ($membership->expiration_notified) {
            throw new Exception('La notificación de vencimiento ya fue enviada para esta membresía.');
        }

        $user = $membership->member?->user;
        if (!$user) {
            throw new Exception("No se encontró el usuario para la membresía #{$membership->id}.");
        }

        $user->notify(new \App\Notifications\MembershipExpiredNotification($membership));

        $this->membershipRepository->update($membership->id, [
            'expiration_notified' => true,
        ]);
    }
}
