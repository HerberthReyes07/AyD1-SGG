<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-0"><i class="bi bi-card-checklist me-2"></i>Detalles de Membresía #{{ $membership->id }}</h2>
                <small class="text-muted">
                    Consulta la información detallada de tu membresía
                </small>
            </div>
            <div>
                <a href="{{ route('member-memberships.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Volver al Listado
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

        <div class="row g-4">
            <!-- Información Principal -->
            <div class="col-md-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <strong class="mb-0"><i class="bi bi-info-circle me-1"></i>Información General</strong>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="form-label text-muted small fw-bold mb-0">Plan</label>
                                <p class="fs-6 fw-semibold mb-0">{{ $membership->plan?->name }}</p>
                                <p class="text-muted mb-0 small">{{ $membership->plan?->description }}</p>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label text-muted small fw-bold mb-0">Estado</label>
                                <div>
                                    @php
                                        $statusClass = match($membership->status?->value ?? '') {
                                            'active'    => 'bg-success',
                                            'frozen'    => 'bg-info text-dark',
                                            'expired'   => 'bg-secondary',
                                            'cancelled' => 'bg-danger',
                                            default     => 'bg-light text-dark'
                                        };
                                        $statusLabel = $membership->status?->label() ?? 'Desconocido';
                                    @endphp
                                    <span class="badge fs-6 {{ $statusClass }}">{{ $statusLabel }}</span>
                                </div>
                            </div>
                            @php
                                $lastPayment = $membership->payments->last();
                                $amountPaid = $lastPayment ? $lastPayment->amount : $membership->plan?->price;
                            @endphp
                            <div class="col-sm-6">
                                <label class="form-label text-muted small fw-bold mb-0">Monto Pagado</label>
                                <p class="fs-5 fw-bold text-dark mb-0">Q{{ number_format($amountPaid, 2) }}</p>
                                @if($lastPayment?->promotion)
                                    <div class="small text-primary mt-1">
                                        <i class="bi bi-tag-fill me-1"></i>Promoción: {{ $lastPayment->promotion->name }}
                                    </div>
                                @endif
                                @if($membership->plan && $lastPayment && $lastPayment->amount != $membership->plan->price)
                                    <p class="text-muted mb-0 small">Precio base del plan: Q{{ number_format($membership->plan->price, 2) }}</p>
                                @endif
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label text-muted small fw-bold mb-0">Duración</label>
                                <p class="fs-6 mb-0">{{ $membership->plan?->duration_months }} mes(es)</p>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label text-muted small fw-bold mb-0">Fecha de Inicio</label>
                                <p class="fs-6 mb-0">{{ $membership->start_date?->format('d/m/Y') }}</p>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label text-muted small fw-bold mb-0">Fecha de Vencimiento</label>
                                <p class="fs-6 mb-0">{{ $membership->end_date?->format('d/m/Y') }}</p>
                            </div>

                            @if($membership->status?->value === 'cancelled')
                                <div class="col-12 border-top pt-3">
                                    <div class="alert alert-warning mb-0">
                                        <strong>Membresía Cancelada</strong>
                                        <p class="mb-1 small"><strong>Fecha de cancelación:</strong> {{ $membership->cancellation_date?->format('d/m/Y') }}</p>
                                        <p class="mb-0 small"><strong>Motivo:</strong> {{ $membership->cancellation_reason }}</p>
                                    </div>
                                </div>
                            @endif

                            @if($membership->status?->value === 'frozen')
                                @php
                                    $openFreeze = $membership->freezes->whereNull('reactivation_date')->first();
                                @endphp
                                @if($openFreeze)
                                    <div class="col-12 border-top pt-3">
                                        <div class="alert alert-info mb-0">
                                            <strong>Membresía Congelada</strong>
                                            <p class="mb-1 small"><strong>Congelada desde:</strong> {{ $openFreeze->start_date?->format('d/m/Y') }}</p>
                                            @if($openFreeze->estimated_reactivation_date)
                                                <p class="mb-1 small"><strong>Reactivación estimada:</strong> {{ $openFreeze->estimated_reactivation_date?->format('d/m/Y') }}</p>
                                            @endif
                                            <p class="mb-0 small"><strong>Motivo:</strong> {{ $openFreeze->reason }}</p>
                                        </div>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Historial de Estado -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <strong class="mb-0"><i class="bi bi-clock-history me-1"></i>Historial de Estado</strong>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Fecha</th>
                                        <th>Estado Anterior</th>
                                        <th>Nuevo Estado</th>
                                        <th>Motivo</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($membership->statusHistories as $history)
                                        <tr>
                                            <td class="ps-3">{{ $history->change_date?->format('d/m/Y') }}</td>
                                            <td>
                                                @if($history->previous_status)
                                                    <span class="badge bg-light text-dark">{{ $history->previous_status->label() }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark">{{ $history->new_status->label() }}</span>
                                            </td>
                                            <td class="pe-3">{{ $history->reason ?? '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-3 text-muted">
                                                <i class="bi bi-inbox d-block mb-2" style="font-size: 2rem;"></i>
                                                No hay historial de cambios registrado.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panel Lateral -->
            <div class="col-md-4">

                <!-- Pagos -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <strong class="mb-0"><i class="bi bi-cash-coin me-1"></i>Pagos Registrados</strong>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            @forelse($membership->payments as $payment)
                                <li class="list-group-item d-flex justify-content-between align-items-center py-3">
                                    <div>
                                        <strong class="text-dark">Q{{ number_format($payment->amount, 2) }}</strong>
                                        <div class="text-muted small">{{ $payment->payment_date?->format('d/m/Y') }}</div>
                                    </div>
                                    <span class="badge bg-light text-dark border">
                                        {{ $payment->paymentMethod?->name }}
                                    </span>
                                </li>
                            @empty
                                <li class="list-group-item text-center py-3 text-muted">
                                    <i class="bi bi-inbox d-block mb-2" style="font-size: 2rem;"></i>
                                    No hay pagos registrados para esta membresía.
                                </li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                {{-- ── Freeze form (active only) ── --}}
                @if($membership->status?->value === 'active')
                    <div class="card shadow-sm mb-4 border-primary">
                        <div class="card-header bg-primary text-white py-3">
                            <strong class="mb-0"><i class="bi bi-snow2 me-1"></i>Solicitar Congelamiento</strong>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-3">
                                Puedes congelar tu membresía activa por un máximo de <strong>15 días acumulados</strong>
                                por trimestre (ventana móvil de 90 días). Mientras tu membresía esté congelada,
                                el tiempo no correrá y se añadirá al final de tu vigencia.
                            </p>
                            <form method="POST" action="{{ route('member-membership.freeze') }}" id="freezeForm">
                                @csrf
                                <div class="mb-3">
                                    <label for="freeze_reason" class="form-label fw-semibold">
                                        Motivo <span class="text-danger">*</span>
                                    </label>
                                    <textarea
                                        class="form-control @error('reason') is-invalid @enderror"
                                        id="freeze_reason"
                                        name="reason"
                                        rows="2"
                                        required
                                        maxlength="255"
                                        placeholder="Ej. Viaje, lesión, etc.">{{ old('reason') }}</textarea>
                                    @error('reason')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="estimated_reactivation_date" class="form-label fw-semibold">
                                        Fecha Estimada de Reactivación
                                        <span class="text-muted fw-normal">(opcional)</span>
                                    </label>
                                    <input
                                        type="date"
                                        class="form-control @error('estimated_reactivation_date') is-invalid @enderror"
                                        id="estimated_reactivation_date"
                                        name="estimated_reactivation_date"
                                        value="{{ old('estimated_reactivation_date') }}"
                                        min="{{ now()->addDay()->toDateString() }}"
                                        max="{{ now()->addDays(15)->toDateString() }}">
                                    <div class="form-text">
                                        Si no indicas una fecha, el sistema usará <strong>15 días</strong> a partir de hoy
                                        como estimado de reactivación.
                                    </div>
                                    @error('estimated_reactivation_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-snow2 me-1"></i>Confirmar Congelamiento
                                </button>
                            </form>
                        </div>
                    </div>
                @endif

                {{-- ── Cancel card (active or frozen) ── --}}
                @if(in_array($membership->status?->value, ['active', 'frozen']))
                    <div class="card shadow-sm border-danger">
                        <div class="card-header bg-danger text-white py-3">
                            <strong class="mb-0"><i class="bi bi-exclamation-triangle me-1"></i>Cancelar Membresía</strong>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small">
                                Si cancelas tu membresía, esta acción no puede deshacerse y se registrará el motivo
                                en el historial.
                            </p>
                            <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#cancelModal">
                                <i class="bi bi-x-circle me-1"></i>Cancelar Membresía
                            </button>
                        </div>
                    </div>

                    <!-- Modal Cancelación -->
                    <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="cancelModalLabel">Confirmar Cancelación</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                                </div>
                                <form method="POST" action="{{ route('member-memberships.cancel', $membership->id) }}">
                                    @csrf
                                    <div class="modal-body">
                                        <p class="text-muted small mb-3">
                                            ¿Deseas cancelar tu membresía <strong>{{ $membership->plan?->name }}</strong>?
                                            Esta acción no se puede deshacer.
                                        </p>
                                        <div class="mb-3">
                                            <label for="cancel_reason" class="form-label">
                                                Motivo de la cancelación <span class="text-danger">*</span>
                                            </label>
                                            <textarea
                                                class="form-control"
                                                id="cancel_reason"
                                                name="reason"
                                                rows="3"
                                                required
                                                placeholder="Ej. Solicitud personal, cambio de ciudad…"></textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                        <button type="submit" class="btn btn-danger"><i class="bi bi-x-circle me-1"></i>Confirmar Cancelación</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>

    </div>
</x-app-layout>
