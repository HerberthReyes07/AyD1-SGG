<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-0"><i class="bi bi-card-checklist me-2"></i>Mis Membresías</h2>
                <small class="text-muted">
                    Consulta el historial de tus membresías, estado y vigencia.
                </small>
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

        <!-- Historial de membresías -->
        <div class="card shadow-sm">
            <div class="card-header bg-white py-3">
                <strong class="mb-0">
                    <i class="bi bi-clock-history me-1"></i>Historial de Membresías
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
                                        href="{{ route('member-memberships.show', $membership->id) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye me-1"></i>Ver Detalles
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox d-block mb-2" style="font-size: 2.5rem;"></i>
                                    No tienes membresías registradas.
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
