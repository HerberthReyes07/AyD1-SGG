<?php

namespace Tests\Feature;

use App\Models\GuestPass;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GuestPassTest extends TestCase
{
    use RefreshDatabase;

    private Role $adminRole;
    private Role $receptionistRole;
    private Role $trainerRole;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        $this->adminRole = Role::create([
            'name' => 'admin',
            'description' => 'Administrador',
        ]);

        $this->receptionistRole = Role::create([
            'name' => 'receptionist',
            'description' => 'Recepcionista',
        ]);

        $this->trainerRole = Role::create([
            'name' => 'trainer',
            'description' => 'Entrenador',
        ]);
    }

    private function createUser(Role $role): User
    {
        return User::create([
            'role_id' => $role->id,
            'first_name' => 'Usuario',
            'last_name' => 'Prueba',
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
            'phone_number' => null,
            'is_active' => true,
        ]);
    }

    private function createGuestPass(
        User $registeredBy,
        array $attributes = []
    ): GuestPass {
        return GuestPass::create(array_merge([
            'guest_name' => 'Luis Fernando Lopez',
            'dpi' => '1234567890123',
            'visit_date' => '2026-08-08',
            'registered_by' => $registeredBy->id,
        ], $attributes));
    }

    public function test_guest_cannot_access_guest_passes(): void
    {
        $response = $this->get(route('guest-passes.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_receptionist_can_access_guest_passes(): void
    {
        $receptionist = $this->createUser($this->receptionistRole);

        $response = $this
            ->actingAs($receptionist)
            ->get(route('guest-passes.index'));

        $response
            ->assertOk()
            ->assertSee('Pases de invitado')
            ->assertSee('Nuevo pase');
    }

    public function test_admin_can_access_guest_passes(): void
    {
        $admin = $this->createUser($this->adminRole);

        $response = $this
            ->actingAs($admin)
            ->get(route('guest-passes.index'));

        $response->assertOk();
    }

    public function test_trainer_cannot_access_guest_passes(): void
    {
        $trainer = $this->createUser($this->trainerRole);

        $response = $this
            ->actingAs($trainer)
            ->get(route('guest-passes.index'));

        $response->assertForbidden();
    }

    public function test_receptionist_can_register_guest_pass(): void
    {
        $receptionist = $this->createUser($this->receptionistRole);

        $response = $this
            ->actingAs($receptionist)
            ->post(route('guest-passes.store'), [
                'guest_name' => 'Carlos Lopez',
                'dpi' => '1234567890123',
                'visit_date' => '2026-08-08',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('guest-passes.index'));

        $this->assertDatabaseHas('guest_passes', [
            'guest_name' => 'Carlos Lopez',
            'dpi' => '1234567890123',
            'registered_by' => $receptionist->id,
        ]);

        $guestPass = GuestPass::where('dpi', '1234567890123')->first();

        $this->assertNotNull($guestPass);
        $this->assertEquals(
            '2026-08-08',
            $guestPass->visit_date->format('Y-m-d')
        );
    }

    public function test_admin_can_register_guest_pass(): void
    {
        $admin = $this->createUser($this->adminRole);

        $response = $this
            ->actingAs($admin)
            ->post(route('guest-passes.store'), [
                'guest_name' => 'Ana Perez',
                'dpi' => '9876543210123',
                'visit_date' => '2026-08-08',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('guest-passes.index'));

        $this->assertDatabaseHas('guest_passes', [
            'dpi' => '9876543210123',
            'registered_by' => $admin->id,
        ]);
    }

    public function test_same_dpi_cannot_receive_two_guest_passes(): void
    {
        $receptionist = $this->createUser($this->receptionistRole);

        $this->createGuestPass($receptionist);

        $response = $this
            ->actingAs($receptionist)
            ->post(route('guest-passes.store'), [
                'guest_name' => 'Luis Fernando Lopez',
                'dpi' => '1234567890123',
                'visit_date' => '2026-08-09',
            ]);

        $response->assertSessionHasErrors('dpi');

        $this->assertDatabaseCount('guest_passes', 1);
    }

    public function test_dpi_must_have_exactly_13_digits(): void
    {
        $receptionist = $this->createUser($this->receptionistRole);

        $response = $this
            ->actingAs($receptionist)
            ->post(route('guest-passes.store'), [
                'guest_name' => 'Luis Lopez',
                'dpi' => '12345',
                'visit_date' => '2026-08-08',
            ]);

        $response->assertSessionHasErrors('dpi');

        $this->assertDatabaseCount('guest_passes', 0);
    }

    public function test_guest_pass_can_be_searched_by_name(): void
    {
        $receptionist = $this->createUser($this->receptionistRole);

        $this->createGuestPass($receptionist);

        $response = $this
            ->actingAs($receptionist)
            ->get(route('guest-passes.index', [
                'search' => 'Luis',
            ]));

        $response
            ->assertOk()
            ->assertSee('Luis Fernando Lopez');
    }

    public function test_guest_pass_can_be_filtered_by_exact_visit_date(): void
    {
        $receptionist = $this->createUser($this->receptionistRole);

        $this->createGuestPass($receptionist, [
            'guest_name' => 'Luis Fernando Lopez',
            'dpi' => '1234567890123',
            'visit_date' => '2026-08-08',
        ]);

        $this->createGuestPass($receptionist, [
            'guest_name' => 'Maria Lopez',
            'dpi' => '9999999999999',
            'visit_date' => '2026-08-10',
        ]);

        $response = $this
            ->actingAs($receptionist)
            ->get(route('guest-passes.index', [
                'date_from' => '2026-08-08',
                'date_to' => '2026-08-08',
            ]));

        $response
            ->assertOk()
            ->assertSee('Luis Fernando Lopez')
            ->assertDontSee('Maria Lopez');
    }

    public function test_guest_passes_can_be_filtered_by_date_range(): void
    {
        $receptionist = $this->createUser($this->receptionistRole);

        $this->createGuestPass($receptionist, [
            'guest_name' => 'Luis Lopez',
            'dpi' => '1111111111111',
            'visit_date' => '2026-08-01',
        ]);

        $this->createGuestPass($receptionist, [
            'guest_name' => 'Carlos Perez',
            'dpi' => '2222222222222',
            'visit_date' => '2026-08-05',
        ]);

        $this->createGuestPass($receptionist, [
            'guest_name' => 'Ana Ramirez',
            'dpi' => '3333333333333',
            'visit_date' => '2026-08-10',
        ]);

        $response = $this
            ->actingAs($receptionist)
            ->get(route('guest-passes.index', [
                'date_from' => '2026-08-01',
                'date_to' => '2026-08-05',
            ]));

        $response
            ->assertOk()
            ->assertSee('Luis Lopez')
            ->assertSee('Carlos Perez')
            ->assertDontSee('Ana Ramirez')
            ->assertSee('2');
    }

public function test_end_date_cannot_be_before_start_date(): void
{
    $receptionist = $this->createUser($this->receptionistRole);

    $response = $this
        ->actingAs($receptionist)
        ->get(route('guest-passes.index', [
            'date_from' => '2026-08-10',
            'date_to' => '2026-08-01',
        ]));

    $response->assertSessionHasErrors('date_to');
}
}