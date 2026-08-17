<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h2 class="mb-0"><i class="bi bi-card-heading me-2"></i>Planes de Membresía</h2>
                <small class="text-muted">Gestión de precios y descripciones de los planes del gimnasio</small>
            </div>
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
                <strong class="mb-0">Planes Registrados</strong>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Nombre del Plan</th>
                                <th>Precio</th>
                                <th>Duración</th>
                                <th>Clases Grupales</th>
                                <th>Entrenador</th>
                                <th>Prioridad Lista de Espera</th>
                                <th class="pe-4 text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($plans as $plan)
                                <tr>
                                    <td class="ps-4">
                                        <strong>{{ $plan->name }}</strong>
                                        @if($plan->description)
                                            <div class="text-muted small">{{ Str::limit($plan->description, 60) }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <strong class="text-success">Q{{ number_format($plan->price, 2) }}</strong>
                                    </td>
                                    <td>
                                        {{ $plan->duration_months }} {{ $plan->duration_months == 1 ? 'mes' : 'meses' }}
                                    </td>
                                    <td>
                                        @if ($plan->includes_group_classes)
                                            <span class="badge bg-success">
                                                {{ $plan->weekly_class_limit ? $plan->weekly_class_limit . '/sem' : 'Ilimitadas' }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">No</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($plan->includes_trainer)
                                            <span class="badge bg-success">Incluido</span>
                                        @else
                                            <span class="badge bg-secondary">No</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($plan->has_waitlist_priority)
                                            <span class="badge bg-info text-dark">Sí</span>
                                        @else
                                            <span class="badge bg-secondary">No</span>
                                        @endif
                                    </td>
                                    <td class="pe-4 text-end">
                                        <a href="{{ route('membership-plans.edit', $plan) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-pencil me-1"></i>Editar
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        No hay planes registrados.
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
