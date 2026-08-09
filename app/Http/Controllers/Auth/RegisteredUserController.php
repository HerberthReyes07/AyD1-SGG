<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => [
                'required',
                'string',
                'max:255',
            ],

            'last_name' => [
                'required',
                'string',
                'max:255',
            ],

            'phone_number' => [
                'nullable',
                'string',
                'max:20',
            ],

            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                'unique:' . User::class,
            ],

            'password' => [
                'required',
                'confirmed',
                Rules\Password::defaults(),
            ],
        ]);

        $memberRole = Role::where(
            'name',
            'member'
        )->firstOrFail();

        $user = User::create([
            'role_id' => $memberRole->id,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'phone_number' =>
                $validated['phone_number'] ?? null,
            'email' => $validated['email'],
            'password' => Hash::make(
                $validated['password']
            ),
            'is_active' => true,
        ]);

        event(new Registered($user));

        $request->session()->put(
            'two_factor.user_id',
            $user->id
        );

        $request->session()->put(
            'two_factor.remember',
            false
        );

        return redirect()
            ->route('two-factor.challenge');
    }
}