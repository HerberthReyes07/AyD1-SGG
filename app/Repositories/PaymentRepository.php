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
        return MembershipPayment::with(['memberMembership.member.user', 'paymentMethod', 'registeredBy'])
            ->get();
    }

    /**
     * Find a payment by ID.
     */
    public function findById(int|string $id): ?MembershipPayment
    {
        return MembershipPayment::with(['memberMembership.member.user', 'paymentMethod', 'registeredBy', 'promotion'])->find($id);
    }

    /**
     * Create a new payment.
     */
    public function create(array $data): MembershipPayment
    {
        return MembershipPayment::create($data);
    }
}

