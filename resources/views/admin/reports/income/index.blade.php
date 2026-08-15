<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-0 fw-bold">{{ __('Reporte de Ingresos') }}</h2>
                <small class="text-muted">
                    {{ __('Ingresos totales y desglose por plan de membresía agrupados por semana o por mes') }}
                </small>
            </div>
        </div>
    </x-slot>

    <div class="container py-4">

        {{-- Filtros --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <form method="GET" action="{{ route('reports.income.index') }}" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="group_by" class="form-label fw-semibold">Agrupar por</label>
                        <select name="group_by" id="group_by" class="form-select">
                            <option value="month" @selected($report['groupBy'] === 'month')>Mes</option>
                            <option value="week" @selected($report['groupBy'] === 'week')>Semana</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="start_date" class="form-label fw-semibold">Fecha inicio (opcional)</label>
                        <input type="date" class="form-control" id="start_date" name="start_date"
                            value="{{ $report['startDate'] ?? '' }}">
                    </div>
                    <div class="col-md-3">
                        <label for="end_date" class="form-label fw-semibold">Fecha fin (opcional)</label>
                        <input type="date" class="form-control" id="end_date" name="end_date"
                            value="{{ $report['endDate'] ?? '' }}">
                    </div>
                    <div class="col-md-3 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100 fw-semibold">
                            <i class="bi bi-filter me-1"></i> Filtrar
                        </button>
                        <a href="{{ route('reports.income.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Barra de Exportación y Acciones --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <span class="badge bg-primary px-3 py-2 fs-6">
                    Agrupación: {{ $report['groupBy'] === 'week' ? 'Semanal' : 'Mensual' }}
                </span>
                @if($report['startDate'] || $report['endDate'])
                    <span class="badge bg-secondary px-3 py-2 fs-6 ms-1">
                        {{ $report['startDate'] ?? 'Inicio' }} a {{ $report['endDate'] ?? 'Hoy' }}
                    </span>
                @endif
            </div>

            <div class="d-flex gap-2 flex-wrap">
                {{-- Botón Exportar Imagen --}}
                <button type="button" class="btn btn-sm btn-outline-dark fw-semibold" id="export-image-btn">
                    <i class="bi bi-image me-1"></i> Exportar Imagen (PNG)
                </button>

                {{-- Botón Exportar Excel --}}
                <a href="{{ route('reports.income.export-excel', request()->query()) }}" class="btn btn-sm btn-outline-success fw-semibold">
                    <i class="bi bi-file-earmark-excel me-1"></i> Excel (.xlsx)
                </a>

                {{-- Botón Exportar CSV --}}
                <a href="{{ route('reports.income.export-csv', request()->query()) }}" class="btn btn-sm btn-outline-info text-dark fw-semibold">
                    <i class="bi bi-file-earmark-spreadsheet me-1"></i> CSV (.csv)
                </a>

                {{-- Formulario Exportar PDF --}}
                <form method="POST" action="{{ route('reports.income.export-pdf') }}" id="export-pdf-form" class="d-inline">
                    @csrf
                    <input type="hidden" name="group_by" value="{{ $report['groupBy'] }}">
                    <input type="hidden" name="start_date" value="{{ $report['startDate'] ?? '' }}">
                    <input type="hidden" name="end_date" value="{{ $report['endDate'] ?? '' }}">
                    <input type="hidden" name="chart_image" id="chart_image_input">
                    <button type="submit" class="btn btn-sm btn-outline-danger fw-semibold">
                        <i class="bi bi-file-earmark-pdf me-1"></i> PDF (.pdf)
                    </button>
                </form>
            </div>
        </div>

        {{-- Tarjetas de Métricas Ejecutivas --}}
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-primary text-white h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-white-50 small mb-1 fw-semibold text-uppercase">Ingreso Total Recaudado</p>
                                <h3 class="mb-0 fw-bold">Q {{ number_format($report['totalIncome'], 2) }}</h3>
                            </div>
                            <div class="bg-white bg-opacity-25 rounded-circle p-3">
                                <i class="bi bi-cash-stack fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-success text-white h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-white-50 small mb-1 fw-semibold text-uppercase">Total Transacciones / Pagos</p>
                                <h3 class="mb-0 fw-bold">{{ number_format($report['totalPaymentsCount']) }}</h3>
                            </div>
                            <div class="bg-white bg-opacity-25 rounded-circle p-3">
                                <i class="bi bi-receipt-cutoff fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-info text-white h-100">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="text-white-50 small mb-1 fw-semibold text-uppercase">Promedio por Transacción</p>
                                <h3 class="mb-0 fw-bold">
                                    Q {{ $report['totalPaymentsCount'] > 0 ? number_format($report['totalIncome'] / $report['totalPaymentsCount'], 2) : '0.00' }}
                                </h3>
                            </div>
                            <div class="bg-white bg-opacity-25 rounded-circle p-3">
                                <i class="bi bi-graph-up-arrow fs-3"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Contenedor imprimible / capturable para imagen --}}
        <div id="report-printable-area">
            {{-- Gráfico Visual --}}
            @if(count($report['periods']) > 0)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3 border-0">
                        <h5 class="card-title fw-bold mb-0 text-dark">
                            <i class="bi bi-bar-chart-line text-primary me-2"></i> Evolución de Ingresos por {{ $report['groupBy'] === 'week' ? 'Semana' : 'Mes' }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <canvas id="income-chart" height="100"></canvas>
                    </div>
                </div>
            @endif

            {{-- Tabla de Desglose por Planes --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <h5 class="card-title fw-bold mb-0 text-dark">
                        <i class="bi bi-table text-primary me-2"></i> Desglose Detallado por Planes de Membresía
                    </h5>
                </div>
                <div class="card-body p-0">
                    @if(count($report['periods']) === 0)
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            No se registraron pagos de membresía para los filtros seleccionados.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Periodo ({{ $report['groupBy'] === 'week' ? 'Semana' : 'Mes' }})</th>
                                        <th class="text-center">Cant. Pagos</th>
                                        <th class="text-end">Ingreso Total</th>
                                        @foreach($report['plans'] as $plan)
                                            <th class="text-end text-nowrap">{{ $plan->name }}</th>
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($report['periods'] as $period)
                                        <tr>
                                            <td class="ps-4 fw-semibold">{{ $period['period_label'] }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-light text-dark border">{{ $period['payment_count'] }}</span>
                                            </td>
                                            <td class="text-end fw-bold text-success">
                                                Q {{ number_format($period['total'], 2) }}
                                            </td>
                                            @foreach($report['plans'] as $plan)
                                                <td class="text-end text-muted">
                                                    Q {{ number_format($period['plans'][$plan->id] ?? 0, 2) }}
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-dark">
                                    <tr class="fw-bold fs-6">
                                        <td class="ps-4">TOTAL GENERAL</td>
                                        <td class="text-center">{{ $report['totalPaymentsCount'] }}</td>
                                        <td class="text-end text-warning">Q {{ number_format($report['totalIncome'], 2) }}</td>
                                        @foreach($report['plans'] as $plan)
                                            <td class="text-end text-warning">
                                                Q {{ number_format($report['incomeByPlan'][$plan->id]['total'] ?? 0, 2) }}
                                            </td>
                                        @endforeach
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const periods = @json($report['periods']);
            const plans = @json($report['plans']);

            if (periods.length > 0) {
                const labels = periods.map(p => p.period_label);
                
                // Palette of distinct colors for plans
                const colors = ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#0dcaf0', '#6f42c1', '#fd7e14', '#20c997'];

                const datasets = plans.map((plan, idx) => {
                    const color = colors[idx % colors.length];
                    return {
                        label: plan.name,
                        data: periods.map(p => p.plans[plan.id] || 0),
                        backgroundColor: color,
                        borderColor: color,
                        borderWidth: 1
                    };
                });

                // Add Total Line dataset
                datasets.unshift({
                    label: 'Ingreso Total (Q)',
                    data: periods.map(p => p.total),
                    type: 'line',
                    borderColor: '#212529',
                    backgroundColor: 'rgba(33, 37, 41, 0.1)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: false
                });

                const ctx = document.getElementById('income-chart').getContext('2d');
                window.incomeChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: datasets
                    },
                    options: {
                        responsive: true,
                        interaction: { mode: 'index', intersect: false },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function(value) { return 'Q ' + value.toLocaleString(); }
                                }
                            }
                        }
                    }
                });
            }

            // Export to Image (PNG)
            document.getElementById('export-image-btn').addEventListener('click', function () {
                const printableArea = document.getElementById('report-printable-area');
                if (window.html2canvas) {
                    html2canvas(printableArea, { scale: 2 }).then(canvas => {
                        const link = document.createElement('a');
                        link.download = 'reporte-ingresos-' + new Date().toISOString().slice(0,10) + '.png';
                        link.href = canvas.toDataURL('image/png');
                        link.click();
                    });
                } else if (window.incomeChart) {
                    const link = document.createElement('a');
                    link.download = 'grafico-ingresos.png';
                    link.href = window.incomeChart.toBase64Image();
                    link.click();
                }
            });

            // Prepare chart image for PDF export form
            document.getElementById('export-pdf-form').addEventListener('submit', function () {
                if (window.incomeChart) {
                    document.getElementById('chart_image_input').value = window.incomeChart.toBase64Image();
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
