<?php

namespace Tests\Feature;

use App\Models\Food;
use App\Models\FoodCategory;
use App\Models\Meal;
use App\Models\Member;
use App\Models\Role;
use App\Models\User;
use App\Services\MealService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MealTrackingTest extends TestCase
{
    use RefreshDatabase;

    private Role $memberRole;

    private FoodCategory $category;

    private MealService $mealService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        $this->mealService = app(MealService::class);

        $this->memberRole = Role::create([
            'name' => 'member',
            'description' => 'Socio',
        ]);

        $this->category = FoodCategory::create([
            'name' => 'Proteinas',
            'description' => 'Alimentos ricos en proteina',
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

    public function test_member_can_register_meal_with_proportional_nutrition(): void
    {
        $member = $this->createMember();
        $food = $this->createFood();

        $response = $this
            ->actingAs($member->user)
            ->post(route('member-meals.store'), [
                'date' => today()->toDateString(),
                'type' => 'breakfast',
                'foods' => [
                    ['food_id' => $food->id, 'quantity_g' => 150],
                ],
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('member-meals.index', ['date' => today()->toDateString()]));

        $this->assertDatabaseHas('meals', [
            'member_id' => $member->user_id,
            'type' => 'breakfast',
        ]);

        $meal = Meal::first();

        $this->assertDatabaseHas('meal_foods', [
            'meal_id' => $meal->id,
            'food_id' => $food->id,
            'quantity_g' => 150,
        ]);

        $summary = $this->mealService->getDailySummary($member, today());

        $this->assertEquals(247.50, $summary['totals']['calories']);
        $this->assertEquals(46.50, $summary['totals']['protein_g']);
        $this->assertEquals(0.0, $summary['totals']['carbs_g']);
        $this->assertEquals(5.40, $summary['totals']['fat_g']);
        $this->assertEquals(247.50, $summary['by_type']['breakfast']['calories']);
    }

    public function test_cannot_register_the_same_food_twice_in_one_meal(): void
    {
        $member = $this->createMember();
        $food = $this->createFood();

        $response = $this
            ->actingAs($member->user)
            ->post(route('member-meals.store'), [
                'date' => today()->toDateString(),
                'type' => 'lunch',
                'foods' => [
                    ['food_id' => $food->id, 'quantity_g' => 100],
                    ['food_id' => $food->id, 'quantity_g' => 50],
                ],
            ]);

        $response->assertSessionHasErrors('foods');

        $this->assertDatabaseCount('meals', 0);
    }

    public function test_inactive_food_cannot_be_registered(): void
    {
        $member = $this->createMember();

        $inactiveFood = $this->createFood([
            'name' => 'Alimento descontinuado',
            'is_active' => false,
        ]);

        $response = $this
            ->actingAs($member->user)
            ->post(route('member-meals.store'), [
                'date' => today()->toDateString(),
                'type' => 'snack',
                'foods' => [
                    ['food_id' => $inactiveFood->id, 'quantity_g' => 50],
                ],
            ]);

        $response->assertSessionHasErrors('foods');

        $this->assertDatabaseCount('meals', 0);
    }

    public function test_daily_summary_breaks_down_totals_by_meal_type(): void
    {
        $member = $this->createMember();

        $chicken = $this->createFood();

        $rice = $this->createFood([
            'name' => 'Arroz blanco cocido',
            'calories_per_serving' => 130,
            'protein_g' => 2.7,
            'carbs_g' => 28,
            'fat_g' => 0.3,
            'reference_serving_g' => 100,
        ]);

        $this
            ->actingAs($member->user)
            ->post(route('member-meals.store'), [
                'date' => today()->toDateString(),
                'type' => 'breakfast',
                'foods' => [
                    ['food_id' => $chicken->id, 'quantity_g' => 100],
                ],
            ]);

        $this
            ->actingAs($member->user)
            ->post(route('member-meals.store'), [
                'date' => today()->toDateString(),
                'type' => 'lunch',
                'foods' => [
                    ['food_id' => $rice->id, 'quantity_g' => 200],
                ],
            ]);

        $summary = $this->mealService->getDailySummary($member, today());

        $this->assertEquals(165.0, $summary['by_type']['breakfast']['calories']);
        $this->assertEquals(260.0, $summary['by_type']['lunch']['calories']);
        $this->assertEquals(0.0, $summary['by_type']['dinner']['calories']);
        $this->assertEquals(425.0, $summary['totals']['calories']);
    }

    public function test_member_cannot_edit_or_delete_another_members_meal(): void
    {
        $owner = $this->createMember();
        $otherMember = $this->createMember();
        $food = $this->createFood();

        $meal = $this->mealService->registerMeal($owner, [
            'date' => today()->toDateString(),
            'type' => 'dinner',
            'foods' => [
                ['food_id' => $food->id, 'quantity_g' => 100],
            ],
        ]);

        $this
            ->actingAs($otherMember->user)
            ->get(route('member-meals.edit', $meal))
            ->assertForbidden();

        $this
            ->actingAs($otherMember->user)
            ->put(route('member-meals.update', $meal), [
                'date' => today()->toDateString(),
                'type' => 'dinner',
                'foods' => [
                    ['food_id' => $food->id, 'quantity_g' => 50],
                ],
            ])
            ->assertForbidden();

        $this
            ->actingAs($otherMember->user)
            ->delete(route('member-meals.destroy', $meal))
            ->assertForbidden();

        $this->assertDatabaseHas('meals', ['id' => $meal->id]);
    }

    public function test_member_can_update_and_delete_own_meal(): void
    {
        $member = $this->createMember();
        $food = $this->createFood();
        $otherFood = $this->createFood([
            'name' => 'Arroz blanco cocido',
            'calories_per_serving' => 130,
            'protein_g' => 2.7,
            'carbs_g' => 28,
            'fat_g' => 0.3,
            'reference_serving_g' => 100,
        ]);

        $meal = $this->mealService->registerMeal($member, [
            'date' => today()->toDateString(),
            'type' => 'snack',
            'foods' => [
                ['food_id' => $food->id, 'quantity_g' => 100],
            ],
        ]);

        $updateResponse = $this
            ->actingAs($member->user)
            ->put(route('member-meals.update', $meal), [
                'date' => today()->toDateString(),
                'type' => 'snack',
                'foods' => [
                    ['food_id' => $otherFood->id, 'quantity_g' => 200],
                ],
            ]);

        $updateResponse->assertSessionHasNoErrors();

        $this->assertDatabaseHas('meal_foods', [
            'meal_id' => $meal->id,
            'food_id' => $otherFood->id,
            'quantity_g' => 200,
        ]);

        $this->assertDatabaseMissing('meal_foods', [
            'meal_id' => $meal->id,
            'food_id' => $food->id,
        ]);

        $deleteResponse = $this
            ->actingAs($member->user)
            ->delete(route('member-meals.destroy', $meal));

        $deleteResponse->assertRedirect();

        $this->assertDatabaseMissing('meals', ['id' => $meal->id]);
        $this->assertDatabaseMissing('meal_foods', ['meal_id' => $meal->id]);
    }
}
