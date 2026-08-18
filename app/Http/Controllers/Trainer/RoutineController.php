<?php

namespace App\Http\Controllers\Trainer;

use App\Http\Controllers\Controller;
use App\Models\Routine;
use App\Models\TrainerAssignment;
use App\Services\RoutineService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoutineController extends Controller
{
    public function __construct(private RoutineService $service) {}

    public function create(TrainerAssignment $trainerAssignment)
    {
        abort_unless($trainerAssignment->trainer_id === Auth::id(), 403);

        return view('trainer.routines.create', compact('trainerAssignment'));
    }

    public function store(Request $request, TrainerAssignment $trainerAssignment)
    {
        abort_unless($trainerAssignment->trainer_id === Auth::id(), 403);

        $validated = $this->validateRoutine($request);

        $this->service->create($trainerAssignment->id, $validated);

        return redirect()->route('assignments.show', $trainerAssignment)
            ->with('status', 'Rutina creada correctamente.');
    }

    public function edit(Routine $routine)
    {
        $routine->load(['routineExercises', 'trainerAssignment']);
        abort_unless($routine->trainerAssignment->trainer_id === Auth::id(), 403);

        return view('trainer.routines.edit', compact('routine'));
    }

    public function update(Request $request, Routine $routine)
    {
        $routine->load('trainerAssignment');
        abort_unless($routine->trainerAssignment->trainer_id === Auth::id(), 403);

        $validated = $this->validateRoutine($request);

        $this->service->update($routine->id, $validated);

        return redirect()->route('assignments.show', $routine->trainer_assignment_id)
            ->with('status', 'Rutina actualizada correctamente.');
    }

    public function toggleActive(Routine $routine)
    {
        $routine->load('trainerAssignment');
        abort_unless($routine->trainerAssignment->trainer_id === Auth::id(), 403);

        $routine->is_active = !$routine->is_active;
        $routine->save();

        return redirect()->route('assignments.show', $routine->trainer_assignment_id)
            ->with('status', 'Estado de la rutina actualizado correctamente.');
    }

    public function history(TrainerAssignment $trainerAssignment)
    {
        abort_unless($trainerAssignment->trainer_id === Auth::id(), 403);
        abort_if($trainerAssignment->end_date !== null, 403); // solo tiene sentido duplicar hacia una asignación activa

        $routineHistory = $this->service->getHistoryForMember($trainerAssignment->member_id);

        return view('trainer.routines.history', compact('trainerAssignment', 'routineHistory'));
    }

    public function duplicate(TrainerAssignment $trainerAssignment, Routine $routine)
    {
        abort_unless($trainerAssignment->trainer_id === Auth::id(), 403);
        abort_if($trainerAssignment->end_date !== null, 403);

        // Confirmar que la rutina a duplicar realmente pertenece a una asignación del mismo socio
        $routine->load('trainerAssignment');
        abort_unless($routine->trainerAssignment->member_id === $trainerAssignment->member_id, 403);

        $this->service->duplicateIntoAssignment($routine->id, $trainerAssignment->id);

        return redirect()->route('assignments.show', $trainerAssignment)
            ->with('status', 'Rutina duplicada correctamente. Ya puedes editarla si lo necesitas.');
    }

    private function validateRoutine(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'exercises' => 'required|array|min:1',
            'exercises.*.exercise_name' => 'required|string|max:255',
            'exercises.*.sets' => 'required|integer|min:1',
            'exercises.*.reps' => 'required|integer|min:1',
            'exercises.*.suggested_day' => 'required|in:monday,tuesday,wednesday,thursday,friday,saturday,sunday',
        ]);
    }
}
