<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h2 class="mb-0"><i class="bi bi-card-checklist me-2"></i>Detalles de Membresía #{{ $membership->id }}</h2>
                <small class="text-muted">
                    Consulta la información detallada de la membresía del socio
                </small>
            </div>
            <div>
                <a href="{{ route('memberships.member', $member->id) }}" class="btn btn-outline-secondary">
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
                        <strong class="mb-0">Información General</strong>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="form-label text-muted small uppercase fw-bold mb-0">Socio</label>
                                <p class="fs-5 fw-bold mb-0">
                                    {{ $membership->member?->user?->first_name }} {{ $membership->member?->user?->last_name }}
                                </p>
                                <p class="text-muted mb-0 small">{{ $membership->member?->user?->email }}</p>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label text-muted small uppercase fw-bold mb-0">Estado</label>
                                <div>
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
                                    <span class="badge fs-6 {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label text-muted small uppercase fw-bold mb-0">Plan</label>
                                <p class="fs-6 fw-semibold mb-0">{{ $membership->plan?->name }}</p>
                                <p class="text-muted mb-0 small">{{ $membership->plan?->description }}</p>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label text-muted small uppercase fw-bold mb-0">Precio del Plan</label>
                                <p class="fs-5 fw-bold text-dark mb-0">Q{{ number_format($membership->plan?->price, 2) }}</p>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label text-muted small uppercase fw-bold mb-0">Fecha de Inicio</label>
                                <p class="fs-6 mb-0">{{ $membership->start_date?->format('d/m/Y') }}</p>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label text-muted small uppercase fw-bold mb-0">Fecha de Fin</label>
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
                            @php $openFreeze = $membership->freezes->whereNull('reactivation_date')->first();
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

                <!-- Historial de Cambios de Estado -->
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <strong class="mb-0">Historial de Estado</strong>
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
                                        <th class="pe-3">Registrado Por</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($membership->statusHistories as $history)
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
                                        <td>{{ $history->reason ?? '-' }}</td>
                                        <td class="pe-3">
                                            {{ $history->changedBy?->first_name }} {{ $history->changedBy?->last_name }}
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-3 text-muted">
                                            <i class="bi bi-inbox d-block fs-2 mb-2 opacity-50"></i>
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

            <!-- Panel Lateral de Acciones -->
            <div class="col-md-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <strong class="mb-0">Pagos Registrados</strong>
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
                                <i class="bi bi-inbox d-block fs-2 mb-2 opacity-50"></i>
                                No hay pagos registrados para esta membresía.
                            </li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                @if($membership->status?->value === 'active')
                <div class="card shadow-sm border-danger">
                    <div class="card-header bg-danger text-white py-3">
                        <strong class="mb-0">Acciones de Control</strong>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small">
                            Cancela la membresía activa del socio. Se le solicitará un motivo y los registros de historial serán preservados.
                        </p>

                        <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#cancelModal">
                            <i class="bi bi-x-circle me-1"></i>Cancelar Membresía
                        </button>
                    </div>
                </div>

                <!-- Modal de Cancelación -->
                <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="cancelModalLabel">Confirmar Cancelación de Membresía</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                            </div>
                            <form method="POST" action="{{ route('memberships.destroy', $membership->id) }}">
                                @csrf
                                @method('DELETE')
                                <div class="modal-body">
                                    <p class="text-muted small mb-3">
                                        ¿Desea cancelar la membresía activa de <strong>{{ $membership->member?->user?->first_name }} {{ $membership->member?->user?->last_name }}</strong>? Esta acción no se puede deshacer.
                                    </p>
                                    <div class="mb-3">
                                        <x-input-label for="reason" :value="__('Motivo de la cancelación:')" />
                                        <textarea class="form-control" id="reason" name="reason" rows="3" required placeholder="Ej. Solicitud del cliente por mudanza"></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cerrar</button>
                                    <button type="submit" class="btn btn-danger"><i class="bi bi-x-circle me-1"></i>Confirmar Cancelación</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endif

                @if($membership->status?->value === 'frozen')
                <div class="card shadow-sm border-primary mt-3">
                    <div class="card-header bg-primary text-white py-3">
                        <strong class="mb-0">Acciones de Control</strong>
                    </div>
                    <div class="card-body">
                        <p class="text-muted small">
                            Reactiva manualmente la membresía congelada del socio. El período congelado será compensado extendiendo la fecha de vencimiento.
                        </p>
                        <form method="POST" action="{{ route('memberships.reactivate', $membership->id) }}">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-arrow-repeat me-1"></i>Reactivar Membresía
                            </button>
                        </form>
                    </div>
                </div>
                @endif
            </div>
        </div>

    </div>
</x-app-layout>