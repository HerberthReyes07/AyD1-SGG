<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h2 class="mb-0">
                    <i class="bi bi-list-check me-2"></i>Sesiones - {{ $groupClass->name }}
                </h2>

                <small class="text-muted">
                    Programa y administra las sesiones de la clase
                </small>
            </div>

            <a
                href="{{ route('group-classes.index') }}"
                class="btn btn-outline-secondary"
            >
                <i class="bi bi-arrow-left me-1"></i>Volver a clases
            </a>
        </div>
    </x-slot>

    <div class="container-xl py-4">

        @if (session('success'))
            <div
                class="alert alert-success alert-dismissible fade show"
                role="alert"
            >
                {{ session('success') }}

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Cerrar"
                ></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Cerrar"
                ></button>
            </div>
        @endif

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <strong>Informacion de la clase</strong>
            </div>

            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-3">
                        <span class="text-muted">Categoria</span>

                        <div>
                            <strong>
                                {{ $groupClass->category?->name ?? 'Sin categoria' }}
                            </strong>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <span class="text-muted">Entrenador</span>

                        <div>
                            <strong>
                                @if ($groupClass->trainer?->user)
                                    {{ $groupClass->trainer->user->first_name }}
                                    {{ $groupClass->trainer->user->last_name }}
                                @else
                                    Sin entrenador
                                @endif
                            </strong>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <span class="text-muted">Duracion</span>

                        <div>
                            <strong>
                                {{ $groupClass->duration_minutes }} min
                            </strong>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <span class="text-muted">Cupo maximo</span>

                        <div>
                            <strong>
                                {{ $groupClass->max_participants }}
                            </strong>
                        </div>
                    </div>

                </div>

            </div>
        </div>

        <div class="card shadow-sm mb-4">

            <div class="card-header bg-white">
                <strong>Programar nueva sesion</strong>
            </div>

            <div class="card-body">

                <form
                    method="POST"
                    action="{{ route('class-sessions.store', $groupClass) }}"
                >
                    @csrf

                    <div class="row g-3">

                        <div class="col-md-10">

                            <label
                                for="starts_at"
                                class="form-label"
                            >
                                Fecha y hora
                            </label>

                            <input
                                type="datetime-local"
                                id="starts_at"
                                name="starts_at"
                                value="{{ old('starts_at') }}"
                                class="form-control"
                            >

                        </div>

                        <div class="col-md-2 d-flex align-items-end">

                            <button
                                type="submit"
                                class="btn btn-primary w-100"
                            >
                                <i class="bi bi-plus-lg me-1"></i>Programar
                            </button>

                        </div>

                    </div>

                </form>

            </div>
        </div>

        <div class="card shadow-sm">

            <div class="card-header bg-white">
                <strong>Sesiones registradas</strong>
            </div>

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">

                            <tr>
                                <th class="ps-3">Fecha</th>
                                <th>Hora</th>
                                <th>Estado</th>
                                <th>Motivo de cambio</th>
                                <th class="text-end pe-3">
                                    Acciones
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            @forelse ($sessions as $session)

                                <tr>

                                    <td class="ps-3">
                                        {{ $session->starts_at->format('d/m/Y') }}
                                    </td>

                                    <td>
                                        {{ $session->starts_at->format('H:i') }}
                                    </td>

                                    <td>

                                        @switch($session->status->value)

                                            @case('scheduled')
                                                <span class="badge bg-primary">
                                                    {{ $session->status->label() }}
                                                </span>
                                                @break

                                            @case('rescheduled')
                                                <span class="badge bg-warning text-dark">
                                                    {{ $session->status->label() }}
                                                </span>
                                                @break

                                            @case('in_progress')
                                                <span class="badge bg-info text-dark">
                                                    {{ $session->status->label() }}
                                                </span>
                                                @break

                                            @case('completed')
                                                <span class="badge bg-success">
                                                    {{ $session->status->label() }}
                                                </span>
                                                @break

                                            @case('cancelled')
                                                <span class="badge bg-danger">
                                                    {{ $session->status->label() }}
                                                </span>
                                                @break

                                        @endswitch

                                    </td>

                                    <td>
                                        {{ $session->change_reason ?? '-' }}
                                    </td>

                                    <td class="text-end pe-3">

                                        <a
                                            href="{{ route('class-enrollments.index', $session) }}"
                                            class="btn btn-sm btn-outline-secondary"
                                        >
                                            <i class="bi bi-people me-1"></i>Inscripciones
                                        </a>

                                        @if (
                                            $session->status->value === 'scheduled' ||
                                            $session->status->value === 'rescheduled'
                                        )

                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#reschedule{{ $session->id }}"
                                            >
                                                <i class="bi bi-calendar-event me-1"></i>Reprogramar
                                            </button>

                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#cancel{{ $session->id }}"
                                            >
                                                <i class="bi bi-x-circle me-1"></i>Cancelar
                                            </button>

                                        @endif

                                    </td>

                                </tr>

                                <div
                                    class="modal fade"
                                    id="reschedule{{ $session->id }}"
                                    tabindex="-1"
                                    aria-hidden="true"
                                >
                                    <div class="modal-dialog modal-dialog-centered">

                                        <div class="modal-content">

                                            <div class="modal-header">

                                                <h5 class="modal-title">
                                                    Reprogramar sesion
                                                </h5>

                                                <button
                                                    type="button"
                                                    class="btn-close"
                                                    data-bs-dismiss="modal"
                                                ></button>

                                            </div>

                                            <form
                                                method="POST"
                                                action="{{ route(
                                                    'class-sessions.reschedule',
                                                    [$groupClass, $session]
                                                ) }}"
                                            >

                                                @csrf
                                                @method('PATCH')

                                                <div class="modal-body">

                                                    <div class="mb-3">

                                                        <label class="form-label">
                                                            Nueva fecha y hora
                                                        </label>

                                                        <input
                                                            type="datetime-local"
                                                            name="starts_at"
                                                            value="{{ $session->starts_at->format('Y-m-d\TH:i') }}"
                                                            class="form-control"
                                                            required
                                                        >

                                                    </div>

                                                    <div>

                                                        <label class="form-label">
                                                            Motivo
                                                        </label>

                                                        <textarea
                                                            name="change_reason"
                                                            rows="3"
                                                            class="form-control"
                                                            required
                                                        ></textarea>

                                                    </div>

                                                </div>

                                                <div class="modal-footer">

                                                    <button
                                                        type="button"
                                                        class="btn btn-outline-secondary"
                                                        data-bs-dismiss="modal"
                                                    >
                                                        <i class="bi bi-x-lg me-1"></i>Cerrar
                                                    </button>

                                                    <button
                                                        type="submit"
                                                        class="btn btn-primary"
                                                    >
                                                        <i class="bi bi-check-lg me-1"></i>Reprogramar
                                                    </button>

                                                </div>

                                            </form>

                                        </div>

                                    </div>
                                </div>

                                <div
                                    class="modal fade"
                                    id="cancel{{ $session->id }}"
                                    tabindex="-1"
                                    aria-hidden="true"
                                >
                                    <div class="modal-dialog modal-dialog-centered">

                                        <div class="modal-content">

                                            <div class="modal-header">

                                                <h5 class="modal-title">
                                                    Cancelar sesion
                                                </h5>

                                                <button
                                                    type="button"
                                                    class="btn-close"
                                                    data-bs-dismiss="modal"
                                                ></button>

                                            </div>

                                            <form
                                                method="POST"
                                                action="{{ route(
                                                    'class-sessions.cancel',
                                                    [$groupClass, $session]
                                                ) }}"
                                            >

                                                @csrf
                                                @method('PATCH')

                                                <div class="modal-body">

                                                    <label class="form-label">
                                                        Motivo de cancelacion
                                                    </label>

                                                    <textarea
                                                        name="change_reason"
                                                        rows="3"
                                                        class="form-control"
                                                        required
                                                    ></textarea>

                                                </div>

                                                <div class="modal-footer">

                                                    <button
                                                        type="button"
                                                        class="btn btn-outline-secondary"
                                                        data-bs-dismiss="modal"
                                                    >
                                                        <i class="bi bi-x-lg me-1"></i>Cerrar
                                                    </button>

                                                    <button
                                                        type="submit"
                                                        class="btn btn-danger"
                                                    >
                                                        <i class="bi bi-x-circle me-1"></i>Cancelar sesion
                                                    </button>

                                                </div>

                                            </form>

                                        </div>

                                    </div>
                                </div>

                            @empty

                                <tr>

                                    <td
                                        colspan="5"
                                        class="text-center py-4 text-muted"
                                    >
                                        <i class="bi bi-inbox text-muted d-block mb-2" style="font-size: 2.5rem;"></i>
                                        No hay sesiones programadas.
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