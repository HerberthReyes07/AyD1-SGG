<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['date', 'weight', 'waist_measurement', 'arm_measurement', 'leg_measurement', 'trainer_assignment_id'])]
class PeriodicMeasurement extends Model
{
    protected function casts(): array
    {
        return [
            'date' => 'date',
            'weight' => 'decimal:2',
            'waist_measurement' => 'decimal:2',
            'arm_measurement' => 'decimal:2',
            'leg_measurement' => 'decimal:2',
        ];
    }

    public function trainerAssignment(): BelongsTo
    {
        return $this->belongsTo(TrainerAssignment::class, 'trainer_assignment_id', 'id');
    }
}
