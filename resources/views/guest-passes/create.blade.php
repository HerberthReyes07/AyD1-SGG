<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="mb-0">Nuevo pase de invitado</h2>
            <small class="text-muted">
                Registra una visita de prueba
            </small>
        </div>
    </x-slot>

    <div class="container-xl py-4">

        <div class="card shadow-sm">
            <div class="card-body">

                <form method="POST" action="{{ route('guest-passes.store') }}">
                    @csrf

                    <div class="row g-3">

                        <div class="col-md-6">
                            <label for="guest_name" class="form-label">
                                Nombre completo
                            </label>

                            <input
                                type="text"
                                class="form-control @error('guest_name') is-invalid @enderror"
                                id="guest_name"
                                name="guest_name"
                                value="{{ old('guest_name') }}"
                            >

                            @error('guest_name')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="dpi" class="form-label">
                                DPI
                            </label>

                            <input
                                type="text"
                                inputmode="numeric"
                                maxlength="13"
                                class="form-control @error('dpi') is-invalid @enderror"
                                id="dpi"
                                name="dpi"
                                value="{{ old('dpi') }}"
                                placeholder="13 digitos"
                            >

                            @error('dpi')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label for="visit_date" class="form-label">
                                Fecha de visita
                            </label>

                            <input
                                type="date"
                                class="form-control @error('visit_date') is-invalid @enderror"
                                id="visit_date"
                                name="visit_date"
                                value="{{ old('visit_date', now()->format('Y-m-d')) }}"
                            >

                            @error('visit_date')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">

                        <a
                            href="{{ route('guest-passes.index') }}"
                            class="btn btn-secondary"
                        >
                            Cancelar
                        </a>

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            Registrar pase
                        </button>

                    </div>

                </form>

            </div>
        </div>

    </div>
</x-app-layout>