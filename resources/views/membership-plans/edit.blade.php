<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Editar Plan: {{ $plan->name }}</h2>
                <small class="text-muted">Modifique únicamente el precio y la descripción del plan</small>
            </div>
            <a href="{{ route('membership-plans.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>Regresar
            </a>
        </div>
    </x-slot>

    <div class="container-xl py-4">
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <strong class="mb-0">Detalles del Plan</strong>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('membership-plans.update', $plan) }}">
                            @csrf
                            @method('PUT')

                            <!-- Campos no editables (Reglas de negocio) -->
                            <div class="alert alert-info py-2 mb-4" role="alert">
                                <i class="bi bi-info-circle me-1"></i>
                                Los parámetros estructurales del plan (nombre, duración y beneficios) están protegidos y no pueden ser modificados.
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label text-muted">Nombre del Plan</label>
                                    <input type="text" class="form-control bg-light" value="{{ $plan->name }}" disabled readonly>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted">Duración (Meses)</label>
                                    <input type="text" class="form-control bg-light" value="{{ $plan->duration_months }}" disabled readonly>
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-4">
                                    <label class="form-label text-muted">Clases Grupales</label>
                                    <input type="text" class="form-control bg-light" value="{{ $plan->includes_group_classes ? ($plan->weekly_class_limit ? $plan->weekly_class_limit . ' por semana' : 'Ilimitadas') : 'No incluye' }}" disabled readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted">Entrenador Personal</label>
                                    <input type="text" class="form-control bg-light" value="{{ $plan->includes_trainer ? 'Incluido' : 'No incluye' }}" disabled readonly>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label text-muted">Prioridad Lista Espera</label>
                                    <input type="text" class="form-control bg-light" value="{{ $plan->has_waitlist_priority ? 'Sí' : 'No' }}" disabled readonly>
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- Campos editables -->
                            <div class="mb-3">
                                <x-input-label for="price" class="fw-bold" :value="__('Precio (Q)')" />
                                <input type="number" step="0.01" min="0" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', $plan->price) }}" required>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <x-input-label for="description" class="fw-bold" :value="__('Descripción')" />
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description', $plan->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="{{ route('membership-plans.index') }}" class="btn btn-outline-secondary">
                                    Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-lg me-1"></i>Guardar Cambios
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
