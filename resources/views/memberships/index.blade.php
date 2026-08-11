<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-0">Membresías</h2>
                <small class="text-muted">
                    Consulta y administra las membresías de los socios
                </small>
            </div>
            <div>
                <a href="{{ route('payments.create') }}" class="btn btn-primary">
                    Nueva Membresía / Pago
                </a>
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

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <strong class="mb-0">Historial de Membresías</strong>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Socio</th>
                                <th>Plan</th>
                                <th>Precio</th>
                                <th>Fecha Inicio</th>
                                <th>Fecha Fin</th>
                                <th>Estado</th>
                                <th class="text-center pe-4">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($memberships as $membership)
                                <tr>
                                    <td class="ps-4">
                                        <strong>
                                            {{ $membership->member?->user?->first_name }} {{ $membership->member?->user?->last_name }}
                                        </strong>
                                        <div class="text-muted small">
                                            {{ $membership->member?->user?->email }}
                                        </div>
                                    </td>
                                    <td>{{ $membership->plan?->name }}</td>
                                    <td>Q{{ number_format($membership->plan?->price, 2) }}</td>
                                    <td>{{ $membership->start_date?->format('d/m/Y') }}</td>
                                    <td>{{ $membership->end_date?->format('d/m/Y') }}</td>
                                    <td>
                                        @php
                                            $statusClass = match($membership->status?->value ?? '') {
                                                'active' => 'bg-success',
                                                'frozen' => 'bg-info text-dark',
                                                'expired' => 'bg-secondary',
                                                'cancelled' => 'bg-danger',
                                                default => 'bg-light text-dark'
                                            };
                                            $statusLabel = $membership->status?->label() ?? 'Desconocido';
                                        @endphp
                                        <span class="badge {{ $statusClass }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                    <td class="pe-4 text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('memberships.show', $membership->id) }}" class="btn btn-sm btn-outline-primary">
                                                Ver Detalles
                                            </a>
                                            @if($membership->status?->value === 'active' || $membership->status?->value === 'expired')
                                                <a href="{{ route('payments.create', ['member_id' => $membership->member_id, 'renewal' => 1]) }}" class="btn btn-sm btn-outline-success">
                                                    Renovar
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">
                                        No se encontraron registros de membresías.
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
