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

        // Future tasks can be added here as private methods, e.g.:
        // $this->markExpiredMemberships();
        // $this->notifyExpiringMemberships();

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
                    changedById:  $membership->member_id,  // attribute change logged against the member's own user ID
                    reason:       'Reactivación automática por vencimiento del período de congelamiento',
                );

                $this->info("  ✓ Membership #{$membership->id} reactivated.");
            } catch (Throwable $e) {
                // Log and continue so one failure does not block other memberships.
                $this->error("  ✗ Membership #{$membership->id} failed: {$e->getMessage()}");
                report($e);
            }
        }
    }
}
