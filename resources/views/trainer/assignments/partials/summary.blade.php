<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="h6 mb-0"><i class="bi bi-person-vcard me-1"></i> Resumen de la asignación</h2>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal"
            data-bs-target="#new-goal">
            <i class="bi bi-pencil"></i> Actualizar objetivo
        </button>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <p class="text-secondary small mb-1">Correo</p>
                <p class="mb-0">{{ $trainerAssignment->member->user->email }}</p>
            </div>
            <div class="col-md-6">
                <p class="text-secondary small mb-1">Teléfono</p>
                <p class="mb-0">{{ $trainerAssignment->member->user->phone_number ?? '—' }}</p>
            </div>
            <div class="col-md-6">
                <p class="text-secondary small mb-1">Fecha de asignación</p>
                <p class="mb-0">{{ $trainerAssignment->assignment_date->format('d/m/Y') }}</p>
            </div>
            <div class="col-md-6">
                <p class="text-secondary small mb-1">Objetivo</p>
                <p class="mb-0">{{ $trainerAssignment->goal ?? 'Sin objetivo definido' }}</p>
            </div>
            <div class="col-md-6">
                <p class="text-secondary small mb-1">Objetivo nutricional</p>
                <p class="mb-0">{{ $calorieGoal?->objective?->label() ?? 'Sin meta calorica definida' }}</p>
            </div>
        </div>
    </div>
</div>

<x-modal name="new-goal" max-width="md">
    <form method="POST" action="{{ route('assignments.update-goal', $trainerAssignment) }}">
        @csrf
        @method('PATCH')
        <div class="modal-header">
            <h5 class="modal-title" id="new-goalLabel">Actualizar objetivo</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="mb-3">
                <label for="goal" class="form-label">Objetivo</label>
                <textarea class="form-control @error('goal') is-invalid @enderror" id="goal" name="goal" rows="5" required>{{ old('goal', $trainerAssignment->goal ?? '') }}</textarea>
                <x-input-error :messages="$errors->get('goal')" class="mt-2" />
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Guardar</button>
        </div>
    </form>
</x-modal>
