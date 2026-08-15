<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-0">{{ __('Editar Empleado') }}</h2>
                <small class="text-muted">
                    {{ __('Edita la información de un empleado existente') }}
                </small>
            </div>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="container-xl" style="max-width: 40rem;">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST" action="{{ route('employees.update', $employee->id) }}">
                        @csrf
                        @method('PUT')

                        <!-- Tipo de Empleado (Lectura Únicamente) -->
                        <div class="mb-3">
                            <x-input-label :value="__('Tipo de Empleado')" />
                            <input type="text" class="form-control mt-1" value="{{ $employee->role->name === 'trainer' ? __('Entrenador') : __('Recepcionista') }}" disabled />
                            <input type="hidden" name="role_id" value="{{ $employee->role_id }}" />
                        </div>

                        <!-- First Name -->
                        <div class="mb-3">
                            <x-input-label for="first_name" :value="__('Nombre')" />
                            <x-text-input id="first_name" name="first_name" type="text" class="mt-1 d-block w-100" :value="old('first_name', $employee->first_name)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
                        </div>

                        <!-- Last Name -->
                        <div class="mb-3">
                            <x-input-label for="last_name" :value="__('Apellido')" />
                            <x-text-input id="last_name" name="last_name" type="text" class="mt-1 d-block w-100" :value="old('last_name', $employee->last_name)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
                        </div>

                        <!-- Email -->
                        <div class="mb-3">
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 d-block w-100" :value="old('email', $employee->email)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                        </div>

                        <!-- Phone -->
                        <div class="mb-3">
                            <x-input-label for="phone_number" :value="__('Teléfono')" />
                            <x-text-input id="phone_number" name="phone_number" type="text" class="mt-1 d-block w-100" :value="old('phone_number', $employee->phone_number)" />
                            <x-input-error class="mt-2" :messages="$errors->get('phone_number')" />
                        </div>

                        <!-- Password (Optional) -->
                        <div class="mb-3">
                            <x-input-label for="password" :value="__('Contraseña (Dejar en blanco para no modificar)')" />
                            <x-text-input id="password" name="password" type="password" class="mt-1 d-block w-100" />
                            <x-input-error class="mt-2" :messages="$errors->get('password')" />
                        </div>

                        <!-- Specialty (Trainer only) -->
                        @if($employee->role->name === 'trainer')
                            <div class="mb-3">
                                <x-input-label for="specialty_id" :value="__('Especialidad del Entrenador')" />
                                <select id="specialty_id" name="specialty_id" class="form-select mt-1" required>
                                    <option value="">{{ __('Seleccione una especialidad') }}</option>
                                    @foreach($specialties as $specialty)
                                        <option value="{{ $specialty->id }}" {{ old('specialty_id', $employee->trainer?->specialty_id) == $specialty->id ? 'selected' : '' }}>
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
                                <option value="1" {{ old('is_active', $employee->is_active) ? 'selected' : '' }}>{{ __('Activo') }}</option>
                                <option value="0" {{ !old('is_active', $employee->is_active) ? 'selected' : '' }}>{{ __('Inactivo') }}</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('is_active')" />
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">
                                {{ __('Cancelar') }}
                            </a>
                            <x-primary-button>
                                {{ __('Guardar Cambios') }}
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
