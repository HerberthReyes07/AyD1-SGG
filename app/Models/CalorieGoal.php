<?php

namespace App\Models;

use App\Enums\CalorieGoalObjective;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['daily_calories', 'objective', 'start_date', 'end_date', 'member_id', 'defined_by'])]
class CalorieGoal extends Model
{
    protected function casts(): array
    {
        return [
            'daily_calories' => 'decimal:2',
            'objective' => CalorieGoalObjective::class,
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'member_id', 'user_id');
    }

    public function definedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'defined_by', 'id');
    }
}
