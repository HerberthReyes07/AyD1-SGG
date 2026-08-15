<x-app-layout>
    <x-slot name="header">
    <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-0">{{ __('Información del Socio') }}</h2>
                <small class="text-muted">
                    {{ __('Muestra la información detallada de un socio') }}
                </small>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('members.index') }}" class="btn btn-primary">
                    {{ __('Regresar') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="container-xl" style="max-width: 40rem;">

            <div class="card shadow-sm">
                <div class="card-body">

                    <!-- First Name -->
                    <div class="mb-3">
                        <x-input-label :value="__('Nombre')" />
                        <div class="form-control mt-1 bg-light">
                            {{ $member->first_name }}
                        </div>
                    </div>

                    <!-- Last Name -->
                    <div class="mb-3">
                        <x-input-label :value="__('Apellido')" />
                        <div class="form-control mt-1 bg-light">
                            {{ $member->last_name }}
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <x-input-label :value="__('Email')" />
                        <div class="form-control mt-1 bg-light">
                            {{ $member->email }}
                        </div>
                    </div>

                    <!-- Phone -->
                    <div class="mb-3">
                        <x-input-label :value="__('Teléfono')" />
                        <div class="form-control mt-1 bg-light">
                            {{ $member->phone_number ?? __('No registrado') }}
                        </div>
                    </div>

                    <!-- Role -->
                    <div class="mb-3">
                        <x-input-label :value="__('Rol')" />
                        <div class="form-control mt-1 bg-light">
                            {{ ucfirst($member->role->name) }}
                        </div>
                    </div>

                    <!-- Specialty (Trainer only) -->
                    @if($member->role->name === 'trainer')
                    <div class="mb-3">
                        <x-input-label :value="__('Especialidad del Entrenador')" />
                        <div class="form-control mt-1 bg-light">
                            {{ $member->trainer?->specialty?->name ?? __('No registrada') }}
                        </div>
                    </div>
                    @endif

                    <!-- Status -->
                    <div class="mb-3">
                        <x-input-label :value="__('Estado')" />
                        <div class="form-control mt-1 bg-light">
                            {{ $member->is_active ? __('Activo') : __('Inactivo') }}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
