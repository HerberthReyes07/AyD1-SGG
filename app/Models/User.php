<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\TwoFactorChannel;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['role_id', 'first_name', 'last_name', 'email', 'password', 'phone_number', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    public function trainer(): HasOne
    {
        return $this->hasOne(Trainer::class, 'user_id', 'id');
    }

    public function member(): HasOne
    {
        return $this->hasOne(Member::class, 'user_id', 'id');
    }

    public function registeredFreezes(): HasMany
    {
        return $this->hasMany(MembershipFreeze::class, 'registered_by', 'id');
    }

    public function authorizedPromotions(): HasMany
    {
        return $this->hasMany(Promotion::class, 'authorized_by', 'id');
    }

    public function registeredPayments(): HasMany
    {
        return $this->hasMany(MembershipPayment::class, 'registered_by', 'id');
    }

    public function registeredGuestPasses(): HasMany
    {
        return $this->hasMany(GuestPass::class, 'registered_by', 'id');
    }

    public function trainerAssignments(): HasMany
    {
        return $this->hasMany(TrainerAssignment::class, 'assigned_by', 'id');
    }

    public function definedCalorieGoals(): HasMany
    {
        return $this->hasMany(CalorieGoal::class, 'defined_by', 'id');
    }

    public function changedMembershipStatuses(): HasMany
    {
        return $this->hasMany(MembershipStatusHistory::class, 'changed_by', 'id');
    }

    public function twoFactorCode(): HasOne
    {
        return $this->hasOne(TwoFactorCode::class);
    }

    /*
    |--------------------------------------------------------------------
    | Métodos de 2FA
    |--------------------------------------------------------------------
    */

    public function generateTwoFactorCode(TwoFactorChannel $channel): string
    {
        // random_int es criptográficamente seguro (a diferencia de rand()).
        $code = (string) random_int(100000, 999999);

        // updateOrCreate: si ya existia un código para este usuario, lo
        // sobreescribe; si no, crea el registro. Así solo hay un código
        // "vigente" por usuario en la tabla en todo momento.
        $this->twoFactorCode()->updateOrCreate(
            ['user_id' => $this->id],
            [
                'code' => $code,
                'channel' => $channel,
                'expires_at' => now()->addMinutes(10),
            ]
        );

        return $code;
    }

    /*
      Verifica el código ingresado contra el registro guardado en
      two_factor_codes (delegado a TwoFactorCode::isValid()).
     */
    public function hasValidTwoFactorCode(string $code): bool
    {
        return $this->twoFactorCode?->isValid($code) ?? false;
    }

    /*
      Borra el registro de two_factor_codes una vez usado (o para
      invalidarlo manualmente).
     */
    public function clearTwoFactorCode(): void
    {
        $this->twoFactorCode()->delete();
    }

    // le dice al canal de Twilio a que numero mandar el SMS, normalizado a formato internacional (+502 Guatemala por defecto)
    public function routeNotificationForTwilio(): ?string
    {
        if (! $this->phone_number) {
            return null;
        }

        $digits = preg_replace('/[^0-9+]/', '', $this->phone_number);

        if (! str_starts_with($digits, '+')) {
            $digits = '+502'.$digits;
        }

        return $digits;
    }
}
