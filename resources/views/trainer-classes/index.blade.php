<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="mb-0">
                Mis clases
            </h2>

            <small class="text-muted">
                Consulta y administra tus sesiones asignadas
            </small>
        </div>
    </x-slot>

    <div class="container-xl py-4">

        <div class="card shadow-sm">

            <div class="card-header bg-white">
                <strong>Sesiones asignadas</strong>
            </div>

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover align-middle mb-0">

                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">
                                    Clase
                                </th>

                                <th>Categoria</th>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Participantes</th>
                                <th>Estado</th>

                                <th class="text-end pe-3">
                                    Acciones
                                </th>
                            </tr>
                        </thead>

                        <tbody>

                            @forelse ($sessions as $session)

                                <tr>

                                    <td class="ps-3">
                                        <strong>
                                            {{ $session->groupClass->name }}
                                        </strong>
                                    </td>

                                    <td>
                                        {{ $session->groupClass->category?->name ?? 'Sin categoria' }}
                                    </td>

                                    <td>
                                        {{ $session->starts_at->format('d/m/Y') }}
                                    </td>

                                    <td>
                                        {{ $session->starts_at->format('H:i') }}
                                    </td>

                                    <td>
                                        {{ $session->participant_count }}
                                        /
                                        {{ $session->groupClass->max_participants }}
                                    </td>

                                    <td>
                                        {{ $session->status->label() }}
                                    </td>

                                    <td class="text-end pe-3">

                                        <a
                                            href="{{ route(
                                                'trainer-classes.show',
                                                $session
                                            ) }}"
                                            class="btn btn-sm btn-primary"
                                        >
                                            Gestionar
                                        </a>

                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td
                                        colspan="7"
                                        class="text-center py-4 text-muted"
                                    >
                                        No tienes sesiones asignadas.
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