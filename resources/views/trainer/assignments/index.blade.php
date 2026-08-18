<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h2 class="mb-0"><i class="bi bi-person-lines-fill me-2"></i>{{ __('Mis socios asignados') }}</h2>
                <small class="text-muted">
                    {{ __('Lista de socios asignados al entrenador') }}
                </small>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('assignments.history') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-clock-history"></i> {{ __('Historial') }}
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="container-xl">

            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <strong>{{ __('Socios asignados activos') }}</strong>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-4 py-3">{{ __('Socio') }}</th>
                                    <th class="py-3">{{ __('Email') }}</th>
                                    <th class="py-3">{{ __('Teléfono') }}</th>
                                    <th class="py-3">{{ __('Objetivo') }}</th>
                                    <th class="py-3">{{ __('Fecha de asignación') }}</th>
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
                                        {{ $assignment->member?->user?->email ?? '-' }}
                                    </td>
                                    <td>
                                        {{ $assignment->phone ?? '—' }}
                                    </td>
                                    <td>
                                        {{ $assignment->goal ?? '—' }}
                                    </td>
                                    <td>
                                        {{ $assignment->assignment_date?->format('d/m/Y') ?? '-' }}
                                    </td>
                                    <td class="px-4 py-3 text-end">
                                        <div class="d-flex justify-content-end gap-1 flex-nowrap">
                                            <a href="{{ route('assignments.show', $assignment) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i> Ver detalle
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox display-6 d-block mb-2 opacity-50"></i>
                                        {{ __('No se encontraron asignaciones activas.') }}
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
