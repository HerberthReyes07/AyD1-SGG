<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="mb-0">Registrar comida</h2>
            <small class="text-muted">
                Selecciona los alimentos que consumiste y la cantidad en gramos
            </small>
        </div>
    </x-slot>

    <div class="container-xl py-4">

        <div class="card shadow-sm">
            <div class="card-body">

                <form method="POST" action="{{ route('member-meals.store') }}">
                    @csrf

                    @include('member-meals._fields')

                    <div class="d-flex justify-content-end gap-2 mt-4">

                        <a
                            href="{{ route('member-meals.index') }}"
                            class="btn btn-outline-secondary"
                        >
                            Cancelar
                        </a>

                        <x-primary-button>
                            Guardar comida
                        </x-primary-button>

                    </div>

                </form>

            </div>
        </div>

    </div>
</x-app-layout>
