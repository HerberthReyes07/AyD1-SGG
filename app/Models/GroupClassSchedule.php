<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\Weekday;

#[Fillable(['weekday', 'start_time', 'group_class_id'])]
class GroupClassSchedule extends Model
{
    protected function casts(): array
    {
        return [
            'weekday' => Weekday::class,
            'start_time' => 'time',
        ];
    }

    public function groupClass(): BelongsTo
    {
        return $this->belongsTo(GroupClass::class, 'group_class_id', 'id');
    }

}
