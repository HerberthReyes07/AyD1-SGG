<?php

namespace Tests\Feature;

use App\Enums\ClassEnrollmentStatus;
use App\Enums\ClassSessionStatus;
use App\Models\ClassEnrollment;
use App\Models\ClassRating;
use App\Models\ClassSession;
use App\Models\GroupClass;
use App\Models\Member;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClassRatingTest extends TestCase
{
    use RefreshDatabase;

    private Role $memberRole;

    private Member $member;
    private User $memberUser;

    private GroupClass $groupClass;
    private ClassSession $session;
    private ClassEnrollment $enrollment;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        $this->memberRole = Role::create([
            'name' => 'member',
            'description' => 'Socio',
        ]);

        $this->memberUser = User::create([
            'role_id' => $this->memberRole->id,
            'first_name' => 'Socio',
            'last_name' => 'Principal',
            'email' => 'socio@test.com',
            'password' => 'password',
            'phone_number' => null,
            'is_active' => true,
        ]);

        $this->member = Member::create([
            'user_id' => $this->memberUser->id,
            'birth_date' => '2000-01-01',
        ]);

        $this->groupClass = GroupClass::create([
            'name' => 'Yoga Rating Test',
            'description' => 'Clase para pruebas',
            'duration_minutes' => 60,
            'max_participants' => 10,
            'is_active' => true,
            'category_id' => null,
            'trainer_id' => null,
        ]);

        $this->session = ClassSession::create([
            'group_class_id' => $this->groupClass->id,
            'starts_at' => '2026-08-08 10:00:00',
            'status' => ClassSessionStatus::Completed,
            'change_reason' => null,
        ]);

        $this->enrollment = ClassEnrollment::create([
            'member_id' => $this->member->user_id,
            'class_session_id' => $this->session->id,
            'enrollment_date' => '2026-08-01',
            'status' => ClassEnrollmentStatus::Attended,
        ]);
    }

    private function createOtherMember(): array
    {
        $user = User::create([
            'role_id' => $this->memberRole->id,
            'first_name' => 'Otro',
            'last_name' => 'Socio',
            'email' => 'otro@test.com',
            'password' => 'password',
            'phone_number' => null,
            'is_active' => true,
        ]);

        $member = Member::create([
            'user_id' => $user->id,
            'birth_date' => '2001-01-01',
        ]);

        return [$user, $member];
    }

    public function test_member_who_attended_can_rate_completed_class(): void
    {
        $response = $this
            ->actingAs($this->memberUser)
            ->post(
                route(
                    'member-classes.rating.store',
                    $this->enrollment
                ),
                [
                    'rating' => 5,
                    'comment' => 'Excelente clase',
                ]
            );

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(
                route('member-classes.history')
            );

        $this->assertDatabaseHas('class_ratings', [
            'class_enrollment_id' => $this->enrollment->id,
            'rating' => 5,
            'comment' => 'Excelente clase',
        ]);
    }

    public function test_member_who_did_not_attend_cannot_rate(): void
    {
        $this->enrollment->update([
            'status' => ClassEnrollmentStatus::NoShow,
        ]);

        $response = $this
            ->actingAs($this->memberUser)
            ->post(
                route(
                    'member-classes.rating.store',
                    $this->enrollment
                ),
                [
                    'rating' => 5,
                    'comment' => 'Comentario',
                ]
            );

        $response->assertSessionHasErrors('rating');

        $this->assertDatabaseCount(
            'class_ratings',
            0
        );
    }

    public function test_rating_cannot_be_less_than_one(): void
    {
        $response = $this
            ->actingAs($this->memberUser)
            ->post(
                route(
                    'member-classes.rating.store',
                    $this->enrollment
                ),
                [
                    'rating' => 0,
                    'comment' => 'Comentario',
                ]
            );

        $response->assertSessionHasErrors('rating');

        $this->assertDatabaseCount(
            'class_ratings',
            0
        );
    }

    public function test_rating_cannot_be_greater_than_five(): void
    {
        $response = $this
            ->actingAs($this->memberUser)
            ->post(
                route(
                    'member-classes.rating.store',
                    $this->enrollment
                ),
                [
                    'rating' => 6,
                    'comment' => 'Comentario',
                ]
            );

        $response->assertSessionHasErrors('rating');

        $this->assertDatabaseCount(
            'class_ratings',
            0
        );
    }

    public function test_member_cannot_rate_another_members_enrollment(): void
    {
        [$otherUser, $otherMember] =
            $this->createOtherMember();

        $response = $this
            ->actingAs($otherUser)
            ->post(
                route(
                    'member-classes.rating.store',
                    $this->enrollment
                ),
                [
                    'rating' => 5,
                    'comment' => 'Intento invalido',
                ]
            );

        $response->assertForbidden();

        $this->assertDatabaseCount(
            'class_ratings',
            0
        );
    }

    public function test_same_class_cannot_be_rated_twice(): void
    {
        ClassRating::create([
            'class_enrollment_id' => $this->enrollment->id,
            'rating' => 4,
            'comment' => 'Primera calificacion',
        ]);

        $response = $this
            ->actingAs($this->memberUser)
            ->post(
                route(
                    'member-classes.rating.store',
                    $this->enrollment
                ),
                [
                    'rating' => 5,
                    'comment' => 'Segunda calificacion',
                ]
            );

        $response->assertSessionHasErrors('rating');

        $this->assertEquals(
            1,
            ClassRating::where(
                'class_enrollment_id',
                $this->enrollment->id
            )->count()
        );
    }

    public function test_class_must_be_completed_before_rating(): void
    {
        $this->session->update([
            'status' => ClassSessionStatus::InProgress,
        ]);

        $response = $this
            ->actingAs($this->memberUser)
            ->post(
                route(
                    'member-classes.rating.store',
                    $this->enrollment
                ),
                [
                    'rating' => 5,
                    'comment' => 'Comentario',
                ]
            );

        $response->assertSessionHasErrors('rating');

        $this->assertDatabaseCount(
            'class_ratings',
            0
        );
    }
}