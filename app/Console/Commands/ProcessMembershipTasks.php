<?php

namespace App\Console\Commands;

use App\Repositories\MembershipRepository;
use App\Services\MembershipService;
use Illuminate\Console\Command;
use Throwable;

/**
 * ProcessMembershipTasks
 *
 * Central daily command for all time-dependent membership operations.
 * Runs once per day via the Laravel Scheduler (see routes/console.php).
 *
 * Currently handles:
 *  - Automatic reactivation of frozen memberships whose estimated_reactivation_date
 *    has been reached.
 *
 * Designed to be extended with future tasks such as:
 *  - Marking memberships as expired when end_date has passed.
 *  - Detecting memberships approaching expiration.
 *  - Sending membership expiration notifications.
 *
 * Business logic stays inside MembershipService. This command only orchestrates.
 */
class ProcessMembershipTasks extends Command
{
    protected $signature   = 'memberships:process-tasks';
    protected $description = 'Run daily time-dependent membership operations (reactivation, expiration, etc.)';

    public function __construct(
        private readonly MembershipRepository $membershipRepository,
        private readonly MembershipService    $membershipService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->reactivateDueFreezes();
        $this->notifyExpiringMemberships();
        $this->notifyExpiredMemberships();  // must run before status flip so membership is still active
        $this->markExpiredMemberships();

        return Command::SUCCESS;
    }

    /**
     * Automatically reactivate frozen memberships whose estimated_reactivation_date
     * is today or in the past and that still have an open freeze record.
     */
    private function reactivateDueFreezes(): void
    {
        $memberships = $this->membershipRepository->findFrozenMembershipsDueForReactivation();

        if ($memberships->isEmpty()) {
            $this->info('No frozen memberships due for reactivation today.');
            return;
        }

        $this->info("Found {$memberships->count()} membership(s) to reactivate.");

        foreach ($memberships as $membership) {
            try {
                $this->membershipService->reactivateFrozenMembership(
                    membershipId: $membership->id,
                    changedById: $membership->member_id,  // attribute change logged against the member's own user ID
                    reason: 'Reactivación automática por vencimiento del período de congelamiento',
                );

                $this->info("  ✓ Membership #{$membership->id} reactivated.");
            } catch (Throwable $e) {
                // Log and continue so one failure does not block other memberships.
                $this->error("  ✗ Membership #{$membership->id} failed: {$e->getMessage()}");
                report($e);
            }
        }
    }

    /**
     * Send a 5-day expiration warning email to every active member whose
     * membership expires exactly 5 days from today and has not yet received
     * the warning.
     */
    private function notifyExpiringMemberships(): void
    {
        $memberships = $this->membershipRepository->findActiveMembershipsDueForExpirationWarning();

        if ($memberships->isEmpty()) {
            $this->info('No memberships require a 5-day expiration warning today.');
            return;
        }

        $this->info("Found {$memberships->count()} membership(s) to warn about upcoming expiration.");

        foreach ($memberships as $membership) {
            try {
                $this->membershipService->sendExpirationWarning($membership);
                $this->info("  ✓ Expiration warning sent for membership #{$membership->id}.");
            } catch (Throwable $e) {
                $this->error("  ✗ Membership #{$membership->id} warning failed: {$e->getMessage()}");
                report($e);
            }
        }
    }

    /**
     * Send an expiration notification email to every active member whose
     * membership end_date is today and who has not yet received this notification.
     *
     * The notification is sent while the membership is still Active so it fires
     * on the expiration day itself, not the day after.
     */
    private function notifyExpiredMemberships(): void
    {
        $memberships = $this->membershipRepository->findActiveMembershipsExpiredToday();

        if ($memberships->isEmpty()) {
            $this->info('No memberships require an expiration notification today.');
            return;
        }

        $this->info("Found {$memberships->count()} membership(s) to notify of expiration.");

        foreach ($memberships as $membership) {
            try {
                $this->membershipService->sendExpirationNotification($membership);
                $this->info("  ✓ Expiration notification sent for membership #{$membership->id}.");
            } catch (Throwable $e) {
                $this->error("  ✗ Membership #{$membership->id} notification failed: {$e->getMessage()}");
                report($e);
            }
        }
    }
    /**
     * Transition all memberships that have reached or passed their end_date to Expired.
     *
     * Processes two groups in sequence:
     *  1. Active memberships with end_date <= today  → Expired
     *
     * This method runs AFTER notifyExpiredMemberships() so the expiration-day email
     * is sent while the membership is still Active, as required.
     *
     * Cancelled and already-expired memberships are excluded by the repository queries.
     * MembershipService::expireMembership() additionally guards against invalid states
     * to prevent duplicate status-history records.
     */
    private function markExpiredMemberships(): void
    {
        $memberships = $this->membershipRepository->findActiveMembershipsToExpire();

        if ($memberships->isEmpty()) {
            $this->info('No active memberships to expire today.');
            return;
        }

        $this->info("Found {$memberships->count()} active membership(s) to expire.");

        foreach ($memberships as $membership) {
            try {
                $this->membershipService->expireMembership(
                    membership: $membership,
                    changedById: $membership->member_id,
                );

                $this->info("  ✓ Membership #{$membership->id} expired.");
            } catch (Throwable $e) {
                $this->error(
                    "  ✗ Membership #{$membership->id} expiration failed: {$e->getMessage()}"
                );
                report($e);
            }
        }
    }
}
