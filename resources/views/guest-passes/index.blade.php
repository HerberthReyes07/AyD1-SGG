<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h2 class="mb-0"><i class="bi bi-person-badge me-2"></i>Pases de invitado</h2>
                <small class="text-muted">
                    Consulta y registra los pases otorgados
                </small>
            </div>

            <a
                href="{{ route('guest-passes.create') }}"
                class="btn btn-primary"
            >
                <i class="bi bi-plus-lg me-1"></i>Nuevo pase
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
                            <x-input-label for="search" :value="__('Buscar invitado')" />

                            <x-text-input
                                id="search"
                                name="search"
                                type="text"
                                class="mt-1 d-block w-100"
                                :value="request('search')"
                                placeholder="Nombre o DPI"
                            />
                        </div>

                        <div class="col-md-2">
                            <x-input-label for="date_from" :value="__('Desde')" />

                            <x-text-input
                                id="date_from"
                                name="date_from"
                                type="date"
                                class="mt-1 d-block w-100 @error('date_from') is-invalid @enderror"
                                :value="request('date_from')"
                            />

                            <x-input-error :messages="$errors->get('date_from')" class="mt-1" />
                        </div>

                        <div class="col-md-2">
                            <x-input-label for="date_to" :value="__('Hasta')" />

                            <x-text-input
                                id="date_to"
                                name="date_to"
                                type="date"
                                class="mt-1 d-block w-100 @error('date_to') is-invalid @enderror"
                                :value="request('date_to')"
                            />

                            <x-input-error :messages="$errors->get('date_to')" class="mt-1" />
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <x-primary-button class="w-100 justify-content-center">
                                <i class="bi bi-search me-1"></i>Buscar
                            </x-primary-button>
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <a
                                href="{{ route('guest-passes.index') }}"
                                class="btn btn-outline-secondary w-100"
                            >
                                <i class="bi bi-x-circle me-1"></i>Limpiar
                            </a>
                        </div>

                    </div>
                </form>

            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

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
                                        <i class="bi bi-inbox d-block fs-2 mb-2 opacity-50"></i>
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