<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'description'])]
class PaymentMethod extends Model
{
    public function payments(): HasMany
    {
        return $this->hasMany(MembershipPayment::class, 'payment_method_id', 'id');
    }
}
