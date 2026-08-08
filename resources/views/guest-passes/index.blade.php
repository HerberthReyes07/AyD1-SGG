<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-0">Pases de invitado</h2>
                <small class="text-muted">
                    Consulta y registra los pases otorgados
                </small>
            </div>

            <a
                href="{{ route('guest-passes.create') }}"
                class="btn btn-primary"
            >
                Nuevo pase
            </a>
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

        <div class="card shadow-sm mb-4">
            <div class="card-body">

                <form method="GET" action="{{ route('guest-passes.index') }}">
                    <div class="row g-3">

                        <div class="col-md-4">
                            <label for="search" class="form-label">
                                Buscar invitado
                            </label>

                            <input
                                type="text"
                                class="form-control"
                                id="search"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="Nombre o DPI"
                            >
                        </div>

                        <div class="col-md-2">
                            <label for="date_from" class="form-label">
                                Desde
                            </label>

                            <input
                                type="date"
                                class="form-control @error('date_from') is-invalid @enderror"
                                id="date_from"
                                name="date_from"
                                value="{{ request('date_from') }}"
                            >

                            @error('date_from')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-2">
                            <label for="date_to" class="form-label">
                                Hasta
                            </label>

                            <input
                                type="date"
                                class="form-control @error('date_to') is-invalid @enderror"
                                id="date_to"
                                name="date_to"
                                value="{{ request('date_to') }}"
                            >

                            @error('date_to')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <button
                                type="submit"
                                class="btn btn-primary w-100"
                            >
                                Buscar
                            </button>
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <a
                                href="{{ route('guest-passes.index') }}"
                                class="btn btn-outline-secondary w-100"
                            >
                                Limpiar
                            </a>
                        </div>

                    </div>
                </form>

            </div>
        </div>

        <div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">

            <div>
                <span class="text-muted">
                    Pases encontrados
                </span>

                <h3 class="mb-0">
                    {{ $totalGuestPasses }}
                </h3>
            </div>

            @if (request('date_from') || request('date_to'))
                <div class="text-end text-muted">
                    Periodo:

                    <strong>
                        {{ request('date_from') ?: 'Inicio' }}
                    </strong>

                    a

                    <strong>
                        {{ request('date_to') ?: 'Actualidad' }}
                    </strong>
                </div>
            @endif

        </div>
    </div>
</div>

        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <strong>Pases registrados</strong>
            </div>

            <div class="card-body p-0">

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Invitado</th>
                                <th>DPI</th>
                                <th>Fecha de visita</th>
                                <th>Registrado por</th>
                                <th>Fecha de registro</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse ($guestPasses as $guestPass)

                                <tr>
                                    <td class="ps-3">
                                        <strong>
                                            {{ $guestPass->guest_name }}
                                        </strong>
                                    </td>

                                    <td>
                                        {{ $guestPass->dpi }}
                                    </td>

                                    <td>
                                        {{ $guestPass->visit_date->format('d/m/Y') }}
                                    </td>

                                    <td>
                                        @if ($guestPass->registeredBy)
                                            {{ $guestPass->registeredBy->first_name }}
                                            {{ $guestPass->registeredBy->last_name }}
                                        @else
                                            Usuario no disponible
                                        @endif
                                    </td>

                                    <td>
                                        {{ $guestPass->created_at->format('d/m/Y H:i') }}
                                    </td>
                                </tr>

                            @empty

                                <tr>
                                    <td
                                        colspan="5"
                                        class="text-center py-4 text-muted"
                                    >
                                        No se encontraron pases de invitado.
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