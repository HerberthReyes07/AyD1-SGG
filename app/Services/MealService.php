<?php

namespace App\Services;

use App\Enums\MealType;
use App\Models\Food;
use App\Models\Meal;
use App\Models\Member;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class MealService
{
    public function registerMeal(Member $member, array $data): Meal
    {
        return DB::transaction(function () use ($member, $data) {
            $meal = Meal::create([
                'date' => $data['date'],
                'type' => $data['type'],
                'member_id' => $member->user_id,
            ]);

            $this->syncFoods($meal, $data['foods']);

            return $meal;
        });
    }

    public function updateMeal(Meal $meal, array $data): Meal
    {
        return DB::transaction(function () use ($meal, $data) {
            $meal->update([
                'date' => $data['date'],
                'type' => $data['type'],
            ]);

            // se borra y se vuelve a crear en vez de hacer diff, mas simple
            $meal->mealFoods()->delete();

            $this->syncFoods($meal, $data['foods']);

            return $meal;
        });
    }

    public function deleteMeal(Meal $meal): void
    {
        $meal->delete();
    }

    public function getDailySummary(Member $member, Carbon $date): array
    {
        $meals = $member->meals()
            ->whereDate('date', $date)
            ->with('mealFoods.food')
            ->get();

        $totals = $this->emptyTotals();

        $byType = [];

        foreach (MealType::cases() as $type) {
            $byType[$type->value] = [
                'label' => $type->label(),
                ...$this->emptyTotals(),
            ];
        }

        foreach ($meals as $meal) {
            foreach ($meal->mealFoods as $mealFood) {
                $nutrition = $this->nutritionFor(
                    $mealFood->food,
                    (float) $mealFood->quantity_g
                );

                foreach ($nutrition as $key => $value) {
                    $totals[$key] += $value;
                    $byType[$meal->type->value][$key] += $value;
                }
            }
        }

        $totals = $this->roundTotals($totals);

        foreach ($byType as $type => $values) {
            $byType[$type] = [
                'label' => $values['label'],
                ...$this->roundTotals(array_intersect_key(
                    $values,
                    $this->emptyTotals()
                )),
            ];
        }

        return [
            'totals' => $totals,
            'by_type' => $byType,
        ];
    }

    // saca calorias  segun la cantidad consumida con la porcion de referencia
    public function nutritionFor(Food $food, float $quantityG): array
    {
        $referenceServingG = (float) $food->reference_serving_g;

        $factor = $referenceServingG > 0
            ? $quantityG / $referenceServingG
            : 0;

        return [
            'calories' => round((float) $food->calories_per_serving * $factor, 2),
            'protein_g' => round((float) $food->protein_g * $factor, 2),
            'carbs_g' => round((float) $food->carbs_g * $factor, 2),
            'fat_g' => round((float) $food->fat_g * $factor, 2),
        ];
    }

    // valida alimentos repetidos e inactivos antes de guardarlos en la comida
    private function syncFoods(Meal $meal, array $foods): void
    {
        $foodIds = array_column($foods, 'food_id');

        if (count($foodIds) !== count(array_unique($foodIds))) {
            throw ValidationException::withMessages([
                'foods' => 'No se puede registrar el mismo alimento dos veces en la misma comida.',
            ]);
        }

        $activeFoodsCount = Food::whereIn('id', $foodIds)
            ->where('is_active', true)
            ->count();

        if ($activeFoodsCount !== count($foodIds)) {
            throw ValidationException::withMessages([
                'foods' => 'Uno o mas alimentos seleccionados no estan disponibles.',
            ]);
        }

        foreach ($foods as $food) {
            $meal->mealFoods()->create([
                'food_id' => $food['food_id'],
                'quantity_g' => $food['quantity_g'],
            ]);
        }
    }

    private function emptyTotals(): array
    {
        return [
            'calories' => 0.0,
            'protein_g' => 0.0,
            'carbs_g' => 0.0,
            'fat_g' => 0.0,
        ];
    }

    private function roundTotals(array $totals): array
    {
        return array_map(
            fn ($value) => round($value, 2),
            $totals
        );
    }
}
