<?php

namespace App\Models;

use App\Enums\Weekday;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['exercise_name', 'sets', 'reps', 'suggested_day', 'routine_id'])]
class RoutineExercise extends Model
{
    protected function casts(): array
    {
        return [
            'suggested_day' => Weekday::class,
        ];
    }

    public function routine(): BelongsTo
    {
        return $this->belongsTo(Routine::class, 'routine_id', 'id');
    }
}
