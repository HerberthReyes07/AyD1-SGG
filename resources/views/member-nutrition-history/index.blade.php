<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="mb-0"><i class="bi bi-graph-up-arrow me-2"></i>Historial nutricional</h2>
            <small class="text-muted">
                Revisa tus tendencias de consumo de los ultimos dias
            </small>
        </div>
    </x-slot>

    <div class="container-xl py-4">

        <div class="btn-group mb-4" role="group">
            <a
                href="{{ route('nutrition-history.index', ['days' => 7]) }}"
                class="btn btn-outline-primary {{ $days === 7 ? 'active' : '' }}"
            >
                <i class="bi bi-calendar3 me-1"></i>Ultimos 7 dias
            </a>

            <a
                href="{{ route('nutrition-history.index', ['days' => 30]) }}"
                class="btn btn-outline-primary {{ $days === 30 ? 'active' : '' }}"
            >
                <i class="bi bi-calendar3 me-1"></i>Ultimos 30 dias
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Fecha</th>
                                <th>Calorias</th>
                                <th>Proteina</th>
                                <th>Carbohidratos</th>
                                <th>Grasas</th>
                                <th class="pe-3">Meta</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($history as $day)
                                <tr>
                                    <td class="ps-3">{{ $day['date']->translatedFormat('d M Y') }}</td>
                                    <td>{{ number_format($day['calories'], 0) }} kcal</td>
                                    <td>{{ number_format($day['protein_g'], 1) }} g</td>
                                    <td>{{ number_format($day['carbs_g'], 1) }} g</td>
                                    <td>{{ number_format($day['fat_g'], 1) }} g</td>
                                    <td class="pe-3">
                                        @if ($day['comparison'])
                                            @php
                                                $badgeClass = match ($day['comparison']['status']) {
                                                    'below' => 'bg-info',
                                                    'above' => 'bg-warning',
                                                    default => 'bg-success',
                                                };

                                                $badgeLabel = match ($day['comparison']['status']) {
                                                    'below' => 'Por debajo',
                                                    'above' => 'Por encima',
                                                    default => 'En rango',
                                                };
                                            @endphp

                                            <span class="badge {{ $badgeClass }}">{{ $badgeLabel }}</span>
                                        @else
                                            <span class="text-muted small">Sin meta</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mt-4">
            <div class="card-header bg-white">
                <strong><i class="bi bi-chat-square-text me-1"></i>Recomendaciones de tu entrenador</strong>
            </div>

            <div class="card-body">
                @if (! $assignment)
                    <p class="text-muted text-center mb-0 py-2">
                        <i class="bi bi-inbox d-block mb-2" style="font-size: 2.5rem;"></i>
                        No tienes un entrenador asignado.
                    </p>
                @elseif ($observations->isEmpty())
                    <p class="text-muted text-center mb-0 py-2">
                        <i class="bi bi-inbox d-block mb-2" style="font-size: 2.5rem;"></i>
                        Tu entrenador todavia no ha dejado observaciones.
                    </p>
                @else
                    @foreach ($observations as $observation)
                        <div class="mb-3 pb-3 {{ ! $loop->last ? 'border-bottom' : '' }}">
                            <p class="text-muted small mb-1">{{ $observation->date->format('d/m/Y') }}</p>
                            <p class="mb-0">{{ $observation->observation }}</p>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>

    </div>
</x-app-layout>
