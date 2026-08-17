@php
    $existingRows = isset($meal)
        ? $meal->mealFoods->map(fn ($mealFood) => [
            'food_id' => $mealFood->food_id,
            'quantity_g' => (float) $mealFood->quantity_g,
        ])->values()
        : collect([['food_id' => null, 'quantity_g' => null]]);
@endphp

<div class="row g-3">

    <div class="col-md-6">
        <x-input-label for="date" :value="__('Fecha')" />

        <x-text-input
            id="date"
            name="date"
            type="date"
            class="mt-1 d-block w-100"
            max="{{ today()->toDateString() }}"
            :value="old('date', isset($meal) ? $meal->date->toDateString() : today()->toDateString())"
        />

        <x-input-error :messages="$errors->get('date')" class="mt-2" />
    </div>

    <div class="col-md-6">
        <x-input-label for="type" :value="__('Tiempo de comida')" />

        <select class="form-select mt-1" id="type" name="type">
            <option value="">Seleccione un tiempo de comida</option>

            @foreach (\App\Enums\MealType::cases() as $mealType)
                <option
                    value="{{ $mealType->value }}"
                    @selected(old('type', isset($meal) ? $meal->type->value : null) === $mealType->value)
                >
                    {{ $mealType->label() }}
                </option>
            @endforeach
        </select>

        <x-input-error :messages="$errors->get('type')" class="mt-2" />
    </div>

</div>

<hr class="my-4">

<div class="d-flex justify-content-between align-items-center mb-2">
    <x-input-label :value="__('Alimentos consumidos')" class="mb-0" />

    <button type="button" class="btn btn-sm btn-outline-primary" id="add-food-row">
        <i class="bi bi-plus-lg me-1"></i>Agregar alimento
    </button>
</div>

<x-input-error :messages="$errors->get('foods')" class="mb-2" />

<div id="foods-container">
    @foreach ($existingRows as $index => $row)
        <div class="row g-2 align-items-start food-row mb-2">
            <div class="col-md-7">
                @if ($index === 0)
                    <x-input-label :value="__('Alimento')" />
                @endif

                <select
                    class="form-select"
                    name="foods[{{ $index }}][food_id]"
                    required
                >
                    <option value="">Seleccione un alimento</option>

                    @foreach ($foods as $food)
                        <option
                            value="{{ $food->id }}"
                            @selected(old('foods.' . $index . '.food_id', $row['food_id']) == $food->id)
                        >
                            {{ $food->name }} &mdash; porcion ref. {{ number_format($food->reference_serving_g, 0) }} g ({{ $food->category->name ?? 'Sin categoria' }})
                        </option>
                    @endforeach
                </select>

                <x-input-error :messages="$errors->get('foods.' . $index . '.food_id')" class="mt-2" />
            </div>

            <div class="col-md-3">
                @if ($index === 0)
                    <x-input-label :value="__('Cantidad (g)')" />
                @endif

                <x-text-input
                    type="number"
                    step="0.01"
                    min="0.01"
                    class="d-block w-100"
                    name="foods[{{ $index }}][quantity_g]"
                    :value="old('foods.' . $index . '.quantity_g', $row['quantity_g'])"
                    required
                />

                <x-input-error :messages="$errors->get('foods.' . $index . '.quantity_g')" class="mt-2" />
            </div>

            <div class="col-md-2">
                @if ($index === 0)
                    <label class="form-label d-block">&nbsp;</label>
                @endif

                <button
                    type="button"
                    class="btn btn-outline-danger w-100 remove-food-row"
                >
                    <i class="bi bi-x-circle me-1"></i>Quitar
                </button>
            </div>
        </div>
    @endforeach
</div>

<template id="food-row-template">
    <div class="row g-2 align-items-start food-row mb-2">
        <div class="col-md-7">
            <select class="form-select" name="foods[__INDEX__][food_id]" required>
                <option value="">Seleccione un alimento</option>

                @foreach ($foods as $food)
                    <option value="{{ $food->id }}">
                        {{ $food->name }} &mdash; porcion ref. {{ number_format($food->reference_serving_g, 0) }} g ({{ $food->category->name ?? 'Sin categoria' }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <x-text-input
                type="number"
                step="0.01"
                min="0.01"
                class="d-block w-100"
                name="foods[__INDEX__][quantity_g]"
                required
            />
        </div>

        <div class="col-md-2">
            <button type="button" class="btn btn-outline-danger w-100 remove-food-row">
                <i class="bi bi-x-circle me-1"></i>Quitar
            </button>
        </div>
    </div>
</template>

<script>
(function () {
    const foodsContainer = document.getElementById('foods-container');
    const rowTemplate = document.getElementById('food-row-template');
    const addButton = document.getElementById('add-food-row');

    let rowIndex = foodsContainer.querySelectorAll('.food-row').length;

    function updateRemoveButtons() {
        const rows = foodsContainer.querySelectorAll('.food-row');

        rows.forEach(function (row) {
            const button = row.querySelector('.remove-food-row');
            button.disabled = rows.length <= 1;
        });
    }

    function addRow() {
        const fragment = rowTemplate.content.cloneNode(true);

        fragment.querySelectorAll('[name]').forEach(function (el) {
            el.name = el.name.replace('__INDEX__', rowIndex);
        });

        foodsContainer.appendChild(fragment);
        rowIndex++;

        updateRemoveButtons();
    }

    addButton.addEventListener('click', addRow);

    foodsContainer.addEventListener('click', function (event) {
        if (!event.target.classList.contains('remove-food-row')) {
            return;
        }

        const rows = foodsContainer.querySelectorAll('.food-row');

        if (rows.length <= 1) {
            return;
        }

        event.target.closest('.food-row').remove();
        updateRemoveButtons();
    });

    updateRemoveButtons();
})();
</script>
