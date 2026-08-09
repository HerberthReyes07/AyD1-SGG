<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">

            <div>
                <h2 class="mb-0">
                    {{ $session->groupClass->name }}
                </h2>

                <small class="text-muted">
                    {{ $session->starts_at->format('d/m/Y H:i') }}
                </small>
            </div>

            <a
                href="{{ route('trainer-classes.index') }}"
                class="btn btn-outline-secondary"
            >
                Volver
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

            <div class="card-body">

                <div class="row g-3">

                    <div class="col-md-3">
                        <span class="text-muted">
                            Estado
                        </span>

                        <h4>
                            {{ $session->status->label() }}
                        </h4>
                    </div>

                    <div class="col-md-3">
                        <span class="text-muted">
                            Fecha
                        </span>

                        <h4>
                            {{ $session->starts_at->format('d/m/Y') }}
                        </h4>
                    </div>

                    <div class="col-md-3">
                        <span class="text-muted">
                            Hora
                        </span>

                        <h4>
                            {{ $session->starts_at->format('H:i') }}
                        </h4>
                    </div>

                    <div class="col-md-3">
                        <span class="text-muted">
                            Participantes
                        </span>

                        <h4>
                            {{ $enrollments->count() }}
                        </h4>
                    </div>

                </div>

            </div>
        </div>

        @if (
            $session->status ===
                \App\Enums\ClassSessionStatus::Scheduled ||
            $session->status ===
                \App\Enums\ClassSessionStatus::Rescheduled
        )

            <div class="card shadow-sm mb-4">

                <div class="card-body">

                    <form
                        method="POST"
                        action="{{ route(
                            'trainer-classes.start',
                            $session
                        ) }}"
                    >

                        @csrf
                        @method('PATCH')

                        <button
                            type="submit"
                            class="btn btn-success"
                        >
                            Iniciar sesion
                        </button>

                    </form>

                </div>

            </div>

        @endif


        <div class="card shadow-sm">

            <div class="card-header bg-white">
                <strong>
                    Participantes
                </strong>
            </div>

            <div class="card-body p-0">

                @if (
                    $session->status ===
                    \App\Enums\ClassSessionStatus::InProgress
                )

                    <form
                        method="POST"
                        action="{{ route(
                            'trainer-classes.attendance',
                            $session
                        ) }}"
                    >

                        @csrf
                        @method('PATCH')

                @endif

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">
                                    Socio
                                </th>

                                <th>
                                    Correo
                                </th>

                                <th>
                                    Asistencia
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

                                        @if (
                                            $session->status ===
                                            \App\Enums\ClassSessionStatus::InProgress
                                        )

                                            <select
                                                name="attendance[{{ $enrollment->id }}]"
                                                class="form-select"
                                                required
                                            >

                                                <option
                                                    value="attended"
                                                    @selected(
                                                        $enrollment->status ===
                                                        \App\Enums\ClassEnrollmentStatus::Attended
                                                    )
                                                >
                                                    Asistio
                                                </option>

                                                <option
                                                    value="no_show"
                                                    @selected(
                                                        $enrollment->status ===
                                                        \App\Enums\ClassEnrollmentStatus::NoShow
                                                    )
                                                >
                                                    No asistio
                                                </option>

                                            </select>

                                        @else

                                            {{ $enrollment->status->label() }}

                                        @endif

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td
                                        colspan="3"
                                        class="text-center py-4 text-muted"
                                    >
                                        No hay participantes inscritos.
                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

                @if (
                    $session->status ===
                    \App\Enums\ClassSessionStatus::InProgress &&
                    $enrollments->isNotEmpty()
                )

                    <div class="p-3 border-top">

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            Guardar asistencia
                        </button>

                    </div>

                    </form>

                @endif

            </div>

        </div>

        @if (
            $session->status ===
            \App\Enums\ClassSessionStatus::InProgress
        )

            <div class="card shadow-sm mt-4">

                <div class="card-body">

                    <form
                        method="POST"
                        action="{{ route(
                            'trainer-classes.complete',
                            $session
                        ) }}"
                    >

                        @csrf
                        @method('PATCH')

                        <button
                            type="submit"
                            class="btn btn-danger"
                        >
                            Finalizar sesion
                        </button>

                    </form>

                </div>

            </div>

        @endif

    </div>
</x-app-layout>