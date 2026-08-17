<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h2 class="mb-0"><i class="bi bi-receipt me-2"></i>Detalle de Transacción #{{ $payment->id }}</h2>
                <small class="text-muted">Comprobante de pago de membresía</small>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('payments.pdf', $payment->id) }}" class="btn btn-danger" target="_blank">
                    <i class="bi bi-file-earmark-pdf me-1"></i>Descargar Comprobante PDF
                </a>
                <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Regresar
                </a>
            </div>
        </div>
    </x-slot>

    <div class="container-xl py-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                        <strong class="mb-0">Resumen del Pago</strong>
                        <span class="badge bg-success fs-6">Pago Registrado</span>
                    </div>

                    <div class="card-body p-4">
                        <!-- Datos del Socio -->
                        <h6 class="text-uppercase text-muted fw-bold mb-3"><i class="bi bi-person me-1"></i>Datos del Socio</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-sm-6">
                                <span class="text-muted small d-block">Nombre del Socio</span>
                                <strong>
                                    {{ $payment->memberMembership?->member?->user?->first_name }}
                                    {{ $payment->memberMembership?->member?->user?->last_name }}
                                </strong>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted small d-block">Correo Electrónico</span>
                                <strong>{{ $payment->memberMembership?->member?->user?->email }}</strong>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Datos de la Membresía -->
                        <h6 class="text-uppercase text-muted fw-bold mb-3"><i class="bi bi-card-checklist me-1"></i>Detalle de la Membresía</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-sm-6">
                                <span class="text-muted small d-block">Plan Adquirido</span>
                                <strong>{{ $payment->memberMembership?->plan?->name }}</strong>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted small d-block">Duración</span>
                                <strong>{{ $payment->memberMembership?->plan?->duration_months }} mes(es)</strong>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted small d-block">Vigencia</span>
                                <span>
                                    {{ $payment->memberMembership?->start_date?->format('d/m/Y') }} al
                                    {{ $payment->memberMembership?->end_date?->format('d/m/Y') }}
                                </span>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted small d-block">Método de Pago</span>
                                <span class="badge bg-light text-dark border">
                                    {{ $payment->paymentMethod?->name ?? 'No especificado' }}
                                </span>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted small d-block">Fecha de Transacción</span>
                                <span>{{ $payment->payment_date?->format('d/m/Y') }}</span>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted small d-block">Registrado Por</span>
                                <span>{{ $payment->registeredBy?->first_name }} {{ $payment->registeredBy?->last_name }}</span>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Desglose Financiero -->
                        <h6 class="text-uppercase text-muted fw-bold mb-3"><i class="bi bi-cash-stack me-1"></i>Desglose Financiero</h6>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Concepto</th>
                                        <th class="text-end">Monto</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Precio Base del Plan ({{ $payment->memberMembership?->plan?->name }})</td>
                                        <td class="text-end">Q{{ number_format($payment->memberMembership?->plan?->price, 2) }}</td>
                                    </tr>
                                    @if ($payment->promotion)
                                        <tr>
                                            <td>
                                                <i class="bi bi-tag-fill text-primary me-1"></i>
                                                Promoción Aplicada: <strong>{{ $payment->promotion->name }}</strong>
                                                <div class="text-muted small">
                                                    ({{ $payment->promotion->type->label() }}:
                                                    {{ $payment->promotion->type->value === 'percentage' ? number_format($payment->promotion->value, 0) . '%' : 'Q' . number_format($payment->promotion->value, 2) }})
                                                </div>
                                            </td>
                                            <td class="text-end text-danger">
                                                -Q{{ number_format(max(0, ($payment->memberMembership?->plan?->price ?? 0) - $payment->amount), 2) }}
                                            </td>
                                        </tr>
                                    @endif
                                    <tr class="table-primary fw-bold fs-5">
                                        <td>Total Pagado</td>
                                        <td class="text-end text-primary">Q{{ number_format($payment->amount, 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="card-footer bg-white py-3 text-end">
                        <a href="{{ route('payments.pdf', $payment->id) }}" class="btn btn-danger">
                            <i class="bi bi-file-earmark-pdf me-1"></i>Descargar Comprobante PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
