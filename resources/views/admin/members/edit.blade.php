<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-0"><i class="bi bi-pencil-square me-2"></i>{{ __('Editar Socio') }}</h2>
                <small class="text-muted">
                    {{ __('Edita la información de un socio existente') }}
                </small>
            </div>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="container-xl" style="max-width: 40rem;">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST" action="{{ route('members.update', $member->id) }}">
                        @csrf
                        @method('PUT')


                        <!-- First Name -->
                        <div class="mb-3">
                            <x-input-label for="first_name" :value="__('Nombre')" />
                            <x-text-input id="first_name" name="first_name" type="text" class="mt-1 d-block w-100" :value="old('first_name', $member->first_name)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
                        </div>

                        <!-- Last Name -->
                        <div class="mb-3">
                            <x-input-label for="last_name" :value="__('Apellido')" />
                            <x-text-input id="last_name" name="last_name" type="text" class="mt-1 d-block w-100" :value="old('last_name', $member->last_name)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 d-block w-100" :value="old('email', $member->email)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                        </div>

                        <!-- Phone -->
                        <div class="mb-3">
                            <x-input-label for="phone_number" :value="__('Teléfono')" />
                            <x-text-input id="phone_number" name="phone_number" type="text" inputmode="tel" oninput="this.value = this.value.replace(/[^0-9+\-() ]/g, '')" class="mt-1 d-block w-100" :value="old('phone_number', $member->phone_number)" />
                            <x-input-error class="mt-2" :messages="$errors->get('phone_number')" />
                        </div>

                        <!-- Birth Date -->
                        <div class="mb-3">
                            <x-input-label for="birth_date" :value="__('Fecha de Nacimiento')" />
                            <x-text-input id="birth_date" name="birth_date" type="date" class="mt-1 d-block w-100" :value="old('birth_date', $member->member?->birth_date?->format('Y-m-d'))" required />
                            <x-input-error class="mt-2" :messages="$errors->get('birth_date')" />
                        </div>

                        <!-- Password (Optional) -->
                        <div class="mb-3">
                            <x-input-label for="password" :value="__('Contraseña (Dejar en blanco para no modificar)')" />
                            <x-text-input id="password" name="password" type="password" class="mt-1 d-block w-100" />
                            <x-input-error class="mt-2" :messages="$errors->get('password')" />
                        </div>

                        <!-- Specialty (Trainer only) -->
                        @if($member->role->name === 'trainer')
                            <div class="mb-3">
                                <x-input-label for="specialty_id" :value="__('Especialidad del Entrenador')" />
                                <select id="specialty_id" name="specialty_id" class="form-select mt-1" required>
                                    <option value="">{{ __('Seleccione una especialidad') }}</option>
                                    @foreach($specialties as $specialty)
                                        <option value="{{ $specialty->id }}" {{ old('specialty_id', $member->trainer?->specialty_id) == $specialty->id ? 'selected' : '' }}>
                                            {{ $specialty->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('specialty_id')" />
                            </div>
                        @endif

                        <!-- Status -->
                        <div class="mb-3">
                            <x-input-label for="is_active" :value="__('Estado')" />
                            <select id="is_active" name="is_active" class="form-select mt-1" required>
                                <option value="1" {{ old('is_active', $member->is_active) ? 'selected' : '' }}>{{ __('Activo') }}</option>
                                <option value="0" {{ !old('is_active', $member->is_active) ? 'selected' : '' }}>{{ __('Inactivo') }}</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('is_active')" />
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('members.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-lg me-1"></i>{{ __('Cancelar') }}
                            </a>
                            <x-primary-button>
                                <i class="bi bi-check-lg me-1"></i>{{ __('Guardar Cambios') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
