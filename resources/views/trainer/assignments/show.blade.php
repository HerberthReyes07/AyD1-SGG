<x-app-layout>

    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h2 class="mb-0"><i class="bi bi-clipboard2-pulse me-2"></i>{{ __('Centro de control de entrenamiento') }}</h2>
                <small class="text-muted">
                    {{ $trainerAssignment->member->user->first_name }}
                    {{ $trainerAssignment->member->user->last_name }}
                </small>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('assignments.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Volver a mis socios
                </a>
            </div>
        </div>
    </x-slot>

    <div class="container py-4">

        @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
        @endif

        @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
        @endif

        <ul class="nav nav-tabs mb-4 flex-nowrap overflow-auto text-nowrap">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#summary" type="button">
                    <i class="bi bi-person-vcard me-1"></i> Resumen
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#routine" type="button">
                    <i class="bi bi-list-check me-1"></i> Rutinas
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#measurements" type="button">
                    <i class="bi bi-rulers me-1"></i> Mediciones
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#nutrition" type="button">
                    <i class="bi bi-egg-fried me-1"></i> Nutricion
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#nutritional-observations" type="button">
                    <i class="bi bi-chat-square-text me-1"></i> Observaciones nutricionales
                </button>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="summary">
                @include('trainer.assignments.partials.summary')
            </div>

            <div class="tab-pane fade" id="routine">
                @include('trainer.assignments.partials.routines')
            </div>

            <div class="tab-pane fade" id="measurements">
                @include('trainer.assignments.partials.measurements')
            </div>

            <div class="tab-pane fade" id="nutrition">
                @include('trainer.assignments.partials.nutrition')
            </div>

            <div class="tab-pane fade" id="nutritional-observations">
                @include('trainer.assignments.partials.nutritional-observations')
            </div>
        </div>
    </div>
</x-app-layout>
