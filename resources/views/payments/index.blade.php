<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h2 class="mb-0"><i class="bi bi-cash-coin me-2"></i>Registro de Pagos</h2>
                <small class="text-muted">
                    Historial de cobros y pagos de membresías registrados
                </small>
            </div>
            <div class="d-flex flex-wrap gap-2">
                {{-- <a href="{{ route('payments.create') }}" class="btn btn-primary">
                    Nueva Membresía / Pago
                </a> --}}
                <a href="{{ route('memberships.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Regresar
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
                <strong class="mb-0">Historial General de Transacciones</strong>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Transacción</th>
                                <th>Socio</th>
                                <th>Membresía / Plan</th>
                                <th>Monto Pagado</th>
                                <th>Método de Pago</th>
                                <th>Promoción</th>
                                <th>Fecha</th>
                                <th>Registrado Por</th>
                                <th class="pe-4 text-end">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($payments as $payment)
                            <tr>
                                <td class="ps-4">
                                    <strong>#{{ $payment->id }}</strong>
                                </td>
                                <td>
                                    <strong>
                                        {{ $payment->memberMembership?->member?->user?->first_name }}
                                        {{ $payment->memberMembership?->member?->user?->last_name }}
                                    </strong>
                                    <div class="text-muted small">
                                        {{ $payment->memberMembership?->member?->user?->email }}
                                    </div>
                                </td>
                                <td>
                                    @if (in_array(Auth::user()->role?->name, ['admin', 'receptionist']) && $payment->memberMembership)
                                        <a href="{{ route('memberships.show', ['id' => $payment->member_membership_id, 'memberId' => $payment->memberMembership->member_id]) }}" class="text-decoration-none">
                                            Membresía #{{ $payment->member_membership_id }}
                                        </a>
                                    @else
                                        <span>Membresía #{{ $payment->member_membership_id }}</span>
                                    @endif
                                    <div class="text-muted small">
                                        Plan: {{ $payment->memberMembership?->plan?->name }}
                                    </div>
                                </td>
                                <td>
                                    <strong class="text-success">Q{{ number_format($payment->amount, 2) }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        {{ $payment->paymentMethod?->name }}
                                    </span>
                                </td>
                                <td>
                                    @if($payment->promotion)
                                    <span class="badge bg-light text-primary border"><i class="bi bi-tag-fill me-1"></i>{{ $payment->promotion->name }}</span>
                                    @else
                                    <span class="text-muted small">Ninguna</span>
                                    @endif
                                </td>
                                <td>
                                    {{ $payment->payment_date?->format('d/m/Y') }}
                                </td>
                                <td>
                                    {{ $payment->registeredBy?->first_name }} {{ $payment->registeredBy?->last_name }}
                                </td>
                                <td class="pe-4 text-end">
                                    <div class="d-flex justify-content-end gap-1">
                                        <a href="{{ route('payments.show', $payment->id) }}" class="btn btn-sm btn-outline-primary" title="Ver Detalle">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('payments.pdf', $payment->id) }}" class="btn btn-sm btn-outline-danger" title="Descargar PDF" target="_blank">
                                            <i class="bi bi-file-earmark-pdf"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox d-block fs-2 mb-2 opacity-50"></i>
                                    No se encontraron registros de pagos.
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
