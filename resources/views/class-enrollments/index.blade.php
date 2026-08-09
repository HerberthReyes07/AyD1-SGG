<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">

            <div>
                <h2 class="mb-0">
                    Inscripciones - {{ $session->groupClass->name }}
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
                Volver a sesiones
            </a>

        </div>
    </x-slot>

    <div class="container-xl py-4">

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
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
                                Inscribir
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
                                                Cancelar inscripcion
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
                                <th>Solicitud</th>
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
                                        {{ $waitlist->created_at->format('d/m/Y H:i') }}
                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td
                                        colspan="4"
                                        class="text-center py-4 text-muted"
                                    >
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