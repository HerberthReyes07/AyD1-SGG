<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h2 class="mb-0"><i class="bi bi-clock-history me-2"></i>Historial de rutinas</h2>
                <small class="text-muted">Rutinas registradas en asignaciones anteriores de este socio</small>
            </div>
            <a href="{{ route('assignments.show', $trainerAssignment) }}"
                class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </x-slot>

    <div class="container py-4">

        @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
        </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <strong><i class="bi bi-list-check me-1"></i>Rutinas anteriores</strong>
            </div>
            <div class="card-body p-0">
                @if ($routineHistory->isEmpty())
                <div class="text-secondary text-center py-5">
                    <i class="bi bi-inbox display-6 d-block mb-2 opacity-50"></i>
                    Este socio no tiene rutinas de asignaciones anteriores.
                </div>
                @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Nombre</th>
                                <th>Ejercicios</th>
                                <th>Entrenador</th>
                                <th>Período</th>
                                <th class="text-end pe-3">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($routineHistory as $routine)
                            <tr>
                                <td class="ps-3">{{ $routine->name }}</td>
                                <td>{{ $routine->routine_exercises_count }}</td>
                                <td>
                                    {{ $routine->trainerAssignment->trainer->user->first_name }}
                                    {{ $routine->trainerAssignment->trainer->user->last_name }}
                                </td>
                                <td class="small text-secondary">
                                    {{ $routine->trainerAssignment->assignment_date->format('d/m/Y') }} —
                                    {{ $routine->trainerAssignment->end_date->format('d/m/Y') }}
                                </td>
                                <td class="text-end pe-3">
                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                        data-bs-toggle="modal" data-bs-target="#routine-detail-{{ $routine->id }}">
                                        <i class="bi bi-eye"></i> Ver
                                    </button>
                                    <form method="POST"
                                        action="{{ route('trainer.assignments.routines.duplicate', [$trainerAssignment, $routine]) }}"
                                        class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-arrow-repeat"></i> Reutilizar
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
    </div>

    @foreach ($routineHistory as $routine)
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
</x-app-layout>
