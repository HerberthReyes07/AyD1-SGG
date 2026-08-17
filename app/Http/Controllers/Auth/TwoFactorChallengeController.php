<?php

namespace App\Http\Controllers\Auth;

use App\Enums\TwoFactorChannel;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\TwoFactorCodeNotification;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TwoFactorChallengeController extends Controller
{
    public function create(Request $request): View
    {
        $this->pendingUser($request);

        return view('auth.two-factor-challenge', [
            'channels' => TwoFactorChannel::cases(),
        ]);
    }

    public function send(Request $request): RedirectResponse
    {
        $user = $this->pendingUser($request);

        $request->validate([
            'channel' => ['required', 'string'],
        ]);

        $channel = TwoFactorChannel::from($request->string('channel')->toString());

        if ($channel === TwoFactorChannel::Sms && ! $user->phone_number) {
            return back()->withErrors([
                'channel' => 'No tienes un numero de telefono registrado para recibir el codigo por SMS.',
            ]);
        }

        $code = $user->generateTwoFactorCode($channel);

        try {
            $user->notify(new TwoFactorCodeNotification($code, $channel));
        } catch (\Throwable $e) {
            report($e);

            return back()->withErrors([
                'channel' => 'No se pudo enviar el codigo, intenta de nuevo o usa otro medio.',
            ]);
        }

        $message = $channel === TwoFactorChannel::Sms
            ? 'Te enviamos un código por SMS.'
            : 'Te enviamos un código a tu correo electrónico.';

        return back()->with('status', $message);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $this->pendingUser($request);

        $request->validate([
            'code' => ['required', 'string'],
        ]);

        if (! $user->hasValidTwoFactorCode($request->string('code')->toString())) {
            throw ValidationException::withMessages([
                'code' => 'El código es incorrecto o ya venció, solicita uno nuevo.',
            ]);
        }

        $remember = (bool) $request->session()->pull('two_factor.remember', false);
        $request->session()->forget('two_factor.user_id');

        $user->clearTwoFactorCode();

        // Aqui si completamos el login de a deveris.
        Auth::login($user, $remember);

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    private function pendingUser(Request $request): User
    {
        $userId = $request->session()->get('two_factor.user_id');

        if (! $userId) {
            throw new HttpResponseException(redirect()->route('login'));
        }

        return User::findOrFail($userId);
    }
}
