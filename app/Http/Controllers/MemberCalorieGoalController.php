<?php

namespace App\Http\Controllers;

use App\Enums\CalorieGoalObjective;
use App\Services\CalorieGoalService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MemberCalorieGoalController extends Controller
{
    public function __construct(
        private readonly CalorieGoalService $calorieGoalService
    ) {}

    public function edit(Request $request)
    {
        $member = $request->user()->member;

        abort_if(! $member, 403);

        $goal = $this->calorieGoalService->getCurrentGoal($member);

        return view('member-calorie-goal.edit', compact('goal'));
    }

    public function update(Request $request)
    {
        $member = $request->user()->member;

        abort_if(! $member, 403);

        $validated = $request->validate([
            'daily_calories' => ['required', 'numeric', 'gt:0'],
            'objective' => ['required', Rule::enum(CalorieGoalObjective::class)],
        ]);

        $this->calorieGoalService->setGoal($member, $validated);

        return redirect()
            ->route('calorie-goals.edit')
            ->with('success', 'Meta calorica actualizada correctamente.');
    }
}
