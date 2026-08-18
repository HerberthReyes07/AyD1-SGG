<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-0"><i class="bi bi-tag me-2"></i>Nueva Promoción</h2>
                <small class="text-muted">Crear y autorizar una nueva promoción de descuento</small>
            </div>
            <a href="{{ route('promotions.index') }}" class="btn btn-outline-secondary">
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
                        <strong class="mb-0">Datos de la Promoción</strong>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('promotions.store') }}">
                            @csrf

                            <div class="mb-3">
                                <x-input-label for="name" class="fw-bold" :value="__('Nombre de la Promoción')" />
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Ej. Descuento de Verano 15%" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <x-input-label for="type" class="fw-bold" :value="__('Tipo de Descuento')" />
                                    <select class="form-select @error('type') is-invalid @enderror" id="type" name="type" required>
                                        <option value="percentage" @selected(old('type') == 'percentage')>Porcentaje (%)</option>
                                        <option value="fixed_amount" @selected(old('type') == 'fixed_amount')>Monto Fijo (Q)</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <x-input-label for="value" class="fw-bold" :value="__('Valor del Descuento')" />
                                    <input type="number" step="0.01" min="0" class="form-control @error('value') is-invalid @enderror" id="value" name="value" value="{{ old('value') }}" placeholder="Ej. 15 ó 50.00" required>
                                    @error('value')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <x-input-label for="start_date" class="fw-bold" :value="__('Fecha de Inicio')" />
                                    <input type="date" class="form-control @error('start_date') is-invalid @enderror" id="start_date" name="start_date" value="{{ old('start_date', now()->toDateString()) }}" required>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6">
                                    <x-input-label for="end_date" class="fw-bold" :value="__('Fecha de Fin')" />
                                    <input type="date" class="form-control @error('end_date') is-invalid @enderror" id="end_date" name="end_date" value="{{ old('end_date', now()->addMonth()->toDateString()) }}" required>
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('promotions.index') }}" class="btn btn-outline-secondary">
                                    Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-lg me-1"></i>Crear Promoción
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
