<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-0">Membresías</h2>
                <small class="text-muted">
                    Consulta y administra las membresías de los socios
                </small>
            </div>

            <div class="d-flex gap-2">
                <a href="{{ route('payments.create') }}" class="btn btn-primary">
                    Nueva Membresía / Pago
                </a>
                <a href="{{ route('payments.index') }}" class="btn btn-secondary">
                    Ver Pagos
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
                        <label for="search" class="form-label fw-bold">
                            Buscar socio
                        </label>

                        <input type="text" class="form-control" id="search" name="search" value="{{ $search ?? '' }}"
                            placeholder="Nombre, apellido o correo electrónico">
                    </div>

                    <div class="col-md-4 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            Buscar
                        </button>

                        <a href="{{ route('memberships.index') }}" class="btn btn-secondary">
                            Limpiar
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
                                            Ver Membresías
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">
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
