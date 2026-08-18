<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h2 class="mb-0">
                    <i class="bi bi-clipboard2-pulse text-primary me-2"></i>
                    {{ __('Reporte: Historial de progreso físico') }}
                </h2>
                <small class="text-muted">
                    {{ __('Muestra el historial de progreso físico de un socio en un rango de fechas específico') }}
                </small>
            </div>
        </div>
    </x-slot>

    <div class="container py-4">

        {{-- Filtros --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('physical-progress.index') }}" class="row g-3 align-items-end">
                    <div class="col-md-5">
                        <label for="member_id" class="form-label">Socio</label>
                        <select name="member_id" id="member_id"
                            class="form-select @error('member_id') is-invalid @enderror" required>
                            <option value="">Selecciona un socio</option>
                            @foreach ($members as $member)
                            <option value="{{ $member->user_id }}" @selected($filters['member_id']==$member->user_id)>
                                {{ $member->user->first_name }} {{ $member->user->last_name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="start_date" class="form-label">Desde (opcional)</label>
                        <input type="date" class="form-control" id="start_date" name="start_date"
                            value="{{ $filters['start_date'] ?? '' }}">
                    </div>
                    <div class="col-md-3">
                        <label for="end_date" class="form-label">Hasta (opcional)</label>
                        <input type="date" class="form-control" id="end_date" name="end_date"
                            value="{{ $filters['end_date'] ?? '' }}">
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        @if ($report)
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h4 class="mb-1">
                            {{ $report['member']->user->first_name }} {{ $report['member']->user->last_name }}
                        </h4>
                        <small class="text-muted">
                            {{ $filters['start_date'] ?? 'Inicio del historial' }} — {{ $filters['end_date'] ?? 'Actualidad' }}
                        </small>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-sm btn-outline-dark fw-semibold" id="export-image">
                            <i class="bi bi-image me-1"></i> Imagen del gráfico
                        </button>
                        <a href="{{ route('physical-progress.export-excel', $filters) }}"
                            class="btn btn-sm btn-outline-success fw-semibold">
                            <i class="bi bi-file-earmark-excel me-1"></i> Excel/CSV
                        </a>
                        <form method="POST" action="{{ route('physical-progress.export-pdf') }}" id="export-pdf-form" class="d-inline">
                            @csrf
                            <input type="hidden" name="member_id" value="{{ $filters['member_id'] }}">
                            <input type="hidden" name="start_date" value="{{ $filters['start_date'] ?? '' }}">
                            <input type="hidden" name="end_date" value="{{ $filters['end_date'] ?? '' }}">
                            <input type="hidden" name="chart_image" id="chart_image_input">
                            <button type="submit" class="btn btn-sm btn-outline-danger fw-semibold">
                                <i class="bi bi-file-earmark-pdf me-1"></i> PDF
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        @if ($report['measurements']->isEmpty())
        <div class="card shadow-sm">
            <div class="card-body text-center py-4">
                <i class="bi bi-inbox fs-1 d-block mb-2 text-muted opacity-50"></i>
                <p class="text-secondary mb-0">Este socio no tiene mediciones registradas en el rango seleccionado.</p>
            </div>
        </div>
        @else
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <canvas id="progress-chart" height="90"></canvas>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <strong>Mediciones registradas</strong>
            </div>
            <div class="card-body p-0">
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
                            @foreach ($report['measurements'] as $measurement)
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
            </div>
        </div>
        @endif
        @endif
    </div>

    @if ($report && $report['measurements']->isNotEmpty())
    @php
    $chartMeasurements = $report['measurements']->map(function ($m) {
    return [
    'date' => $m->date->format('d/m/Y'),
    'weight' => $m->weight,
    'waist' => $m->waist_measurement,
    'arm' => $m->arm_measurement,
    'leg' => $m->leg_measurement,
    ];
    })->values();

    $chartAssignmentPeriods = $report['assignmentPeriods']->map(function ($a) {
    return [
    'trainer' => $a->trainer->user->first_name . ' ' . $a->trainer->user->last_name,
    'assignment_date' => $a->assignment_date->format('d/m/Y'),
    ];
    })->values();
    @endphp
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation@3.0.1/dist/chartjs-plugin-annotation.min.js">
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
                const measurements = @json($chartMeasurements);

                const assignmentPeriods = @json($chartAssignmentPeriods);

                const labels = measurements.map(m => m.date);

                // Construir anotaciones: una línea vertical por cada cambio de entrenador
                // (se omite la primera asignación, ya que no representa un "cambio")
                const annotations = {};
                assignmentPeriods.forEach((period, index) => {
                    console.log('Procesando periodo de asignación:', period);
                    //if (index === 0) return; // primera asignación, no es un cambio

                    // Buscar la medición más cercana (en o después) a la fecha de esta asignación
                    const matchIndex = labels.findIndex(label => {
                        const [d, m, y] = label.split('/');
                        const [pd, pm, py] = period.assignment_date.split('/');
                        return new Date(y, m - 1, d) >= new Date(py, pm - 1, pd);
                    });

                    if (matchIndex === -1) return;

                    annotations['change' + index] = {
                        type: 'line',
                        xMin: labels[matchIndex],
                        xMax: labels[matchIndex],
                        borderColor: '#6c757d',
                        borderWidth: 2,
                        borderDash: [6, 6],
                        label: {
                            display: true,
                            content: 'Nuevo entrenador: ' + period.trainer,
                            position: 'start',
                            backgroundColor: '#6c757d',
                            font: { size: 10 },
                        },
                    };
                });

                window.progressChart = new Chart(document.getElementById('progress-chart'), {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [
                            { label: 'Peso (kg)', data: measurements.map(m => m.weight), borderColor: '#0d6efd', tension: 0.2 },
                            { label: 'Cintura (cm)', data: measurements.map(m => m.waist), borderColor: '#198754', tension: 0.2, spanGaps: true },
                            { label: 'Brazo (cm)', data: measurements.map(m => m.arm), borderColor: '#ffc107', tension: 0.2, spanGaps: true },
                            { label: 'Pierna (cm)', data: measurements.map(m => m.leg), borderColor: '#dc3545', tension: 0.2, spanGaps: true },
                        ],
                    },
                    options: {
                        responsive: true,
                        interaction: { mode: 'index', intersect: false },
                        scales: { y: { beginAtZero: false } },
                        plugins: { annotation: { annotations: annotations } },
                    },
                });

                document.getElementById('export-image').addEventListener('click', function () {
                    const link = document.createElement('a');
                    link.download = 'progreso-fisico.png';
                    link.href = window.progressChart.toBase64Image();
                    link.click();
                });

                document.getElementById('export-pdf-form').addEventListener('submit', function () {
                    document.getElementById('chart_image_input').value = window.progressChart.toBase64Image();
                });
            });
    </script>
    @endpush
    @endif
</x-app-layout>
