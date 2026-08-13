<?php

namespace App\Http\Controllers\Trainer;

use App\Services\TrainerAssignmentService;
use App\Models\TrainerAssignment;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{

    public function __construct(
        private readonly TrainerAssignmentService $trainerAssignmentService,
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
        if (! $this->trainerAssignmentService->isActive($trainerAssignment)) {
            return redirect()->route('assignments.index')
                ->with('error', 'Esta asignación ya no está activa.');
        }

        if ($trainerAssignment->trainer_id !== Auth::id()) {
            return redirect()->route('assignments.index')
                ->with('error', 'No tienes permiso para ver esta asignación.');
        }

        $measurements = $this->trainerAssignmentService->getMeasuresForAssignment($trainerAssignment);

        return view('trainer.assignments.show', compact('trainerAssignment', 'measurements'));
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
}
