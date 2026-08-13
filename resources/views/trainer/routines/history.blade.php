<x-app-layout>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h4 mb-0">Historial de rutinas</h1>
            <a href="{{ route('assignments.show', $trainerAssignment) }}"
                class="btn btn-sm btn-outline-secondary">
                &larr; Volver
            </a>
        </div>

        @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
        @endif

        <div class="card">
            <div class="card-body">
                @if ($routineHistory->isEmpty())
                <p class="text-secondary text-center mb-0 py-3">
                    Este socio no tiene rutinas de asignaciones anteriores.
                </p>
                @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Ejercicios</th>
                                <th>Entrenador</th>
                                <th>Período</th>
                                <th class="text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($routineHistory as $routine)
                            <tr>
                                <td>{{ $routine->name }}</td>
                                <td>{{ $routine->routine_exercises_count }}</td>
                                <td>
                                    {{ $routine->trainerAssignment->trainer->user->first_name }}
                                    {{ $routine->trainerAssignment->trainer->user->last_name }}
                                </td>
                                <td class="small text-secondary">
                                    {{ $routine->trainerAssignment->assignment_date->format('d/m/Y') }} —
                                    {{ $routine->trainerAssignment->end_date->format('d/m/Y') }}
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-secondary"
                                        data-bs-toggle="modal" data-bs-target="#routine-detail-{{ $routine->id }}">
                                        Ver
                                    </button>
                                    <form method="POST"
                                        action="{{ route('trainer.assignments.routines.duplicate', [$trainerAssignment, $routine]) }}"
                                        class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-primary">
                                            Reutilizar
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
