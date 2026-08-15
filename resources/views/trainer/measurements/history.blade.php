<x-app-layout>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h4 mb-0">Historial de mediciones</h1>
            <a href="{{ route('assignments.show', $trainerAssignment) }}"
                class="btn btn-sm btn-outline-secondary">
                &larr; Volver
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                @if ($measurementHistory->isEmpty())
                <p class="text-secondary text-center mb-0 py-3">
                    Este socio no tiene mediciones de asignaciones anteriores.
                </p>
                @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Peso (kg)</th>
                                <th>Cintura (cm)</th>
                                <th>Brazo (cm)</th>
                                <th>Pierna (cm)</th>
                                <th>Entrenador</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($measurementHistory as $measurement)
                            <tr>
                                <td>{{ $measurement->date->format('d/m/Y') }}</td>
                                <td>{{ $measurement->weight }}</td>
                                <td>{{ $measurement->waist_measurement ?? '—' }}</td>
                                <td>{{ $measurement->arm_measurement ?? '—' }}</td>
                                <td>{{ $measurement->leg_measurement ?? '—' }}</td>
                                <td>
                                    {{ $measurement->trainerAssignment->trainer->user->first_name }}
                                    {{ $measurement->trainerAssignment->trainer->user->last_name }}
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
</x-app-layout>
