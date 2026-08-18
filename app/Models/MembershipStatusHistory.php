<?php

namespace App\Models;

use App\Enums\MembershipStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['previous_status', 'new_status', 'change_date', 'reason', 'changed_by', 'member_membership_id'])]
class MembershipStatusHistory extends Model
{
    protected function casts(): array
    {
        return [
            'previous_status' => MembershipStatus::class,
            'new_status' => MembershipStatus::class,
            'change_date' => 'date',
        ];
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by', 'id');
    }

    public function memberMembership(): BelongsTo
    {
        return $this->belongsTo(MemberMembership::class, 'member_membership_id', 'id');
    }
}
