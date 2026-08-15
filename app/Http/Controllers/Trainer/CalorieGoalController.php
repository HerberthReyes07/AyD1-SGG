<?php

namespace App\Http\Controllers\Trainer;

use App\Enums\CalorieGoalObjective;
use App\Http\Controllers\Controller;
use App\Models\TrainerAssignment;
use App\Services\CalorieGoalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class CalorieGoalController extends Controller
{
    public function __construct(private CalorieGoalService $service) {}

    public function store(Request $request, TrainerAssignment $trainerAssignment)
    {
        // Guard: confirmar que el entrenador autenticado es dueño de esta asignación
        abort_unless($trainerAssignment->trainer_id === Auth::id(), 403);

        abort_unless(
            $this->service->canBeAdjustedByTrainer($trainerAssignment->member),
            403,
            'Solo los socios con Plan Elite pueden tener su meta calorica ajustada por el entrenador.'
        );

        $validated = $request->validate([
            'daily_calories' => ['required', 'numeric', 'gt:0'],
            'objective' => ['required', Rule::enum(CalorieGoalObjective::class)],
        ]);

        $this->service->setGoal($trainerAssignment->member, $validated, Auth::id());

        return back()->with('status', 'Meta calorica actualizada correctamente.');
    }
}
