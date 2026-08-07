<?php

namespace App\Models;

use App\Enums\TwoFactorChannel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'code', 'channel', 'expires_at'])]
class TwoFactorCode extends Model
{
    protected function casts(): array
    {
        return [
            'channel' => TwoFactorChannel::class,
            'expires_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    //Compara el código recibido contra este registro, verificando también que no haya vencido.
     
    public function isValid(string $code): bool
    {
        if ($this->expires_at->isPast()) {
            return false;
        }

        // hash_equals() compara en tiempo constante (evita timing attacks).
        return hash_equals($this->code, $code);
    }
}