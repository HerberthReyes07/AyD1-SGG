<x-guest-layout>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div>
            <x-input-label
                for="first_name"
                :value="__('Nombre')"
            />

            <x-text-input
                id="first_name"
                class="block mt-1 w-full"
                type="text"
                name="first_name"
                :value="old('first_name')"
                required
                autofocus
                autocomplete="given-name"
            />

            <x-input-error
                :messages="$errors->get('first_name')"
                class="mt-2"
            />
        </div>

        <div class="mt-4">
            <x-input-label
                for="last_name"
                :value="__('Apellido')"
            />

            <x-text-input
                id="last_name"
                class="block mt-1 w-full"
                type="text"
                name="last_name"
                :value="old('last_name')"
                required
                autocomplete="family-name"
            />

            <x-input-error
                :messages="$errors->get('last_name')"
                class="mt-2"
            />
        </div>

        <div class="mt-4">
            <x-input-label
                for="phone_number"
                :value="__('Telefono')"
            />

            <x-text-input
                id="phone_number"
                class="block mt-1 w-full"
                type="text"
                name="phone_number"
                :value="old('phone_number')"
                autocomplete="tel"
            />

            <x-input-error
                :messages="$errors->get('phone_number')"
                class="mt-2"
            />
        </div>

        <div class="mt-4">
            <x-input-label
                for="email"
                :value="__('Correo electronico')"
            />

            <x-text-input
                id="email"
                class="block mt-1 w-full"
                type="email"
                name="email"
                :value="old('email')"
                required
                autocomplete="username"
            />

            <x-input-error
                :messages="$errors->get('email')"
                class="mt-2"
            />
        </div>

        <div class="mt-4">
            <x-input-label
                for="password"
                :value="__('Contrasena')"
            />

            <x-text-input
                id="password"
                class="block mt-1 w-full"
                type="password"
                name="password"
                required
                autocomplete="new-password"
            />

            <x-input-error
                :messages="$errors->get('password')"
                class="mt-2"
            />
        </div>

        <div class="mt-4">
            <x-input-label
                for="password_confirmation"
                :value="__('Confirmar contrasena')"
            />

            <x-text-input
                id="password_confirmation"
                class="block mt-1 w-full"
                type="password"
                name="password_confirmation"
                required
                autocomplete="new-password"
            />

            <x-input-error
                :messages="$errors->get('password_confirmation')"
                class="mt-2"
            />
        </div>

        <div class="flex items-center justify-end mt-4">

            <a
                class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md"
                href="{{ route('login') }}"
            >
                Ya tienes una cuenta?
            </a>

            <x-primary-button class="ms-4">
                Registrarse
            </x-primary-button>

        </div>

    </form>

</x-guest-layout>