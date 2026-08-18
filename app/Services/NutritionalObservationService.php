<?php

namespace App\Services;

use App\Models\NutritionalObservation;
use App\Models\TrainerAssignment;
use Illuminate\Support\Collection;

class NutritionalObservationService
{
    public function getForAssignment(TrainerAssignment $assignment): Collection
    {
        return NutritionalObservation::where('trainer_assignment_id', $assignment->id)
            ->orderByDesc('date')
            ->get();
    }

    public function register(int $trainerAssignmentId, array $data): NutritionalObservation
    {
        return NutritionalObservation::create([
            'trainer_assignment_id' => $trainerAssignmentId,
            'date' => $data['date'],
            'observation' => $data['observation'],
        ]);
    }
}
