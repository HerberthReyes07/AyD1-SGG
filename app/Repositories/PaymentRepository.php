<?php

namespace App\Repositories;

use App\Models\MembershipPayment;
use Illuminate\Database\Eloquent\Collection;

class PaymentRepository
{
    /**
     * Get all payments.
     */
    public function getAll(): Collection
    {
        return MembershipPayment::with(['memberMembership.member.user', 'memberMembership.plan', 'paymentMethod', 'registeredBy', 'promotion'])
            ->orderByDesc('id')
            ->get();
    }

    /**
     * Find a payment by ID.
     */
    public function findById(int|string $id): ?MembershipPayment
    {
        return MembershipPayment::with(['memberMembership.member.user', 'memberMembership.plan', 'paymentMethod', 'registeredBy', 'promotion'])->find($id);
    }

    /**
     * Get all payments for a specific member.
     */
    public function getByMemberId(int|string $memberId): Collection
    {
        return MembershipPayment::whereHas('memberMembership', function ($q) use ($memberId) {
            $q->where('member_id', $memberId);
        })
            ->with(['memberMembership.member.user', 'memberMembership.plan', 'paymentMethod', 'registeredBy', 'promotion'])
            ->orderByDesc('id')
            ->get();
    }

    /**
     * Create a new payment.
     */
    public function create(array $data): MembershipPayment
    {
        return MembershipPayment::create($data);
    }
}

