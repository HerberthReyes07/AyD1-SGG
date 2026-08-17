<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h2 class="mb-0"><i class="bi bi-card-checklist me-2"></i>Membresías del Socio</h2>
                <small class="text-muted">
                    Consulta el historial de membresías del socio
                </small>
            </div>

            <div>
                <a href="{{ route('memberships.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Volver a Membresías
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

        <!-- Información del socio -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <strong class="mb-0">Información del Socio</strong>
            </div>

            <div class="card-body">
                <div class="row">

                    <div class="col-md-6 mb-3">
                        <span class="text-muted d-block small">
                            Nombre
                        </span>

                        <strong>
                            {{ $member->first_name }} {{ $member->last_name }}
                        </strong>
                    </div>

                    <div class="col-md-6 mb-3">
                        <span class="text-muted d-block small">
                            Correo electrónico
                        </span>

                        <strong>
                            {{ $member->email }}
                        </strong>
                    </div>

                    <div class="col-md-6">
                        <span class="text-muted d-block small">
                            Teléfono
                        </span>

                        <strong>
                            {{ $member->phone_number ?? 'No registrado' }}
                        </strong>
                    </div>

                    <div class="col-md-6">
                        <span class="text-muted d-block small">
                            Estado
                        </span>

                        @if ($member->is_active)
                        <span class="badge bg-success">
                            Activo
                        </span>
                        @else
                        <span class="badge bg-secondary">
                            Inactivo
                        </span>
                        @endif
                    </div>

                </div>
            </div>
        </div>

        <!-- Historial de membresías -->
        <div class="card shadow-sm">

            <div class="card-header bg-white py-3">
                <strong class="mb-0">
                    Historial de Membresías
                </strong>
            </div>

            <div class="card-body p-0">

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Plan</th>
                                <th>Monto Pagado</th>
                                <th>Fecha Inicio</th>
                                <th>Fecha Fin</th>
                                <th>Estado</th>
                                <th class="text-center pe-4">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse ($memberships as $membership)
                            @php
                                $lastPayment = $membership->payments->last();
                                $amountPaid = $lastPayment ? $lastPayment->amount : $membership->plan?->price;
                            @endphp

                            <tr>

                                <td class="ps-4">
                                    <strong>
                                        {{ $membership->plan?->name }}
                                    </strong>
                                </td>

                                <td>
                                    <strong class="text-dark">Q{{ number_format($amountPaid, 2) }}</strong>
                                    @if ($lastPayment?->promotion)
                                        <div class="small text-primary">
                                            <i class="bi bi-tag-fill me-1"></i>{{ $lastPayment->promotion->name }}
                                        </div>
                                    @endif
                                    @if ($membership->plan && $lastPayment && $lastPayment->amount != $membership->plan->price)
                                        <div class="small text-muted text-decoration-line-through">
                                            Q{{ number_format($membership->plan->price, 2) }}
                                        </div>
                                    @endif
                                </td>

                                <td>
                                    {{ $membership->start_date?->format('d/m/Y') }}
                                </td>

                                <td>
                                    {{ $membership->end_date?->format('d/m/Y') }}
                                </td>

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

                                    <a
                                        href="{{ route('memberships.show', [
                                            'id' => $membership->id,
                                            'memberId' => $member->id,
                                        ]) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye me-1"></i>Ver Detalles
                                    </a>

                                </td>

                            </tr>

                            @empty

                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox d-block fs-2 mb-2 opacity-50"></i>
                                    Este socio no tiene membresías registradas.
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