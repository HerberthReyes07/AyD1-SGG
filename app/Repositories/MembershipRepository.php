<?php

namespace App\Repositories;

use App\Models\MemberMembership;
use App\Models\MembershipFreeze;
use App\Enums\MembershipStatus;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class MembershipRepository
{
    /**
     * Get all memberships for a specific member.
     */
    public function findByMemberId(int|string $memberId): Collection
    {
        return MemberMembership::where('member_id', $memberId)
            ->with(['plan', 'payments'])
            ->get();
    }

    /**
     * Find the active membership for a member, if any exists.
     */
    public function findActiveByMemberId(int|string $memberId): ?MemberMembership
    {
        return MemberMembership::where('member_id', $memberId)
            ->where('status', MembershipStatus::Active)
            ->first();
    }

    /**
     * Find a membership by ID.
     */
    public function findById(int|string $id): ?MemberMembership
    {
        return MemberMembership::with(['plan', 'member.user', 'payments', 'statusHistories'])->find($id);
    }

    /**
     * Create a new membership.
     */
    public function create(array $data): MemberMembership
    {
        return MemberMembership::create($data);
    }

    /**
     * Update an existing membership's attributes (e.g., status, cancellation info).
     */
    public function update(int|string $id, array $data): bool
    {
        $membership = MemberMembership::findOrFail($id);
        return $membership->update($data);
    }

    public function findCurrentByMemberId(int|string $memberId): ?MemberMembership
    {
        return MemberMembership::where('member_id', $memberId)
            ->whereIn('status', [
                MembershipStatus::Active,
                MembershipStatus::Frozen,
            ])
            ->with('plan')
            ->first();
    }

    /**
     * Return the open freeze for a membership, if any.
     */
    public function findOpenFreezeByMembershipId(int|string $membershipId): ?MembershipFreeze
    {
        return MembershipFreeze::where('member_membership_id', $membershipId)
            ->whereNull('reactivation_date')
            ->first();
    }

    /**
     * Return total frozen days accumulated for a membership in the last 90 rolling days.
     *
     * Using a rolling 90-day window rather than a fixed calendar quarter avoids
     * arbitrary boundary effects and matches the "per quarter" intent without
     * assuming any specific calendar quarter definition.
     *
     * Closed freezes contribute their stored frozen_days value.
     * An open freeze (reactivation_date IS NULL) contributes the elapsed days since start_date.
     */
    public function getAccumulatedFrozenDaysInQuarter(int|string $membershipId): int
    {
        $windowStart = Carbon::now()->subDays(90)->startOfDay();
        $now = Carbon::now()->startOfDay();

        $freezes = MembershipFreeze::where('member_membership_id', $membershipId)
            ->where(function ($q) use ($windowStart) {
                // Include freezes that started or ended within the window, or are still open.
                $q->where('start_date', '>=', $windowStart)
                  ->orWhere('reactivation_date', '>=', $windowStart)
                  ->orWhereNull('reactivation_date');
            })
            ->get();

        $total = 0;
        foreach ($freezes as $freeze) {
            $freezeStart = Carbon::parse($freeze->start_date)->startOfDay();
            $freezeEnd = $freeze->reactivation_date
                ? Carbon::parse($freeze->reactivation_date)->startOfDay()
                : $now;

            $overlapStart = $freezeStart->greaterThan($windowStart) ? $freezeStart : $windowStart;
            $overlapEnd = $freezeEnd->lessThan($now) ? $freezeEnd : $now;

            if ($overlapStart->lessThanOrEqualTo($overlapEnd)) {
                $total += (int) $overlapStart->diffInDays($overlapEnd);
            }
        }

        return $total;
    }

    /**
     * Return all frozen memberships whose estimated_reactivation_date is today or in the past
     * and that still have an open freeze record (reactivation_date IS NULL).
     *
     * Used by the membership scheduler to trigger automatic reactivation.
     */
    public function findFrozenMembershipsDueForReactivation(): Collection
    {
        return MemberMembership::where('status', MembershipStatus::Frozen)
            ->whereHas('freezes', function ($q) {
                $q->whereNull('reactivation_date')
                  ->whereNotNull('estimated_reactivation_date')
                  ->whereDate('estimated_reactivation_date', '<=', Carbon::today());
            })
            ->with([
                'freezes' => function ($q) {
                    $q->whereNull('reactivation_date');
                },
            ])
            ->get();
    }
}
