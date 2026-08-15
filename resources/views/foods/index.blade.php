<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-0">Catálogo de alimentos</h2>
                <small class="text-muted">
                    Consulta y administra los alimentos registrados
                </small>
            </div>

            <a
                href="{{ route('foods.create') }}"
                class="btn btn-primary"
            >
                Nuevo alimento
            </a>
        </div>
    </x-slot>

    <div class="container-xl py-4">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
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

                <form method="GET" action="{{ route('foods.index') }}">
                    <div class="row g-3">

                        <div class="col-md-5">
                            <label for="search" class="form-label">
                                Buscar alimento
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="search"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="Ejemplo: Pollo"
                            >
                        </div>

                        <div class="col-md-3">
                            <label for="category_id" class="form-label">
                                Categoria
                            </label>

                            <select
                                class="form-select"
                                id="category_id"
                                name="category_id"
                            >
                                <option value="">Todas</option>

                                @foreach ($categories as $category)
                                    <option
                                        value="{{ $category->id }}"
                                        @selected(request('category_id') == $category->id)
                                    >
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label for="status" class="form-label">
                                Estado
                            </label>

                            <select
                                class="form-select"
                                id="status"
                                name="status"
                            >
                                <option value="">Todos</option>

                                <option
                                    value="active"
                                    @selected(request('status') === 'active')
                                >
                                    Activos
                                </option>

                                <option
                                    value="inactive"
                                    @selected(request('status') === 'inactive')
                                >
                                    Inactivos
                                </option>
                            </select>
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <button
                                type="submit"
                                class="btn btn-primary w-100"
                            >
                                Buscar
                            </button>
                        </div>

                    </div>
                </form>

            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <strong>Alimentos registrados</strong>
            </div>

            <div class="card-body p-0">

                <div class="table-responsive">
                    <table class="table table-sm table-hover align-middle mb-0">

                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Nombre</th>
                                <th>Categoria</th>
                                <th>Calorias</th>
                                <th>Proteina</th>
                                <th>Carbohidratos</th>
                                <th>Grasa</th>
                                <th>Porcion</th>
                                <th>Estado</th>
                                <th class="text-center pe-3">
                                    Acciones
                                </th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse ($foods as $food)

                                <tr>
                                    <td class="ps-3">
                                        <strong>{{ $food->name }}</strong>
                                    </td>

                                    <td>
                                        {{ $food->category->name ?? 'Sin categoria' }}
                                    </td>

                                    <td>
                                        {{ number_format($food->calories_per_serving, 2) }}
                                        kcal
                                    </td>

                                    <td>
                                        {{ number_format($food->protein_g, 2) }} g
                                    </td>

                                    <td>
                                        {{ number_format($food->carbs_g, 2) }} g
                                    </td>

                                    <td>
                                        {{ number_format($food->fat_g, 2) }} g
                                    </td>

                                    <td>
                                        {{ number_format($food->reference_serving_g, 2) }} g
                                    </td>

                                    <td>
                                        @if ($food->is_active)
                                            <span class="badge bg-success">
                                                Activo
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                Inactivo
                                            </span>
                                        @endif
                                    </td>

                                    <td class="pe-3">
                                        <div class="d-flex justify-content-center gap-1 flex-nowrap">

                                            <a
                                                href="{{ route('foods.edit', $food) }}"
                                                class="btn btn-sm btn-outline-primary"
                                            >
                                                Editar
                                            </a>

                                            <button
                                                type="button"
                                                class="btn btn-sm {{ $food->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}"
                                                data-bs-toggle="modal"
                                                data-bs-target="#statusModal{{ $food->id }}"
                                            >
                                                {{ $food->is_active ? 'Desactivar' : 'Activar' }}
                                            </button>

                                        </div>
                                    </td>
                                </tr>

                                <div
                                    class="modal fade"
                                    id="statusModal{{ $food->id }}"
                                    tabindex="-1"
                                    aria-hidden="true"
                                >
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">

                                            <div class="modal-header">
                                                <h5 class="modal-title">
                                                    {{ $food->is_active ? 'Desactivar alimento' : 'Activar alimento' }}
                                                </h5>

                                                <button
                                                    type="button"
                                                    class="btn-close"
                                                    data-bs-dismiss="modal"
                                                    aria-label="Cerrar"
                                                ></button>
                                            </div>

                                            <div class="modal-body">

                                                @if ($food->is_active)
                                                    ¿Deseas desactivar el alimento
                                                    <strong>{{ $food->name }}</strong>?
                                                @else
                                                    ¿Deseas activar el alimento
                                                    <strong>{{ $food->name }}</strong>?
                                                @endif

                                            </div>

                                            <div class="modal-footer">

                                                <button
                                                    type="button"
                                                    class="btn btn-secondary"
                                                    data-bs-dismiss="modal"
                                                >
                                                    Cancelar
                                                </button>

                                                <form
                                                    method="POST"
                                                    action="{{ route('foods.toggle-status', $food) }}"
                                                >
                                                    @csrf
                                                    @method('PATCH')

                                                    <button
                                                        type="submit"
                                                        class="btn {{ $food->is_active ? 'btn-danger' : 'btn-success' }}"
                                                    >
                                                        {{ $food->is_active ? 'Desactivar' : 'Activar' }}
                                                    </button>
                                                </form>

                                            </div>

                                        </div>
                                    </div>
                                </div>

                            @empty

                                <tr>
                                    <td
                                        colspan="9"
                                        class="text-center py-4 text-muted"
                                    >
                                        No se encontraron alimentos.
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
