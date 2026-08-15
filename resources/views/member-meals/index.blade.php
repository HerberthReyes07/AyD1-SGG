<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-0">Mis comidas</h2>
                <small class="text-muted">
                    Registra lo que comes y revisa tus totales diarios
                </small>
            </div>

            <a
                href="{{ route('member-meals.create') }}"
                class="btn btn-primary"
            >
                Registrar comida
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

        <div class="card shadow-sm mb-4">
            <div class="card-body">

                <form method="GET" action="{{ route('member-meals.index') }}">
                    <div class="row g-3">

                        <div class="col-md-4">
                            <x-input-label for="date" :value="__('Fecha')" />

                            <x-text-input
                                id="date"
                                name="date"
                                type="date"
                                class="mt-1 d-block w-100"
                                max="{{ today()->toDateString() }}"
                                :value="$date->toDateString()"
                            />
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <x-primary-button class="w-100 justify-content-center">
                                Ver
                            </x-primary-button>
                        </div>

                    </div>
                </form>

            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap gap-2">
                @if ($goal)
                    @php
                        $badgeClass = match ($comparison['status']) {
                            'below' => 'bg-info',
                            'above' => 'bg-warning',
                            default => 'bg-success',
                        };

                        $badgeLabel = match ($comparison['status']) {
                            'below' => 'Por debajo de la meta',
                            'above' => 'Por encima de la meta',
                            default => 'Dentro del rango',
                        };
                    @endphp

                    <div>
                        <div class="text-muted small">Meta calorica del dia ({{ $goal->objective?->label() ?? 'Sin objetivo' }})</div>
                        <div class="fs-5">
                            {{ number_format($comparison['consumed'], 0) }} / {{ number_format($comparison['goal'], 0) }} kcal
                            <span class="badge {{ $badgeClass }} ms-2">{{ $badgeLabel }}</span>
                        </div>
                    </div>
                @else
                    <div class="text-muted">Aun no has definido una meta calorica para hoy.</div>
                @endif

                <a href="{{ route('calorie-goals.edit') }}" class="btn btn-sm btn-outline-primary">
                    {{ $goal ? 'Ajustar meta' : 'Definir meta' }}
                </a>
            </div>
        </div>

        <div class="row g-3 mb-4">

            <div class="col-6 col-md-3">
                <div class="card shadow-sm text-center h-100">
                    <div class="card-body">
                        <div class="text-muted small">Calorias</div>
                        <div class="fs-4 fw-bold">{{ number_format($summary['totals']['calories'], 0) }}</div>
                        <div class="text-muted small">kcal</div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card shadow-sm text-center h-100">
                    <div class="card-body">
                        <div class="text-muted small">Proteina</div>
                        <div class="fs-4 fw-bold">{{ number_format($summary['totals']['protein_g'], 1) }}</div>
                        <div class="text-muted small">g</div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card shadow-sm text-center h-100">
                    <div class="card-body">
                        <div class="text-muted small">Carbohidratos</div>
                        <div class="fs-4 fw-bold">{{ number_format($summary['totals']['carbs_g'], 1) }}</div>
                        <div class="text-muted small">g</div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card shadow-sm text-center h-100">
                    <div class="card-body">
                        <div class="text-muted small">Grasas</div>
                        <div class="fs-4 fw-bold">{{ number_format($summary['totals']['fat_g'], 1) }}</div>
                        <div class="text-muted small">g</div>
                    </div>
                </div>
            </div>

        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <strong>Desglose por tiempo de comida</strong>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-3">Tiempo de comida</th>
                                <th>Calorias</th>
                                <th>Proteina</th>
                                <th>Carbohidratos</th>
                                <th>Grasas</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($summary['by_type'] as $typeSummary)
                                <tr>
                                    <td class="ps-3">{{ $typeSummary['label'] }}</td>
                                    <td>{{ number_format($typeSummary['calories'], 0) }} kcal</td>
                                    <td>{{ number_format($typeSummary['protein_g'], 1) }} g</td>
                                    <td>{{ number_format($typeSummary['carbs_g'], 1) }} g</td>
                                    <td>{{ number_format($typeSummary['fat_g'], 1) }} g</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @foreach (\App\Enums\MealType::cases() as $mealType)

            @php
                $mealsOfType = $meals->get($mealType->value, collect());
            @endphp

            @if ($mealsOfType->isNotEmpty())
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <strong>{{ $mealType->label() }}</strong>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover mb-0 align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Alimento</th>
                                        <th>Cantidad</th>
                                        <th>Calorias</th>
                                        <th>Proteina</th>
                                        <th>Carbohidratos</th>
                                        <th>Grasas</th>
                                        <th class="text-center pe-3">Acciones</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach ($mealsOfType as $meal)
                                        @foreach ($meal->mealFoods as $index => $mealFood)
                                            @php
                                                $nutrition = $mealService->nutritionFor($mealFood->food, (float) $mealFood->quantity_g);
                                            @endphp

                                            <tr>
                                                <td class="ps-3">{{ $mealFood->food->name }}</td>
                                                <td>{{ number_format($mealFood->quantity_g, 0) }} g</td>
                                                <td>{{ number_format($nutrition['calories'], 0) }} kcal</td>
                                                <td>{{ number_format($nutrition['protein_g'], 1) }} g</td>
                                                <td>{{ number_format($nutrition['carbs_g'], 1) }} g</td>
                                                <td>{{ number_format($nutrition['fat_g'], 1) }} g</td>

                                                @if ($index === 0)
                                                    <td
                                                        class="pe-3 text-center"
                                                        rowspan="{{ $meal->mealFoods->count() }}"
                                                    >
                                                        <div class="d-flex justify-content-center gap-1 flex-nowrap">
                                                            <a
                                                                href="{{ route('member-meals.edit', $meal) }}"
                                                                class="btn btn-sm btn-outline-primary"
                                                            >
                                                                Editar
                                                            </a>

                                                            <button
                                                                type="button"
                                                                class="btn btn-sm btn-outline-danger"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#delete-meal-{{ $meal->id }}"
                                                            >
                                                                Eliminar
                                                            </button>
                                                        </div>
                                                    </td>
                                                @endif
                                            </tr>
                                        @endforeach

                                        <x-modal name="delete-meal-{{ $meal->id }}">
                                            <form
                                                method="POST"
                                                action="{{ route('member-meals.destroy', $meal) }}"
                                                class="p-4"
                                            >
                                                @csrf
                                                @method('DELETE')

                                                <h2 class="h5 fw-medium text-dark">Eliminar comida</h2>

                                                <p class="mt-1 small text-secondary">
                                                    Deseas eliminar este registro de {{ $mealType->label() }}?
                                                </p>

                                                <div class="mt-4 d-flex justify-content-end">
                                                    <x-secondary-button type="button" data-bs-dismiss="modal">
                                                        Cancelar
                                                    </x-secondary-button>

                                                    <x-danger-button class="ms-3">
                                                        Eliminar
                                                    </x-danger-button>
                                                </div>
                                            </form>
                                        </x-modal>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

        @endforeach

        @if ($meals->isEmpty())
            <div class="card shadow-sm">
                <div class="card-body text-center text-muted py-4">
                    No has registrado comidas para esta fecha.
                </div>
            </div>
        @endif

    </div>
</x-app-layout>
