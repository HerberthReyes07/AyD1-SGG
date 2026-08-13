<?php

namespace App\Http\Controllers;

use App\Enums\MealType;
use App\Models\Food;
use App\Models\Meal;
use App\Services\MealService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MemberMealController extends Controller
{
    public function __construct(
        private readonly MealService $mealService
    ) {}

    public function index(Request $request)
    {
        $member = $request->user()->member;

        abort_if(! $member, 403);

        $date = $request->filled('date')
            ? Carbon::parse($request->date)
            : today();

        // agrupa por tipo para poder pintar desayuno/almuerzo/cena/snack por separado
        $meals = $member->meals()
            ->whereDate('date', $date)
            ->with('mealFoods.food.category')
            ->get()
            ->groupBy(fn (Meal $meal) => $meal->type->value);

        $summary = $this->mealService->getDailySummary($member, $date);

        $mealService = $this->mealService;

        return view('member-meals.index', compact(
            'meals',
            'summary',
            'date',
            'mealService'
        ));
    }

    public function create()
    {
        $foods = Food::with('category')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('member-meals.create', compact('foods'));
    }

    public function store(Request $request)
    {
        $member = $request->user()->member;

        abort_if(! $member, 403);

        $validated = $this->validateMeal($request);

        $this->mealService->registerMeal($member, $validated);

        return redirect()
            ->route('member-meals.index', ['date' => $validated['date']])
            ->with('success', 'Comida registrada correctamente.');
    }

    public function edit(Request $request, Meal $meal)
    {
        $member = $request->user()->member;

        abort_if(! $member, 403);
        abort_if($meal->member_id !== $member->user_id, 403);

        $meal->load('mealFoods.food');

        $foods = Food::with('category')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('member-meals.edit', compact('meal', 'foods'));
    }

    public function update(Request $request, Meal $meal)
    {
        $member = $request->user()->member;

        abort_if(! $member, 403);
        abort_if($meal->member_id !== $member->user_id, 403);

        $validated = $this->validateMeal($request);

        $this->mealService->updateMeal($meal, $validated);

        return redirect()
            ->route('member-meals.index', ['date' => $validated['date']])
            ->with('success', 'Comida actualizada correctamente.');
    }

    public function destroy(Request $request, Meal $meal)
    {
        $member = $request->user()->member;

        abort_if(! $member, 403);
        abort_if($meal->member_id !== $member->user_id, 403);

        $date = $meal->date->toDateString();

        $this->mealService->deleteMeal($meal);

        return redirect()
            ->route('member-meals.index', ['date' => $date])
            ->with('success', 'Comida eliminada correctamente.');
    }

    // reglas compartidas entre store y update
    private function validateMeal(Request $request): array
    {
        return $request->validate([
            'date' => ['required', 'date', 'before_or_equal:today'],
            'type' => ['required', Rule::enum(MealType::class)],
            'foods' => ['required', 'array', 'min:1'],
            'foods.*.food_id' => ['required', 'integer', 'exists:foods,id'],
            'foods.*.quantity_g' => ['required', 'numeric', 'gt:0'],
        ]);
    }
}
