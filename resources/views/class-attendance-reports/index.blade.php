<x-app-layout>

    <x-slot name="header">
        <div>
            <h2 class="mb-0">
                Reporte de asistencia por clase
            </h2>

            <small class="text-muted">
                Check-in de socios en clases grupales
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

                <form
                    method="GET"
                    action="{{ route(
                        'class-attendance-reports.index'
                    ) }}"
                >

                    <div class="row g-3 align-items-end">

                        <div class="col-md-3">

                            <label class="form-label">
                                Desde
                            </label>

                            <input
                                type="date"
                                name="date_from"
                                value="{{ request('date_from') }}"
                                class="form-control"
                            >

                        </div>


                        <div class="col-md-3">

                            <label class="form-label">
                                Hasta
                            </label>

                            <input
                                type="date"
                                name="date_to"
                                value="{{ request('date_to') }}"
                                class="form-control"
                            >

                        </div>


                        <div class="col-md-3">

                            <label class="form-label">
                                Clase grupal
                            </label>

                            <select
                                name="group_class_id"
                                class="form-select"
                            >

                                <option value="">
                                    Todas
                                </option>

                                @foreach ($groupClasses as $groupClass)

                                    <option
                                        value="{{ $groupClass->id }}"
                                        @selected(
                                            request('group_class_id')
                                            == $groupClass->id
                                        )
                                    >
                                        {{ $groupClass->name }}
                                    </option>

                                @endforeach

                            </select>

                        </div>


                        <div class="col-md-3">

                            <button
                                type="submit"
                                class="btn btn-primary"
                            >
                                Consultar
                            </button>

                            <a
                                href="{{ route(
                                    'class-attendance-reports.index'
                                ) }}"
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
        <div
            class="d-flex justify-content-end
                mb-4 gap-2 flex-wrap"
        >

            {{-- Imagen PNG --}}
            <button
                type="button"
                id="export-image-btn"
                class="btn btn-sm
                    btn-outline-dark fw-semibold"
            >
                <i class="bi bi-image me-1"></i>
                Exportar Imagen (PNG)
            </button>


            {{-- Excel --}}
            <a
                href="{{
                    route(
                        'class-attendance-reports.export-excel',
                        request()->query()
                    )
                }}"
                class="btn btn-sm
                    btn-outline-success fw-semibold"
            >
                <i
                    class="bi bi-file-earmark-excel me-1"
                ></i>

                Excel (.xlsx)
            </a>


            {{-- PDF --}}
            <form
                method="POST"
                action="{{
                    route(
                        'class-attendance-reports.export-pdf'
                    )
                }}"
                class="d-inline"
            >

                @csrf

                <input
                    type="hidden"
                    name="date_from"
                    value="{{ request('date_from') }}"
                >

                <input
                    type="hidden"
                    name="date_to"
                    value="{{ request('date_to') }}"
                >

                <input
                    type="hidden"
                    name="group_class_id"
                    value="{{ request('group_class_id') }}"
                >

                <button
                    type="submit"
                    class="btn btn-sm
                        btn-outline-danger fw-semibold"
                >
                    <i
                        class="bi bi-file-earmark-pdf me-1"
                    ></i>

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

                    <div
                        class="d-flex flex-column
                            flex-md-row
                            justify-content-between
                            align-items-md-center gap-2"
                    >

                        <div>

                            <h4 class="mb-1">
                                Reporte de asistencia por clase
                            </h4>

                            <small class="text-muted">
                                Clases grupales
                            </small>

                        </div>


                        <div class="text-md-end">

                            <small class="text-muted d-block">
                                Desde:
                                {{ request('date_from') ?: 'Sin limite' }}
                            </small>

                            <small class="text-muted d-block">
                                Hasta:
                                {{ request('date_to') ?: 'Sin limite' }}
                            </small>

                            <small class="text-muted d-block">

                                Clase:

                                @if (request('group_class_id'))

                                    {{
                                        $groupClasses
                                            ->firstWhere(
                                                'id',
                                                request(
                                                    'group_class_id'
                                                )
                                            )
                                            ?->name
                                        ?? 'Todas'
                                    }}

                                @else

                                    Todas

                                @endif

                            </small>

                        </div>

                    </div>

                </div>

            </div>


            {{-- Resumen --}}
            <div class="row g-3 mb-4">

                <div class="col-md-3">

                    <div class="card shadow-sm h-100">

                        <div class="card-body">

                            <small class="text-muted">
                                Participantes
                            </small>

                            <h3 class="mb-0">
                                {{ $summary['total'] }}
                            </h3>

                        </div>

                    </div>

                </div>


                <div class="col-md-3">

                    <div class="card shadow-sm h-100">

                        <div class="card-body">

                            <small class="text-muted">
                                Asistieron
                            </small>

                            <h3 class="mb-0">
                                {{ $summary['attended'] }}
                            </h3>

                        </div>

                    </div>

                </div>


                <div class="col-md-3">

                    <div class="card shadow-sm h-100">

                        <div class="card-body">

                            <small class="text-muted">
                                No asistieron
                            </small>

                            <h3 class="mb-0">
                                {{ $summary['no_show'] }}
                            </h3>

                        </div>

                    </div>

                </div>


                <div class="col-md-3">

                    <div class="card shadow-sm h-100">

                        <div class="card-body">

                            <small class="text-muted">
                                Porcentaje asistencia
                            </small>

                            <h3 class="mb-0">
                                {{
                                    number_format(
                                        $summary['percentage'],
                                        2
                                    )
                                }}%
                            </h3>

                        </div>

                    </div>

                </div>

            </div>


            {{-- Tabla --}}
            <div class="card shadow-sm">

                <div class="card-header bg-white">

                    <strong>
                        Registros de asistencia
                    </strong>

                </div>


                <div class="card-body p-0">

                    <div class="table-responsive">

                        <table
                            class="table table-hover
                                align-middle mb-0"
                        >

                            <thead class="table-light">

                                <tr>

                                    <th class="ps-3">
                                        Clase
                                    </th>

                                    <th>
                                        Fecha
                                    </th>

                                    <th>
                                        Hora
                                    </th>

                                    <th>
                                        Socio
                                    </th>

                                    <th>
                                        Correo
                                    </th>

                                    <th>
                                        Check-in
                                    </th>

                                    <th>
                                        Estado
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                @forelse ($records as $enrollment)

                                    <tr>

                                        <td class="ps-3">

                                            <strong>
                                                {{
                                                    $enrollment
                                                        ->classSession
                                                        ->groupClass
                                                        ->name
                                                }}
                                            </strong>

                                        </td>


                                        <td>
                                            {{
                                                $enrollment
                                                    ->classSession
                                                    ->starts_at
                                                    ->format(
                                                        'd/m/Y'
                                                    )
                                            }}
                                        </td>


                                        <td>
                                            {{
                                                $enrollment
                                                    ->classSession
                                                    ->starts_at
                                                    ->format(
                                                        'H:i'
                                                    )
                                            }}
                                        </td>


                                        <td>

                                            {{
                                                $enrollment
                                                    ->member
                                                    ->user
                                                    ->first_name
                                            }}

                                            {{
                                                $enrollment
                                                    ->member
                                                    ->user
                                                    ->last_name
                                            }}

                                        </td>


                                        <td>
                                            {{
                                                $enrollment
                                                    ->member
                                                    ->user
                                                    ->email
                                            }}
                                        </td>


                                        <td>

                                            @if (
                                                $enrollment
                                                    ->classAttendance
                                            )

                                                {{
                                                    $enrollment
                                                        ->classAttendance
                                                        ->check_in_at
                                                        ->format(
                                                            'd/m/Y H:i:s'
                                                        )
                                                }}

                                            @else

                                                <span
                                                    class="text-muted"
                                                >
                                                    --
                                                </span>

                                            @endif

                                        </td>


                                        <td>

                                            @if (
                                                $enrollment->status ===
                                                \App\Enums\ClassEnrollmentStatus::Attended
                                            )

                                                <span
                                                    class="badge bg-success"
                                                >
                                                    Asistio
                                                </span>

                                            @else

                                                <span
                                                    class="badge bg-secondary"
                                                >
                                                    No asistio
                                                </span>

                                            @endif

                                        </td>

                                    </tr>

                                @empty

                                    <tr>

                                        <td
                                            colspan="7"
                                            class="text-center
                                                py-4 text-muted"
                                        >
                                            No hay registros para el
                                            rango seleccionado.
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

        <script
            src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"
        ></script>

        <script>
            document.addEventListener(
                'DOMContentLoaded',
                function () {
                    const exportButton =
                        document.getElementById(
                            'export-image-btn'
                        );

                    const printableArea =
                        document.getElementById(
                            'report-printable-area'
                        );

                    if (
                        ! exportButton ||
                        ! printableArea
                    ) {
                        return;
                    }


                    exportButton.addEventListener(
                        'click',
                        function () {
                            if (! window.html2canvas) {
                                alert(
                                    'No fue posible cargar el generador de imagen.'
                                );

                                return;
                            }


                            exportButton.disabled = true;

                            const originalText =
                                exportButton.innerHTML;

                            exportButton.innerHTML =
                                'Generando imagen...';


                            html2canvas(
                                printableArea,
                                {
                                    scale: 2,
                                    backgroundColor:
                                        '#ffffff',
                                    useCORS: true
                                }
                            )
                                .then(
                                    function (canvas) {
                                        const link =
                                            document
                                                .createElement(
                                                    'a'
                                                );

                                        const date =
                                            new Date()
                                                .toISOString()
                                                .slice(
                                                    0,
                                                    10
                                                );

                                        link.download =
                                            'reporte-asistencia-clases-'
                                            + date
                                            + '.png';

                                        link.href =
                                            canvas.toDataURL(
                                                'image/png'
                                            );

                                        link.click();
                                    }
                                )
                                .catch(
                                    function () {
                                        alert(
                                            'Ocurrio un error al generar la imagen.'
                                        );
                                    }
                                )
                                .finally(
                                    function () {
                                        exportButton.disabled =
                                            false;

                                        exportButton.innerHTML =
                                            originalText;
                                    }
                                );
                        }
                    );
                }
            );
        </script>

    @endpush

</x-app-layout>