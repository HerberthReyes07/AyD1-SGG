<?php

namespace App\Services;

use App\Models\Routine;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class RoutineService
{
    public function getForAssignment(int $trainerAssignmentId): Collection
    {
        return Routine::where('trainer_assignment_id', $trainerAssignmentId)
            ->withCount('routineExercises')
            ->orderByDesc('created_at')
            ->get();
    }

    public function create(int $trainerAssignmentId, array $data): Routine
    {
        return DB::transaction(function () use ($trainerAssignmentId, $data) {
            $routine = Routine::create([
                'trainer_assignment_id' => $trainerAssignmentId,
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'is_active' => true,
            ]);

            foreach ($data['exercises'] as $exercise) {
                $routine->routineExercises()->create($exercise);
            }

            return $routine;
        });
    }

    public function update(int $routineId, array $data): Routine
    {
        return DB::transaction(function () use ($routineId, $data) {
            $routine = Routine::findOrFail($routineId);

            $routine->update([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
            ]);

            $routine->routineExercises()->delete();

            foreach ($data['exercises'] as $exercise) {
                $routine->routineExercises()->create($exercise);
            }

            return $routine;
        });
    }

    public function toggleActive(int $routineId): Routine
    {
        $routine = Routine::findOrFail($routineId);
        $routine->update(['is_active' => !$routine->is_active]);

        return $routine;
    }

    public function getHistoryForMember(int $memberId): Collection
    {
        return Routine::whereHas('trainerAssignment', function ($query) use ($memberId) {
            $query->where('member_id', $memberId)
                ->whereNotNull('end_date'); // solo asignaciones ya finalizadas
        })
            ->with(['trainerAssignment.trainer.user', 'routineExercises'])
            ->withCount('routineExercises')
            ->orderByDesc('created_at')
            ->get();
    }

    public function duplicateIntoAssignment(int $routineId, int $targetAssignmentId): Routine
    {
        return DB::transaction(function () use ($routineId, $targetAssignmentId) {
            $original = Routine::with('routineExercises')->findOrFail($routineId);

            $name = $original->name;
            $exists = Routine::where('trainer_assignment_id', $targetAssignmentId)
                ->where('name', $name)
                ->exists();

            if ($exists) {
                $name .= ' (copia)';
            }

            $copy = Routine::create([
                'trainer_assignment_id' => $targetAssignmentId,
                'name' => $name,
                'description' => $original->description,
                'is_active' => true,
            ]);


            foreach ($original->routineExercises as $exercise) {
                $copy->routineExercises()->create($exercise->only(['exercise_name', 'sets', 'reps', 'suggested_day']));
            }

            return $copy;
        });
    }

    public function getActiveForAssignment(int $trainerAssignmentId): Collection
    {
        return Routine::where('trainer_assignment_id', $trainerAssignmentId)
            ->where('is_active', true)
            ->with('routineExercises')
            ->get();
    }
}
