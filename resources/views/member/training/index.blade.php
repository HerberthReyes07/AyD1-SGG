<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-0">{{ __('Mi entrenamiento') }}</h2>
                <small class="text-muted">
                    {{ __('Consulta tu progreso y asignaciones de entrenamiento') }}
                </small>
            </div>
        </div>
    </x-slot>

    <div class="container py-4">
        @if ($accessState === 'upgrade_required' || $accessState === 'pending_assignment')
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="bi bi-clipboard2-pulse text-secondary" style="font-size: 3rem;"></i>
                <p class="mt-3 text-secondary mb-0">
                    @if ($accessState === 'upgrade_required')
                    Esta sección requiere Plan Élite.
                    @else
                    Tu plan incluye entrenador personal. Estamos gestionando tu asignación.
                    @endif
                </p>
            </div>
        </div>
        @else
        <ul class="nav nav-tabs mb-4">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#summary" type="button">
                    Resumen
                </button>
            </li>
            @if ($accessState === 'active')
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#routine" type="button">Mi rutina</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#progress" type="button">Mi
                    progreso</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#trainer-history" type="button">
                    Mis entrenadores
                </button>
            </li>
            @endif
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="summary">
                @include('member.training.partials.summary')
            </div>

            @if ($accessState === 'active')
            <div class="tab-pane fade" id="routine">
                @include('member.training.partials.routine')
            </div>
            <div class="tab-pane fade" id="progress">
                @include('member.training.partials.progress')
            </div>
            <div class="tab-pane fade" id="trainer-history">
                @include('member.training.partials.trainer-history')
            </div>
            @endif
        </div>
        @endif
    </div>
</x-app-layout>
