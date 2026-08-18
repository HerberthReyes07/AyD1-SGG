<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['rating', 'comment', 'trainer_assignment_id'])]
class TrainerRating extends Model
{
    public function trainerAssignment(): BelongsTo
    {
        return $this->belongsTo(TrainerAssignment::class, 'trainer_assignment_id', 'id');
    }
}
