<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h2 class="mb-0"><i class="bi bi-tags me-2"></i>Promociones y Descuentos</h2>
                <small class="text-muted">Gestión de promociones autorizadas por administración</small>
            </div>
            <a href="{{ route('promotions.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg me-1"></i>Nueva Promoción
            </a>
        </div>
    </x-slot>

    <div class="container-xl py-4">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <strong class="mb-0">Promociones Registradas</strong>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Nombre de la Promoción</th>
                                <th>Tipo</th>
                                <th>Valor</th>
                                <th>Fecha Inicio</th>
                                <th>Fecha Fin</th>
                                <th>Estado</th>
                                <th>Autorizado Por</th>
                                <th class="pe-4 text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($promotions as $promotion)
                                <tr>
                                    <td class="ps-4">
                                        <strong>{{ $promotion->name }}</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            {{ $promotion->type->label() }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong class="text-primary">
                                            @if ($promotion->type->value === 'percentage')
                                                {{ number_format($promotion->value, 0) }}%
                                            @else
                                                Q{{ number_format($promotion->value, 2) }}
                                            @endif
                                        </strong>
                                    </td>
                                    <td>{{ $promotion->start_date?->format('d/m/Y') }}</td>
                                    <td>{{ $promotion->end_date?->format('d/m/Y') }}</td>
                                    <td>
                                        @if ($promotion->is_active && $promotion->start_date <= now() && $promotion->end_date >= now())
                                            <span class="badge bg-success">Activa / Vigente</span>
                                        @elseif ($promotion->is_active)
                                            <span class="badge bg-warning text-dark">Activa / Fuera de rango</span>
                                        @else
                                            <span class="badge bg-secondary">Inactiva</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small>{{ $promotion->authorizedBy?->first_name }} {{ $promotion->authorizedBy?->last_name }}</small>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <div class="d-flex justify-content-end gap-1">
                                            <a href="{{ route('promotions.edit', $promotion) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil me-1"></i>Editar
                                            </a>
                                            <form method="POST" action="{{ route('promotions.toggle-status', $promotion) }}" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm {{ $promotion->is_active ? 'btn-outline-danger' : 'btn-outline-success' }}">
                                                    {{ $promotion->is_active ? 'Desactivar' : 'Activar' }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        No hay promociones registradas.
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
