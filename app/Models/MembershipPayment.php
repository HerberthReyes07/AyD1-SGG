<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['amount', 'payment_date', 'member_membership_id', 'payment_method_id', 'promotion_id', 'registered_by'])]
class MembershipPayment extends Model
{
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payment_date' => 'date',
        ];
    }

    public function memberMembership(): BelongsTo
    {
        return $this->belongsTo(MemberMembership::class, 'member_membership_id', 'id');
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class, 'payment_method_id', 'id');
    }

    public function promotion(): BelongsTo
    {
        return $this->belongsTo(Promotion::class, 'promotion_id', 'id');
    }

    public function registeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_by', 'id');
    }
}
