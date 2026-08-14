<?php

namespace App\Http\Controllers;

use App\Models\TrainerAssignment;
use App\Services\CalorieGoalService;
use App\Services\MealService;
use App\Services\NutritionalObservationService;
use Illuminate\Http\Request;

class MemberNutritionHistoryController extends Controller
{
    public function __construct(
        private readonly MealService $mealService,
        private readonly CalorieGoalService $calorieGoalService,
        private readonly NutritionalObservationService $nutritionalObservationService
    ) {}

    public function index(Request $request)
    {
        $member = $request->user()->member;

        abort_if(! $member, 403);

        $days = in_array((int) $request->get('days'), [7, 30], true)
            ? (int) $request->get('days')
            : 7;

        $startDate = today()->subDays($days - 1);

        $history = $this->mealService->getHistory($member, $startDate, today());

        // agrega el estado contra la meta vigente de cada dia, si habia una definida
        $history = array_map(function (array $day) use ($member) {
            $goal = $this->calorieGoalService->getCurrentGoal($member, $day['date']);

            $day['comparison'] = $goal
                ? $this->calorieGoalService->compare($day['calories'], $goal)
                : null;

            return $day;
        }, $history);

        $assignment = TrainerAssignment::where('member_id', $member->user_id)
            ->whereNull('end_date')
            ->first();

        $observations = $assignment
            ? $this->nutritionalObservationService->getForAssignment($assignment)
            : collect();

        return view('member-nutrition-history.index', compact('history', 'days', 'assignment', 'observations'));
    }
}
