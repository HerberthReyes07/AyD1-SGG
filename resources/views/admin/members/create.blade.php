<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 font-medium mb-0">
            {{ __('Registrar Socio') }}
        </h2>
    </x-slot>

    <div class="py-4">
        <div class="container-xl" style="max-width: 40rem;">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST" action="{{ route('members.store') }}">
                        @csrf

                        <!-- First Name -->
                        <div class="mb-3">
                            <x-input-label for="first_name" :value="__('Nombre')" />
                            <x-text-input id="first_name" name="first_name" type="text" class="mt-1 d-block w-100" :value="old('first_name')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
                        </div>

                        <!-- Last Name -->
                        <div class="mb-3">
                            <x-input-label for="last_name" :value="__('Apellido')" />
                            <x-text-input id="last_name" name="last_name" type="text" class="mt-1 d-block w-100" :value="old('last_name')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 d-block w-100" :value="old('email')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                        </div>

                        <!-- Phone -->
                        <div class="mb-3">
                            <x-input-label for="phone_number" :value="__('Teléfono')" />
                            <x-text-input id="phone_number" name="phone_number" type="text" class="mt-1 d-block w-100" :value="old('phone_number')" />
                            <x-input-error class="mt-2" :messages="$errors->get('phone_number')" />
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <x-input-label for="password" :value="__('Contraseña')" />
                            <x-text-input id="password" name="password" type="password" class="mt-1 d-block w-100" required />
                            <x-input-error class="mt-2" :messages="$errors->get('password')" />
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('members.index') }}" class="btn btn-outline-secondary">
                                {{ __('Cancelar') }}
                            </a>
                            <x-primary-button>
                                {{ __('Guardar') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
