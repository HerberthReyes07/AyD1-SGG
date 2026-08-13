<?php

namespace App\Services;

use App\Models\PeriodicMeasurement;
use Illuminate\Support\Collection;

class PeriodicMeasurementService
{
    public function getHistoryForAssignment(int $trainerAssignmentId): Collection
    {
        return PeriodicMeasurement::where('trainer_assignment_id', $trainerAssignmentId)
            ->orderByDesc('date')
            ->get();
    }

    public function register(int $trainerAssignmentId, array $data): PeriodicMeasurement
    {
        return PeriodicMeasurement::create([
            'trainer_assignment_id' => $trainerAssignmentId,
            'date' => $data['date'],
            'weight' => $data['weight'],
            'waist_measurement' => $data['waist_measurement'] ?? null,
            'arm_measurement' => $data['arm_measurement'] ?? null,
            'leg_measurement' => $data['leg_measurement'] ?? null,
        ]);
    }
}
