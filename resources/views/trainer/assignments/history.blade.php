<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h2 class="mb-0"><i class="bi bi-clock-history me-2"></i>{{ __('Historial de asignaciones') }}</h2>
                <small class="text-muted">
                    {{ __('Revisa el registro completo de asignaciones activas y finalizadas') }}
                </small>
            </div>

            <a href="{{ route('assignments.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> {{ __('Volver al listado') }}
            </a>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="container-xl">

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
                                    <th class="py-3">{{ __('Email') }}</th>
                                    <th class="py-3">{{ __('Teléfono') }}</th>
                                    <th class="py-3">{{ __('Fecha de asignación') }}</th>
                                    <th class="py-3">{{ __('Fecha de finalización') }}</th>
                                    <th class="py-3">{{ __('Estado') }}</th>
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
                                        {{ $assignment->member?->user?->email ?? '-' }}
                                    </td>
                                    <td>
                                        {{ $assignment->member?->user?->phone ?? '-' }}
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
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox display-6 d-block mb-2 opacity-50"></i>
                                        {{ __('No se encontraron asignaciones registradas.') }}
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
