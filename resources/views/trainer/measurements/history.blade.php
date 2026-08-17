<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h2 class="mb-0"><i class="bi bi-clock-history me-2"></i>Historial de mediciones</h2>
                <small class="text-muted">Mediciones registradas en asignaciones anteriores de este socio</small>
            </div>
            <a href="{{ route('assignments.show', $trainerAssignment) }}"
                class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Volver
            </a>
        </div>
    </x-slot>

    <div class="container py-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <strong><i class="bi bi-rulers me-1"></i>Mediciones anteriores</strong>
            </div>
            <div class="card-body p-0">
                @if ($measurementHistory->isEmpty())
                <div class="text-secondary text-center py-5">
                    <i class="bi bi-inbox display-6 d-block mb-2 opacity-50"></i>
                    Este socio no tiene mediciones de asignaciones anteriores.
                </div>
                @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Fecha</th>
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
                                <td class="ps-3">{{ $measurement->date->format('d/m/Y') }}</td>
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
