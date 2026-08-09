<?php

namespace Tests\Feature;

use App\Enums\ClassEnrollmentStatus;
use App\Enums\ClassSessionStatus;
use App\Enums\WaitlistStatus;
use App\Models\ClassCategory;
use App\Models\ClassEnrollment;
use App\Models\ClassRating;
use App\Models\ClassSession;
use App\Models\ClassWaitlist;
use App\Models\GroupClass;
use App\Models\Member;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GroupClassReportTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $receptionist;

    private Role $memberRole;

    private GroupClass $groupClass;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        $adminRole = Role::create([
            'name' => 'admin',
            'description' => 'Administrador',
        ]);

        $receptionistRole = Role::create([
            'name' => 'receptionist',
            'description' => 'Recepcionista',
        ]);

        $this->memberRole = Role::create([
            'name' => 'member',
            'description' => 'Socio',
        ]);

        $this->admin = User::create([
            'role_id' => $adminRole->id,
            'first_name' => 'Admin',
            'last_name' => 'Reporte',
            'email' => 'admin.report@test.com',
            'password' => 'password',
            'phone_number' => null,
            'is_active' => true,
        ]);

        $this->receptionist = User::create([
            'role_id' => $receptionistRole->id,
            'first_name' => 'Recepcionista',
            'last_name' => 'Reporte',
            'email' => 'recep.report@test.com',
            'password' => 'password',
            'phone_number' => null,
            'is_active' => true,
        ]);

        $category = ClassCategory::create([
            'name' => 'Yoga',
            'description' => 'Categoria de prueba',
        ]);

        $this->groupClass = GroupClass::create([
            'name' => 'Yoga Reporte',
            'description' => 'Clase para reportes',
            'duration_minutes' => 60,
            'max_participants' => 20,
            'is_active' => true,
            'category_id' => $category->id,
            'trainer_id' => null,
        ]);
    }

    private function createMember(
        string $email
    ): Member {
        $user = User::create([
            'role_id' => $this->memberRole->id,
            'first_name' => 'Socio',
            'last_name' => 'Reporte',
            'email' => $email,
            'password' => 'password',
            'phone_number' => null,
            'is_active' => true,
        ]);

        return Member::create([
            'user_id' => $user->id,
            'birth_date' => '2000-01-01',
        ]);
    }

    private function createSession(
        string $startsAt,
        ClassSessionStatus $status
    ): ClassSession {
        return ClassSession::create([
            'group_class_id' => $this->groupClass->id,
            'starts_at' => $startsAt,
            'status' => $status,
            'change_reason' => in_array(
                $status,
                [
                    ClassSessionStatus::Cancelled,
                    ClassSessionStatus::Rescheduled,
                ],
                true
            )
                ? 'Motivo de prueba'
                : null,
        ]);
    }

    public function test_admin_can_access_group_class_report(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->get(
                route('group-class-reports.index')
            );

        $response
            ->assertOk()
            ->assertSee(
                'Reportes de clases grupales'
            );
    }

    public function test_receptionist_cannot_access_group_class_report(): void
    {
        $response = $this
            ->actingAs($this->receptionist)
            ->get(
                route('group-class-reports.index')
            );

        $response->assertForbidden();
    }

    public function test_report_calculates_session_summary(): void
    {
        $this->createSession(
            '2026-08-10 10:00:00',
            ClassSessionStatus::Scheduled
        );

        $this->createSession(
            '2026-08-11 10:00:00',
            ClassSessionStatus::Scheduled
        );

        $this->createSession(
            '2026-08-12 10:00:00',
            ClassSessionStatus::Completed
        );

        $this->createSession(
            '2026-08-13 10:00:00',
            ClassSessionStatus::Cancelled
        );

        $this->createSession(
            '2026-08-14 10:00:00',
            ClassSessionStatus::Rescheduled
        );

        $response = $this
            ->actingAs($this->admin)
            ->get(
                route('group-class-reports.index')
            );

        $response->assertOk();

        $response->assertViewHas(
            'summary',
            function ($summary) {
                return
                    $summary['total'] === 5 &&
                    $summary['scheduled'] === 2 &&
                    $summary['completed'] === 1 &&
                    $summary['cancelled'] === 1 &&
                    $summary['rescheduled'] === 1;
            }
        );
    }

    public function test_report_can_be_filtered_by_date_range(): void
    {
        $this->createSession(
            '2026-08-05 10:00:00',
            ClassSessionStatus::Completed
        );

        $this->createSession(
            '2026-08-10 10:00:00',
            ClassSessionStatus::Scheduled
        );

        $this->createSession(
            '2026-08-12 10:00:00',
            ClassSessionStatus::Cancelled
        );

        $this->createSession(
            '2026-08-20 10:00:00',
            ClassSessionStatus::Completed
        );

        $response = $this
            ->actingAs($this->admin)
            ->get(
                route(
                    'group-class-reports.index',
                    [
                        'date_from' => '2026-08-07',
                        'date_to' => '2026-08-14',
                    ]
                )
            );

        $response->assertOk();

        $response->assertViewHas(
            'summary',
            function ($summary) {
                return
                    $summary['total'] === 2 &&
                    $summary['scheduled'] === 1 &&
                    $summary['cancelled'] === 1 &&
                    $summary['completed'] === 0;
            }
        );
    }

    public function test_invalid_date_range_is_rejected(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->get(
                route(
                    'group-class-reports.index',
                    [
                        'date_from' => '2026-08-20',
                        'date_to' => '2026-08-10',
                    ]
                )
            );

        $response->assertSessionHasErrors(
            'date_to'
        );
    }

    public function test_report_calculates_demand_attendance_waitlist_and_rating(): void
    {
        $member1 = $this->createMember(
            'member1.report@test.com'
        );

        $member2 = $this->createMember(
            'member2.report@test.com'
        );

        $member3 = $this->createMember(
            'member3.report@test.com'
        );

        $session = $this->createSession(
            '2026-08-12 10:00:00',
            ClassSessionStatus::Completed
        );

        $attended1 = ClassEnrollment::create([
            'member_id' => $member1->user_id,
            'class_session_id' => $session->id,
            'enrollment_date' => '2026-08-01',
            'status' =>
                ClassEnrollmentStatus::Attended,
        ]);

        $attended2 = ClassEnrollment::create([
            'member_id' => $member2->user_id,
            'class_session_id' => $session->id,
            'enrollment_date' => '2026-08-01',
            'status' =>
                ClassEnrollmentStatus::Attended,
        ]);

        ClassEnrollment::create([
            'member_id' => $member3->user_id,
            'class_session_id' => $session->id,
            'enrollment_date' => '2026-08-01',
            'status' =>
                ClassEnrollmentStatus::NoShow,
        ]);

        ClassRating::create([
            'class_enrollment_id' => $attended1->id,
            'rating' => 5,
            'comment' => 'Excelente',
        ]);

        ClassRating::create([
            'class_enrollment_id' => $attended2->id,
            'rating' => 3,
            'comment' => 'Buena',
        ]);

        ClassWaitlist::create([
            'member_id' => $member1->user_id,
            'class_session_id' => $session->id,
            'requested_date' => '2026-08-01',
            'status' => WaitlistStatus::Notified,
        ]);

        ClassWaitlist::create([
            'member_id' => $member2->user_id,
            'class_session_id' => $session->id,
            'requested_date' => '2026-08-01',
            'status' => WaitlistStatus::Waiting,
        ]);

        $response = $this
            ->actingAs($this->admin)
            ->get(
                route('group-class-reports.index')
            );

        $response->assertOk();

        $response->assertViewHas(
            'classes',
            function ($classes) {

                $class = $classes->first();

                return
                    $class['name'] === 'Yoga Reporte' &&
                    $class['sessions'] === 1 &&
                    $class['enrollments'] === 3 &&
                    $class['waitlist_requests'] === 2 &&
                    $class['waitlist_promotions'] === 1 &&
                    $class['attended'] === 2 &&
                    $class['no_show'] === 1 &&
                    $class['average_rating'] === 4.0 &&
                    $class['ratings_count'] === 2;
            }
        );
    }
}