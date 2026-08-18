<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Editar clase grupal</h2>

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
                            <x-input-label for="name" value="Nombre" />
                            <x-text-input
                                id="name"
                                name="name"
                                type="text"
                                class="mt-1 d-block w-100"
                                :value="old('name', $groupClass->name)"
                            />
                            <x-input-error class="mt-2" :messages="$errors->get('name')" />
                        </div>

                        <div class="col-md-3">
                            <x-input-label for="category_id" value="Categoria" />
                            <select
                                id="category_id"
                                name="category_id"
                                class="form-select mt-1"
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
                            <x-input-error class="mt-2" :messages="$errors->get('category_id')" />
                        </div>

                        <div class="col-md-3">
                            <x-input-label for="trainer_id" value="Entrenador" />
                            <select
                                id="trainer_id"
                                name="trainer_id"
                                class="form-select mt-1"
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
                            <x-input-error class="mt-2" :messages="$errors->get('trainer_id')" />
                        </div>

                        <div class="col-md-3">
                            <x-input-label for="duration_minutes" value="Duracion (minutos)" />
                            <x-text-input
                                type="number"
                                id="duration_minutes"
                                name="duration_minutes"
                                min="1"
                                class="mt-1 d-block w-100"
                                :value="old(
                                    'duration_minutes',
                                    $groupClass->duration_minutes
                                )"
                            />
                            <x-input-error class="mt-2" :messages="$errors->get('duration_minutes')" />
                        </div>

                        <div class="col-md-3">
                            <x-input-label for="max_participants" value="Cupo maximo" />
                            <x-text-input
                                type="number"
                                id="max_participants"
                                name="max_participants"
                                min="1"
                                class="mt-1 d-block w-100"
                                :value="old(
                                    'max_participants',
                                    $groupClass->max_participants
                                )"
                            />
                            <x-input-error class="mt-2" :messages="$errors->get('max_participants')" />
                        </div>

                        <div class="col-md-12">
                            <x-input-label for="description" value="Descripcion" />
                            <textarea
                                id="description"
                                name="description"
                                rows="4"
                                class="form-control mt-1"
                            >{{ old(
                                'description',
                                $groupClass->description
                            ) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('description')" />
                        </div>

                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">

                        <a
                            href="{{ route('group-classes.index') }}"
                            class="btn btn-outline-secondary"
                        >
                            <i class="bi bi-x-lg me-1"></i>Cancelar
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
