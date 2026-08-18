<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'description', 'duration_minutes', 'max_participants', 'is_active', 'category_id', 'trainer_id'])]
class GroupClass extends Model
{
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ClassCategory::class, 'category_id', 'id');
    }

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(Trainer::class, 'trainer_id', 'user_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(GroupClassSchedule::class, 'group_class_id', 'id');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(ClassSession::class, 'group_class_id', 'id');
    }

}
