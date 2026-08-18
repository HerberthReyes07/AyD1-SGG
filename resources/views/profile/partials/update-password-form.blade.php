<section>
    <header>
        <h2 class="h5 fw-medium text-dark">
            <i class="bi bi-shield-lock me-2"></i>{{ __('Actualizar contraseña') }}
        </h2>

        <p class="mt-1 small text-secondary">
            {{ __('Asegurate de usar una contraseña larga y aleatoria para mayor seguridad.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-4">
        @csrf
        @method('put')

        <div class="mb-3">
            <x-input-label for="update_password_current_password" :value="__('Contraseña actual')" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="mt-1 d-block w-100" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div class="mb-3">
            <x-input-label for="update_password_password" :value="__('Contraseña nueva')" />
            <x-text-input id="update_password_password" name="password" type="password" class="mt-1 d-block w-100" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div class="mb-3">
            <x-input-label for="update_password_password_confirmation" :value="__('Confirmar contraseña')" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="mt-1 d-block w-100" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="d-flex align-items-center gap-3">
            <x-primary-button><i class="bi bi-check-lg me-1"></i>{{ __('Guardar') }}</x-primary-button>

            @if (session('status') === 'password-updated')
                <p class="small text-secondary mb-0 fade-out-message">{{ __('Guardado.') }}</p>
            @endif
        </div>
    </form>
</section>
