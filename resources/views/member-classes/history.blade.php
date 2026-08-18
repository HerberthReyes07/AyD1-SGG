<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h2 class="mb-0">
                    <i class="bi bi-calendar-check me-2"></i>Historial de clases
                </h2>

                <small class="text-muted">
                    Consulta las clases tomadas y califica tu experiencia
                </small>
            </div>
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
                        <li>{{ $error }}</li>
                    @endforeach

                </ul>
            </div>
        @endif

        <div class="card shadow-sm">

            <div class="card-header bg-white">
                <strong>
                    <i class="bi bi-list-check me-1"></i>Clases realizadas
                </strong>
            </div>

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Clase</th>
                                <th>Categoria</th>
                                <th>Fecha</th>
                                <th>Entrenador</th>
                                <th>Asistencia</th>
                                <th>Calificacion</th>
                                <th class="text-end pe-3">
                                    Acciones
                                </th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse ($enrollments as $enrollment)

                                <tr>

                                    <td class="ps-3">
                                        <strong>
                                            {{ $enrollment->classSession->groupClass->name }}
                                        </strong>
                                    </td>

                                    <td>
                                        {{ $enrollment->classSession->groupClass->category?->name ?? 'Sin categoria' }}
                                    </td>

                                    <td>
                                        {{ $enrollment->classSession->starts_at->format('d/m/Y H:i') }}
                                    </td>

                                    <td>

                                        @if (
                                            $enrollment
                                                ->classSession
                                                ->groupClass
                                                ->trainer
                                                ?->user
                                        )

                                            {{ $enrollment
                                                ->classSession
                                                ->groupClass
                                                ->trainer
                                                ->user
                                                ->first_name }}

                                            {{ $enrollment
                                                ->classSession
                                                ->groupClass
                                                ->trainer
                                                ->user
                                                ->last_name }}

                                        @else

                                            Sin entrenador

                                        @endif

                                    </td>

                                    <td>

                                        @if (
                                            $enrollment->status ===
                                            \App\Enums\ClassEnrollmentStatus::Attended
                                        )

                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle me-1"></i>Asistio
                                            </span>

                                        @else

                                            <span class="badge bg-secondary">
                                                <i class="bi bi-x-circle me-1"></i>No asistio
                                            </span>

                                        @endif

                                    </td>

                                    <td>

                                        @if ($enrollment->classRating)

                                            <strong>
                                                {{ $enrollment->classRating->rating }}
                                                / 5
                                            </strong>

                                        @else

                                            <span class="text-muted">
                                                Sin calificar
                                            </span>

                                        @endif

                                    </td>

                                    <td class="text-end pe-3">

                                        @if (
                                            $enrollment->status ===
                                                \App\Enums\ClassEnrollmentStatus::Attended &&
                                            ! $enrollment->classRating
                                        )

                                            <button
                                                type="button"
                                                class="btn btn-sm btn-primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#rating{{ $enrollment->id }}"
                                            >
                                                <i class="bi bi-star me-1"></i>Calificar
                                            </button>

                                        @elseif ($enrollment->classRating)

                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-secondary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#viewRating{{ $enrollment->id }}"
                                            >
                                                <i class="bi bi-eye me-1"></i>Ver calificacion
                                            </button>

                                        @else

                                            <span class="text-muted">
                                                No disponible
                                            </span>

                                        @endif

                                    </td>

                                </tr>

                                @if (
                                    $enrollment->status ===
                                        \App\Enums\ClassEnrollmentStatus::Attended &&
                                    ! $enrollment->classRating
                                )

                                    <div
                                        class="modal fade"
                                        id="rating{{ $enrollment->id }}"
                                        tabindex="-1"
                                        aria-hidden="true"
                                    >

                                        <div class="modal-dialog modal-dialog-centered">

                                            <div class="modal-content">

                                                <div class="modal-header">

                                                    <h5 class="modal-title">
                                                        Calificar clase
                                                    </h5>

                                                    <button
                                                        type="button"
                                                        class="btn-close"
                                                        data-bs-dismiss="modal"
                                                    ></button>

                                                </div>

                                                <form
                                                    method="POST"
                                                    action="{{ route(
                                                        'member-classes.rating.store',
                                                        $enrollment
                                                    ) }}"
                                                >

                                                    @csrf

                                                    <div class="modal-body">

                                                        <div class="mb-3">

                                                            <label class="form-label">
                                                                Puntuacion
                                                            </label>

                                                            <select
                                                                name="rating"
                                                                class="form-select"
                                                                required
                                                            >

                                                                <option value="">
                                                                    Seleccione
                                                                </option>

                                                                <option value="1">
                                                                    1 - Muy mala
                                                                </option>

                                                                <option value="2">
                                                                    2 - Mala
                                                                </option>

                                                                <option value="3">
                                                                    3 - Regular
                                                                </option>

                                                                <option value="4">
                                                                    4 - Buena
                                                                </option>

                                                                <option value="5">
                                                                    5 - Excelente
                                                                </option>

                                                            </select>

                                                        </div>

                                                        <div>

                                                            <label class="form-label">
                                                                Comentario
                                                            </label>

                                                            <textarea
                                                                name="comment"
                                                                rows="4"
                                                                maxlength="255"
                                                                class="form-control"
                                                                placeholder="Comentario opcional"
                                                            ></textarea>

                                                        </div>

                                                    </div>

                                                    <div class="modal-footer">

                                                        <button
                                                            type="button"
                                                            class="btn btn-secondary"
                                                            data-bs-dismiss="modal"
                                                        >
                                                            Cancelar
                                                        </button>

                                                        <button
                                                            type="submit"
                                                            class="btn btn-primary"
                                                        >
                                                            <i class="bi bi-check-lg me-1"></i>Guardar calificacion
                                                        </button>

                                                    </div>

                                                </form>

                                            </div>

                                        </div>

                                    </div>

                                @endif


                                @if ($enrollment->classRating)

                                    <div
                                        class="modal fade"
                                        id="viewRating{{ $enrollment->id }}"
                                        tabindex="-1"
                                        aria-hidden="true"
                                    >

                                        <div class="modal-dialog modal-dialog-centered">

                                            <div class="modal-content">

                                                <div class="modal-header">

                                                    <h5 class="modal-title">
                                                        Calificacion
                                                    </h5>

                                                    <button
                                                        type="button"
                                                        class="btn-close"
                                                        data-bs-dismiss="modal"
                                                    ></button>

                                                </div>

                                                <div class="modal-body">

                                                    <p>
                                                        <strong>Puntuacion:</strong>

                                                        {{ $enrollment->classRating->rating }}
                                                        / 5
                                                    </p>

                                                    <p class="mb-0">
                                                        <strong>Comentario:</strong>

                                                        {{ $enrollment->classRating->comment
                                                            ?: 'Sin comentario' }}
                                                    </p>

                                                </div>

                                            </div>

                                        </div>

                                    </div>

                                @endif

                            @empty

                                <tr>

                                    <td
                                        colspan="7"
                                        class="text-center py-4 text-muted"
                                    >
                                        <i class="bi bi-inbox d-block mb-2" style="font-size: 2.5rem;"></i>
                                        Aun no tienes clases finalizadas.
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