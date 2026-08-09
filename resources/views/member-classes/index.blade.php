<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="mb-0">
                Clases disponibles
            </h2>

            <small class="text-muted">
                Consulta e inscribete en las proximas clases grupales
            </small>
        </div>
    </x-slot>

    <div class="container-xl py-4">

        @if (session('success'))
            <div
                class="alert alert-success alert-dismissible fade show"
                role="alert"
            >
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
            <div class="alert alert-danger">
                <ul class="mb-0">

                    @foreach ($errors->all() as $error)
                        <li>
                            {{ $error }}
                        </li>
                    @endforeach

                </ul>
            </div>
        @endif

        <div class="card shadow-sm mb-4">

            <div class="card-body">

                <form
                    method="GET"
                    action="{{ route('member-classes.index') }}"
                >

                    <div class="row g-3">

                        <div class="col-md-6">

                            <label
                                for="search"
                                class="form-label"
                            >
                                Buscar clase
                            </label>

                            <input
                                type="text"
                                id="search"
                                name="search"
                                value="{{ request('search') }}"
                                class="form-control"
                                placeholder="Ejemplo: Yoga"
                            >

                        </div>

                        <div class="col-md-4">

                            <label
                                for="category_id"
                                class="form-label"
                            >
                                Categoria
                            </label>

                            <select
                                id="category_id"
                                name="category_id"
                                class="form-select"
                            >

                                <option value="">
                                    Todas
                                </option>

                                @foreach ($categories as $category)

                                    <option
                                        value="{{ $category->id }}"
                                        @selected(
                                            request('category_id') == $category->id
                                        )
                                    >
                                        {{ $category->name }}
                                    </option>

                                @endforeach

                            </select>

                        </div>

                        <div class="col-md-2 d-flex align-items-end">

                            <button
                                type="submit"
                                class="btn btn-primary w-100"
                            >
                                Buscar
                            </button>

                        </div>

                    </div>

                </form>

            </div>

        </div>

        <div class="row g-4">

            @forelse ($sessions as $session)

                @php
                    $enrollment = $enrollments->get(
                        $session->id
                    );

                    $waitlist = $waitlists->get(
                        $session->id
                    );

                    $isEnrolled =
                        $enrollment &&
                        $enrollment->status ===
                            \App\Enums\ClassEnrollmentStatus::Enrolled;

                    $isWaiting =
                        $waitlist &&
                        $waitlist->status ===
                            \App\Enums\WaitlistStatus::Waiting;

                    $available =
                        max(
                            0,
                            $session->groupClass->max_participants -
                            $session->enrolled_count
                        );
                @endphp

                <div class="col-md-6">

                    <div class="card shadow-sm h-100">

                        <div class="card-body">

                            <div
                                class="d-flex justify-content-between align-items-start mb-3"
                            >

                                <div>

                                    <h4 class="mb-1">
                                        {{ $session->groupClass->name }}
                                    </h4>

                                    <span class="badge bg-secondary">
                                        {{ $session->groupClass->category?->name ?? 'Sin categoria' }}
                                    </span>

                                </div>

                                @if ($isEnrolled)

                                    <span class="badge bg-success">
                                        Inscrito
                                    </span>

                                @elseif ($isWaiting)

                                    <span class="badge bg-warning text-dark">
                                        En espera
                                    </span>

                                @endif

                            </div>

                            <div class="row g-3">

                                <div class="col-md-6">

                                    <span class="text-muted">
                                        Fecha
                                    </span>

                                    <div>
                                        <strong>
                                            {{ $session->starts_at->format('d/m/Y') }}
                                        </strong>
                                    </div>

                                </div>

                                <div class="col-md-6">

                                    <span class="text-muted">
                                        Hora
                                    </span>

                                    <div>
                                        <strong>
                                            {{ $session->starts_at->format('H:i') }}
                                        </strong>
                                    </div>

                                </div>

                                <div class="col-md-6">

                                    <span class="text-muted">
                                        Duracion
                                    </span>

                                    <div>
                                        <strong>
                                            {{ $session->groupClass->duration_minutes }}
                                            min
                                        </strong>
                                    </div>

                                </div>

                                <div class="col-md-6">

                                    <span class="text-muted">
                                        Cupos disponibles
                                    </span>

                                    <div>
                                        <strong>
                                            {{ $available }}
                                            /
                                            {{ $session->groupClass->max_participants }}
                                        </strong>
                                    </div>

                                </div>

                                <div class="col-md-12">

                                    <span class="text-muted">
                                        Entrenador
                                    </span>

                                    <div>
                                        <strong>

                                            @if ($session->groupClass->trainer?->user)

                                                {{ $session->groupClass->trainer->user->first_name }}
                                                {{ $session->groupClass->trainer->user->last_name }}

                                            @else

                                                Sin entrenador asignado

                                            @endif

                                        </strong>
                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="card-footer bg-white">

                            @if ($isEnrolled)

                                <form
                                    method="POST"
                                    action="{{ route(
                                        'member-classes.cancel',
                                        $session
                                    ) }}"
                                >

                                    @csrf
                                    @method('PATCH')

                                    <button
                                        type="submit"
                                        class="btn btn-outline-danger w-100"
                                    >
                                        Cancelar inscripcion
                                    </button>

                                </form>

                            @elseif ($isWaiting)

                                <form
                                    method="POST"
                                    action="{{ route(
                                        'member-classes.waitlist.cancel',
                                        $session
                                    ) }}"
                                >

                                    @csrf
                                    @method('PATCH')

                                    <button
                                        type="submit"
                                        class="btn btn-outline-danger w-100"
                                    >
                                        Salir de lista de espera
                                    </button>

                                </form>

                            @else

                                <form
                                    method="POST"
                                    action="{{ route(
                                        'member-classes.enroll',
                                        $session
                                    ) }}"
                                >

                                    @csrf

                                    <button
                                        type="submit"
                                        class="btn btn-primary w-100"
                                    >

                                        @if ($available > 0)
                                            Inscribirme
                                        @else
                                            Unirme a lista de espera
                                        @endif

                                    </button>

                                </form>

                            @endif

                        </div>

                    </div>

                </div>

            @empty

                <div class="col-12">

                    <div class="card shadow-sm">

                        <div class="card-body text-center py-5 text-muted">
                            No hay clases disponibles.
                        </div>

                    </div>

                </div>

            @endforelse

        </div>

    </div>
</x-app-layout>