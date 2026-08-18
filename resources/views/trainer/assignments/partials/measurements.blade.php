<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="h6 mb-0"><i class="bi bi-rulers me-1"></i> Historial de mediciones durante la asignación</h2>
    <div class="d-flex gap-2">
        <a href="{{ route('assignments.measurements.history', $trainerAssignment) }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-clock-history"></i> Historial completo
        </a>
        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#new-measurement">
            <i class="bi bi-plus-lg"></i> Registrar medición
        </button>
    </div>
</div>

<div class="card">
    <div class="card-body">
        @if ($measurements->isEmpty())
            <div class="text-secondary text-center py-3">
                <i class="bi bi-inbox display-6 d-block mb-2 opacity-50"></i>
                Todavía no hay mediciones registradas para este socio.
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Peso (kg)</th>
                            <th>Cintura (cm)</th>
                            <th>Brazo (cm)</th>
                            <th>Pierna (cm)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($measurements as $measurement)
                            <tr>
                                <td>{{ $measurement->date->format('d/m/Y') }}</td>
                                <td>{{ $measurement->weight }}</td>
                                <td>{{ $measurement->waist_measurement ?? '—' }}</td>
                                <td>{{ $measurement->arm_measurement ?? '—' }}</td>
                                <td>{{ $measurement->leg_measurement ?? '—' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<x-modal name="new-measurement" max-width="md">
    <form method="POST" action="{{ route('assignments.measurements.store', $trainerAssignment) }}">
        @csrf
        <div class="modal-header">
            <h5 class="modal-title" id="new-measurementLabel">Registrar medición</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="mb-3">
                <label for="date" class="form-label">Fecha</label>
                <input type="date" class="form-control @error('date') is-invalid @enderror"
                    id="date" name="date" value="{{ old('date', now()->format('Y-m-d')) }}" required>
                <x-input-error :messages="$errors->get('date')" class="mt-2" />
            </div>

            <div class="mb-3">
                <label for="weight" class="form-label">Peso (kg)</label>
                <input type="number" step="0.01" class="form-control @error('weight') is-invalid @enderror"
                    id="weight" name="weight" value="{{ old('weight') }}" required>
                <x-input-error :messages="$errors->get('weight')" class="mt-2" />
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="waist_measurement" class="form-label">Cintura (cm)</label>
                    <input type="number" step="0.01" class="form-control @error('waist_measurement') is-invalid @enderror"
                        id="waist_measurement" name="waist_measurement" value="{{ old('waist_measurement') }}">
                    <x-input-error :messages="$errors->get('waist_measurement')" class="mt-2" />
                </div>
                <div class="col-md-4 mb-3">
                    <label for="arm_measurement" class="form-label">Brazo (cm)</label>
                    <input type="number" step="0.01" class="form-control @error('arm_measurement') is-invalid @enderror"
                        id="arm_measurement" name="arm_measurement" value="{{ old('arm_measurement') }}">
                    <x-input-error :messages="$errors->get('arm_measurement')" class="mt-2" />
                </div>
                <div class="col-md-4 mb-3">
                    <label for="leg_measurement" class="form-label">Pierna (cm)</label>
                    <input type="number" step="0.01" class="form-control @error('leg_measurement') is-invalid @enderror"
                        id="leg_measurement" name="leg_measurement" value="{{ old('leg_measurement') }}">
                    <x-input-error :messages="$errors->get('leg_measurement')" class="mt-2" />
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Guardar</button>
        </div>
    </form>
</x-modal>
