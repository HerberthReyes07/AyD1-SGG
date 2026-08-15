<?php

namespace App\Repositories;

use App\Enums\MembershipStatus;
use App\Models\MemberMembership;
use App\Models\MembershipPayment;
use App\Models\MembershipPlan;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ReportRepository
{
    /**
     * Get payments with relations within date range.
     */
    public function getPaymentsForIncomeReport(?string $startDate = null, ?string $endDate = null): Collection
    {
        $query = MembershipPayment::with([
            'memberMembership.plan',
            'memberMembership.member.user',
            'paymentMethod',
        ]);

        if ($startDate) {
            $query->whereDate('payment_date', '>=', Carbon::parse($startDate));
        }

        if ($endDate) {
            $query->whereDate('payment_date', '<=', Carbon::parse($endDate));
        }

        return $query->orderBy('payment_date', 'asc')->get();
    }

    /**
     * Get all active and inactive plans for report headers and breakdown columns.
     */
    public function getAllMembershipPlans(): Collection
    {
        return MembershipPlan::orderBy('name')->get();
    }

    /**
     * Get active memberships expiring within the specified number of days (default 7).
     * Upcoming expirations MUST only include active memberships and exclude frozen or cancelled.
     */
    public function getExpiringActiveMemberships(int $days = 7): Collection
    {
        $today = Carbon::today();
        $limitDate = Carbon::today()->addDays($days);

        return MemberMembership::where('status', MembershipStatus::Active)
            ->whereDate('end_date', '>=', $today)
            ->whereDate('end_date', '<=', $limitDate)
            ->with(['member.user', 'plan'])
            ->orderBy('end_date', 'asc')
            ->get();
    }

    /**
     * Get expired memberships.
     */
    public function getExpiredMemberships(?string $startDate = null, ?string $endDate = null): Collection
    {
        $today = Carbon::today();

        $query = MemberMembership::where(function ($q) use ($today) {
            $q->where('status', MembershipStatus::Expired)
              ->orWhere(function ($q2) use ($today) {
                  $q2->where('status', MembershipStatus::Active)
                     ->whereDate('end_date', '<', $today);
              });
        })
        ->whereNotIn('status', [MembershipStatus::Cancelled, MembershipStatus::Frozen]);

        if ($startDate) {
            $query->whereDate('end_date', '>=', Carbon::parse($startDate));
        }

        if ($endDate) {
            $query->whereDate('end_date', '<=', Carbon::parse($endDate));
        }

        return $query->with(['member.user', 'plan'])
            ->orderBy('end_date', 'desc')
            ->get();
    }
}
