<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-0"><i class="bi bi-person-plus me-2"></i>{{ __('Registrar Socio') }}</h2>
                <small class="text-muted">
                    {{ __('Registra la información de un nuevo socio') }}
                </small>
            </div>
        </div>
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
                            <x-text-input
                                id="phone_number"
                                name="phone_number"
                                type="text"
                                inputmode="tel"
                                oninput="this.value = this.value.replace(/[^0-9+\-() ]/g, '')"
                                class="mt-1 d-block w-100"
                                :value="old('phone_number')" />
                            <x-input-error class="mt-2" :messages="$errors->get('phone_number')" />
                        </div>

                        <!-- Birth Date -->
                        <div class="mb-3">
                            <x-input-label for="birth_date" :value="__('Fecha de Nacimiento')" />
                            <x-text-input id="birth_date" name="birth_date" type="date" class="mt-1 d-block w-100" :value="old('birth_date')" required />
                            <x-input-error class="mt-2" :messages="$errors->get('birth_date')" />
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <x-input-label for="password" :value="__('Contraseña')" />
                            <x-text-input id="password" name="password" type="password" class="mt-1 d-block w-100" required />
                            <x-input-error class="mt-2" :messages="$errors->get('password')" />
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('members.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-lg me-1"></i>{{ __('Cancelar') }}
                            </a>
                            <x-primary-button>
                                <i class="bi bi-check-lg me-1"></i>{{ __('Guardar') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>