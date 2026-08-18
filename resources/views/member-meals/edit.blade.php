<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="mb-0"><i class="bi bi-pencil me-2"></i>Editar comida</h2>
            <small class="text-muted">
                Actualiza los alimentos o la cantidad consumida
            </small>
        </div>
    </x-slot>

    <div class="container-xl py-4">

        <div class="card shadow-sm">
            <div class="card-body">

                <form method="POST" action="{{ route('member-meals.update', $meal) }}">
                    @csrf
                    @method('PUT')

                    @include('member-meals._fields')

                    <div class="d-flex justify-content-end gap-2 mt-4">

                        <a
                            href="{{ route('member-meals.index', ['date' => $meal->date->toDateString()]) }}"
                            class="btn btn-outline-secondary"
                        >
                            Cancelar
                        </a>

                        <x-primary-button>
                            <i class="bi bi-check-lg me-1"></i>Guardar cambios
                        </x-primary-button>

                    </div>

                </form>

            </div>
        </div>

    </div>
</x-app-layout>
