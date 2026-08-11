<?php

namespace App\Repositories;

use App\Models\MemberMembership;
use App\Enums\MembershipStatus;
use Illuminate\Database\Eloquent\Collection;

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
}

