@if ($currentAssignment)
<div class="card">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <p class="text-secondary small mb-1">Entrenador</p>
                <p class="mb-0">
                    {{ $currentAssignment->trainer->user->first_name }}
                    {{ $currentAssignment->trainer->user->last_name }}
                </p>
            </div>
            <div class="col-md-6">
                <p class="text-secondary small mb-1">Especialidad</p>
                <p class="mb-0">{{ $currentAssignment->trainer->specialty->name }}</p>
            </div>
            <div class="col-md-6">
                <p class="text-secondary small mb-1">Desde</p>
                <p class="mb-0">{{ $currentAssignment->assignment_date->format('d/m/Y') }}</p>
            </div>
            <div class="col-md-6">
                <p class="text-secondary small mb-1">Objetivo</p>
                <p class="mb-0">{{ $currentAssignment->goal ?? 'Aún no definido por tu entrenador' }}</p>
            </div>
        </div>
    </div>
</div>
@else
{{-- accessState === 'history_only', ya no tiene asignación activa --}}
<div class="card">
    <div class="card-body text-center py-4">
        <p class="text-secondary mb-0">
            No tienes un entrenador asignado actualmente. Revisa tu historial para ver asignaciones anteriores.
        </p>
    </div>
</div>
@endif
