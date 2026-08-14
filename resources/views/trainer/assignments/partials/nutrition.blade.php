<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="h6 mb-0">Meta calorica</h2>
    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#calorie-goal">
        Definir / ajustar meta
    </button>
</div>

<div class="card mb-4">
    <div class="card-body">
        @if ($calorieGoal)
            <div class="row g-3">
                <div class="col-md-4">
                    <p class="text-secondary small mb-1">Calorias diarias</p>
                    <p class="mb-0">{{ number_format($calorieGoal->daily_calories, 0) }} kcal</p>
                </div>
                <div class="col-md-4">
                    <p class="text-secondary small mb-1">Objetivo</p>
                    <p class="mb-0">{{ $calorieGoal->objective?->label() ?? 'Sin especificar' }}</p>
                </div>
                <div class="col-md-4">
                    <p class="text-secondary small mb-1">Definida por</p>
                    <p class="mb-0">{{ $calorieGoal->defined_by ? 'Ti (entrenador)' : 'El socio' }}</p>
                </div>
            </div>
        @else
            <p class="text-secondary text-center mb-0 py-3">
                Este socio todavia no tiene una meta calorica definida.
            </p>
        @endif
    </div>
</div>

<h2 class="h6 mb-3">Historial nutricional (ultimos 7 dias)</h2>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Calorias</th>
                        <th>Proteina</th>
                        <th>Carbohidratos</th>
                        <th>Grasas</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($nutritionHistory as $day)
                        <tr>
                            <td>{{ $day['date']->format('d/m/Y') }}</td>
                            <td>{{ number_format($day['calories'], 0) }} kcal</td>
                            <td>{{ number_format($day['protein_g'], 1) }} g</td>
                            <td>{{ number_format($day['carbs_g'], 1) }} g</td>
                            <td>{{ number_format($day['fat_g'], 1) }} g</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<x-modal name="calorie-goal" max-width="md">
    <form method="POST" action="{{ route('assignments.calorie-goal.store', $trainerAssignment) }}">
        @csrf
        <div class="modal-header">
            <h5 class="modal-title" id="calorie-goalLabel">Definir / ajustar meta calorica</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="mb-3">
                <label for="daily_calories" class="form-label">Calorias diarias</label>
                <input type="number" step="1" min="1" class="form-control @error('daily_calories') is-invalid @enderror"
                    id="daily_calories" name="daily_calories" value="{{ old('daily_calories') }}" required>
                <x-input-error :messages="$errors->get('daily_calories')" class="mt-2" />
            </div>

            <div class="mb-3">
                <label for="objective" class="form-label">Objetivo</label>
                <select class="form-select @error('objective') is-invalid @enderror" id="objective" name="objective" required>
                    <option value="">Seleccione un objetivo</option>
                    @foreach (\App\Enums\CalorieGoalObjective::cases() as $objective)
                        <option value="{{ $objective->value }}" @selected(old('objective') === $objective->value)>
                            {{ $objective->label() }}
                        </option>
                    @endforeach
                </select>
                <x-input-error :messages="$errors->get('objective')" class="mt-2" />
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
    </form>
</x-modal>
