<?php

namespace Tests\Feature;

use App\Enums\ClassEnrollmentStatus;
use App\Enums\ClassSessionStatus;
use App\Models\ClassAttendance;
use App\Models\ClassEnrollment;
use App\Models\ClassSession;
use App\Models\GroupClass;
use App\Models\Member;
use App\Models\Role;
use App\Models\Trainer;
use App\Models\TrainerSpecialty;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClassAttendanceReportTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $receptionist;
    private User $trainerUser;

    private Role $memberRole;

    private Trainer $trainer;

    private GroupClass $yoga;
    private GroupClass $spinning;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        Carbon::setTestNow(
            Carbon::parse('2026-08-13 17:39:47')
        );

        $adminRole = Role::create([
            'name' => 'admin',
            'description' => 'Administrador',
        ]);

        $receptionistRole = Role::create([
            'name' => 'receptionist',
            'description' => 'Recepcionista',
        ]);

        $trainerRole = Role::create([
            'name' => 'trainer',
            'description' => 'Entrenador',
        ]);

        $this->memberRole = Role::create([
            'name' => 'member',
            'description' => 'Socio',
        ]);

        $this->admin = User::create([
            'role_id' => $adminRole->id,
            'first_name' => 'Admin',
            'last_name' => 'Reporte',
            'email' => 'admin.attendance@test.com',
            'password' => 'password',
            'phone_number' => null,
            'is_active' => true,
        ]);

        $this->receptionist = User::create([
            'role_id' => $receptionistRole->id,
            'first_name' => 'Recepcionista',
            'last_name' => 'Reporte',
            'email' => 'recep.attendance@test.com',
            'password' => 'password',
            'phone_number' => null,
            'is_active' => true,
        ]);

        $specialty = TrainerSpecialty::create([
            'name' => 'Clases grupales',
            'description' => 'Especialidad de prueba',
        ]);

        $this->trainerUser = User::create([
            'role_id' => $trainerRole->id,
            'first_name' => 'Entrenador',
            'last_name' => 'Prueba',
            'email' => 'trainer.attendance@test.com',
            'password' => 'password',
            'phone_number' => null,
            'is_active' => true,
        ]);

        $this->trainer = Trainer::create([
            'user_id' => $this->trainerUser->id,
            'specialty_id' => $specialty->id,
        ]);

        $this->yoga = GroupClass::create([
            'name' => 'Yoga Reporte',
            'description' => 'Yoga para pruebas',
            'duration_minutes' => 60,
            'max_participants' => 20,
            'is_active' => true,
            'category_id' => null,
            'trainer_id' => $this->trainer->user_id,
        ]);

        $this->spinning = GroupClass::create([
            'name' => 'Spinning Reporte',
            'description' => 'Spinning para pruebas',
            'duration_minutes' => 45,
            'max_participants' => 20,
            'is_active' => true,
            'category_id' => null,
            'trainer_id' => $this->trainer->user_id,
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    private function createMember(
        string $email
    ): Member {
        $user = User::create([
            'role_id' => $this->memberRole->id,
            'first_name' => 'Socio',
            'last_name' => 'Prueba',
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
        GroupClass $groupClass,
        string $startsAt,
        ClassSessionStatus $status =
            ClassSessionStatus::Completed
    ): ClassSession {
        return ClassSession::create([
            'group_class_id' => $groupClass->id,
            'starts_at' => $startsAt,
            'status' => $status,
            'change_reason' => null,
        ]);
    }

    private function createEnrollment(
        Member $member,
        ClassSession $session,
        ClassEnrollmentStatus $status
    ): ClassEnrollment {
        return ClassEnrollment::create([
            'member_id' => $member->user_id,
            'class_session_id' => $session->id,
            'enrollment_date' => '2026-08-01',
            'status' => $status,
        ]);
    }

    public function test_admin_can_access_attendance_report(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->get(
                route(
                    'class-attendance-reports.index'
                )
            );

        $response
            ->assertOk()
            ->assertSee(
                'Reporte de asistencia por clase'
            );
    }

    public function test_receptionist_cannot_access_attendance_report(): void
    {
        $response = $this
            ->actingAs($this->receptionist)
            ->get(
                route(
                    'class-attendance-reports.index'
                )
            );

        $response->assertForbidden();
    }

    public function test_marking_attended_creates_class_check_in(): void
    {
        $member = $this->createMember(
            'elite@test.com'
        );

        $session = $this->createSession(
            $this->yoga,
            '2026-08-21 11:37:00',
            ClassSessionStatus::InProgress
        );

        $enrollment = $this->createEnrollment(
            $member,
            $session,
            ClassEnrollmentStatus::Enrolled
        );

        $response = $this
            ->actingAs($this->trainerUser)
            ->patch(
                route(
                    'trainer-classes.attendance',
                    $session
                ),
                [
                    'attendance' => [
                        $enrollment->id =>
                            ClassEnrollmentStatus::Attended->value,
                    ],
                ]
            );

        $response->assertSessionHasNoErrors();

        $enrollment->refresh();

        $this->assertEquals(
            ClassEnrollmentStatus::Attended,
            $enrollment->status
        );

        $this->assertDatabaseHas(
            'class_attendances',
            [
                'class_enrollment_id' =>
                    $enrollment->id,
                'check_in_at' =>
                    '2026-08-13 17:39:47',
            ]
        );
    }

    public function test_marking_no_show_removes_class_check_in(): void
    {
        $member = $this->createMember(
            'premium@test.com'
        );

        $session = $this->createSession(
            $this->yoga,
            '2026-08-21 11:37:00',
            ClassSessionStatus::InProgress
        );

        $enrollment = $this->createEnrollment(
            $member,
            $session,
            ClassEnrollmentStatus::Attended
        );

        ClassAttendance::create([
            'class_enrollment_id' =>
                $enrollment->id,
            'check_in_at' =>
                '2026-08-13 17:30:00',
        ]);

        $response = $this
            ->actingAs($this->trainerUser)
            ->patch(
                route(
                    'trainer-classes.attendance',
                    $session
                ),
                [
                    'attendance' => [
                        $enrollment->id =>
                            ClassEnrollmentStatus::NoShow->value,
                    ],
                ]
            );

        $response->assertSessionHasNoErrors();

        $enrollment->refresh();

        $this->assertEquals(
            ClassEnrollmentStatus::NoShow,
            $enrollment->status
        );

        $this->assertDatabaseMissing(
            'class_attendances',
            [
                'class_enrollment_id' =>
                    $enrollment->id,
            ]
        );
    }

    public function test_report_filters_by_date_range(): void
    {
        $member1 = $this->createMember(
            'member1@test.com'
        );

        $member2 = $this->createMember(
            'member2@test.com'
        );

        $inside = $this->createSession(
            $this->yoga,
            '2026-08-10 10:00:00'
        );

        $outside = $this->createSession(
            $this->yoga,
            '2026-08-20 10:00:00'
        );

        $this->createEnrollment(
            $member1,
            $inside,
            ClassEnrollmentStatus::Attended
        );

        $this->createEnrollment(
            $member2,
            $outside,
            ClassEnrollmentStatus::NoShow
        );

        $response = $this
            ->actingAs($this->admin)
            ->get(
                route(
                    'class-attendance-reports.index',
                    [
                        'date_from' =>
                            '2026-08-07',
                        'date_to' =>
                            '2026-08-14',
                    ]
                )
            );

        $response->assertOk();

        $response->assertViewHas(
            'summary',
            function ($summary) {
                return
                    $summary['total'] === 1 &&
                    $summary['attended'] === 1 &&
                    $summary['no_show'] === 0 &&
                    $summary['percentage'] === 100.0;
            }
        );
    }

    public function test_report_filters_by_group_class(): void
        {
            $member1 = $this->createMember(
                'yoga.member@test.com'
            );

            $member2 = $this->createMember(
                'spinning.member@test.com'
            );

            $yogaSession = $this->createSession(
                $this->yoga,
                '2026-08-10 10:00:00'
            );

            $spinningSession = $this->createSession(
                $this->spinning,
                '2026-08-10 12:00:00'
            );

            $this->createEnrollment(
                $member1,
                $yogaSession,
                ClassEnrollmentStatus::Attended
            );

            $this->createEnrollment(
                $member2,
                $spinningSession,
                ClassEnrollmentStatus::Attended
            );

            $response = $this
                ->actingAs($this->admin)
                ->get(
                    route(
                        'class-attendance-reports.index',
                        [
                            'group_class_id' =>
                                $this->yoga->id,
                        ]
                    )
                );

            $response->assertOk();

            $response->assertViewHas(
                'records',
                function ($records) {
                    return
                        $records->count() === 1 &&
                        $records->first()
                            ->classSession
                            ->groupClass
                            ->id === $this->yoga->id &&
                        $records->first()
                            ->classSession
                            ->groupClass
                            ->name === 'Yoga Reporte';
                }
            );

            $response->assertViewHas(
                'summary',
                function ($summary) {
                    return
                        $summary['total'] === 1 &&
                        $summary['attended'] === 1 &&
                        $summary['no_show'] === 0 &&
                        $summary['percentage'] === 100.0;
                }
            );
        }

    public function test_invalid_date_range_is_rejected(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->get(
                route(
                    'class-attendance-reports.index',
                    [
                        'date_from' =>
                            '2026-08-20',
                        'date_to' =>
                            '2026-08-10',
                    ]
                )
            );

        $response->assertSessionHasErrors(
            'date_to'
        );
    }


    public function test_admin_can_export_attendance_report_excel(): void
{
    $member = $this->createMember(
        'excel.member@test.com'
    );

    $session = $this->createSession(
        $this->yoga,
        '2026-08-10 10:00:00'
    );

    $this->createEnrollment(
        $member,
        $session,
        ClassEnrollmentStatus::Attended
    );

    $response = $this
        ->actingAs($this->admin)
        ->get(
            route(
                'class-attendance-reports.export-excel',
                [
                    'date_from' => '2026-08-01',
                    'date_to' => '2026-08-31',
                    'group_class_id' =>
                        $this->yoga->id,
                ]
            )
        );

    $response->assertOk();

    $response->assertHeader(
        'content-type',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    );

    $this->assertStringContainsString(
        '.xlsx',
        $response->headers->get(
            'content-disposition'
        )
    );
}


public function test_receptionist_cannot_export_attendance_report_excel(): void
{
    $response = $this
        ->actingAs($this->receptionist)
        ->get(
            route(
                'class-attendance-reports.export-excel'
            )
        );

    $response->assertForbidden();
}


public function test_admin_can_export_attendance_report_pdf(): void
{
    $member = $this->createMember(
        'pdf.member@test.com'
    );

    $session = $this->createSession(
        $this->yoga,
        '2026-08-10 10:00:00'
    );

    $this->createEnrollment(
        $member,
        $session,
        ClassEnrollmentStatus::Attended
    );

    $response = $this
        ->actingAs($this->admin)
        ->post(
            route(
                'class-attendance-reports.export-pdf'
            ),
            [
                'date_from' => '2026-08-01',
                'date_to' => '2026-08-31',
                'group_class_id' =>
                    $this->yoga->id,
            ]
        );

    $response->assertOk();

    $response->assertHeader(
        'content-type',
        'application/pdf'
    );

    $this->assertStringContainsString(
        '.pdf',
        $response->headers->get(
            'content-disposition'
        )
    );
}


public function test_receptionist_cannot_export_attendance_report_pdf(): void
{
    $response = $this
        ->actingAs($this->receptionist)
        ->post(
            route(
                'class-attendance-reports.export-pdf'
            )
        );

    $response->assertForbidden();
}
}