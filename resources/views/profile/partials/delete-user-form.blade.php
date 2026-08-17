<section class="mb-4">
    <header>
        <h2 class="h5 fw-medium text-dark">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ __('Eliminar cuenta') }}
        </h2>

        <p class="mt-1 small text-secondary">
            {{ __('Una vez eliminada tu cuenta, todos sus recursos y datos se borraran permanentemente. Antes de
            eliminar tu cuenta, descarga cualquier informacion que quieras conservar.') }}
        </p>
    </header>

    <x-danger-button type="button" data-bs-toggle="modal" data-bs-target="#confirm-user-deletion">
        <i class="bi bi-trash me-1"></i>{{ __('Eliminar cuenta') }}
    </x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->updatePassword->isNotEmpty()">
        <form method="post" action="{{ route('profile.destroy') }}" class="p-4">
            @csrf
            @method('delete')

            <h2 class="h5 fw-medium text-dark">
                {{ __('¿Seguro que quieres eliminar tu cuenta?') }}
            </h2>

            <p class="mt-1 small text-secondary">
                {{ __('Una vez eliminada tu cuenta, todos sus recursos y datos se borraran permanentemente. Ingresa tu
                contraseña para confirmar que deseas eliminar tu cuenta de forma definitiva.') }}
            </p>

            <div class="mt-4">
                <x-input-label for="password" value="{{ __('Contraseña') }}" class="visually-hidden" />

                <x-text-input id="password" name="password" type="password" class="mt-1 d-block w-75"
                    placeholder="{{ __('Contraseña') }}" />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-4 d-flex justify-content-end">
                <x-secondary-button type="button" data-bs-dismiss="modal">
                    {{ __('Cancelar') }}
                </x-secondary-button>

                <x-danger-button class="ms-3">
                    <i class="bi bi-trash me-1"></i>{{ __('Eliminar cuenta') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
