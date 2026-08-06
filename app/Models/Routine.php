<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'description', 'is_active', 'trainer_assignment_id'])]
class Routine extends Model
{
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function trainerAssignment(): BelongsTo
    {
        return $this->belongsTo(TrainerAssignment::class, 'trainer_assignment_id', 'id');
    }

    public function routineExercises(): HasMany
    {
        return $this->hasMany(RoutineExercise::class, 'routine_id', 'id');
    }
}
