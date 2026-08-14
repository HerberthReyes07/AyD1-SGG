<div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="h6 mb-0">Observaciones nutricionales</h2>
    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#nutritional-observation">
        Agregar observacion
    </button>
</div>

<div class="card">
    <div class="card-body">
        @if ($observations->isEmpty())
            <p class="text-secondary text-center mb-0 py-3">
                Todavia no has dejado observaciones para este socio.
            </p>
        @else
            @foreach ($observations as $observation)
                <div class="mb-3 pb-3 {{ ! $loop->last ? 'border-bottom' : '' }}">
                    <p class="text-secondary small mb-1">{{ $observation->date->format('d/m/Y') }}</p>
                    <p class="mb-0">{{ $observation->observation }}</p>
                </div>
            @endforeach
        @endif
    </div>
</div>

<x-modal name="nutritional-observation" max-width="md">
    <form method="POST" action="{{ route('assignments.nutritional-observations.store', $trainerAssignment) }}">
        @csrf
        <div class="modal-header">
            <h5 class="modal-title" id="nutritional-observationLabel">Agregar observacion nutricional</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div class="mb-3">
                <label for="obs-date" class="form-label">Fecha</label>
                <input type="date" class="form-control @error('date') is-invalid @enderror"
                    id="obs-date" name="date" value="{{ old('date', now()->format('Y-m-d')) }}" required>
                <x-input-error :messages="$errors->get('date')" class="mt-2" />
            </div>

            <div class="mb-3">
                <label for="observation" class="form-label">Observacion / recomendacion</label>
                <textarea class="form-control @error('observation') is-invalid @enderror"
                    id="observation" name="observation" rows="4" required>{{ old('observation') }}</textarea>
                <x-input-error :messages="$errors->get('observation')" class="mt-2" />
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
    </form>
</x-modal>
