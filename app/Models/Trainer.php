<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'specialty_id'])]
class Trainer extends Model
{
    protected $primaryKey = 'user_id';
    public $incrementing = false;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function specialty(): BelongsTo
    {
        return $this->belongsTo(TrainerSpecialty::class, 'specialty_id', 'id');
    }

    public function groupClasses(): HasMany
    {
        return $this->hasMany(GroupClass::class, 'trainer_id', 'user_id');
    }

    public function trainerAssignments(): HasMany
    {
        return $this->hasMany(TrainerAssignment::class, 'trainer_id', 'user_id');
    }

}
