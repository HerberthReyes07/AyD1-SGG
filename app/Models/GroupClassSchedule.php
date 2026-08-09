<?php

namespace App\Models;

use App\Enums\Weekday;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['weekday', 'start_time', 'group_class_id'])]
class GroupClassSchedule extends Model
{
    protected function casts(): array
    {
        return [
            'weekday' => Weekday::class,
        ];
    }

    public function groupClass(): BelongsTo
    {
        return $this->belongsTo(
            GroupClass::class,
            'group_class_id',
            'id'
        );
    }
}