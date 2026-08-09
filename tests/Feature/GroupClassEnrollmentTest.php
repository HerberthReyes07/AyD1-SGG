<?php

namespace Tests\Feature;

use App\Enums\ClassEnrollmentStatus;
use App\Enums\ClassSessionStatus;
use App\Enums\WaitlistStatus;
use App\Models\ClassEnrollment;
use App\Models\ClassSession;
use App\Models\ClassWaitlist;
use App\Models\GroupClass;
use App\Models\Member;
use App\Models\MemberMembership;
use App\Models\MembershipPlan;
use App\Models\Role;
use App\Models\User;
use App\Services\GroupClassEnrollmentService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class GroupClassEnrollmentTest extends TestCase
{
    use RefreshDatabase;

    private GroupClassEnrollmentService $service;
    private Role $memberRole;

    private MembershipPlan $basicPlan;
    private MembershipPlan $premiumPlan;
    private MembershipPlan $elitePlan;

    private GroupClass $groupClass;

    private int $userNumber = 0;

    protected function setUp(): void
    {
        parent::setUp();

        Carbon::setTestNow(
            Carbon::parse('2026-08-08 10:00:00')
        );

        $this->service = app(
            GroupClassEnrollmentService::class
        );

        $this->memberRole = Role::create([
            'name' => 'member',
            'description' => 'Socio',
        ]);

        $this->basicPlan = $this->createPlan(
            'Basic',
            false,
            null,
            false
        );

        $this->premiumPlan = $this->createPlan(
            'Premium',
            true,
            3,
            false
        );

        $this->elitePlan = $this->createPlan(
            'Elite',
            true,
            null,
            true
        );

        $this->groupClass = GroupClass::create([
            'name' => 'Yoga de prueba',
            'description' => 'Clase para pruebas',
            'duration_minutes' => 60,
            'max_participants' => 1,
            'is_active' => true,
            'category_id' => null,
            'trainer_id' => null,
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    private function createPlan(
        string $name,
        bool $includesClasses,
        ?int $weeklyLimit,
        bool $priority
    ): MembershipPlan {
        return MembershipPlan::create([
            'name' => $name,
            'description' => 'Plan de prueba',
            'price' => 100,
            'duration_months' => 1,
            'includes_group_classes' => $includesClasses,
            'weekly_class_limit' => $weeklyLimit,
            'includes_trainer' => false,
            'has_waitlist_priority' => $priority,
        ]);
    }

    private function createMember(
        MembershipPlan $plan
    ): Member {
        $this->userNumber++;

        $user = User::create([
            'role_id' => $this->memberRole->id,
            'first_name' => 'Socio',
            'last_name' => 'Prueba ' . $this->userNumber,
            'email' => 'socio' . $this->userNumber . '@test.com',
            'password' => 'password',
            'phone_number' => null,
            'is_active' => true,
        ]);

        $member = Member::create([
            'user_id' => $user->id,
            'birth_date' => '2000-01-01',
        ]);

        MemberMembership::create([
            'member_id' => $member->user_id,
            'plan_id' => $plan->id,
            'status' => 'active',
            'start_date' => '2026-08-01',
            'end_date' => '2026-12-31',
            'cancellation_reason' => null,
            'cancellation_date' => null,
        ]);

        return $member;
    }

    private function createSession(
        string $date
    ): ClassSession {
        return ClassSession::create([
            'group_class_id' => $this->groupClass->id,
            'starts_at' => $date,
            'status' => ClassSessionStatus::Scheduled,
            'change_reason' => null,
        ]);
    }

    public function test_basic_member_cannot_enroll(): void
    {
        $member = $this->createMember(
            $this->basicPlan
        );

        $session = $this->createSession(
            '2026-08-10 10:00:00'
        );

        try {
            $this->service->enroll(
                $member,
                $session
            );

            $this->fail(
                'Un socio Basic no deberia poder inscribirse.'
            );
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey(
                'member_id',
                $exception->errors()
            );
        }

        $this->assertDatabaseCount(
            'class_enrollments',
            0
        );
    }

    public function test_premium_member_cannot_exceed_weekly_limit(): void
    {
        $member = $this->createMember(
            $this->premiumPlan
        );

        $session1 = $this->createSession(
            '2026-08-10 10:00:00'
        );

        $session2 = $this->createSession(
            '2026-08-11 10:00:00'
        );

        $session3 = $this->createSession(
            '2026-08-12 10:00:00'
        );

        $session4 = $this->createSession(
            '2026-08-13 10:00:00'
        );

        $this->service->enroll(
            $member,
            $session1
        );

        $this->service->enroll(
            $member,
            $session2
        );

        $this->service->enroll(
            $member,
            $session3
        );

        try {
            $this->service->enroll(
                $member,
                $session4
            );

            $this->fail(
                'Premium no deberia superar 3 clases por semana.'
            );
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey(
                'member_id',
                $exception->errors()
            );
        }

        $this->assertEquals(
            3,
            ClassEnrollment::where(
                'member_id',
                $member->user_id
            )
                ->where(
                    'status',
                    ClassEnrollmentStatus::Enrolled->value
                )
                ->count()
        );
    }

    public function test_full_class_adds_member_to_waitlist(): void
    {
        $premiumA = $this->createMember(
            $this->premiumPlan
        );

        $premiumB = $this->createMember(
            $this->premiumPlan
        );

        $session = $this->createSession(
            '2026-08-15 10:00:00'
        );

        $this->service->enroll(
            $premiumA,
            $session
        );

        $result = $this->service->enroll(
            $premiumB,
            $session
        );

        $this->assertEquals(
            'waitlist',
            $result['type']
        );

        $this->assertDatabaseHas(
            'class_waitlists',
            [
                'member_id' => $premiumB->user_id,
                'class_session_id' => $session->id,
                'status' => WaitlistStatus::Waiting->value,
            ]
        );

        $this->assertEquals(
            1,
            ClassEnrollment::where(
                'class_session_id',
                $session->id
            )
                ->where(
                    'status',
                    ClassEnrollmentStatus::Enrolled->value
                )
                ->count()
        );
    }

    public function test_elite_member_is_promoted_before_premium(): void
    {
        $premiumA = $this->createMember(
            $this->premiumPlan
        );

        $premiumB = $this->createMember(
            $this->premiumPlan
        );

        $elite = $this->createMember(
            $this->elitePlan
        );

        $session = $this->createSession(
            '2026-08-15 10:00:00'
        );

        $this->service->enroll(
            $premiumA,
            $session
        );

        $this->service->enroll(
            $premiumB,
            $session
        );

        $this->service->enroll(
            $elite,
            $session
        );

        $this->service->cancel(
            $premiumA,
            $session
        );

        $this->assertDatabaseHas(
            'class_enrollments',
            [
                'member_id' => $elite->user_id,
                'class_session_id' => $session->id,
                'status' =>
                    ClassEnrollmentStatus::Enrolled->value,
            ]
        );

        $this->assertDatabaseHas(
            'class_waitlists',
            [
                'member_id' => $premiumB->user_id,
                'class_session_id' => $session->id,
                'status' =>
                    WaitlistStatus::Waiting->value,
            ]
        );

        $this->assertDatabaseHas(
            'class_waitlists',
            [
                'member_id' => $elite->user_id,
                'class_session_id' => $session->id,
                'status' =>
                    WaitlistStatus::Notified->value,
            ]
        );
    }

    public function test_class_capacity_is_never_exceeded(): void
    {
        $premiumA = $this->createMember(
            $this->premiumPlan
        );

        $premiumB = $this->createMember(
            $this->premiumPlan
        );

        $elite = $this->createMember(
            $this->elitePlan
        );

        $session = $this->createSession(
            '2026-08-15 14:00:00'
        );

        $this->service->enroll(
            $premiumA,
            $session
        );

        $this->service->enroll(
            $premiumB,
            $session
        );

        $this->service->enroll(
            $elite,
            $session
        );

        $enrolledCount = ClassEnrollment::where(
            'class_session_id',
            $session->id
        )
            ->where(
                'status',
                ClassEnrollmentStatus::Enrolled->value
            )
            ->count();

        $waitingCount = ClassWaitlist::where(
            'class_session_id',
            $session->id
        )
            ->where(
                'status',
                WaitlistStatus::Waiting->value
            )
            ->count();

        $this->assertEquals(
            1,
            $enrolledCount
        );

        $this->assertEquals(
            2,
            $waitingCount
        );

        $this->assertLessThanOrEqual(
            $this->groupClass->max_participants,
            $enrolledCount
        );
    }
}