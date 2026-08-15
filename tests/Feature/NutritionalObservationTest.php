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
use App\Services\MealService;
use App\Services\NutritionalObservationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NutritionalObservationTest extends TestCase
{
    use RefreshDatabase;

    private Role $memberRole;

    private Role $trainerRole;

    private NutritionalObservationService $observationService;

    private MealService $mealService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        $this->observationService = app(NutritionalObservationService::class);
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

    public function test_trainer_with_active_assignment_can_add_observation(): void
    {
        $member = $this->createMember();
        $trainer = $this->createTrainer();
        $assignment = $this->createAssignment($member, $trainer);

        $response = $this
            ->actingAs($trainer->user)
            ->post(route('assignments.nutritional-observations.store', $assignment), [
                'date' => today()->toDateString(),
                'observation' => 'Reduce el consumo de sodio esta semana.',
            ]);

        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('nutritional_observations', [
            'trainer_assignment_id' => $assignment->id,
            'observation' => 'Reduce el consumo de sodio esta semana.',
        ]);
    }

    public function test_trainer_cannot_add_observation_for_an_assignment_that_is_not_theirs(): void
    {
        $member = $this->createMember();
        $ownerTrainer = $this->createTrainer();
        $otherTrainer = $this->createTrainer();
        $assignment = $this->createAssignment($member, $ownerTrainer);

        $response = $this
            ->actingAs($otherTrainer->user)
            ->post(route('assignments.nutritional-observations.store', $assignment), [
                'date' => today()->toDateString(),
                'observation' => 'No deberia poder ver esto.',
            ]);

        $response->assertForbidden();

        $this->assertDatabaseCount('nutritional_observations', 0);
    }

    public function test_trainer_without_active_assignment_cannot_view_members_page(): void
    {
        $member = $this->createMember();
        $ownerTrainer = $this->createTrainer();
        $otherTrainer = $this->createTrainer();
        $assignment = $this->createAssignment($member, $ownerTrainer);

        $response = $this
            ->actingAs($otherTrainer->user)
            ->get(route('assignments.show', $assignment));

        $response->assertRedirect(route('assignments.index'));
        $response->assertSessionHas('error');
    }

    public function test_todays_meal_log_is_visible_to_the_assigned_trainer(): void
    {
        $member = $this->createMember();
        $trainer = $this->createTrainer();
        $assignment = $this->createAssignment($member, $trainer);
        $food = $this->createFood();

        $this->mealService->registerMeal($member, [
            'date' => today()->toDateString(),
            'type' => 'breakfast',
            'foods' => [
                ['food_id' => $food->id, 'quantity_g' => 100],
            ],
        ]);

        $response = $this
            ->actingAs($trainer->user)
            ->get(route('assignments.show', $assignment));

        $response
            ->assertOk()
            ->assertSee('Pechuga de pollo');
    }

    public function test_member_sees_trainer_observations_on_nutrition_history(): void
    {
        $member = $this->createMember();
        $trainer = $this->createTrainer();
        $assignment = $this->createAssignment($member, $trainer);

        $this->observationService->register($assignment->id, [
            'date' => today()->toDateString(),
            'observation' => 'Buen progreso esta semana.',
        ]);

        $response = $this
            ->actingAs($member->user)
            ->get(route('nutrition-history.index'));

        $response
            ->assertOk()
            ->assertSee('Buen progreso esta semana.');
    }

    public function test_member_without_trainer_sees_empty_state(): void
    {
        $member = $this->createMember();

        $response = $this
            ->actingAs($member->user)
            ->get(route('nutrition-history.index'));

        $response
            ->assertOk()
            ->assertSee('No tienes un entrenador asignado.');
    }
}
