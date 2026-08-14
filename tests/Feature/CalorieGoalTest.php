<?php

namespace Tests\Feature;

use App\Models\Food;
use App\Models\FoodCategory;
use App\Models\Member;
use App\Models\Role;
use App\Models\Trainer;
use App\Models\TrainerAssignment;
use App\Models\TrainerSpecialty;
use App\Models\User;
use App\Services\CalorieGoalService;
use App\Services\MealService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CalorieGoalTest extends TestCase
{
    use RefreshDatabase;

    private Role $memberRole;

    private Role $trainerRole;

    private CalorieGoalService $calorieGoalService;

    private MealService $mealService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        $this->calorieGoalService = app(CalorieGoalService::class);
        $this->mealService = app(MealService::class);

        $this->memberRole = Role::create([
            'name' => 'member',
            'description' => 'Socio',
        ]);

        $this->trainerRole = Role::create([
            'name' => 'trainer',
            'description' => 'Entrenador',
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

    private function createTrainer(): Trainer
    {
        $user = User::create([
            'role_id' => $this->trainerRole->id,
            'first_name' => 'Entrenador',
            'last_name' => 'Prueba',
            'email' => fake()->unique()->safeEmail(),
            'password' => 'password',
            'phone_number' => null,
            'is_active' => true,
        ]);

        $specialty = TrainerSpecialty::create([
            'name' => 'General '.fake()->unique()->word(),
            'description' => null,
            'is_active' => true,
        ]);

        return Trainer::create([
            'user_id' => $user->id,
            'specialty_id' => $specialty->id,
        ]);
    }

    private function createAssignment(Member $member, Trainer $trainer, ?string $endDate = null): TrainerAssignment
    {
        return TrainerAssignment::create([
            'assignment_date' => today(),
            'end_date' => $endDate,
            'goal' => null,
            'reassignment_reason' => null,
            'member_id' => $member->user_id,
            'trainer_id' => $trainer->user_id,
            'assigned_by' => $trainer->user_id,
        ]);
    }

    private function createFood(array $attributes = []): Food
    {
        $category = FoodCategory::create([
            'name' => 'Proteinas '.fake()->unique()->word(),
            'description' => null,
        ]);

        return Food::create(array_merge([
            'name' => 'Pechuga de pollo',
            'category_id' => $category->id,
            'calories_per_serving' => 165,
            'protein_g' => 31,
            'carbs_g' => 0,
            'fat_g' => 3.6,
            'reference_serving_g' => 100,
            'is_active' => true,
        ], $attributes));
    }

    public function test_member_can_set_calorie_goal_with_objective(): void
    {
        $member = $this->createMember();

        $response = $this
            ->actingAs($member->user)
            ->put(route('calorie-goals.update'), [
                'daily_calories' => 2200,
                'objective' => 'maintenance',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('calorie-goals.edit'));

        $this->assertDatabaseHas('calorie_goals', [
            'member_id' => $member->user_id,
            'daily_calories' => 2200.00,
            'objective' => 'maintenance',
            'defined_by' => null,
        ]);
    }

    public function test_setting_a_new_goal_closes_the_previous_one(): void
    {
        $member = $this->createMember();

        $first = $this->calorieGoalService->setGoal($member, [
            'daily_calories' => 1800,
            'objective' => 'lose_weight',
        ]);

        $second = $this->calorieGoalService->setGoal($member, [
            'daily_calories' => 2500,
            'objective' => 'gain_muscle',
        ]);

        $first->refresh();

        $this->assertEquals(today()->subDay()->toDateString(), $first->end_date->toDateString());
        $this->assertNull($second->end_date);

        $current = $this->calorieGoalService->getCurrentGoal($member);

        $this->assertEquals($second->id, $current->id);
    }

    public function test_compare_classifies_consumption_against_the_goal(): void
    {
        $member = $this->createMember();

        $goal = $this->calorieGoalService->setGoal($member, [
            'daily_calories' => 2000,
            'objective' => 'maintenance',
        ]);

        $this->assertEquals('below', $this->calorieGoalService->compare(1700, $goal)['status']);
        $this->assertEquals('within', $this->calorieGoalService->compare(1800, $goal)['status']);
        $this->assertEquals('within', $this->calorieGoalService->compare(2000, $goal)['status']);
        $this->assertEquals('within', $this->calorieGoalService->compare(2200, $goal)['status']);
        $this->assertEquals('above', $this->calorieGoalService->compare(2300, $goal)['status']);
    }

    public function test_history_returns_daily_totals_including_days_without_meals(): void
    {
        $member = $this->createMember();
        $food = $this->createFood();

        $this->mealService->registerMeal($member, [
            'date' => today()->toDateString(),
            'type' => 'breakfast',
            'foods' => [
                ['food_id' => $food->id, 'quantity_g' => 100],
            ],
        ]);

        $this->mealService->registerMeal($member, [
            'date' => today()->subDays(2)->toDateString(),
            'type' => 'lunch',
            'foods' => [
                ['food_id' => $food->id, 'quantity_g' => 200],
            ],
        ]);

        $history = $this->mealService->getHistory($member, today()->subDays(2), today());

        $this->assertCount(3, $history);

        $this->assertEquals(today()->toDateString(), $history[0]['date']->toDateString());
        $this->assertEquals(165.0, $history[0]['calories']);

        $this->assertEquals(0.0, $history[1]['calories']);

        $this->assertEquals(today()->subDays(2)->toDateString(), $history[2]['date']->toDateString());
        $this->assertEquals(330.0, $history[2]['calories']);
    }

    public function test_trainer_with_active_assignment_can_adjust_member_goal(): void
    {
        $member = $this->createMember();
        $trainer = $this->createTrainer();
        $assignment = $this->createAssignment($member, $trainer);

        $response = $this
            ->actingAs($trainer->user)
            ->post(route('assignments.calorie-goal.store', $assignment), [
                'daily_calories' => 2100,
                'objective' => 'gain_muscle',
            ]);

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('calorie_goals', [
            'member_id' => $member->user_id,
            'daily_calories' => 2100.00,
            'objective' => 'gain_muscle',
            'defined_by' => $trainer->user_id,
        ]);
    }

    public function test_trainer_cannot_adjust_goal_for_an_assignment_that_is_not_theirs(): void
    {
        $member = $this->createMember();
        $ownerTrainer = $this->createTrainer();
        $otherTrainer = $this->createTrainer();
        $assignment = $this->createAssignment($member, $ownerTrainer);

        $response = $this
            ->actingAs($otherTrainer->user)
            ->post(route('assignments.calorie-goal.store', $assignment), [
                'daily_calories' => 2100,
                'objective' => 'gain_muscle',
            ]);

        $response->assertForbidden();

        $this->assertDatabaseCount('calorie_goals', 0);
    }
}
