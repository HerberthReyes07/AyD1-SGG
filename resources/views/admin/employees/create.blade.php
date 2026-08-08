<x-app-layout>
    <x-slot name="header">
        <h2 class="h4 font-medium mb-0">
            {{ __('Registrar Empleado') }}
        </h2>
    </x-slot>

    @php
        $roles = \App\Models\Role::whereIn('name', ['trainer', 'receptionist'])->get();
        $trainerRole = $roles->firstWhere('name', 'trainer');
    @endphp

    <div class="py-4">
        <div class="container-xl" style="max-width: 40rem;">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST" action="{{ route('employees.store') }}">
                        @csrf

                        <!-- Tipo de Empleado -->
                        <div class="mb-3">
                            <x-input-label for="role_id" :value="__('Tipo de Empleado')" />
                            <select id="role_id" name="role_id" class="form-select mt-1" required>
                                <option value="">{{ __('Seleccione un tipo') }}</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                                        
                                        {{ match($role->name) {
                                            'trainer' => __('Entrenador'),
                                            'receptionist' => __('Recepcionista'),
                                            default => $role->name,
                                        } }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('role_id')" />
                        </div>

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

                        <!-- Specialty (Trainer only) -->
                        <div class="mb-3" id="specialty-wrapper" style="display: none;">
                            <x-input-label for="specialty_id" :value="__('Especialidad del Entrenador')" />
                            <select id="specialty_id" name="specialty_id" class="form-select mt-1">
                                <option value="">{{ __('Seleccione una especialidad') }}</option>
                                @foreach($specialties as $specialty)
                                    <option value="{{ $specialty->id }}" {{ old('specialty_id') == $specialty->id ? 'selected' : '' }}>
                                        {{ $specialty->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('specialty_id')" />
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const roleSelect = document.getElementById('role_id');
            const specialtyWrapper = document.getElementById('specialty-wrapper');
            const specialtySelect = document.getElementById('specialty_id');
            const trainerRoleId = "{{ $trainerRole?->id ?? '' }}";

            function toggleSpecialty() {
                if (roleSelect.value === trainerRoleId && trainerRoleId !== '') {
                    specialtyWrapper.style.display = 'block';
                    specialtySelect.required = true;
                } else {
                    specialtyWrapper.style.display = 'none';
                    specialtySelect.required = false;
                }
            }

            roleSelect.addEventListener('change', toggleSpecialty);
            toggleSpecialty();
        });
    </script>
</x-app-layout>
