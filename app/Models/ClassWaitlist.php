<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\WaitlistStatus;

#[Fillable(['requested_date', 'status', 'member_id', 'class_session_id'])]
class ClassWaitlist extends Model
{
    protected function casts(): array
    {
        return [
            'status' => WaitlistStatus::class,
            'requested_date' => 'date',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'member_id', 'user_id');
    }

    public function classSession(): BelongsTo
    {
        return $this->belongsTo(ClassSession::class, 'class_session_id', 'id');
    }

}
