<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h2 class="mb-0"><i class="bi bi-calendar-event me-2"></i>Clases grupales</h2>

                <small class="text-muted">
                    Administra las plantillas de clases grupales
                </small>
            </div>

            <a
                href="{{ route('group-classes.create') }}"
                class="btn btn-primary"
            >
                <i class="bi bi-plus-lg me-1"></i>Nueva clase
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
            <div class="card-body">

                <form
                    method="GET"
                    action="{{ route('group-classes.index') }}"
                >
                    <div class="row g-3">

                        <div class="col-md-5">
                            <label
                                for="search"
                                class="form-label"
                            >
                                Buscar clase
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="search"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="Ejemplo: Yoga"
                            >
                        </div>

                        <div class="col-md-3">
                            <label
                                for="category_id"
                                class="form-label"
                            >
                                Categoria
                            </label>

                            <select
                                class="form-select"
                                id="category_id"
                                name="category_id"
                            >
                                <option value="">
                                    Todas
                                </option>

                                @foreach ($categories as $category)
                                    <option
                                        value="{{ $category->id }}"
                                        @selected(
                                            request('category_id') == $category->id
                                        )
                                    >
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label
                                for="status"
                                class="form-label"
                            >
                                Estado
                            </label>

                            <select
                                class="form-select"
                                id="status"
                                name="status"
                            >
                                <option value="">
                                    Todos
                                </option>

                                <option
                                    value="active"
                                    @selected(request('status') === 'active')
                                >
                                    Activas
                                </option>

                                <option
                                    value="inactive"
                                    @selected(request('status') === 'inactive')
                                >
                                    Inactivas
                                </option>
                            </select>
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <button
                                type="submit"
                                class="btn btn-primary w-100"
                            >
                                <i class="bi bi-search me-1"></i>Buscar
                            </button>
                        </div>

                    </div>
                </form>

            </div>
        </div>

        <div class="card shadow-sm">

            <div class="card-header bg-white">
                <strong>
                    Plantillas registradas
                </strong>
            </div>

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Nombre</th>
                                <th>Categoria</th>
                                <th>Entrenador</th>
                                <th>Duracion</th>
                                <th>Cupo maximo</th>
                                <th>Estado</th>
                                <th class="text-center">
                                    Acciones
                                </th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse ($groupClasses as $groupClass)

                                <tr>

                                    <td class="ps-3">
                                        <strong>
                                            {{ $groupClass->name }}
                                        </strong>
                                    </td>

                                    <td>
                                        {{ $groupClass->category?->name ?? 'Sin categoria' }}
                                    </td>

                                    <td>
                                        @if ($groupClass->trainer?->user)
                                            {{ $groupClass->trainer->user->first_name }}
                                            {{ $groupClass->trainer->user->last_name }}
                                        @else
                                            Sin entrenador
                                        @endif
                                    </td>

                                    <td>
                                        {{ $groupClass->duration_minutes }}
                                        min
                                    </td>

                                    <td>
                                        {{ $groupClass->max_participants }}
                                    </td>

                                    <td>
                                        @if ($groupClass->is_active)
                                            <span class="badge bg-success">
                                                Activa
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                Inactiva
                                            </span>
                                        @endif
                                    </td>

                                    <td>
                                        <div class="d-flex justify-content-center gap-2">

                                            <a
                                                href="{{ route('group-class-schedules.index', $groupClass) }}"
                                                class="btn btn-sm btn-outline-secondary"
                                            >
                                                <i class="bi bi-clock me-1"></i>Horarios
                                            </a>

                                            <a
                                                href="{{ route('class-sessions.index', $groupClass) }}"
                                                class="btn btn-sm btn-outline-secondary"
                                            >
                                                <i class="bi bi-list-check me-1"></i>Sesiones
                                            </a>

                                            <a
                                                href="{{ route('group-classes.edit', $groupClass) }}"
                                                class="btn btn-sm btn-outline-primary"
                                            >
                                                <i class="bi bi-pencil-square me-1"></i>Editar
                                            </a>

                                            <form
                                                method="POST"
                                                action="{{ route('group-classes.toggle-status', $groupClass) }}"
                                            >
                                                @csrf
                                                @method('PATCH')

                                                @if ($groupClass->is_active)
                                                    <button
                                                        type="submit"
                                                        class="btn btn-sm btn-outline-danger"
                                                    >
                                                        <i class="bi bi-x-circle me-1"></i>Desactivar
                                                    </button>
                                                @else
                                                    <button
                                                        type="submit"
                                                        class="btn btn-sm btn-outline-success"
                                                    >
                                                        <i class="bi bi-check-circle me-1"></i>Activar
                                                    </button>
                                                @endif

                                            </form>

                                        </div>
                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td
                                        colspan="7"
                                        class="text-center py-4 text-muted"
                                    >
                                        <i class="bi bi-inbox text-muted d-block mb-2" style="font-size: 2.5rem;"></i>
                                        No se encontraron clases grupales.
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