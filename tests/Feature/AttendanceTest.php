<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\MemberMembership;
use App\Models\MembershipPlan;
use App\Models\Role;
use App\Models\User;
use App\Services\AttendanceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    private Role $memberRole;

    private Role $receptionistRole;

    private MembershipPlan $plan;

    private AttendanceService $attendanceService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        $this->attendanceService = app(AttendanceService::class);

        $this->memberRole = Role::create([
            'name' => 'member',
            'description' => 'Socio',
        ]);

        $this->receptionistRole = Role::create([
            'name' => 'receptionist',
            'description' => 'Recepcionista',
        ]);

        $this->plan = MembershipPlan::create([
            'name' => 'Basic',
            'description' => 'Plan basico',
            'price' => 150,
            'duration_months' => 1,
            'includes_group_classes' => false,
            'weekly_class_limit' => null,
            'includes_trainer' => false,
            'has_waitlist_priority' => false,
        ]);
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

    private function createReceptionist(): User
    {
        return User::create([
            'role_id' => $this->receptionistRole->id,
            'first_name' => 'Recepcion',
            'last_name' => 'Prueba',
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
            'phone_number' => null,
            'is_active' => true,
        ]);
    }

    private function createMembership(Member $member, string $status): MemberMembership
    {
        return MemberMembership::create([
            'member_id' => $member->user_id,
            'plan_id' => $this->plan->id,
            'status' => $status,
            'start_date' => today()->subDays(10),
            'end_date' => today()->addDays(20),
            'cancellation_reason' => null,
            'cancellation_date' => null,
        ]);
    }

    public function test_receptionist_can_check_in_member_with_active_membership(): void
    {
        $receptionist = $this->createReceptionist();
        $member = $this->createMember();
        $this->createMembership($member, 'active');

        $response = $this
            ->actingAs($receptionist)
            ->post(route('attendance.check-in'), [
                'member_id' => $member->user_id,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('attendances', [
            'member_id' => $member->user_id,
        ]);
    }

    public function test_check_in_is_blocked_when_membership_is_frozen(): void
    {
        $receptionist = $this->createReceptionist();
        $member = $this->createMember();
        $this->createMembership($member, 'frozen');

        $response = $this
            ->actingAs($receptionist)
            ->post(route('attendance.check-in'), [
                'member_id' => $member->user_id,
            ]);

        $response->assertSessionHas('error');
        $this->assertStringContainsString('congelada', $response->getSession()->get('error'));

        $this->assertDatabaseCount('attendances', 0);
    }

    public function test_check_in_is_blocked_when_membership_is_expired(): void
    {
        $receptionist = $this->createReceptionist();
        $member = $this->createMember();
        $this->createMembership($member, 'expired');

        $response = $this
            ->actingAs($receptionist)
            ->post(route('attendance.check-in'), [
                'member_id' => $member->user_id,
            ]);

        $response->assertSessionHas('error');
        $this->assertStringContainsString('vencida', $response->getSession()->get('error'));

        $this->assertDatabaseCount('attendances', 0);
    }

    public function test_check_in_is_blocked_when_membership_is_cancelled(): void
    {
        $receptionist = $this->createReceptionist();
        $member = $this->createMember();
        $this->createMembership($member, 'cancelled');

        $response = $this
            ->actingAs($receptionist)
            ->post(route('attendance.check-in'), [
                'member_id' => $member->user_id,
            ]);

        $response->assertSessionHas('error');
        $this->assertStringContainsString('cancelada', $response->getSession()->get('error'));

        $this->assertDatabaseCount('attendances', 0);
    }

    public function test_check_in_is_blocked_when_member_has_no_membership(): void
    {
        $receptionist = $this->createReceptionist();
        $member = $this->createMember();

        $response = $this
            ->actingAs($receptionist)
            ->post(route('attendance.check-in'), [
                'member_id' => $member->user_id,
            ]);

        $response->assertSessionHas('error');
        $this->assertStringContainsString('no tiene una membresia vigente', $response->getSession()->get('error'));

        $this->assertDatabaseCount('attendances', 0);
    }

    public function test_member_cannot_check_in_twice_without_checking_out(): void
    {
        $receptionist = $this->createReceptionist();
        $member = $this->createMember();
        $this->createMembership($member, 'active');

        $this->attendanceService->checkIn($member);

        $response = $this
            ->actingAs($receptionist)
            ->post(route('attendance.check-in'), [
                'member_id' => $member->user_id,
            ]);

        $response->assertSessionHas('error');
        $this->assertStringContainsString('ya tiene un check-in abierto', $response->getSession()->get('error'));

        $this->assertDatabaseCount('attendances', 1);
    }

    public function test_receptionist_can_check_out_an_open_attendance(): void
    {
        $receptionist = $this->createReceptionist();
        $member = $this->createMember();
        $this->createMembership($member, 'active');

        $attendance = $this->attendanceService->checkIn($member);

        $response = $this
            ->actingAs($receptionist)
            ->patch(route('attendance.check-out', $attendance));

        $response->assertSessionHas('success');

        $this->assertNotNull($attendance->fresh()->check_out_at);
    }

    public function test_cannot_check_out_an_already_closed_attendance(): void
    {
        $receptionist = $this->createReceptionist();
        $member = $this->createMember();
        $this->createMembership($member, 'active');

        $attendance = $this->attendanceService->checkIn($member);
        $this->attendanceService->checkOut($attendance);

        $response = $this
            ->actingAs($receptionist)
            ->patch(route('attendance.check-out', $attendance));

        $response->assertSessionHas('error');
    }

    public function test_member_cannot_access_attendance_page(): void
    {
        $member = $this->createMember();

        $response = $this
            ->actingAs($member->user)
            ->get(route('attendance.index'));

        $response->assertForbidden();
    }
}
