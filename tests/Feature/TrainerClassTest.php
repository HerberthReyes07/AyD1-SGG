<?php

namespace Tests\Feature;

use App\Enums\ClassEnrollmentStatus;
use App\Enums\ClassSessionStatus;
use App\Models\ClassEnrollment;
use App\Models\ClassSession;
use App\Models\GroupClass;
use App\Models\Member;
use App\Models\Role;
use App\Models\Trainer;
use App\Models\TrainerSpecialty;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TrainerClassTest extends TestCase
{
    use RefreshDatabase;

    private Role $trainerRole;
    private Role $memberRole;

    private User $trainerUser;
    private Trainer $trainer;

    private GroupClass $groupClass;
    private ClassSession $session;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        $this->trainerRole = Role::create([
            'name' => 'trainer',
            'description' => 'Entrenador',
        ]);

        $this->memberRole = Role::create([
            'name' => 'member',
            'description' => 'Socio',
        ]);

        $specialty = TrainerSpecialty::create([
            'name' => 'Entrenamiento grupal',
            'description' => 'Especialidad para pruebas',
        ]);

        $this->trainerUser = User::create([
            'role_id' => $this->trainerRole->id,
            'first_name' => 'Entrenador',
            'last_name' => 'Principal',
            'email' => 'trainer@test.com',
            'password' => 'password',
            'phone_number' => null,
            'is_active' => true,
        ]);

        $this->trainer = Trainer::create([
            'user_id' => $this->trainerUser->id,
            'specialty_id' => $specialty->id,
        ]);

        $this->groupClass = GroupClass::create([
            'name' => 'Yoga Test',
            'description' => 'Clase de prueba',
            'duration_minutes' => 60,
            'max_participants' => 10,
            'is_active' => true,
            'category_id' => null,
            'trainer_id' => $this->trainer->user_id,
        ]);

        $this->session = ClassSession::create([
            'group_class_id' => $this->groupClass->id,
            'starts_at' => now()->addDay(),
            'status' => ClassSessionStatus::Scheduled,
            'change_reason' => null,
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

    private function createEnrollment(
        Member $member,
        ClassEnrollmentStatus $status = ClassEnrollmentStatus::Enrolled
    ): ClassEnrollment {
        return ClassEnrollment::create([
            'member_id' => $member->user_id,
            'class_session_id' => $this->session->id,
            'enrollment_date' => today(),
            'status' => $status,
        ]);
    }

    public function test_assigned_trainer_can_view_session(): void
    {
        $response = $this
            ->actingAs($this->trainerUser)
            ->get(
                route(
                    'trainer-classes.show',
                    $this->session
                )
            );

        $response
            ->assertOk()
            ->assertSee('Yoga Test');
    }

    public function test_other_trainer_cannot_manage_session(): void
    {
        $specialty = TrainerSpecialty::first();

        $otherUser = User::create([
            'role_id' => $this->trainerRole->id,
            'first_name' => 'Otro',
            'last_name' => 'Entrenador',
            'email' => 'otro@test.com',
            'password' => 'password',
            'phone_number' => null,
            'is_active' => true,
        ]);

        Trainer::create([
            'user_id' => $otherUser->id,
            'specialty_id' => $specialty->id,
        ]);

        $response = $this
            ->actingAs($otherUser)
            ->get(
                route(
                    'trainer-classes.show',
                    $this->session
                )
            );

        $response->assertForbidden();
    }

    public function test_assigned_trainer_can_start_session(): void
    {
        $response = $this
            ->actingAs($this->trainerUser)
            ->patch(
                route(
                    'trainer-classes.start',
                    $this->session
                )
            );

        $response->assertRedirect(
            route(
                'trainer-classes.show',
                $this->session
            )
        );

        $this->session->refresh();

        $this->assertEquals(
            ClassSessionStatus::InProgress,
            $this->session->status
        );
    }

    public function test_trainer_can_mark_attendance(): void
    {
        $member = $this->createMember();

        $enrollment = $this->createEnrollment(
            $member
        );

        $this->session->update([
            'status' => ClassSessionStatus::InProgress,
        ]);

        $response = $this
            ->actingAs($this->trainerUser)
            ->patch(
                route(
                    'trainer-classes.attendance',
                    $this->session
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
    }

    public function test_completing_session_marks_pending_members_as_no_show(): void
    {
        $member1 = $this->createMember();
        $member2 = $this->createMember();

        $attended = $this->createEnrollment(
            $member1,
            ClassEnrollmentStatus::Attended
        );

        $pending = $this->createEnrollment(
            $member2,
            ClassEnrollmentStatus::Enrolled
        );

        $this->session->update([
            'status' => ClassSessionStatus::InProgress,
        ]);

        $response = $this
            ->actingAs($this->trainerUser)
            ->patch(
                route(
                    'trainer-classes.complete',
                    $this->session
                )
            );

        $response->assertSessionHasNoErrors();

        $this->session->refresh();
        $attended->refresh();
        $pending->refresh();

        $this->assertEquals(
            ClassSessionStatus::Completed,
            $this->session->status
        );

        $this->assertEquals(
            ClassEnrollmentStatus::Attended,
            $attended->status
        );

        $this->assertEquals(
            ClassEnrollmentStatus::NoShow,
            $pending->status
        );
    }
}