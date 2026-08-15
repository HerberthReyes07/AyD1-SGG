<?php

namespace App\Services;

use App\Repositories\ReportRepository;
use Carbon\Carbon;

class ReportService
{
    public function __construct(
        protected ReportRepository $reportRepository
    ) {}

    /**
     * Build Income Report structured by Week or Month.
     */
    public function getIncomeReport(string $groupBy = 'month', ?string $startDate = null, ?string $endDate = null): array
    {
        $payments = $this->reportRepository->getPaymentsForIncomeReport($startDate, $endDate);
        $plans = $this->reportRepository->getAllMembershipPlans();

        $totalIncome = (float) $payments->sum('amount');

        // Income breakdown by plan overall
        $incomeByPlan = [];
        foreach ($plans as $plan) {
            $incomeByPlan[$plan->id] = [
                'plan_name' => $plan->name,
                'total' => 0.0,
                'count' => 0,
            ];
        }

        $incomeByPlan['other'] = [
            'plan_name' => 'Sin Plan / Otro',
            'total' => 0.0,
            'count' => 0,
        ];

        // Group payments by period (week or month)
        $groupedPeriods = [];

        foreach ($payments as $payment) {
            $date = Carbon::parse($payment->payment_date);

            if ($groupBy === 'week') {
                $startOfWeek = $date->copy()->startOfWeek();
                $endOfWeek = $date->copy()->endOfWeek();
                $periodKey = $date->format('o-W');
                $periodLabel = 'Semana ' . $date->isoWeek . ' (' . $startOfWeek->format('d/m/Y') . ' - ' . $endOfWeek->format('d/m/Y') . ')';
                $sortKey = $startOfWeek->format('Y-m-d');
            } else {
                $periodKey = $date->format('Y-m');
                $periodLabel = ucfirst($date->translatedFormat('F Y'));
                $sortKey = $date->format('Y-m-01');
            }

            if (!isset($groupedPeriods[$periodKey])) {
                $planBreakdown = [];
                foreach ($plans as $plan) {
                    $planBreakdown[$plan->id] = 0.0;
                }
                $planBreakdown['other'] = 0.0;

                $groupedPeriods[$periodKey] = [
                    'period_key' => $periodKey,
                    'period_label' => $periodLabel,
                    'sort_key' => $sortKey,
                    'total' => 0.0,
                    'payment_count' => 0,
                    'plans' => $planBreakdown,
                ];
            }

            $planId = $payment->memberMembership?->plan_id ?? 'other';
            $amount = (float) $payment->amount;

            $groupedPeriods[$periodKey]['total'] += $amount;
            $groupedPeriods[$periodKey]['payment_count'] += 1;

            if (isset($groupedPeriods[$periodKey]['plans'][$planId])) {
                $groupedPeriods[$periodKey]['plans'][$planId] += $amount;
            } else {
                $groupedPeriods[$periodKey]['plans']['other'] += $amount;
            }

            if (isset($incomeByPlan[$planId])) {
                $incomeByPlan[$planId]['total'] += $amount;
                $incomeByPlan[$planId]['count'] += 1;
            } else {
                $incomeByPlan['other']['total'] += $amount;
                $incomeByPlan['other']['count'] += 1;
            }
        }

        // Sort periods chronologically
        usort($groupedPeriods, fn($a, $b) => strcmp($a['sort_key'], $b['sort_key']));

        // Clean up 'other' plan if zero
        if ($incomeByPlan['other']['total'] === 0.0 && $incomeByPlan['other']['count'] === 0) {
            unset($incomeByPlan['other']);
        }

        return [
            'groupBy' => $groupBy,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'totalIncome' => $totalIncome,
            'totalPaymentsCount' => $payments->count(),
            'incomeByPlan' => $incomeByPlan,
            'plans' => $plans,
            'periods' => $groupedPeriods,
            'payments' => $payments,
        ];
    }

    /**
     * Build Membership Expiration Report.
     */
    public function getMembershipExpirationReport(?string $startDate = null, ?string $endDate = null): array
    {
        $expiringActive = $this->reportRepository->getExpiringActiveMemberships(7);
        $expired = $this->reportRepository->getExpiredMemberships($startDate, $endDate);

        return [
            'startDate' => $startDate,
            'endDate' => $endDate,
            'expiringActive' => $expiringActive,
            'expired' => $expired,
            'expiringCount' => $expiringActive->count(),
            'expiredCount' => $expired->count(),
        ];
    }
}
