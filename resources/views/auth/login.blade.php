<x-guest-layout>

    <!-- Session Status -->
    <x-auth-session-status class="mb-3" :status="session('status')" />

    <div class="mb-4">
        <h4 class="fw-bold mb-1">
            Bienvenido
        </h4>

        <p class="text-secondary mb-0">
            Ingresa tus credenciales para continuar.
        </p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Correo electrónico -->
        <div class="mb-3">
            <label for="email" class="form-label fw-semibold">
                Correo electrónico
            </label>

            <div class="input-group">
                <span class="input-group-text bg-white">
                    <i class="bi bi-envelope text-secondary"></i>
                </span>

                <input id="email" type="email" name="email" value="{{ old('email') }}" class="form-control"
                    placeholder="correo@ejemplo.com" required autofocus autocomplete="username">
            </div>

            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Contraseña -->
        <div class="mb-3">
            <label for="password" class="form-label fw-semibold">
                Contraseña
            </label>

            <div class="input-group">
                <span class="input-group-text bg-white">
                    <i class="bi bi-lock text-secondary"></i>
                </span>

                <input id="password" type="password" name="password" class="form-control"
                    placeholder="Ingresa tu contraseña" required autocomplete="current-password">
            </div>

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Recordarme -->
        <div class="d-flex justify-content-between align-items-center mb-4">

            <div class="form-check">
                <input id="remember_me" type="checkbox" class="form-check-input" name="remember">

                <label for="remember_me" class="form-check-label text-secondary">
                    Recordarme
                </label>
            </div>

            @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="link-primary text-decoration-none small ms-4">
                ¿Olvidaste tu contraseña?
            </a>
            @endif

        </div>

        <!-- Botón -->
        <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
            Iniciar sesión
        </button>

    </form>

</x-guest-layout>
