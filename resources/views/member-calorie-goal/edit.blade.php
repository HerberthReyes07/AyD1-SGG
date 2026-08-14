<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="mb-0">Meta calorica</h2>
            <small class="text-muted">
                Define cuantas calorias quieres consumir al dia segun tu objetivo
            </small>
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

        @if ($goal)
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <h3 class="h6 text-muted mb-3">Meta vigente</h3>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="text-muted small">Calorias diarias</div>
                            <div class="fs-4 fw-bold">{{ number_format($goal->daily_calories, 0) }} kcal</div>
                        </div>

                        <div class="col-md-4">
                            <div class="text-muted small">Objetivo</div>
                            <div class="fs-5">{{ $goal->objective?->label() ?? 'Sin especificar' }}</div>
                        </div>

                        <div class="col-md-4">
                            <div class="text-muted small">Definida por</div>
                            <div class="fs-5">
                                @if ($goal->defined_by)
                                    Tu entrenador ({{ $goal->definedBy->first_name }} {{ $goal->definedBy->last_name }})
                                @else
                                    Ti mismo
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-info">
                Aun no has definido una meta calorica. Completa el formulario para empezar.
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body">

                <form method="POST" action="{{ route('calorie-goals.update') }}">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">

                        <div class="col-md-6">
                            <x-input-label for="daily_calories" :value="__('Calorias diarias')" />

                            <x-text-input
                                id="daily_calories"
                                name="daily_calories"
                                type="number"
                                step="1"
                                min="1"
                                class="mt-1 d-block w-100"
                                :value="old('daily_calories')"
                                required
                            />

                            <x-input-error :messages="$errors->get('daily_calories')" class="mt-2" />
                        </div>

                        <div class="col-md-6">
                            <x-input-label for="objective" :value="__('Objetivo')" />

                            <select class="form-select mt-1" id="objective" name="objective" required>
                                <option value="">Seleccione un objetivo</option>

                                @foreach (\App\Enums\CalorieGoalObjective::cases() as $objective)
                                    <option
                                        value="{{ $objective->value }}"
                                        @selected(old('objective', $goal?->objective?->value) === $objective->value)
                                    >
                                        {{ $objective->label() }}
                                    </option>
                                @endforeach
                            </select>

                            <x-input-error :messages="$errors->get('objective')" class="mt-2" />
                        </div>

                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <x-primary-button>
                            Guardar meta
                        </x-primary-button>
                    </div>

                </form>

            </div>
        </div>

    </div>
</x-app-layout>
