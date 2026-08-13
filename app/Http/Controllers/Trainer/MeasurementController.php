<?php

namespace App\Http\Controllers\Trainer;

use App\Models\TrainerAssignment;
use App\Services\PeriodicMeasurementService;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MeasurementController extends Controller
{
    public function __construct(private PeriodicMeasurementService $service) {}

    public function store(Request $request, TrainerAssignment $trainerAssignment)
    {
        // Guard: confirmar que el entrenador autenticado es dueño de esta asignación
        abort_unless($trainerAssignment->trainer_id === Auth::id(), 403);

        $validated = $request->validate([
            'date' => 'required|date',
            'weight' => 'required|numeric|min:0',
            'waist_measurement' => 'nullable|numeric|min:0',
            'arm_measurement' => 'nullable|numeric|min:0',
            'leg_measurement' => 'nullable|numeric|min:0',
        ]);

        $this->service->register($trainerAssignment->id, $validated);

        return back()->with('status', 'Medición registrada correctamente.');
    }

    public function history(TrainerAssignment $trainerAssignment)
    {
        abort_unless($trainerAssignment->trainer_id === Auth::id(), 403);

        $measurementHistory = $this->service->getHistoryForMember($trainerAssignment->member_id);

        return view('trainer.measurements.history', compact('trainerAssignment', 'measurementHistory'));
    }
}
