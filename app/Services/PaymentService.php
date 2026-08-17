<?php

namespace App\Services;

use App\Repositories\PaymentRepository;
use App\Repositories\MembershipRepository;
use App\Models\MembershipPayment;
use App\Models\MembershipPlan;
use Illuminate\Support\Facades\DB;
use Exception;

use App\Models\Promotion;
use App\Enums\PromotionType;

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

            // Calculate final amount considering promotion discount
            $finalAmount = (float) $plan->price;

            if ($promotionId) {
                $promotion = Promotion::find($promotionId);
                if ($promotion) {
                    $today = now()->startOfDay();
                    if (!$promotion->is_active || $promotion->start_date->gt($today) || $promotion->end_date->lt($today)) {
                        throw new Exception('La promoción seleccionada no está activa o no se encuentra en su período de vigencia.');
                    }

                    if ($promotion->type === PromotionType::Percentage) {
                        $discount = $finalAmount * ($promotion->value / 100);
                        $finalAmount = max(0, $finalAmount - $discount);
                    } elseif ($promotion->type === PromotionType::FixedAmount) {
                        $finalAmount = max(0, $finalAmount - $promotion->value);
                    }
                }
            }

            // Create payment
            return $this->paymentRepository->create([
                'amount' => round($finalAmount, 2),
                'payment_date' => now(),
                'member_membership_id' => $membership->id,
                'payment_method_id' => $paymentMethodId,
                'promotion_id' => $promotionId,
                'registered_by' => $registeredBy,
            ]);
        });
    }
}


