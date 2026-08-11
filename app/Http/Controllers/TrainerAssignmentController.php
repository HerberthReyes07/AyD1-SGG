<?php

namespace App\Http\Controllers;

use App\Models\TrainerAssignment;
use App\Services\TrainerAssignmentService;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class TrainerAssignmentController extends Controller
{
    public function __construct(
        private readonly TrainerAssignmentService $trainerAssignmentService,
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $assignments = $this->trainerAssignmentService->getActiveAssignments();

        return view('admin.trainer-assignments.index', compact('assignments'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = $this->trainerAssignmentService->getCreateData();

        return view('admin.trainer-assignments.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'required|exists:members,user_id',
            'trainer_id' => 'required|exists:trainers,user_id',
            'goal' => 'nullable|string|max:500',
        ]);

        if (! $this->trainerAssignmentService->createAssignment($validated)) {
            return back()->with('error', 'Este socio ya tiene un entrenador asignado.')->withInput();
        }

        return redirect()->route('trainer-assignments.index')
            ->with('status', 'Entrenador asignado correctamente.');
    }

    public function reassignCreate(TrainerAssignment $trainerAssignment)
    {
        if (! $this->trainerAssignmentService->isActive($trainerAssignment)) {
            return redirect()->route('trainer-assignments.index')
                ->with('error', 'Esta asignación ya no está activa.');
        }

        $data = $this->trainerAssignmentService->getReassignData($trainerAssignment);

        return view('admin.trainer-assignments.reassign', $data);
    }

    public function reassignStore(Request $request, TrainerAssignment $trainerAssignment)
    {
        if (! $this->trainerAssignmentService->isActive($trainerAssignment)) {
            return redirect()->route('trainer-assignments.index')
                ->with('error', 'Esta asignación ya no está activa.');
        }

        $validated = $request->validate([
            'new_trainer_id' => [
                'required',
                'exists:trainers,user_id',
                Rule::notIn([$trainerAssignment->trainer_id]),
            ],
            'reassignment_reason' => 'required|string|max:500',
            'goal' => 'nullable|string|max:500',
        ]);

        $this->trainerAssignmentService->reassign($trainerAssignment, $validated);

        return redirect()->route('trainer-assignments.index')
            ->with('status', 'Entrenador reasignado correctamente.');
    }

    public function history()
    {
        $assignments = $this->trainerAssignmentService->getHistoryAssignments();

        return view('admin.trainer-assignments.history', compact('assignments'));
    }
}
