<x-app-layout>

    <x-slot name="header">

        <div>
            <h2 class="mb-0">
                <i class="bi bi-people text-primary me-2"></i>
                Reportes de clases grupales
            </h2>

            <small class="text-muted">
                Demanda, asistencia, lista de espera y calificaciones
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


        <div class="card shadow-sm mb-4">

            <div class="card-body">

                <form
                    method="GET"
                    action="{{ route('group-class-reports.index') }}"
                >

                    <div class="row g-3 align-items-end">

                        <div class="col-md-4">

                            <label class="form-label">
                                Desde
                            </label>

                            <input
                                type="date"
                                name="date_from"
                                class="form-control"
                                value="{{ request('date_from') }}"
                            >

                        </div>

                        <div class="col-md-4">

                            <label class="form-label">
                                Hasta
                            </label>

                            <input
                                type="date"
                                name="date_to"
                                class="form-control"
                                value="{{ request('date_to') }}"
                            >

                        </div>

                        <div class="col-md-4">

                            <button
                                type="submit"
                                class="btn btn-primary"
                            >
                                <i class="bi bi-search me-1"></i>
                                Consultar
                            </button>

                            <a
                                href="{{ route('group-class-reports.index') }}"
                                class="btn btn-outline-secondary"
                            >
                                <i class="bi bi-x-lg me-1"></i>
                                Limpiar
                            </a>

                        </div>

                    </div>

                </form>

            </div>

        </div>


        <div class="row g-3 mb-4">

            <div class="col-md-4 col-xl-2">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted">
                            Total
                        </small>

                        <h3 class="mb-0">
                            {{ $summary['total'] }}
                        </h3>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-xl-2">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted">
                            Programadas
                        </small>

                        <h3 class="mb-0">
                            {{ $summary['scheduled'] }}
                        </h3>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-xl-2">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted">
                            Completadas
                        </small>

                        <h3 class="mb-0">
                            {{ $summary['completed'] }}
                        </h3>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-xl-2">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted">
                            Canceladas
                        </small>

                        <h3 class="mb-0">
                            {{ $summary['cancelled'] }}
                        </h3>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-xl-2">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <small class="text-muted">
                            Reprogramadas
                        </small>

                        <h3 class="mb-0">
                            {{ $summary['rescheduled'] }}
                        </h3>
                    </div>
                </div>
            </div>

        </div>


        <div class="card shadow-sm mb-4">

            <div class="card-header bg-white">
                <strong>
                    Demanda y rendimiento por clase
                </strong>
            </div>

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">

                            <tr>
                                <th class="ps-3">
                                    Clase
                                </th>

                                <th>
                                    Categoria
                                </th>

                                <th>
                                    Sesiones
                                </th>

                                <th>
                                    Inscripciones
                                </th>

                                <th>
                                    Lista espera
                                </th>

                                <th>
                                    Promociones
                                </th>

                                <th>
                                    Asistieron
                                </th>

                                <th>
                                    No asistieron
                                </th>

                                <th>
                                    Calificacion
                                </th>
                            </tr>

                        </thead>

                        <tbody>

                            @forelse ($classes as $class)

                                <tr>

                                    <td class="ps-3">
                                        <strong>
                                            {{ $class['name'] }}
                                        </strong>
                                    </td>

                                    <td>
                                        {{ $class['category'] }}
                                    </td>

                                    <td>
                                        {{ $class['sessions'] }}
                                    </td>

                                    <td>
                                        {{ $class['enrollments'] }}
                                    </td>

                                    <td>
                                        {{ $class['waitlist_requests'] }}
                                    </td>

                                    <td>
                                        {{ $class['waitlist_promotions'] }}
                                    </td>

                                    <td>
                                        {{ $class['attended'] }}
                                    </td>

                                    <td>
                                        {{ $class['no_show'] }}
                                    </td>

                                    <td>

                                        @if (
                                            $class['average_rating'] !== null
                                        )

                                            {{ number_format(
                                                $class['average_rating'],
                                                2
                                            ) }}
                                            / 5

                                            <small class="text-muted">
                                                ({{ $class['ratings_count'] }})
                                            </small>

                                        @else

                                            <span class="text-muted">
                                                Sin calificaciones
                                            </span>

                                        @endif

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td
                                        colspan="9"
                                        class="text-center py-4 text-muted"
                                    >
                                        <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                                        No hay informacion para el periodo seleccionado.
                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>


        <div class="card shadow-sm">

            <div class="card-header bg-white">

                <strong>
                    Sesiones canceladas o reprogramadas
                </strong>

            </div>

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">

                            <tr>
                                <th class="ps-3">
                                    Clase
                                </th>

                                <th>
                                    Fecha
                                </th>

                                <th>
                                    Estado
                                </th>

                                <th>
                                    Motivo
                                </th>
                            </tr>

                        </thead>

                        <tbody>

                            @forelse ($changedSessions as $session)

                                <tr>

                                    <td class="ps-3">
                                        {{ $session->groupClass->name }}
                                    </td>

                                    <td>
                                        {{ $session->starts_at->format(
                                            'd/m/Y H:i'
                                        ) }}
                                    </td>

                                    <td>
                                        {{ $session->status->label() }}
                                    </td>

                                    <td>
                                        {{ $session->change_reason
                                            ?? 'Sin motivo registrado' }}
                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td
                                        colspan="4"
                                        class="text-center py-4 text-muted"
                                    >
                                        <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                                        No hay sesiones canceladas o reprogramadas.
                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</x-app-layout>