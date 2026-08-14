<div class="card mb-4">
    <div class="card-body">
        <h2 class="h6 mb-3">Evolución en el tiempo</h2>
        <canvas id="progress-chart" height="90"></canvas>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h2 class="h6 mb-3">Mediciones de tu asignación actual</h2>
        @if ($measurements->isEmpty())
        <p class="text-secondary text-center mb-0 py-3">
            Tu entrenador todavía no ha registrado mediciones.
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
                    </tr>
                </thead>
                <tbody>
                    @foreach ($measurements as $measurement)
                    <tr>
                        <td>{{ $measurement->date->format('d/m/Y') }}</td>
                        <td>{{ $measurement->weight }}</td>
                        <td>{{ $measurement->waist_measurement ?? '—' }}</td>
                        <td>{{ $measurement->arm_measurement ?? '—' }}</td>
                        <td>{{ $measurement->leg_measurement ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const chartData = @json($chartData);

        if (chartData.labels.length === 0) return;

        new Chart(document.getElementById('progress-chart'), {
            type: 'line',
            data: {
                labels: chartData.labels,
                datasets: [
                    {
                        label: 'Peso (kg)',
                        data: chartData.weight,
                        borderColor: '#0d6efd',
                        tension: 0.2,
                    },
                    {
                        label: 'Cintura (cm)',
                        data: chartData.waist,
                        borderColor: '#198754',
                        tension: 0.2,
                        spanGaps: true,
                    },
                    {
                        label: 'Brazo (cm)',
                        data: chartData.arm,
                        borderColor: '#ffc107',
                        tension: 0.2,
                        spanGaps: true,
                    },
                    {
                        label: 'Pierna (cm)',
                        data: chartData.leg,
                        borderColor: '#dc3545',
                        tension: 0.2,
                        spanGaps: true,
                    },
                ],
            },
            options: {
                responsive: true,
                interaction: { mode: 'index', intersect: false },
                scales: {
                    y: { beginAtZero: false },
                },
            },
        });
    });
</script>
@endpush
