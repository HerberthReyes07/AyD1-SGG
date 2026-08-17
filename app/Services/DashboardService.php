<?php

namespace App\Services;

use App\Enums\ClassEnrollmentStatus;
use App\Enums\MembershipStatus;
use App\Models\Attendance;
use App\Models\ClassSession;
use App\Models\GuestPass;
use App\Models\Member;
use App\Models\MemberMembership;
use App\Models\MembershipPayment;
use App\Models\Trainer;
use Illuminate\Support\Collection;

class DashboardService
{
    public function __construct(
        protected ReportService $reportService,
        protected CalorieGoalService $calorieGoalService,
        protected MealService $mealService,
    ) {}

    public function getAdminStats(): array
    {
        $statusCounts = MemberMembership::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $membershipsByStatus = collect(MembershipStatus::cases())->map(fn (MembershipStatus $status) => [
            'label' => $status->label(),
            'value' => (int) ($statusCounts[$status->value] ?? 0),
        ])->values();

        $incomeStart = now()->subMonths(5)->startOfMonth()->toDateString();
        $income = $this->reportService->getIncomeReport('month', $incomeStart, null);

        return [
            'totalMembers' => Member::count(),
            'activeMemberships' => (int) ($statusCounts[MembershipStatus::Active->value] ?? 0),
            'guestPassesThisMonth' => GuestPass::whereMonth('visit_date', now()->month)
                ->whereYear('visit_date', now()->year)
                ->count(),
            'upcomingSessions' => ClassSession::where('starts_at', '>=', now())
                ->where('starts_at', '<=', now()->addDays(7))
                ->count(),
            'membershipsByStatus' => $membershipsByStatus,
            'incomeTrend' => collect($income['periods'])->map(fn (array $period) => [
                'label' => $period['period_label'],
                'total' => $period['total'],
            ])->values(),
            'attendanceTrend' => $this->attendanceLastDays(7),
        ];
    }

    public function getReceptionistStats(): array
    {
        $today = today();

        $todayAttendances = Attendance::whereDate('check_in_at', $today)->get();

        return [
            'todayCheckIns' => $todayAttendances->count(),
            'currentlyInGym' => $todayAttendances->whereNull('check_out_at')->count(),
            'guestPassesToday' => GuestPass::whereDate('visit_date', $today)->count(),
            'paymentsToday' => MembershipPayment::whereDate('payment_date', $today)->count(),
            'hourlyAttendance' => $this->hourlyAttendanceToday(),
        ];
    }

    public function getTrainerStats(Trainer $trainer): array
    {
        $upcomingSessionsQuery = ClassSession::whereHas(
            'groupClass',
            fn ($query) => $query->where('trainer_id', $trainer->user_id)
        )->where('starts_at', '>=', now());

        return [
            'activeAssignments' => $trainer->trainerAssignments()->whereNull('end_date')->count(),
            'pastAssignments' => $trainer->trainerAssignments()->whereNotNull('end_date')->count(),
            'upcomingSessionsCount' => (clone $upcomingSessionsQuery)
                ->where('starts_at', '<=', now()->addDays(7))
                ->count(),
            'upcomingSessions' => $upcomingSessionsQuery
                ->with('groupClass')
                ->withCount('enrollments')
                ->orderBy('starts_at')
                ->limit(5)
                ->get(),
        ];
    }

    public function getMemberStats(Member $member): array
    {
        $today = today();

        $membership = $member->memberships()
            ->where('status', MembershipStatus::Active)
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today)
            ->with('plan')
            ->orderByDesc('start_date')
            ->first();

        $goal = $this->calorieGoalService->getCurrentGoal($member);

        $history = $this->mealService->getHistory($member, $today->copy()->subDays(6), $today);
        $todayConsumed = (float) ($history[0]['calories'] ?? 0);

        $upcomingClasses = $member->classEnrollments()
            ->where('status', ClassEnrollmentStatus::Enrolled)
            ->whereHas('classSession', fn ($query) => $query->where('starts_at', '>=', now()))
            ->with('classSession.groupClass')
            ->get()
            ->sortBy(fn ($enrollment) => $enrollment->classSession->starts_at)
            ->take(3)
            ->values();

        return [
            'membership' => $membership,
            'goal' => $goal,
            'todayConsumed' => $todayConsumed,
            'comparison' => $goal ? $this->calorieGoalService->compare($todayConsumed, $goal) : null,
            'calorieTrend' => collect($history)->reverse()->map(fn (array $day) => [
                'label' => $day['date']->format('d/m'),
                'calories' => $day['calories'],
            ])->values(),
            'weeklyAttendance' => $member->attendances()
                ->whereDate('check_in_at', '>=', $today->copy()->startOfWeek())
                ->count(),
            'upcomingClasses' => $upcomingClasses,
        ];
    }

    private function attendanceLastDays(int $days): Collection
    {
        $start = today()->subDays($days - 1);

        $byDate = Attendance::whereDate('check_in_at', '>=', $start)
            ->get()
            ->groupBy(fn (Attendance $attendance) => $attendance->check_in_at->toDateString());

        $result = collect();

        for ($date = $start->copy(); $date->lte(today()); $date->addDay()) {
            $result->push([
                'label' => ucfirst($date->translatedFormat('D d/m')),
                'count' => $byDate->get($date->toDateString(), collect())->count(),
            ]);
        }

        return $result;
    }

    private function hourlyAttendanceToday(): Collection
    {
        $byHour = Attendance::whereDate('check_in_at', today())
            ->get()
            ->groupBy(fn (Attendance $attendance) => $attendance->check_in_at->format('H'));

        $result = collect();

        foreach (range(6, 21) as $hour) {
            $key = str_pad((string) $hour, 2, '0', STR_PAD_LEFT);

            $result->push([
                'label' => "{$key}:00",
                'count' => $byHour->get($key, collect())->count(),
            ]);
        }

        return $result;
    }
}
