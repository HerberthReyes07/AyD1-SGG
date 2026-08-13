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
        </div>
    </div>
</div>
