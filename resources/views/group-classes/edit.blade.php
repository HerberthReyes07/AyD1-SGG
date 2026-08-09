<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="mb-0">Editar clase grupal</h2>

            <small class="text-muted">
                Actualiza la informacion de la plantilla de clase grupal
            </small>
        </div>
    </x-slot>

    <div class="container-xl py-4">

        <div class="card shadow-sm">
            <div class="card-body">

                <form
                    method="POST"
                    action="{{ route('group-classes.update', $groupClass) }}"
                >
                    @csrf
                    @method('PUT')

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label
                                for="name"
                                class="form-label"
                            >
                                Nombre
                            </label>

                            <input
                                type="text"
                                id="name"
                                name="name"
                                value="{{ old('name', $groupClass->name) }}"
                                class="form-control @error('name') is-invalid @enderror"
                            >

                            @error('name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label
                                for="category_id"
                                class="form-label"
                            >
                                Categoria
                            </label>

                            <select
                                id="category_id"
                                name="category_id"
                                class="form-select @error('category_id') is-invalid @enderror"
                            >
                                <option value="">
                                    Sin categoria
                                </option>

                                @foreach ($categories as $category)
                                    <option
                                        value="{{ $category->id }}"
                                        @selected(
                                            old(
                                                'category_id',
                                                $groupClass->category_id
                                            ) == $category->id
                                        )
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

                        <div class="col-md-3">
                            <label
                                for="trainer_id"
                                class="form-label"
                            >
                                Entrenador
                            </label>

                            <select
                                id="trainer_id"
                                name="trainer_id"
                                class="form-select @error('trainer_id') is-invalid @enderror"
                            >
                                <option value="">
                                    Sin asignar
                                </option>

                                @foreach ($trainers as $trainer)
                                    <option
                                        value="{{ $trainer->user_id }}"
                                        @selected(
                                            old(
                                                'trainer_id',
                                                $groupClass->trainer_id
                                            ) == $trainer->user_id
                                        )
                                    >
                                        {{ $trainer->user->first_name }}
                                        {{ $trainer->user->last_name }}

                                        @if ($trainer->specialty)
                                            - {{ $trainer->specialty->name }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>

                            @error('trainer_id')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label
                                for="duration_minutes"
                                class="form-label"
                            >
                                Duracion (minutos)
                            </label>

                            <input
                                type="number"
                                id="duration_minutes"
                                name="duration_minutes"
                                min="1"
                                value="{{ old(
                                    'duration_minutes',
                                    $groupClass->duration_minutes
                                ) }}"
                                class="form-control @error('duration_minutes') is-invalid @enderror"
                            >

                            @error('duration_minutes')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label
                                for="max_participants"
                                class="form-label"
                            >
                                Cupo maximo
                            </label>

                            <input
                                type="number"
                                id="max_participants"
                                name="max_participants"
                                min="1"
                                value="{{ old(
                                    'max_participants',
                                    $groupClass->max_participants
                                ) }}"
                                class="form-control @error('max_participants') is-invalid @enderror"
                            >

                            @error('max_participants')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-12">
                            <label
                                for="description"
                                class="form-label"
                            >
                                Descripcion
                            </label>

                            <textarea
                                id="description"
                                name="description"
                                rows="4"
                                class="form-control @error('description') is-invalid @enderror"
                            >{{ old(
                                'description',
                                $groupClass->description
                            ) }}</textarea>

                            @error('description')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">

                        <a
                            href="{{ route('group-classes.index') }}"
                            class="btn btn-secondary"
                        >
                            Cancelar
                        </a>

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            Guardar cambios
                        </button>

                    </div>

                </form>

            </div>
        </div>

    </div>
</x-app-layout>