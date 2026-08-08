<?php

namespace Tests\Feature;

use App\Models\Food;
use App\Models\FoodCategory;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FoodCatalogTest extends TestCase
{
    use RefreshDatabase;

    private Role $adminRole;
    private Role $receptionistRole;
    private FoodCategory $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminRole = Role::create([
            'name' => 'admin',
            'description' => 'Administrador',
        ]);

        $this->receptionistRole = Role::create([
            'name' => 'receptionist',
            'description' => 'Recepcionista',
        ]);

        $this->category = FoodCategory::create([
            'name' => 'Proteinas',
            'description' => 'Alimentos ricos en proteina',
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

    private function createFood(array $attributes = []): Food
    {
        return Food::create(array_merge([
            'name' => 'Pechuga de pollo',
            'category_id' => $this->category->id,
            'calories_per_serving' => 165,
            'protein_g' => 31,
            'carbs_g' => 0,
            'fat_g' => 3.6,
            'reference_serving_g' => 100,
            'is_active' => true,
        ], $attributes));
    }

    public function test_guest_cannot_access_food_catalog(): void
    {
        $response = $this->get(route('foods.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_receptionist_cannot_access_food_catalog(): void
    {
        $receptionist = $this->createUser($this->receptionistRole);

        $response = $this
            ->actingAs($receptionist)
            ->get(route('foods.index'));

        $response->assertForbidden();
    }

    public function test_receptionist_cannot_create_food(): void
    {
        $receptionist = $this->createUser($this->receptionistRole);

        $response = $this
            ->actingAs($receptionist)
            ->post(route('foods.store'), [
                'name' => 'Huevo cocido',
                'category_id' => $this->category->id,
                'calories_per_serving' => 155,
                'protein_g' => 13,
                'carbs_g' => 1.1,
                'fat_g' => 11,
                'reference_serving_g' => 100,
            ]);

        $response->assertForbidden();

        $this->assertDatabaseMissing('foods', [
            'name' => 'Huevo cocido',
        ]);
    }

    public function test_admin_can_access_food_catalog(): void
    {
        $admin = $this->createUser($this->adminRole);

        $this->createFood();

        $response = $this
            ->actingAs($admin)
            ->get(route('foods.index'));

        $response
            ->assertOk()
            ->assertSee('Pechuga de pollo')
            ->assertSee('Nuevo alimento');
    }

    public function test_admin_can_create_food(): void
    {
        $admin = $this->createUser($this->adminRole);

        $response = $this
            ->actingAs($admin)
            ->post(route('foods.store'), [
                'name' => 'Huevo cocido',
                'category_id' => $this->category->id,
                'calories_per_serving' => 155,
                'protein_g' => 13,
                'carbs_g' => 1.1,
                'fat_g' => 11,
                'reference_serving_g' => 100,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('foods.index'));

        $this->assertDatabaseHas('foods', [
            'name' => 'Huevo cocido',
            'category_id' => $this->category->id,
            'is_active' => 1,
        ]);
    }

    public function test_food_name_must_be_unique(): void
    {
        $admin = $this->createUser($this->adminRole);

        $this->createFood([
            'name' => 'Huevo cocido',
        ]);

        $response = $this
            ->actingAs($admin)
            ->post(route('foods.store'), [
                'name' => 'Huevo cocido',
                'category_id' => $this->category->id,
                'calories_per_serving' => 155,
                'protein_g' => 13,
                'carbs_g' => 1.1,
                'fat_g' => 11,
                'reference_serving_g' => 100,
            ]);

        $response->assertSessionHasErrors('name');

        $this->assertDatabaseCount('foods', 1);
    }

    public function test_admin_can_update_food(): void
    {
        $admin = $this->createUser($this->adminRole);
        $food = $this->createFood();

        $response = $this
            ->actingAs($admin)
            ->put(route('foods.update', $food), [
                'name' => 'Pechuga de pollo cocida',
                'category_id' => $this->category->id,
                'calories_per_serving' => 170,
                'protein_g' => 32,
                'carbs_g' => 0,
                'fat_g' => 4,
                'reference_serving_g' => 100,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('foods.index'));

        $this->assertDatabaseHas('foods', [
            'id' => $food->id,
            'name' => 'Pechuga de pollo cocida',
            'calories_per_serving' => 170,
        ]);
    }

    public function test_admin_can_deactivate_food(): void
    {
        $admin = $this->createUser($this->adminRole);
        $food = $this->createFood();

        $response = $this
            ->actingAs($admin)
            ->patch(route('foods.toggle-status', $food));

        $response->assertRedirect(route('foods.index'));

        $this->assertDatabaseHas('foods', [
            'id' => $food->id,
            'is_active' => 0,
        ]);
    }

    public function test_admin_can_activate_food_again(): void
    {
        $admin = $this->createUser($this->adminRole);

        $food = $this->createFood([
            'is_active' => false,
        ]);

        $response = $this
            ->actingAs($admin)
            ->patch(route('foods.toggle-status', $food));

        $response->assertRedirect(route('foods.index'));

        $this->assertDatabaseHas('foods', [
            'id' => $food->id,
            'is_active' => 1,
        ]);
    }
}