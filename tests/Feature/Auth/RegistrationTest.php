<?php

namespace Tests\Feature\Auth;

use App\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::create([
            'name' => 'member',
            'description' => 'Socio',
        ]);
    }

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register_and_continue_to_two_factor(): void
    {
        $response = $this->post('/register', [
            'first_name' => 'Test',
            'last_name' => 'User',
            'phone_number' => '55555555',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertGuest();

        $response->assertRedirect(
            route('two-factor.challenge')
        );

        $this->assertDatabaseHas('users', [
            'first_name' => 'Test',
            'last_name' => 'User',
            'phone_number' => '55555555',
            'email' => 'test@example.com',
            'is_active' => true,
        ]);

        $userId = session('two_factor.user_id');

        $this->assertNotNull($userId);

        $this->assertDatabaseHas('users', [
            'id' => $userId,
            'email' => 'test@example.com',
        ]);
    }
}