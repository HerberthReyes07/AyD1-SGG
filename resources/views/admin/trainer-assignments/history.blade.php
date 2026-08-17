<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h2 class="mb-0"><i class="bi bi-clock-history me-2"></i>{{ __('Historial de asignaciones de entrenadores') }}</h2>
                <small class="text-muted">
                    {{ __('Revisa el registro completo de asignaciones activas y finalizadas') }}
                </small>
            </div>

            <a href="{{ route('trainer-assignments.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>{{ __('Volver al listado') }}
            </a>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="container-xl">
            @if (session('status'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('status') }}

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
            @endif

            @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <strong>{{ __('Registro de asignaciones') }}</strong>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-4 py-3">{{ __('Socio') }}</th>
                                    <th class="py-3">{{ __('Entrenador') }}</th>
                                    <th class="py-3">{{ __('Fecha de asignación') }}</th>
                                    <th class="py-3">{{ __('Fecha de finalización') }}</th>
                                    <th class="py-3">{{ __('Estado') }}</th>
                                    <th class="px-4 py-3 text-end">{{ __('Acciones') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($assignments as $assignment)
                                <tr>
                                    <td class="px-4 py-3 fw-medium">
                                        {{ $assignment->member?->user?->first_name }} {{
                                        $assignment->member?->user?->last_name }}
                                    </td>
                                    <td>
                                        {{ $assignment->trainer?->user?->first_name }} {{
                                        $assignment->trainer?->user?->last_name }}
                                    </td>
                                    <td>
                                        {{ $assignment->assignment_date?->format('d/m/Y') ?? '-' }}
                                    </td>
                                    <td>
                                        {{ $assignment->end_date?->format('d/m/Y') ?? '-' }}
                                    </td>
                                    <td>
                                        @if (is_null($assignment->end_date))
                                        <span class="badge bg-success">{{ __('Activa') }}</span>
                                        @else
                                        <span class="badge bg-secondary">{{ __('Finalizada') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-end">
                                        <button type="button" class="btn btn-sm btn-outline-secondary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#history-details-modal-{{ $assignment->id }}">
                                            <i class="bi bi-eye me-1"></i>{{ __('Ver detalle') }}
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox text-muted d-block mb-2" style="font-size: 2.5rem;"></i>
                                        {{ __('No se encontraron asignaciones registradas.') }}
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @foreach ($assignments as $assignment)
            <x-modal name="history-details-modal-{{ $assignment->id }}" maxWidth="md">
                <div class="modal-header">
                    <h5 class="modal-title" id="history-details-modal-{{ $assignment->id }}Label">
                        {{ __('Detalle de la asignación') }}
                    </h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <span class="text-muted d-block">{{ __('Socio') }}</span>
                            <strong>{{ $assignment->member?->user?->first_name }} {{
                                $assignment->member?->user?->last_name }}</strong>
                        </div>

                        <div class="col-md-6">
                            <span class="text-muted d-block">{{ __('Entrenador') }}</span>
                            <strong>{{ $assignment->trainer?->user?->first_name }} {{
                                $assignment->trainer?->user?->last_name }}</strong>
                        </div>

                        <div class="col-md-6">
                            <span class="text-muted d-block">{{ __('Objetivo') }}</span>
                            <strong>{{ $assignment->goal ?? __('Sin objetivo registrado') }}</strong>
                        </div>

                        <div class="col-md-6">
                            <span class="text-muted d-block">{{ __('Registrada por') }}</span>
                            <strong>{{ $assignment->assignedBy?->first_name }} {{ $assignment->assignedBy?->last_name
                                }}</strong>
                        </div>

                        <div class="col-md-6">
                            <span class="text-muted d-block">{{ __('Fecha de asignación') }}</span>
                            <strong>{{ $assignment->assignment_date?->format('d/m/Y') ?? '-' }}</strong>
                        </div>

                        <div class="col-md-6">
                            <span class="text-muted d-block">{{ __('Estado') }}</span>
                            <strong>
                                @if (is_null($assignment->end_date))
                                {{ __('Activa') }}
                                @else
                                {{ __('Finalizada') }}
                                @endif
                            </strong>
                        </div>

                        @if ($assignment->reassignment_reason)
                        <div class="col-6">
                            <span class="text-muted d-block">{{ __('Fecha de finalización') }}</span>
                            <p class="mb-0"><strong>{{ $assignment->end_date?->format('d/m/Y')}}</strong></p>
                        </div>
                        <div class="col-6">
                            <span class="text-muted d-block">{{ __('Motivo de reasignación') }}</span>
                            <p class="mb-0">{{ $assignment->reassignment_reason }}</p>
                        </div>
                        @endif
                    </div>
                </div>
            </x-modal>
            @endforeach
        </div>
    </div>
</x-app-layout>
