<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="h4 font-medium mb-0">
                {{ __('Gestión de Empleados') }}
            </h2>
            <a href="{{ route('employees.create') }}" class="btn btn-primary">
                {{ __('Agregar Empleado') }}
            </a>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="container-xl">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-4 py-3">{{ __('Nombre') }}</th>
                                    <th class="py-3">{{ __('Email') }}</th>
                                    <th class="py-3">{{ __('Teléfono') }}</th>
                                    <th class="py-3">{{ __('Rol') }}</th>
                                    <th class="py-3">{{ __('Especialidad') }}</th>
                                    <th class="py-3">{{ __('Estado') }}</th>
                                    <th class="px-4 py-3 text-end">{{ __('Acciones') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($employees as $employee)
                                    <tr>
                                        <td class="px-4 py-3 fw-medium">
                                            {{ $employee->first_name }} {{ $employee->last_name }}
                                        </td>
                                        <td>{{ $employee->email }}</td>
                                        <td>{{ $employee->phone_number ?? '-' }}</td>
                                        <td>
                                            <span class="badge bg-secondary">
                                                {{ ucfirst($employee->role->label()) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($employee->role->name === 'trainer' && $employee->trainer && $employee->trainer->specialty)
                                                {{ $employee->trainer->specialty->name }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if($employee->is_active)
                                                <span class="badge bg-success">{{ __('Activo') }}</span>
                                            @else
                                                <span class="badge bg-danger">{{ __('Inactivo') }}</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-end">
                                            <div class="d-flex justify-content-end gap-2">
                                                <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-sm btn-outline-secondary">
                                                    {{ __('Editar') }}
                                                </a>
                                                @if($employee->is_active)
                                                    <form method="POST" action="{{ route('employees.destroy', $employee->id) }}" onsubmit="return confirm('¿De verdad desea desactivar a este empleado?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            {{ __('Desactivar') }}
                                                        </button>
                                                    </form>
                                                @else($employee->is_active)

                                                    <form method="POST" action="{{ route('employees.activate', $employee->id) }}" onsubmit="return confirm('¿De verdad desea activar a este empleado?');">
                                                        @csrf
                                                        @method('POST')
                                                        <button type="submit" class="btn btn-sm btn-outline-success">
                                                            {{ __('Activar') }}
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">
                                            {{ __('No se encontraron empleados registrados.') }}
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
