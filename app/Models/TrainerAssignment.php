<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable('assignment_date', 'end_date', 'goal', 'reassignment_reason', 'member_id', 'trainer_id', 'assigned_by')]
class TrainerAssignment extends Model
{
    protected function casts(): array
    {
        return [
            'assignment_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'member_id', 'user_id');
    }

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(Trainer::class, 'trainer_id', 'user_id');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by', 'id');
    }

    public function routines(): HasMany
    {
        return $this->hasMany(Routine::class, 'trainer_assignment_id', 'id');
    }

    public function periodicMeasurements(): HasMany
    {
        return $this->hasMany(PeriodicMeasurement::class, 'trainer_assignment_id', 'id');
    }

    public function trainerRatings(): HasMany
    {
        return $this->hasMany(TrainerRating::class, 'trainer_assignment_id', 'id');
    }

    public function nutritionalObservations(): HasMany
    {
        return $this->hasMany(NutritionalObservation::class, 'trainer_assignment_id', 'id');
    }
}
