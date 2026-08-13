<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="h6 mb-0">Rutinas</h2>
    <div class="d-flex gap-2">
        <a href="{{ route('assignments.routines.create', $trainerAssignment) }}" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-lg"></i> Nueva rutina
        </a>
        @if ($trainerAssignment->member->trainerAssignments()->whereNotNull('end_date')->exists())
        <a href="{{ route('trainer.assignments.routines.history', $trainerAssignment) }}"
            class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-clock-history"></i> Historial
        </a>
        @endif
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if ($routines->isEmpty())
        <p class="text-secondary text-center mb-0 py-3">
            Todavía no hay rutinas creadas para este socio.
        </p>
        @else
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Ejercicios</th>
                        <th>Estado</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($routines as $routine)
                    <tr>
                        <td>{{ $routine->name }}</td>
                        <td>{{ $routine->routine_exercises_count }}</td>
                        <td>
                            <span class="badge {{ $routine->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $routine->is_active ? 'Activa' : 'Inactiva' }}
                            </span>
                        </td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                                data-bs-target="#routine-detail-{{ $routine->id }}">
                                Ver
                            </button>
                            <a href="{{ route('routines.edit', $routine) }}" class="btn btn-sm btn-outline-primary">
                                Editar
                            </a>

                            <form action="{{ route('routines.toggle-active', $routine) }}" method="POST"
                                class="d-inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="btn btn-sm btn-outline-{{ $routine->is_active ? 'warning' : 'success' }}">
                                    {{ $routine->is_active ? 'Desactivar' : 'Activar' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

@foreach ($routines as $routine)
<x-modal :name="'routine-detail-' . $routine->id" max-width="lg">
    <div class="modal-header">
        <h5 class="modal-title">{{ $routine->name }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
        @if ($routine->description)
        <p class="text-secondary">{{ $routine->description }}</p>
        @endif
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>Ejercicio</th>
                    <th>Series</th>
                    <th>Reps</th>
                    <th>Día</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($routine->routineExercises as $exercise)
                <tr>
                    <td>{{ $exercise->exercise_name }}</td>
                    <td>{{ $exercise->sets }}</td>
                    <td>{{ $exercise->reps }}</td>
                    <td>{{ $exercise->suggested_day->label() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-modal>
@endforeach
