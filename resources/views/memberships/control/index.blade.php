<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h2 class="mb-0"><i class="bi bi-card-checklist me-2"></i>Membresías</h2>
                <small class="text-muted">
                    Consulta y administra las membresías de los socios
                </small>
            </div>

            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('payments.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-1"></i>Nueva Membresía / Pago
                </a>
                <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-cash-coin me-1"></i>Ver Pagos
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

        <div class="card shadow-sm">
            <!-- <div class="card-header bg-white py-3">
                <strong class="mb-0">Historial de Membresías</strong>
            </div> -->

            <!-- Member Filtering -->
            <div class="card-body border-bottom">
                <form method="GET" action="{{ route('memberships.index') }}" class="row g-3 align-items-end">

                    <div class="col-md-8">
                        <x-input-label for="search" :value="__('Buscar socio')" class="fw-bold" />

                        <x-text-input
                            id="search"
                            name="search"
                            type="text"
                            class="mt-1 d-block w-100"
                            :value="$search ?? ''"
                            placeholder="Nombre, apellido o correo electrónico"
                        />
                    </div>

                    <div class="col-md-4 d-flex flex-wrap gap-2">
                        <x-primary-button>
                            <i class="bi bi-search me-1"></i>Buscar
                        </x-primary-button>

                        <a href="{{ route('memberships.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-1"></i>Limpiar
                        </a>
                    </div>

                </form>
            </div>

            <!-- Member List -->
            <div class="card-body p-0 border-bottom">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Socio</th>
                                <th>Correo</th>
                                <th>Teléfono</th>
                                <th class="text-center pe-4">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($members as $member)
                            <tr>
                                <td class="ps-4">
                                    <strong>
                                        {{ $member->first_name }} {{ $member->last_name }}
                                    </strong>
                                </td>

                                <td>
                                    {{ $member->email }}
                                </td>

                                <td>
                                    {{ $member->phone_number ?? 'No registrado' }}
                                </td>

                                <td class="pe-4 text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="{{ route('memberships.member', $member->id) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye me-1"></i>Ver Membresías
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
                                    <i class="bi bi-inbox d-block fs-2 mb-2 opacity-50"></i>
                                    No se encontraron socios.
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
