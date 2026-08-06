<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['start_date', 'estimated_reactivation_date', 'reactivation_date', 'reason', 'frozen_days', 'member_membership_id', 'registered_by'])]
class MembershipFreeze extends Model
{
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'estimated_reactivation_date' => 'date',
            'reactivation_date' => 'date',
        ];
    }

    public function memberMembership(): BelongsTo
    {
        return $this->belongsTo(MemberMembership::class, 'member_membership_id', 'id');
    }

    public function registeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_by', 'id');
    }
}
