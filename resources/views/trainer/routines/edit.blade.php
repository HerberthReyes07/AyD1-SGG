<x-app-layout>
    <div class="container py-4">
        <h1 class="h4 mb-4">Editar rutina</h1>

        <form method="POST" action="{{ route('routines.update', $routine) }}">
            @csrf
            @method('PUT')

            <div class="card mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Nombre de la rutina</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                            name="name" value="{{ old('name', $routine->name) }}" required>
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Descripción</label>
                        <textarea class="form-control" id="description" name="description"
                            rows="2">{{ old('description', $routine->description) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="h6 mb-0">Ejercicios</h2>
                <button type="button" class="btn btn-sm btn-outline-primary" id="add-exercise">
                    <i class="bi bi-plus-lg"></i> Agregar ejercicio
                </button>
            </div>

            <div id="exercises-container">
                @foreach ($routine->routineExercises as $index => $exercise)
                <div class="card mb-2 exercise-row">
                    <div class="card-body">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">Ejercicio</label>
                                <input type="text" class="form-control" name="exercises[{{ $index }}][exercise_name]"
                                    value="{{ $exercise->exercise_name }}" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Series</label>
                                <input type="number" class="form-control" name="exercises[{{ $index }}][sets]"
                                    value="{{ $exercise->sets }}" min="1" required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Repeticiones</label>
                                <input type="number" class="form-control" name="exercises[{{ $index }}][reps]"
                                    value="{{ $exercise->reps }}" min="1" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Día sugerido</label>
                                <select class="form-select" name="exercises[{{ $index }}][suggested_day]" required>
                                    @foreach (['monday' => 'Lunes', 'tuesday' => 'Martes', 'wednesday' => 'Miércoles',
                                    'thursday' => 'Jueves', 'friday' => 'Viernes', 'saturday' => 'Sábado', 'sunday' =>
                                    'Domingo'] as $value => $label)
                                    <option value="{{ $value }}" @selected($exercise->suggested_day->value ===
                                        $value)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-outline-danger btn-sm remove-exercise">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <template id="exercise-row-template">
                <div class="card mb-2 exercise-row">
                    <div class="card-body">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label">Ejercicio</label>
                                <input type="text" class="form-control" name="exercises[__INDEX__][exercise_name]"
                                    required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Series</label>
                                <input type="number" class="form-control" name="exercises[__INDEX__][sets]" min="1"
                                    required>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Repeticiones</label>
                                <input type="number" class="form-control" name="exercises[__INDEX__][reps]" min="1"
                                    required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Día sugerido</label>
                                <select class="form-select" name="exercises[__INDEX__][suggested_day]" required>
                                    <option value="monday">Lunes</option>
                                    <option value="tuesday">Martes</option>
                                    <option value="wednesday">Miércoles</option>
                                    <option value="thursday">Jueves</option>
                                    <option value="friday">Viernes</option>
                                    <option value="saturday">Sábado</option>
                                    <option value="sunday">Domingo</option>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-outline-danger btn-sm remove-exercise">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </template>

            <div class="mt-4 d-flex gap-2">
                <a href="{{ route('assignments.show', $routine->trainer_assignment_id) }}"
                    class="btn btn-outline-secondary">
                    Cancelar
                </a>
                <button type="submit" class="btn btn-primary">Guardar cambios</button>
            </div>
        </form>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let exerciseIndex = {{ $routine->routineExercises->count() }};
            const container = document.getElementById('exercises-container');
            const template = document.getElementById('exercise-row-template');

            function addExerciseRow() {
                const clone = template.content.cloneNode(true);
                clone.querySelectorAll('[name]').forEach(el => {
                    el.name = el.name.replace('__INDEX__', exerciseIndex);
                });
                container.appendChild(clone);
                exerciseIndex++;
            }

            document.getElementById('add-exercise').addEventListener('click', addExerciseRow);

            container.addEventListener('click', function (e) {
                if (e.target.closest('.remove-exercise')) {
                    e.target.closest('.exercise-row').remove();
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
