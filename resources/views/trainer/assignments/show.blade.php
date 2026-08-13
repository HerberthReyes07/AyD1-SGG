<x-app-layout>

    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-0">{{ __('Centro de control de entrenamiento') }}</h2>
                <small class="text-muted">
                    <h1 class="h5 mb-0">
                        {{ $trainerAssignment->member->user->first_name }}
                        {{ $trainerAssignment->member->user->last_name }}
                    </h1>
                </small>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('assignments.index') }}" class="btn btn-sm btn-secondary">
                    &larr; Volver a mis socios
                </a>
            </div>
        </div>
    </x-slot>

    <div class="container py-4">

        @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#summary" type="button">
                    Resumen
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#routine" type="button">
                    Rutinas
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#measurements" type="button">
                    Mediciones
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#nutritional-observations" type="button">
                    Observaciones nutricionales
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

            <div class="tab-pane fade" id="nutritional-observations">
                <p class="text-secondary">Próximamente.</p>
            </div>
        </div>
    </div>
</x-app-layout>
