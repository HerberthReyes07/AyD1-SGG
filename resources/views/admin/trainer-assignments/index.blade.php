<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-0">{{ __('Asignaciones de entrenadores') }}</h2>
                <small class="text-muted">
                    {{ __('Consulta las asignaciones activas y administra las reasignaciones') }}
                </small>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('trainer-assignments.history') }}" class="btn btn-outline-info">
                    {{ __('Historial') }}
                </a>
                <a href="{{ route('trainer-assignments.create') }}" class="btn btn-primary">
                    {{ __('Nueva asignación') }}
                </a>
            </div>
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
                    <strong>{{ __('Asignaciones activas') }}</strong>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-4 py-3">{{ __('Socio') }}</th>
                                    <th class="py-3">{{ __('Entrenador') }}</th>
                                    <th class="py-3">{{ __('Fecha de asignación') }}</th>
                                    <th class="py-3">{{ __('Estado') }}</th>
                                    <th class="px-4 py-3 text-end">{{ __('Acciones') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($assignments as $assignment)
                                    <tr>
                                        <td class="px-4 py-3 fw-medium">
                                            {{ $assignment->member?->user?->first_name }} {{ $assignment->member?->user?->last_name }}
                                        </td>
                                        <td>
                                            {{ $assignment->trainer?->user?->first_name }} {{ $assignment->trainer?->user?->last_name }}
                                        </td>
                                        <td>
                                            {{ $assignment->assignment_date?->format('d/m/Y') ?? '-' }}
                                        </td>
                                        <td>
                                            @if (is_null($assignment->end_date))
                                                <span class="badge bg-success">{{ __('Activa') }}</span>
                                            @else
                                                <span class="badge bg-secondary">{{ __('Finalizada') }}</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-end">
                                            <div class="d-flex justify-content-end gap-1 flex-nowrap">
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-outline-secondary"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#assignment-details-modal-{{ $assignment->id }}"
                                                >
                                                    {{ __('Ver detalle') }}
                                                </button>

                                                <a href="{{ route('trainer-assignments.reassign.create', $assignment) }}"
                                                    class="btn btn-sm btn-outline-primary">
                                                    {{ __('Reasignar') }}
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
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
                <x-modal name="assignment-details-modal-{{ $assignment->id }}" maxWidth="md">
                    <div class="modal-header">
                        <h5 class="modal-title" id="assignment-details-modal-{{ $assignment->id }}Label">
                            {{ __('Detalle de la asignación') }}
                        </h5>

                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
                    </div>

                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <span class="text-muted d-block">{{ __('Socio') }}</span>
                                <strong>{{ $assignment->member?->user?->first_name }} {{ $assignment->member?->user?->last_name }}</strong>
                            </div>

                            <div class="col-md-6">
                                <span class="text-muted d-block">{{ __('Entrenador') }}</span>
                                <strong>{{ $assignment->trainer?->user?->first_name }} {{ $assignment->trainer?->user?->last_name }}</strong>
                            </div>

                            <div class="col-md-6">
                                <span class="text-muted d-block">{{ __('Objetivo') }}</span>
                                <strong>{{ $assignment->goal ?? __('Sin objetivo registrado') }}</strong>
                            </div>

                            <div class="col-md-6">
                                <span class="text-muted d-block">{{ __('Registrada por') }}</span>
                                <strong>{{ $assignment->assignedBy?->first_name }} {{ $assignment->assignedBy?->last_name }}</strong>
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
                                <div class="col-12">
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
