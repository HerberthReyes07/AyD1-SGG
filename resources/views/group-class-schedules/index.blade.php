<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h2 class="mb-0">
                    <i class="bi bi-clock me-2"></i>Horarios - {{ $groupClass->name }}
                </h2>

                <small class="text-muted">
                    Configura los horarios recurrentes de la clase
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
                                {{ $groupClass->duration_minutes }} minutos
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
                <strong>Agregar horario</strong>
            </div>

            <div class="card-body">

                <form
                    method="POST"
                    action="{{ route('group-class-schedules.store', $groupClass) }}"
                >
                    @csrf

                    <div class="row g-3">

                        <div class="col-md-5">
                            <label
                                for="weekday"
                                class="form-label"
                            >
                                Dia de la semana
                            </label>

                            <select
                                id="weekday"
                                name="weekday"
                                class="form-select @error('weekday') is-invalid @enderror"
                            >
                                <option value="">
                                    Seleccione un dia
                                </option>

                                @foreach ($weekdays as $weekday)
                                    <option
                                        value="{{ $weekday->value }}"
                                        @selected(old('weekday') === $weekday->value)
                                    >
                                        {{ $weekday->label() }}
                                    </option>
                                @endforeach
                            </select>

                            @error('weekday')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-5">
                            <label
                                for="start_time"
                                class="form-label"
                            >
                                Hora de inicio
                            </label>

                            <input
                                type="time"
                                id="start_time"
                                name="start_time"
                                value="{{ old('start_time') }}"
                                class="form-control @error('start_time') is-invalid @enderror"
                            >

                            @error('start_time')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <button
                                type="submit"
                                class="btn btn-primary w-100"
                            >
                                <i class="bi bi-plus-lg me-1"></i>Agregar
                            </button>
                        </div>

                    </div>

                </form>

            </div>
        </div>

        <div class="card shadow-sm">

            <div class="card-header bg-white">
                <strong>Horarios registrados</strong>
            </div>

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Dia</th>
                                <th>Hora de inicio</th>
                                <th class="text-end pe-3">
                                    Acciones
                                </th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse ($schedules as $schedule)

                                <tr>

                                    <td class="ps-3">
                                        <strong>
                                            {{ $schedule->weekday->label() }}
                                        </strong>
                                    </td>

                                    <td>
                                        {{ substr((string) $schedule->start_time, 0, 5) }}
                                    </td>

                                    <td class="text-end pe-3">

                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editSchedule{{ $schedule->id }}"
                                        >
                                            <i class="bi bi-pencil-square me-1"></i>Editar
                                        </button>

                                        <form
                                            method="POST"
                                            action="{{ route(
                                                'group-class-schedules.destroy',
                                                [$groupClass, $schedule]
                                            ) }}"
                                            class="d-inline"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('¿Deseas eliminar este horario?')"
                                            >
                                                <i class="bi bi-trash me-1"></i>Eliminar
                                            </button>
                                        </form>

                                    </td>

                                </tr>

                                <div
                                    class="modal fade"
                                    id="editSchedule{{ $schedule->id }}"
                                    tabindex="-1"
                                    aria-hidden="true"
                                >
                                    <div class="modal-dialog modal-dialog-centered">

                                        <div class="modal-content">

                                            <div class="modal-header">
                                                <h5 class="modal-title">
                                                    Editar horario
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
                                                    'group-class-schedules.update',
                                                    [$groupClass, $schedule]
                                                ) }}"
                                            >
                                                @csrf
                                                @method('PUT')

                                                <div class="modal-body">

                                                    <div class="mb-3">
                                                        <label class="form-label">
                                                            Dia
                                                        </label>

                                                        <select
                                                            name="weekday"
                                                            class="form-select"
                                                        >
                                                            @foreach ($weekdays as $weekday)
                                                                <option
                                                                    value="{{ $weekday->value }}"
                                                                    @selected(
                                                                        $schedule->weekday === $weekday
                                                                    )
                                                                >
                                                                    {{ $weekday->label() }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div>
                                                        <label class="form-label">
                                                            Hora
                                                        </label>

                                                        <input
                                                            type="time"
                                                            name="start_time"
                                                            value="{{ substr((string) $schedule->start_time, 0, 5) }}"
                                                            class="form-control"
                                                        >
                                                    </div>

                                                </div>

                                                <div class="modal-footer">

                                                    <button
                                                        type="button"
                                                        class="btn btn-outline-secondary"
                                                        data-bs-dismiss="modal"
                                                    >
                                                        <i class="bi bi-x-lg me-1"></i>Cancelar
                                                    </button>

                                                    <button
                                                        type="submit"
                                                        class="btn btn-primary"
                                                    >
                                                        <i class="bi bi-check-lg me-1"></i>Guardar cambios
                                                    </button>

                                                </div>

                                            </form>

                                        </div>

                                    </div>
                                </div>

                            @empty

                                <tr>
                                    <td
                                        colspan="3"
                                        class="text-center py-4 text-muted"
                                    >
                                        <i class="bi bi-inbox text-muted d-block mb-2" style="font-size: 2.5rem;"></i>
                                        Esta clase aun no tiene horarios.
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