<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="mb-0"><i class="bi bi-door-open me-2"></i>Asistencia</h2>
            <small class="text-muted">
                Registra el check-in y check-out de los socios
            </small>
        </div>
    </x-slot>

    <div class="container-xl py-4">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Cerrar"
                ></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Cerrar"
                ></button>
            </div>
        @endif

        <div class="card shadow-sm mb-4">
            <div class="card-body">

                <form method="GET" action="{{ route('attendance.index') }}">
                    <div class="row g-3">

                        <div class="col-md-6">
                            <x-input-label for="search" :value="__('Buscar socio')" />

                            <x-text-input
                                id="search"
                                name="search"
                                type="text"
                                class="mt-1 d-block w-100"
                                :value="request('search')"
                                placeholder="Nombre o correo"
                            />
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <x-primary-button class="w-100 justify-content-center">
                                <i class="bi bi-search me-2"></i>Buscar
                            </x-primary-button>
                        </div>

                    </div>
                </form>

            </div>
        </div>

        @if (request()->filled('search'))
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <strong>Resultados de la busqueda</strong>
                </div>

                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Socio</th>
                                    <th>Correo</th>
                                    <th>Plan</th>
                                    <th>Estado de membresia</th>
                                    <th class="text-center pe-3">Accion</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($members as $row)
                                    @php
                                        $status = $row['membership']?->status;

                                        $badgeClass = match ($status?->value) {
                                            'active' => 'bg-success',
                                            'frozen' => 'bg-info',
                                            'expired' => 'bg-warning',
                                            'cancelled' => 'bg-danger',
                                            default => 'bg-secondary',
                                        };

                                        $badgeLabel = $status?->label() ?? 'Sin membresia';
                                    @endphp

                                    <tr>
                                        <td class="ps-3">{{ $row['user']->first_name }} {{ $row['user']->last_name }}</td>
                                        <td>{{ $row['user']->email }}</td>
                                        <td>{{ $row['membership']?->plan?->name ?? '—' }}</td>
                                        <td><span class="badge {{ $badgeClass }}">{{ $badgeLabel }}</span></td>
                                        <td class="pe-3 text-center">
                                            @if ($row['openAttendance'])
                                                <span class="text-muted small">Ya tiene check-in abierto</span>
                                            @else
                                                <form method="POST" action="{{ route('attendance.check-in') }}">
                                                    @csrf
                                                    <input type="hidden" name="member_id" value="{{ $row['user']->id }}">

                                                    <button type="submit" class="btn btn-sm btn-primary">
                                                        <i class="bi bi-box-arrow-in-right me-1"></i>Registrar check-in
                                                    </button>
                                                </form>
                                            @endif

                                            @if ($status && $status !== \App\Enums\MembershipStatus::Active)
                                                <a
                                                    href="{{ route('memberships.member', $row['user']->id) }}"
                                                    class="d-block small mt-1"
                                                >
                                                    Ver / renovar membresia
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            <i class="bi bi-inbox d-block fs-2 mb-2 opacity-50"></i>
                                            No se encontraron socios con ese criterio.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <strong>Asistencia de hoy</strong>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Socio</th>
                                <th>Check-in</th>
                                <th>Check-out</th>
                                <th class="text-center pe-3">Accion</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($todaysAttendances as $attendance)
                                <tr>
                                    <td class="ps-3">
                                        {{ $attendance->member->user->first_name }}
                                        {{ $attendance->member->user->last_name }}
                                    </td>
                                    <td>{{ $attendance->check_in_at->format('H:i') }}</td>
                                    <td>{{ $attendance->check_out_at?->format('H:i') ?? '—' }}</td>
                                    <td class="pe-3 text-center">
                                        @if (! $attendance->check_out_at)
                                            <form method="POST" action="{{ route('attendance.check-out', $attendance) }}">
                                                @csrf
                                                @method('PATCH')

                                                <button type="submit" class="btn btn-sm btn-outline-secondary">
                                                    <i class="bi bi-box-arrow-right me-1"></i>Registrar check-out
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        <i class="bi bi-inbox d-block fs-2 mb-2 opacity-50"></i>
                                        Todavia no hay asistencias registradas hoy.
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
