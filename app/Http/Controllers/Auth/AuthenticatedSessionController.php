<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        //Valida email y password sin iniciar sesion
        $request->authenticate();

        //se Busca al usuario para generarle su código de 2FA después.
        $user = User::where('email', $request->string('email'))->firstOrFail();

        //se  guarda como "pendiente de verificación" en sesión.
        $request->session()->put('two_factor.user_id', $user->id);
        $request->session()->put('two_factor.remember', $request->boolean('remember'));

        //se mandamos a elegir el canal e ingresar el código.
        return redirect()->route('two-factor.challenge');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
