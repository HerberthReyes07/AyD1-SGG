<x-app-layout>

    <x-slot name="header">
        <div>
            <h2 class="mb-0">
                Reporte de pases de invitado
            </h2>

            <small class="text-muted">
                Uso de pases de invitado / dia de prueba en un periodo
            </small>
        </div>
    </x-slot>

    <div class="container-xl py-4">

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Filtros --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">

                <form method="GET" action="{{ route('guest-pass-reports.index') }}">
                    <div class="row g-3 align-items-end">

                        <div class="col-md-3">
                            <label class="form-label">Desde</label>

                            <input
                                type="date"
                                name="date_from"
                                value="{{ request('date_from') }}"
                                class="form-control"
                            >
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Hasta</label>

                            <input
                                type="date"
                                name="date_to"
                                value="{{ request('date_to') }}"
                                class="form-control"
                            >
                        </div>

                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">
                                Consultar
                            </button>

                            <a
                                href="{{ route('guest-pass-reports.index') }}"
                                class="btn btn-outline-secondary"
                            >
                                Limpiar
                            </a>
                        </div>

                    </div>
                </form>

            </div>
        </div>

        {{-- Botones de exportacion --}}
        <div class="d-flex justify-content-end mb-4 gap-2 flex-wrap">

            {{-- Imagen PNG --}}
            <button
                type="button"
                id="export-image-btn"
                class="btn btn-sm btn-outline-dark fw-semibold"
            >
                <i class="bi bi-image me-1"></i>
                Exportar Imagen (PNG)
            </button>

            {{-- Excel --}}
            <a
                href="{{ route('guest-pass-reports.export-excel', request()->query()) }}"
                class="btn btn-sm btn-outline-success fw-semibold"
            >
                <i class="bi bi-file-earmark-excel me-1"></i>
                Excel (.xlsx)
            </a>

            {{-- PDF --}}
            <form
                method="POST"
                action="{{ route('guest-pass-reports.export-pdf') }}"
                class="d-inline"
            >
                @csrf

                <input type="hidden" name="date_from" value="{{ request('date_from') }}">
                <input type="hidden" name="date_to" value="{{ request('date_to') }}">

                <button type="submit" class="btn btn-sm btn-outline-danger fw-semibold">
                    <i class="bi bi-file-earmark-pdf me-1"></i>
                    PDF (.pdf)
                </button>
            </form>

        </div>

        {{--
            Todo lo que este dentro de este contenedor
            sera capturado al exportar la imagen PNG.
        --}}
        <div id="report-printable-area">

            {{-- Encabezado para la imagen --}}
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
                        <div>
                            <h4 class="mb-1">Reporte de pases de invitado</h4>
                            <small class="text-muted">Dia de prueba / invitados</small>
                        </div>

                        <div class="text-md-end">
                            <small class="text-muted d-block">
                                Desde: {{ request('date_from') ?: 'Sin limite' }}
                            </small>
                            <small class="text-muted d-block">
                                Hasta: {{ request('date_to') ?: 'Sin limite' }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Resumen --}}
            <div class="row g-3 mb-4">

                <div class="col-md-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <small class="text-muted">Pases registrados</small>
                            <h3 class="mb-0">{{ $summary['total'] }}</h3>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <small class="text-muted">Dias con registro</small>
                            <h3 class="mb-0">{{ $summary['active_days'] }}</h3>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <small class="text-muted">Promedio por dia</small>
                            <h3 class="mb-0">{{ number_format($summary['average_per_day'], 1) }}</h3>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Tabla --}}
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <strong>Pases registrados</strong>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Invitado</th>
                                    <th>DPI</th>
                                    <th>Fecha de visita</th>
                                    <th>Registrado por</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($records as $guestPass)
                                    <tr>
                                        <td class="ps-3">
                                            <strong>{{ $guestPass->guest_name }}</strong>
                                        </td>
                                        <td>{{ $guestPass->dpi }}</td>
                                        <td>{{ $guestPass->visit_date->format('d/m/Y') }}</td>
                                        <td>
                                            @if ($guestPass->registeredBy)
                                                {{ $guestPass->registeredBy->first_name }}
                                                {{ $guestPass->registeredBy->last_name }}
                                            @else
                                                <span class="text-muted">--</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">
                                            No hay registros para el rango seleccionado.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const exportButton = document.getElementById('export-image-btn');
                const printableArea = document.getElementById('report-printable-area');

                if (!exportButton || !printableArea) {
                    return;
                }

                exportButton.addEventListener('click', function () {
                    if (!window.html2canvas) {
                        alert('No fue posible cargar el generador de imagen.');
                        return;
                    }

                    exportButton.disabled = true;
                    const originalText = exportButton.innerHTML;
                    exportButton.innerHTML = 'Generando imagen...';

                    html2canvas(printableArea, {
                        scale: 2,
                        backgroundColor: '#ffffff',
                        useCORS: true
                    })
                        .then(function (canvas) {
                            const link = document.createElement('a');
                            const date = new Date().toISOString().slice(0, 10);

                            link.download = 'reporte-pases-invitado-' + date + '.png';
                            link.href = canvas.toDataURL('image/png');
                            link.click();
                        })
                        .catch(function () {
                            alert('Ocurrio un error al generar la imagen.');
                        })
                        .finally(function () {
                            exportButton.disabled = false;
                            exportButton.innerHTML = originalText;
                        });
                });
            });
        </script>
    @endpush

</x-app-layout>
