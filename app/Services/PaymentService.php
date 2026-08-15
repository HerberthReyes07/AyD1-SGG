<?php

namespace App\Services;

use App\Repositories\PaymentRepository;
use App\Repositories\MembershipRepository;
use App\Models\MembershipPayment;
use App\Models\MembershipPlan;
use Illuminate\Support\Facades\DB;
use Exception;

class PaymentService
{
    protected PaymentRepository $paymentRepository;
    protected MembershipService $membershipService;
    protected MembershipRepository $membershipRepository;

    public function __construct(
        PaymentRepository $paymentRepository,
        MembershipService $membershipService,
        MembershipRepository $membershipRepository
    ) {
        $this->paymentRepository = $paymentRepository;
        $this->membershipService = $membershipService;
        $this->membershipRepository = $membershipRepository;
    }

    /**
     * Register a membership payment (creates/extends/reactivates membership and registers payment).
     */
    public function registerPayment(array $data): MembershipPayment
    {
        $memberId = $data['member_id'];
        $planId = $data['plan_id'];
        $paymentMethodId = $data['payment_method_id'];
        $promotionId = $data['promotion_id'] ?? null;
        $registeredBy = $data['registered_by'];

        $plan = MembershipPlan::findOrFail($planId);

        return DB::transaction(function () use ($memberId, $planId, $paymentMethodId, $promotionId, $registeredBy, $plan) {
            // Process membership state change or extension based on payment
            $membership = $this->membershipService->processMembershipPayment($memberId, $planId, $registeredBy);

            // Create payment (use plan price directly, no discount calculations)
            return $this->paymentRepository->create([
                'amount' => $plan->price,
                'payment_date' => now(),
                'member_membership_id' => $membership->id,
                'payment_method_id' => $paymentMethodId,
                'promotion_id' => $promotionId,
                'registered_by' => $registeredBy,
            ]);
        });
    }
}


