<?php

namespace App\Http\Controllers;

use App\Services\CalorieGoalService;
use App\Services\MealService;
use Illuminate\Http\Request;

class MemberNutritionHistoryController extends Controller
{
    public function __construct(
        private readonly MealService $mealService,
        private readonly CalorieGoalService $calorieGoalService
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

        return view('member-nutrition-history.index', compact('history', 'days'));
    }
}
