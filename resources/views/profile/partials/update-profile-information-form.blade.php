<section>
    <header>
        <h2 class="h5 fw-medium text-dark">
            <i class="bi bi-person-lines-fill me-2"></i>Informacion del perfil
        </h2>

        <p class="mt-1 small text-secondary">
            Actualiza la informacion de tu cuenta y correo electronico.
        </p>
    </header>

    <form
        id="send-verification"
        method="post"
        action="{{ route('verification.send') }}"
    >
        @csrf
    </form>

    <form
        method="post"
        action="{{ route('profile.update') }}"
        class="mt-4"
    >
        @csrf
        @method('patch')

        <div class="mb-3">
            <x-input-label
                for="first_name"
                :value="__('Nombre')"
            />

            <x-text-input
                id="first_name"
                name="first_name"
                type="text"
                class="mt-1 d-block w-100"
                :value="old('first_name', $user->first_name)"
                required
                autofocus
                autocomplete="given-name"
            />

            <x-input-error
                class="mt-2"
                :messages="$errors->get('first_name')"
            />
        </div>

        <div class="mb-3">
            <x-input-label
                for="last_name"
                :value="__('Apellido')"
            />

            <x-text-input
                id="last_name"
                name="last_name"
                type="text"
                class="mt-1 d-block w-100"
                :value="old('last_name', $user->last_name)"
                required
                autocomplete="family-name"
            />

            <x-input-error
                class="mt-2"
                :messages="$errors->get('last_name')"
            />
        </div>

        <div class="mb-3">
            <x-input-label
                for="phone_number"
                :value="__('Telefono')"
            />

            <x-text-input
                id="phone_number"
                name="phone_number"
                type="text"
                class="mt-1 d-block w-100"
                :value="old('phone_number', $user->phone_number)"
                autocomplete="tel"
            />

            <x-input-error
                class="mt-2"
                :messages="$errors->get('phone_number')"
            />
        </div>

        <div class="mb-3">
            <x-input-label
                for="email"
                :value="__('Correo electronico')"
            />

            <x-text-input
                id="email"
                name="email"
                type="email"
                class="mt-1 d-block w-100"
                :value="old('email', $user->email)"
                required
                autocomplete="username"
            />

            <x-input-error
                class="mt-2"
                :messages="$errors->get('email')"
            />

            @if (
                $user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail &&
                ! $user->hasVerifiedEmail()
            )

                <div>
                    <p class="small mt-2 text-dark">
                        Tu correo electronico no esta verificado.

                        <button
                            form="send-verification"
                            type="submit"
                            class="btn btn-link btn-sm p-0 align-baseline"
                        >
                            Reenviar correo de verificacion
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 fw-medium small text-success">
                            Se envio un nuevo enlace de verificacion.
                        </p>
                    @endif
                </div>

            @endif
        </div>

        <div class="d-flex align-items-center gap-3">

            <x-primary-button>
                <i class="bi bi-check-lg me-1"></i>Guardar
            </x-primary-button>

            @if (session('status') === 'profile-updated')
                <p class="small text-secondary mb-0 fade-out-message">
                    Guardado.
                </p>
            @endif

        </div>
    </form>
</section>