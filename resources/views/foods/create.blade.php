<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="mb-0">Nuevo Alimento</h2>
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
                            <label for="name" class="form-label">
                                Nombre
                            </label>

                            <input
                                type="text"
                                class="form-control @error('name') is-invalid @enderror"
                                id="name"
                                name="name"
                                value="{{ old('name') }}"
                            >

                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="category_id" class="form-label">
                                Categoria
                            </label>

                            <select
                                class="form-select @error('category_id') is-invalid @enderror"
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

                            @error('category_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="reference_serving_g" class="form-label">
                                Porcion de referencia (g)
                            </label>

                            <input
                                type="number"
                                step="0.01"
                                min="0.01"
                                class="form-control @error('reference_serving_g') is-invalid @enderror"
                                id="reference_serving_g"
                                name="reference_serving_g"
                                value="{{ old('reference_serving_g') }}"
                            >

                            @error('reference_serving_g')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="calories_per_serving" class="form-label">
                                Calorias por porcion
                            </label>

                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                class="form-control @error('calories_per_serving') is-invalid @enderror"
                                id="calories_per_serving"
                                name="calories_per_serving"
                                value="{{ old('calories_per_serving') }}"
                            >

                            @error('calories_per_serving')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="protein_g" class="form-label">
                                Proteina (g)
                            </label>

                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                class="form-control @error('protein_g') is-invalid @enderror"
                                id="protein_g"
                                name="protein_g"
                                value="{{ old('protein_g') }}"
                            >

                            @error('protein_g')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="carbs_g" class="form-label">
                                Carbohidratos (g)
                            </label>

                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                class="form-control @error('carbs_g') is-invalid @enderror"
                                id="carbs_g"
                                name="carbs_g"
                                value="{{ old('carbs_g') }}"
                            >

                            @error('carbs_g')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="fat_g" class="form-label">
                                Grasa (g)
                            </label>

                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                class="form-control @error('fat_g') is-invalid @enderror"
                                id="fat_g"
                                name="fat_g"
                                value="{{ old('fat_g') }}"
                            >

                            @error('fat_g')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">

                        <a
                            href="{{ route('foods.index') }}"
                            class="btn btn-secondary"
                        >
                            Cancelar
                        </a>

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            Guardar alimento
                        </button>

                    </div>

                </form>

            </div>
        </div>

    </div>
</x-app-layout>