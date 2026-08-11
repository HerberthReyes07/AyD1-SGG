<?php

namespace App\Http\Controllers\Trainer;

use App\Services\TrainerAssignmentService;
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
}
