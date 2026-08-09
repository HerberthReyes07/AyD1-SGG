<?php

namespace Tests\Feature;

use App\Enums\ClassSessionStatus;
use App\Models\ClassCategory;
use App\Models\ClassSession;
use App\Models\GroupClass;
use App\Models\GroupClassSchedule;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GroupClassManagementTest extends TestCase
{
    use RefreshDatabase;

    private Role $adminRole;
    private Role $receptionistRole;

    private User $admin;
    private User $receptionist;

    private ClassCategory $category;
    private GroupClass $groupClass;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        Carbon::setTestNow(
            Carbon::parse('2026-08-08 10:00:00')
        );

        $this->adminRole = Role::create([
            'name' => 'admin',
            'description' => 'Administrador',
        ]);

        $this->receptionistRole = Role::create([
            'name' => 'receptionist',
            'description' => 'Recepcionista',
        ]);

        $this->admin = User::create([
            'role_id' => $this->adminRole->id,
            'first_name' => 'Admin',
            'last_name' => 'Test',
            'email' => 'admin@test.com',
            'password' => 'password',
            'phone_number' => null,
            'is_active' => true,
        ]);

        $this->receptionist = User::create([
            'role_id' => $this->receptionistRole->id,
            'first_name' => 'Recepcionista',
            'last_name' => 'Test',
            'email' => 'recepcion@test.com',
            'password' => 'password',
            'phone_number' => null,
            'is_active' => true,
        ]);

        $this->category = ClassCategory::create([
            'name' => 'Yoga',
            'description' => 'Categoria de prueba',
        ]);

        $this->groupClass = GroupClass::create([
            'name' => 'Yoga Inicial',
            'description' => 'Clase inicial',
            'duration_minutes' => 60,
            'max_participants' => 15,
            'is_active' => true,
            'category_id' => $this->category->id,
            'trainer_id' => null,
        ]);
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_admin_can_access_group_class_management(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->get(route('group-classes.index'));

        $response
            ->assertOk()
            ->assertSee('Clases grupales')
            ->assertSee('Yoga Inicial');
    }

    public function test_receptionist_cannot_manage_group_class_templates(): void
    {
        $response = $this
            ->actingAs($this->receptionist)
            ->get(route('group-classes.index'));

        $response->assertForbidden();
    }

    public function test_admin_can_create_group_class(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->post(route('group-classes.store'), [
                'name' => 'Yoga Avanzado',
                'description' => 'Clase avanzada',
                'duration_minutes' => 45,
                'max_participants' => 20,
                'category_id' => $this->category->id,
                'trainer_id' => null,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(
                route('group-classes.index')
            );

        $this->assertDatabaseHas('group_classes', [
            'name' => 'Yoga Avanzado',
            'duration_minutes' => 45,
            'max_participants' => 20,
            'category_id' => $this->category->id,
            'is_active' => true,
        ]);
    }

    public function test_admin_can_update_group_class(): void
    {
        $response = $this
            ->actingAs($this->admin)
            ->put(
                route(
                    'group-classes.update',
                    $this->groupClass
                ),
                [
                    'name' => 'Yoga Inicial Editado',
                    'description' => 'Descripcion actualizada',
                    'duration_minutes' => 50,
                    'max_participants' => 12,
                    'category_id' => $this->category->id,
                    'trainer_id' => null,
                ]
            );

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(
                route('group-classes.index')
            );

        $this->assertDatabaseHas('group_classes', [
            'id' => $this->groupClass->id,
            'name' => 'Yoga Inicial Editado',
            'duration_minutes' => 50,
            'max_participants' => 12,
        ]);
    }

    public function test_admin_can_deactivate_and_activate_group_class(): void
    {
        $this->actingAs($this->admin)
            ->patch(
                route(
                    'group-classes.toggle-status',
                    $this->groupClass
                )
            )
            ->assertSessionHasNoErrors();

        $this->groupClass->refresh();

        $this->assertFalse(
            $this->groupClass->is_active
        );

        $this->actingAs($this->admin)
            ->patch(
                route(
                    'group-classes.toggle-status',
                    $this->groupClass
                )
            )
            ->assertSessionHasNoErrors();

        $this->groupClass->refresh();

        $this->assertTrue(
            $this->groupClass->is_active
        );
    }

    public function test_duplicate_schedule_is_rejected(): void
    {
        $response1 = $this
            ->actingAs($this->admin)
            ->post(
                route(
                    'group-class-schedules.store',
                    $this->groupClass
                ),
                [
                    'weekday' => 'monday',
                    'start_time' => '15:30',
                ]
            );

        $response1->assertSessionHasNoErrors();

        $response2 = $this
            ->actingAs($this->admin)
            ->post(
                route(
                    'group-class-schedules.store',
                    $this->groupClass
                ),
                [
                    'weekday' => 'monday',
                    'start_time' => '15:30',
                ]
            );

        $response2->assertSessionHasErrors(
            'start_time'
        );

        $this->assertEquals(
            1,
            GroupClassSchedule::where(
                'group_class_id',
                $this->groupClass->id
            )->count()
        );
    }

    public function test_duplicate_session_is_rejected(): void
    {
        $startsAt = '2026-08-20T15:30';

        $response1 = $this
            ->actingAs($this->admin)
            ->post(
                route(
                    'class-sessions.store',
                    $this->groupClass
                ),
                [
                    'starts_at' => $startsAt,
                ]
            );

        $response1->assertSessionHasNoErrors();

        $response2 = $this
            ->actingAs($this->admin)
            ->post(
                route(
                    'class-sessions.store',
                    $this->groupClass
                ),
                [
                    'starts_at' => $startsAt,
                ]
            );

        $response2->assertSessionHasErrors(
            'starts_at'
        );

        $this->assertEquals(
            1,
            ClassSession::where(
                'group_class_id',
                $this->groupClass->id
            )->count()
        );
    }

    public function test_admin_can_reschedule_session_with_reason(): void
    {
        $session = ClassSession::create([
            'group_class_id' => $this->groupClass->id,
            'starts_at' => '2026-08-20 15:30:00',
            'status' => ClassSessionStatus::Scheduled,
            'change_reason' => null,
        ]);

        $response = $this
            ->actingAs($this->admin)
            ->patch(
                route(
                    'class-sessions.reschedule',
                    [$this->groupClass, $session]
                ),
                [
                    'starts_at' => '2026-08-21T16:00',
                    'change_reason' =>
                        'Cambio de disponibilidad del entrenador',
                ]
            );

        $response->assertSessionHasNoErrors();

        $session->refresh();

        $this->assertEquals(
            ClassSessionStatus::Rescheduled,
            $session->status
        );

        $this->assertEquals(
            '2026-08-21 16:00',
            $session->starts_at->format(
                'Y-m-d H:i'
            )
        );

        $this->assertEquals(
            'Cambio de disponibilidad del entrenador',
            $session->change_reason
        );
    }

    public function test_admin_can_cancel_session_with_reason(): void
    {
        $session = ClassSession::create([
            'group_class_id' => $this->groupClass->id,
            'starts_at' => '2026-08-20 15:30:00',
            'status' => ClassSessionStatus::Scheduled,
            'change_reason' => null,
        ]);

        $response = $this
            ->actingAs($this->admin)
            ->patch(
                route(
                    'class-sessions.cancel',
                    [$this->groupClass, $session]
                ),
                [
                    'change_reason' =>
                        'Entrenador no disponible',
                ]
            );

        $response->assertSessionHasNoErrors();

        $session->refresh();

        $this->assertEquals(
            ClassSessionStatus::Cancelled,
            $session->status
        );

        $this->assertEquals(
            'Entrenador no disponible',
            $session->change_reason
        );
    }

    public function test_inactive_group_class_cannot_schedule_new_session(): void
    {
        $this->groupClass->update([
            'is_active' => false,
        ]);

        $response = $this
            ->actingAs($this->admin)
            ->post(
                route(
                    'class-sessions.store',
                    $this->groupClass
                ),
                [
                    'starts_at' =>
                        '2026-08-20T15:30',
                ]
            );

        $response->assertSessionHasErrors(
            'starts_at'
        );

        $this->assertDatabaseCount(
            'class_sessions',
            0
        );
    }
}