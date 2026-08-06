<?php

namespace App\Models;

use App\Enums\ClassSessionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['group_class_id', 'status', 'starts_at', 'change_reason'])]
class ClassSession extends Model
{
    protected function casts(): array
    {
        return [
            'status' => ClassSessionStatus::class,
            'starts_at' => 'datetime',
        ];
    }

    public function groupClass(): BelongsTo
    {
        return $this->belongsTo(GroupClass::class, 'group_class_id', 'id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(ClassEnrollment::class, 'class_session_id', 'id');
    }

    public function waitlists(): HasMany
    {
        return $this->hasMany(ClassWaitlist::class, 'class_session_id', 'id');
    }

}
