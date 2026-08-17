<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h2 class="mb-0 fw-bold">
                    <i class="bi bi-calendar-x text-primary me-2"></i>
                    {{ __('Reporte de Vencimiento de Membresías') }}
                </h2>
                <small class="text-muted">
                    {{ __('Seguimiento a membresías activas por vencer (próximos 7 días) y membresías ya vencidas') }}
                </small>
            </div>
        </div>
    </x-slot>

    <div class="container py-4">

        {{-- Filtros --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-4">
                <form method="GET" action="{{ route('reports.membership-expiration.index') }}" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="start_date" class="form-label fw-semibold">Fecha inicio fin/vencimiento (opcional)</label>
                        <input type="date" class="form-control" id="start_date" name="start_date"
                            value="{{ $report['startDate'] ?? '' }}">
                    </div>
                    <div class="col-md-4">
                        <label for="end_date" class="form-label fw-semibold">Fecha fin fin/vencimiento (opcional)</label>
                        <input type="date" class="form-control" id="end_date" name="end_date"
                            value="{{ $report['endDate'] ?? '' }}">
                    </div>
                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary w-100 fw-semibold">
                            <i class="bi bi-filter me-1"></i> Filtrar Vencidas
                        </button>
                        <a href="{{ route('reports.membership-expiration.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-counterclockwise"></i>
                        </a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Barra de Exportación --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div>
                <span class="badge bg-warning text-dark px-3 py-2 fs-6">
                    <i class="bi bi-clock-history me-1"></i> Por vencer (7 días): {{ $report['expiringCount'] }}
                </span>
                <span class="badge bg-danger px-3 py-2 fs-6 ms-1">
                    <i class="bi bi-exclamation-triangle me-1"></i> Vencidas: {{ $report['expiredCount'] }}
                </span>
            </div>

            <div class="d-flex gap-2 flex-wrap">
                {{-- Botón Exportar Imagen --}}
                <button type="button" class="btn btn-sm btn-outline-dark fw-semibold" id="export-image-btn">
                    <i class="bi bi-image me-1"></i> Exportar Imagen (PNG)
                </button>

                {{-- Botón Exportar Excel --}}
                <a href="{{ route('reports.membership-expiration.export-excel', request()->query()) }}" class="btn btn-sm btn-outline-success fw-semibold">
                    <i class="bi bi-file-earmark-excel me-1"></i> Excel (.xlsx)
                </a>

                {{-- Botón Exportar CSV --}}
                <a href="{{ route('reports.membership-expiration.export-csv', request()->query()) }}" class="btn btn-sm btn-outline-info text-dark fw-semibold">
                    <i class="bi bi-file-earmark-spreadsheet me-1"></i> CSV (.csv)
                </a>

                {{-- Formulario Exportar PDF --}}
                <form method="POST" action="{{ route('reports.membership-expiration.export-pdf') }}" id="export-pdf-form" class="d-inline">
                    @csrf
                    <input type="hidden" name="start_date" value="{{ $report['startDate'] ?? '' }}">
                    <input type="hidden" name="end_date" value="{{ $report['endDate'] ?? '' }}">
                    <input type="hidden" name="chart_image" id="chart_image_input">
                    <button type="submit" class="btn btn-sm btn-outline-danger fw-semibold">
                        <i class="bi bi-file-earmark-pdf me-1"></i> PDF (.pdf)
                    </button>
                </form>
            </div>
        </div>

        {{-- Contenedor Imprimible / Capturable --}}
        <div id="report-printable-area">
            {{-- SECCIÓN 1: MEMBRESÍAS POR VENCER EN LOS PRÓXIMOS 7 DÍAS --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-warning bg-opacity-15 py-3 border-0 d-flex justify-content-between align-items-center">
                    <h5 class="card-title fw-bold mb-0 text-dark">
                        <i class="bi bi-bell-fill text-warning me-2"></i> Membresías Activas por Vencer (Próximos 7 Días)
                    </h5>
                    <span class="badge bg-warning text-dark">{{ $report['expiringCount'] }} socio(s)</span>
                </div>
                <div class="card-body p-0">
                    @if($report['expiringActive']->isEmpty())
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-check-circle fs-2 text-success d-block mb-1"></i>
                            No hay membresías activas por vencer en los próximos 7 días.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Socio</th>
                                        <th>Correo</th>
                                        <th>Plan</th>
                                        <th>Fecha Inicio</th>
                                        <th>Fecha Vencimiento</th>
                                        <th class="text-center">Estado</th>
                                        <th class="pe-4 text-end">Tiempo Restante</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $today = \Carbon\Carbon::today(); @endphp
                                    @foreach($report['expiringActive'] as $m)
                                        @php
                                            $endDate = \Carbon\Carbon::parse($m->end_date);
                                            $daysLeft = (int) $today->diffInDays($endDate, false);
                                        @endphp
                                        <tr>
                                            <td class="ps-4 fw-bold">
                                                {{ $m->member?->user?->first_name }} {{ $m->member?->user?->last_name }}
                                            </td>
                                            <td>{{ $m->member?->user?->email }}</td>
                                            <td><span class="badge bg-light text-dark border">{{ $m->plan?->name ?? 'Sin Plan' }}</span></td>
                                            <td>{{ \Carbon\Carbon::parse($m->start_date)->format('d/m/Y') }}</td>
                                            <td class="fw-bold text-danger">{{ $endDate->format('d/m/Y') }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-success">Activa</span>
                                            </td>
                                            <td class="pe-4 text-end">
                                                @if($daysLeft > 0)
                                                    <span class="badge bg-warning text-dark px-3 py-1">Faltan {{ $daysLeft }} día(s)</span>
                                                @else
                                                    <span class="badge bg-danger px-3 py-1">Vence Hoy</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            {{-- SECCIÓN 2: MEMBRESÍAS VENCIDAS --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-danger bg-opacity-10 py-3 border-0 d-flex justify-content-between align-items-center">
                    <h5 class="card-title fw-bold mb-0 text-danger">
                        <i class="bi bi-x-circle-fill text-danger me-2"></i> Membresías Vencidas
                    </h5>
                    <span class="badge bg-danger">{{ $report['expiredCount'] }} socio(s)</span>
                </div>
                <div class="card-body p-0">
                    @if($report['expired']->isEmpty())
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-info-circle fs-2 text-info d-block mb-1"></i>
                            No hay membresías vencidas registradas para el rango seleccionado.
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-4">Socio</th>
                                        <th>Correo</th>
                                        <th>Plan</th>
                                        <th>Fecha Inicio</th>
                                        <th>Fecha Vencimiento</th>
                                        <th class="text-center">Estado</th>
                                        <th class="pe-4 text-end">Antigüedad Vencimiento</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $today = \Carbon\Carbon::today(); @endphp
                                    @foreach($report['expired'] as $m)
                                        @php
                                            $endDate = \Carbon\Carbon::parse($m->end_date);
                                            $daysExpired = (int) $endDate->diffInDays($today, false);
                                        @endphp
                                        <tr>
                                            <td class="ps-4 fw-semibold text-secondary">
                                                {{ $m->member?->user?->first_name }} {{ $m->member?->user?->last_name }}
                                            </td>
                                            <td>{{ $m->member?->user?->email }}</td>
                                            <td><span class="badge bg-light text-dark border">{{ $m->plan?->name ?? 'Sin Plan' }}</span></td>
                                            <td>{{ \Carbon\Carbon::parse($m->start_date)->format('d/m/Y') }}</td>
                                            <td class="fw-semibold">{{ $endDate->format('d/m/Y') }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-secondary">{{ $m->status?->label() ?? 'Vencida' }}</span>
                                            </td>
                                            <td class="pe-4 text-end">
                                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger px-3 py-1">
                                                    Vencida hace {{ $daysExpired }} día(s)
                                                </span>
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

    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Export to Image (PNG)
            document.getElementById('export-image-btn').addEventListener('click', function () {
                const printableArea = document.getElementById('report-printable-area');
                if (window.html2canvas) {
                    html2canvas(printableArea, { scale: 2 }).then(canvas => {
                        const link = document.createElement('a');
                        link.download = 'reporte-vencimiento-membresias-' + new Date().toISOString().slice(0,10) + '.png';
                        link.href = canvas.toDataURL('image/png');
                        link.click();
                    });
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
