<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

            <div>
                <h2 class="mb-0">
                    <i class="bi bi-people me-2"></i>Inscripciones - {{ $session->groupClass->name }}
                </h2>

                <small class="text-muted">
                    {{ $session->starts_at->format('d/m/Y H:i') }}
                </small>
            </div>

            <a
                href="{{ route(
                    'class-sessions.index',
                    $session->groupClass
                ) }}"
                class="btn btn-outline-secondary"
            >
                <i class="bi bi-arrow-left me-1"></i>Volver a sesiones
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

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="alert"
                    aria-label="Cerrar"
                ></button>
            </div>
        @endif

        <div class="card shadow-sm mb-4">

            <div class="card-header bg-white">
                <strong>Estado del cupo</strong>
            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-4">
                        <span class="text-muted">
                            Inscritos
                        </span>

                        <h3>
                            {{ $enrollments->count() }}
                        </h3>
                    </div>

                    <div class="col-md-4">
                        <span class="text-muted">
                            Cupo maximo
                        </span>

                        <h3>
                            {{ $session->groupClass->max_participants }}
                        </h3>
                    </div>

                    <div class="col-md-4">
                        <span class="text-muted">
                            Lista de espera
                        </span>

                        <h3>
                            {{ $waitlists->count() }}
                        </h3>
                    </div>

                </div>

            </div>
        </div>

        <div class="card shadow-sm mb-4">

            <div class="card-header bg-white">
                <strong>Registrar inscripcion</strong>
            </div>

            <div class="card-body">

                <form
                    method="POST"
                    action="{{ route(
                        'class-enrollments.store',
                        $session
                    ) }}"
                >
                    @csrf

                    <div class="row g-3">

                        <div class="col-md-10">

                            <label
                                for="member_id"
                                class="form-label"
                            >
                                Socio
                            </label>

                            <select
                                id="member_id"
                                name="member_id"
                                class="form-select"
                                required
                            >

                                <option value="">
                                    Seleccione un socio
                                </option>

                                @foreach ($members as $member)

                                    <option
                                        value="{{ $member->user_id }}"
                                    >
                                        {{ $member->user->first_name }}
                                        {{ $member->user->last_name }}
                                        -
                                        {{ $member->user->email }}
                                    </option>

                                @endforeach

                            </select>

                        </div>

                        <div class="col-md-2 d-flex align-items-end">

                            <button
                                type="submit"
                                class="btn btn-primary w-100"
                            >
                                <i class="bi bi-plus-lg me-1"></i>Inscribir
                            </button>

                        </div>

                    </div>

                </form>

            </div>
        </div>

        <div class="card shadow-sm mb-4">

            <div class="card-header bg-white">
                <strong>Socios inscritos</strong>
            </div>

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover mb-0">

                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Socio</th>
                                <th>Correo</th>
                                <th>Fecha de inscripcion</th>
                                <th class="text-end pe-3">
                                    Acciones
                                </th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse ($enrollments as $enrollment)

                                <tr>

                                    <td class="ps-3">
                                        {{ $enrollment->member->user->first_name }}
                                        {{ $enrollment->member->user->last_name }}
                                    </td>

                                    <td>
                                        {{ $enrollment->member->user->email }}
                                    </td>

                                    <td>
                                        {{ $enrollment->enrollment_date->format('d/m/Y') }}
                                    </td>

                                    <td class="text-end pe-3">

                                        <form
                                            method="POST"
                                            action="{{ route(
                                                'class-enrollments.cancel',
                                                [
                                                    $session,
                                                    $enrollment->member
                                                ]
                                            ) }}"
                                        >
                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-outline-danger"
                                            >
                                                <i class="bi bi-x-circle me-1"></i>Cancelar inscripcion
                                            </button>

                                        </form>

                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td
                                        colspan="4"
                                        class="text-center py-4 text-muted"
                                    >
                                        <i class="bi bi-inbox text-muted d-block mb-2" style="font-size: 2.5rem;"></i>
                                        No hay socios inscritos.
                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>
        </div>

        <div class="card shadow-sm">

            <div class="card-header bg-white">
                <strong>Lista de espera</strong>
            </div>

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover mb-0">

                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Socio</th>
                                <th>Plan</th>
                                <th>Prioridad</th>
                                <th>Estado</th>
                                <th>Solicitud</th>
                                <th class="text-end pe-3">Acciones</th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse ($waitlists as $waitlist)

                                @php
                                    $membership = $waitlist
                                        ->member
                                        ->memberships
                                        ->where('status', \App\Enums\MembershipStatus::Active)
                                        ->first();

                                    $plan = $membership?->plan;
                                @endphp

                                <tr>

                                    <td class="ps-3">
                                        {{ $waitlist->member->user->first_name }}
                                        {{ $waitlist->member->user->last_name }}
                                    </td>

                                    <td>
                                        {{ $plan?->name ?? 'Sin plan activo' }}
                                    </td>

                                    <td>
                                        @if ($plan?->has_waitlist_priority)
                                            <span class="badge bg-warning text-dark">
                                                Elite
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                Normal
                                            </span>
                                        @endif
                                    </td>

                                    <td>
                                        @if ($waitlist->status === \App\Enums\WaitlistStatus::Notified)
                                            <span class="badge bg-info text-dark">
                                                <i class="bi bi-envelope-check me-1"></i>Notificado
                                            </span>
                                        @else
                                            <span class="badge bg-light text-dark border">
                                                <i class="bi bi-hourglass-split me-1"></i>Esperando
                                            </span>
                                        @endif
                                    </td>

                                    <td>
                                        {{ $waitlist->created_at->format('d/m/Y H:i') }}
                                    </td>

                                    <td class="text-end pe-3">
                                        <form
                                            method="POST"
                                            action="{{ route(
                                                'class-enrollments.waitlist.cancel',
                                                [$session, $waitlist->member]
                                            ) }}"
                                        >
                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                class="btn btn-sm btn-outline-danger"
                                            >
                                                <i class="bi bi-x-circle me-1"></i>Retirar
                                            </button>

                                        </form>
                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td
                                        colspan="6"
                                        class="text-center py-4 text-muted"
                                    >
                                        <i class="bi bi-inbox text-muted d-block mb-2" style="font-size: 2.5rem;"></i>
                                        No hay socios en lista de espera.
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