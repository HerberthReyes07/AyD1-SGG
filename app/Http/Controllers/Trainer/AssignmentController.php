<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Models\Meal;
use App\Models\TrainerAssignment;
use App\Services\CalorieGoalService;
use App\Services\MealService;
use App\Services\NutritionalObservationService;
use App\Services\RoutineService;
use App\Services\TrainerAssignmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssignmentController extends Controller
{
    public function __construct(
        private readonly TrainerAssignmentService $trainerAssignmentService,
        private readonly RoutineService $routineService,
        private readonly MealService $mealService,
        private readonly CalorieGoalService $calorieGoalService,
        private readonly NutritionalObservationService $nutritionalObservationService,
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $assignments = $this->trainerAssignmentService->getActiveForTrainer(Auth::id());

        return view('trainer.assignments.index', compact('assignments'));
    }

    public function history()
    {
        $assignments = $this->trainerAssignmentService->getHistoryForTrainer(Auth::id());

        return view('trainer.assignments.history', compact('assignments'));
    }

    public function show(TrainerAssignment $trainerAssignment)
    {
        abort_unless($trainerAssignment->trainer_id === Auth::id(), 403);

        $trainerAssignment->load(['member.user']);
        $measurements = $this->trainerAssignmentService->getMeasuresForAssignment($trainerAssignment);
        $routines = $this->routineService->getForAssignment($trainerAssignment->id);

        $calorieGoal = $this->calorieGoalService->getCurrentGoal($trainerAssignment->member);

        $canAdjustGoal = $this->calorieGoalService->canBeAdjustedByTrainer($trainerAssignment->member);

        $nutritionHistory = $this->mealService->getHistory(
            $trainerAssignment->member,
            today()->subDays(6),
            today()
        );

        $dailyMeals = $trainerAssignment->member->meals()
            ->whereDate('date', today())
            ->with('mealFoods.food.category')
            ->get()
            ->groupBy(fn (Meal $meal) => $meal->type->value);

        $dailySummary = $this->mealService->getDailySummary($trainerAssignment->member, today());

        $observations = $this->nutritionalObservationService->getForAssignment($trainerAssignment);

        $mealService = $this->mealService;

        return view('trainer.assignments.show', compact(
            'trainerAssignment',
            'measurements',
            'calorieGoal',
            'canAdjustGoal',
            'nutritionHistory',
            'dailyMeals',
            'dailySummary',
            'observations',
            'mealService',
            'routines'
        ));
    }

    public function store(Request $request, TrainerAssignment $trainerAssignment)
    {
        abort_unless($trainerAssignment->trainer_id === Auth::id(), 403);

        $validated = $request->validate([
            'date' => 'required|date',
            'weight' => 'required|numeric|min:0',
            'waist_measurement' => 'nullable|numeric|min:0',
            'arm_measurement' => 'nullable|numeric|min:0',
            'leg_measurement' => 'nullable|numeric|min:0',
        ]);

        $this->trainerAssignmentService->register($trainerAssignment->id, $validated);

        return back()->with('status', 'Medición registrada correctamente.');
    }

    public function updateGoal(Request $request, TrainerAssignment $trainerAssignment)
    {
        abort_unless($trainerAssignment->trainer_id === Auth::id(), 403);

        $validated = $request->validate([
            'goal' => 'required|string|max:1000',
        ]);

        $this->trainerAssignmentService->updateGoal($trainerAssignment, $validated['goal']);

        return back()->with('status', 'Objetivo actualizado correctamente.');
    }
}
