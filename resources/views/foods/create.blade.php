<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="mb-0"><i class="bi bi-egg-fried me-2"></i>Nuevo Alimento</h2>
            <small class="text-muted">
                Registra la informacion nutricional del alimento
            </small>
        </div>
    </x-slot>

    <div class="container-xl py-4">

        <div class="card shadow-sm">
            <div class="card-body">

                <form method="POST" action="{{ route('foods.store') }}">
                    @csrf

                    <div class="row g-3">

                        <div class="col-md-8">
                            <x-input-label for="name" value="Nombre" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 d-block w-100" :value="old('name')" />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <div class="col-md-4">
                            <x-input-label for="category_id" value="Categoria" />
                            <select
                                class="form-select mt-1"
                                id="category_id"
                                name="category_id"
                            >
                                <option value="">
                                    Seleccione una categoria
                                </option>

                                @foreach ($categories as $category)
                                    <option
                                        value="{{ $category->id }}"
                                        @selected(old('category_id') == $category->id)
                                    >
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('category_id')" />
                        </div>

                        <div class="col-md-4">
                            <x-input-label for="reference_serving_g" value="Porcion de referencia (g)" />
                            <x-text-input
                                type="number"
                                step="0.01"
                                min="0.01"
                                id="reference_serving_g"
                                name="reference_serving_g"
                                class="mt-1 d-block w-100"
                                :value="old('reference_serving_g')"
                            />
                            <x-input-error class="mt-2" :messages="$errors->get('reference_serving_g')" />
                        </div>

                        <div class="col-md-4">
                            <x-input-label for="calories_per_serving" value="Calorias por porcion" />
                            <x-text-input
                                type="number"
                                step="0.01"
                                min="0"
                                id="calories_per_serving"
                                name="calories_per_serving"
                                class="mt-1 d-block w-100"
                                :value="old('calories_per_serving')"
                            />
                            <x-input-error class="mt-2" :messages="$errors->get('calories_per_serving')" />
                        </div>

                        <div class="col-md-4">
                            <x-input-label for="protein_g" value="Proteina (g)" />
                            <x-text-input
                                type="number"
                                step="0.01"
                                min="0"
                                id="protein_g"
                                name="protein_g"
                                class="mt-1 d-block w-100"
                                :value="old('protein_g')"
                            />
                            <x-input-error class="mt-2" :messages="$errors->get('protein_g')" />
                        </div>

                        <div class="col-md-4">
                            <x-input-label for="carbs_g" value="Carbohidratos (g)" />
                            <x-text-input
                                type="number"
                                step="0.01"
                                min="0"
                                id="carbs_g"
                                name="carbs_g"
                                class="mt-1 d-block w-100"
                                :value="old('carbs_g')"
                            />
                            <x-input-error class="mt-2" :messages="$errors->get('carbs_g')" />
                        </div>

                        <div class="col-md-4">
                            <x-input-label for="fat_g" value="Grasa (g)" />
                            <x-text-input
                                type="number"
                                step="0.01"
                                min="0"
                                id="fat_g"
                                name="fat_g"
                                class="mt-1 d-block w-100"
                                :value="old('fat_g')"
                            />
                            <x-input-error class="mt-2" :messages="$errors->get('fat_g')" />
                        </div>

                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">

                        <a
                            href="{{ route('foods.index') }}"
                            class="btn btn-outline-secondary"
                        >
                            <i class="bi bi-x-lg me-1"></i>Cancelar
                        </a>

                        <x-primary-button>
                            <i class="bi bi-check-lg me-1"></i>Guardar alimento
                        </x-primary-button>

                    </div>

                </form>

            </div>
        </div>

    </div>
</x-app-layout>
