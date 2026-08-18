<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use App\Models\Member;
use App\Models\MemberMembership;
use App\Models\MembershipPlan;
use App\Models\MembershipFreeze;
use App\Enums\MembershipStatus;
use App\Services\MembershipService;
use App\Repositories\MembershipRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class MembershipFreezeTest extends TestCase
{
    use RefreshDatabase;

    private Role $memberRole;
    private Role $adminRole;
    private MembershipService $membershipService;
    private MembershipRepository $membershipRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        $this->memberRole = Role::create([
            'name' => 'member',
            'description' => 'Socio',
        ]);

        $this->adminRole = Role::create([
            'name' => 'admin',
            'description' => 'Administrador',
        ]);

        $this->membershipService = app(MembershipService::class);
        $this->membershipRepository = app(MembershipRepository::class);
    }

    private function createMember(): Member
    {
        $user = User::create([
            'role_id' => $this->memberRole->id,
            'first_name' => 'Socio',
            'last_name' => 'Prueba',
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
            'phone_number' => null,
            'is_active' => true,
        ]);

        return Member::create([
            'user_id' => $user->id,
            'birth_date' => '2000-01-01',
        ]);
    }

    private function createPlan(): MembershipPlan
    {
        return MembershipPlan::create([
            'name' => 'Basic Monthly ' . fake()->unique()->word(),
            'description' => 'Monthly plan',
            'price' => 50.00,
            'duration_months' => 1,
            'includes_group_classes' => false,
            'includes_trainer' => false,
        ]);
    }

    public function test_quota_calculation_ignores_older_freezes()
    {
        $member = $this->createMember();
        $plan = $this->createPlan();

        $membership = MemberMembership::create([
            'member_id' => $member->user_id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'start_date' => Carbon::now()->subDays(120)->toDateString(),
            'end_date' => Carbon::now()->addDays(30)->toDateString(),
        ]);

        // Freeze that ended 95 days ago (started 105 days ago) - outside rolling 90 days.
        MembershipFreeze::create([
            'member_membership_id' => $membership->id,
            'start_date' => Carbon::now()->subDays(105)->toDateString(),
            'estimated_reactivation_date' => Carbon::now()->subDays(95)->toDateString(),
            'reactivation_date' => Carbon::now()->subDays(95)->toDateString(),
            'reason' => 'Trip',
            'frozen_days' => 10,
            'registered_by' => $member->user_id,
        ]);

        $accumulated = $this->membershipRepository->getAccumulatedFrozenDaysInQuarter($membership->id);
        $this->assertEquals(0, $accumulated);
    }

    public function test_quota_calculation_includes_overlapping_portions()
    {
        $member = $this->createMember();
        $plan = $this->createPlan();

        $membership = MemberMembership::create([
            'member_id' => $member->user_id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'start_date' => Carbon::now()->subDays(120)->toDateString(),
            'end_date' => Carbon::now()->addDays(30)->toDateString(),
        ]);

        // Freeze started 95 days ago and reactivated 85 days ago.
        // The last 90 days window starts at subDays(90).
        // The portion inside the window is subDays(90) to subDays(85) = 5 days.
        MembershipFreeze::create([
            'member_membership_id' => $membership->id,
            'start_date' => Carbon::now()->subDays(95)->toDateString(),
            'estimated_reactivation_date' => Carbon::now()->subDays(85)->toDateString(),
            'reactivation_date' => Carbon::now()->subDays(85)->toDateString(),
            'reason' => 'Trip',
            'frozen_days' => 10,
            'registered_by' => $member->user_id,
        ]);

        $accumulated = $this->membershipRepository->getAccumulatedFrozenDaysInQuarter($membership->id);
        $this->assertEquals(5, $accumulated);
    }

    public function test_quota_calculation_includes_open_freezes()
    {
        $member = $this->createMember();
        $plan = $this->createPlan();

        $membership = MemberMembership::create([
            'member_id' => $member->user_id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'start_date' => Carbon::now()->subDays(50)->toDateString(),
            'end_date' => Carbon::now()->addDays(30)->toDateString(),
        ]);

        // Open freeze started 10 days ago.
        // It overlaps the window from subDays(10) to now = 10 days.
        MembershipFreeze::create([
            'member_membership_id' => $membership->id,
            'start_date' => Carbon::now()->subDays(10)->toDateString(),
            'estimated_reactivation_date' => Carbon::now()->addDays(5)->toDateString(),
            'reactivation_date' => null,
            'reason' => 'Trip',
            'frozen_days' => null,
            'registered_by' => $member->user_id,
        ]);

        $accumulated = $this->membershipRepository->getAccumulatedFrozenDaysInQuarter($membership->id);
        $this->assertEquals(10, $accumulated);
    }

    public function test_cannot_freeze_if_no_remaining_quota()
    {
        $member = $this->createMember();
        $plan = $this->createPlan();

        $membership = MemberMembership::create([
            'member_id' => $member->user_id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'start_date' => Carbon::now()->subDays(50)->toDateString(),
            'end_date' => Carbon::now()->addDays(30)->toDateString(),
        ]);

        // Accumulated 15 days of freeze already.
        MembershipFreeze::create([
            'member_membership_id' => $membership->id,
            'start_date' => Carbon::now()->subDays(30)->toDateString(),
            'estimated_reactivation_date' => Carbon::now()->subDays(15)->toDateString(),
            'reactivation_date' => Carbon::now()->subDays(15)->toDateString(),
            'reason' => 'Sickness',
            'frozen_days' => 15,
            'registered_by' => $member->user_id,
        ]);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Has alcanzado el máximo de 15 días de congelamiento acumulados');

        $this->membershipService->freezeMembership(
            $membership->id,
            $member->user_id,
            'Freeze request',
            Carbon::now()->addDays(5)->toDateString()
        );
    }

    public function test_cannot_freeze_longer_than_remaining_quota()
    {
        $member = $this->createMember();
        $plan = $this->createPlan();

        $membership = MemberMembership::create([
            'member_id' => $member->user_id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'start_date' => Carbon::now()->subDays(50)->toDateString(),
            'end_date' => Carbon::now()->addDays(30)->toDateString(),
        ]);

        // 10 days accumulated. 5 days remaining.
        MembershipFreeze::create([
            'member_membership_id' => $membership->id,
            'start_date' => Carbon::now()->subDays(30)->toDateString(),
            'estimated_reactivation_date' => Carbon::now()->subDays(20)->toDateString(),
            'reactivation_date' => Carbon::now()->subDays(20)->toDateString(),
            'reason' => 'Sickness',
            'frozen_days' => 10,
            'registered_by' => $member->user_id,
        ]);

        // Requesting 6 days freeze (exceeds 5 remaining).
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('supera tu cupo disponible de congelamiento restante');

        $this->membershipService->freezeMembership(
            $membership->id,
            $member->user_id,
            'Freeze request',
            Carbon::now()->addDays(6)->toDateString()
        );
    }

    public function test_defaults_to_15_days_and_fails_if_quota_is_insufficient()
    {
        $member = $this->createMember();
        $plan = $this->createPlan();

        $membership = MemberMembership::create([
            'member_id' => $member->user_id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'start_date' => Carbon::now()->subDays(50)->toDateString(),
            'end_date' => Carbon::now()->addDays(30)->toDateString(),
        ]);

        // 5 days accumulated. 10 days remaining.
        MembershipFreeze::create([
            'member_membership_id' => $membership->id,
            'start_date' => Carbon::now()->subDays(30)->toDateString(),
            'estimated_reactivation_date' => Carbon::now()->subDays(25)->toDateString(),
            'reactivation_date' => Carbon::now()->subDays(25)->toDateString(),
            'reason' => 'Sickness',
            'frozen_days' => 5,
            'registered_by' => $member->user_id,
        ]);

        // Omitting estimated date will default to 15 days, which exceeds 10 days remaining.
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('supera tu cupo disponible de congelamiento restante');

        $this->membershipService->freezeMembership(
            $membership->id,
            $member->user_id,
            'Freeze request'
        );
    }

    public function test_cancellation_closes_active_freeze_correctly()
    {
        $member = $this->createMember();
        $plan = $this->createPlan();
        $adminUser = User::create([
            'role_id' => $this->adminRole->id,
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@test.com',
            'password' => 'password',
            'is_active' => true,
        ]);

        $membership = MemberMembership::create([
            'member_id' => $member->user_id,
            'plan_id' => $plan->id,
            'status' => 'frozen',
            'start_date' => Carbon::now()->subDays(20)->toDateString(),
            'end_date' => Carbon::now()->addDays(30)->toDateString(),
        ]);

        $freeze = MembershipFreeze::create([
            'member_membership_id' => $membership->id,
            'start_date' => Carbon::now()->subDays(5)->toDateString(),
            'estimated_reactivation_date' => Carbon::now()->addDays(10)->toDateString(),
            'reactivation_date' => null,
            'reason' => 'Sick',
            'frozen_days' => null,
            'registered_by' => $member->user_id,
        ]);

        $this->membershipService->cancelMembership($membership->id, 'Request cancel', $adminUser->id);

        $membership->refresh();
        $freeze->refresh();

        $this->assertEquals(MembershipStatus::Cancelled, $membership->status);
        $this->assertEquals(Carbon::now()->startOfDay(), $freeze->reactivation_date);
        $this->assertEquals(5, $freeze->frozen_days);
    }
}
