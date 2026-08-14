<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Models\TrainerAssignment;
use App\Services\NutritionalObservationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NutritionalObservationController extends Controller
{
    public function __construct(private NutritionalObservationService $service) {}

    public function store(Request $request, TrainerAssignment $trainerAssignment)
    {
        // Guard: confirmar que el entrenador autenticado es dueño de esta asignación
        abort_unless($trainerAssignment->trainer_id === Auth::id(), 403);

        $validated = $request->validate([
            'date' => ['required', 'date', 'before_or_equal:today'],
            'observation' => ['required', 'string', 'max:2000'],
        ]);

        $this->service->register($trainerAssignment->id, $validated);

        return back()->with('status', 'Observacion registrada correctamente.');
    }
}
