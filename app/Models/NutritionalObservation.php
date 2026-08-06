<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['date', 'observation', 'trainer_assignment_id'])]
class NutritionalObservation extends Model
{
    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function trainerAssignment(): BelongsTo
    {
        return $this->belongsTo(TrainerAssignment::class, 'trainer_assignment_id', 'id');
    }
}
