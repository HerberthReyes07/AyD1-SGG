<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="mb-0"><i class="bi bi-person-badge me-2"></i>Nuevo pase de invitado</h2>
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
                            <x-input-label for="guest_name" :value="__('Nombre completo')" />

                            <x-text-input
                                id="guest_name"
                                name="guest_name"
                                type="text"
                                class="mt-1 d-block w-100 @error('guest_name') is-invalid @enderror"
                                :value="old('guest_name')"
                            />

                            <x-input-error :messages="$errors->get('guest_name')" class="mt-1" />
                        </div>

                        <div class="col-md-3">
                            <x-input-label for="dpi" :value="__('DPI')" />

                            <x-text-input
                                id="dpi"
                                name="dpi"
                                type="text"
                                inputmode="numeric"
                                maxlength="13"
                                class="mt-1 d-block w-100 @error('dpi') is-invalid @enderror"
                                :value="old('dpi')"
                                placeholder="13 digitos"
                            />

                            <x-input-error :messages="$errors->get('dpi')" class="mt-1" />
                        </div>

                        <div class="col-md-3">
                            <x-input-label for="visit_date" :value="__('Fecha de visita')" />

                            <x-text-input
                                id="visit_date"
                                name="visit_date"
                                type="date"
                                class="mt-1 d-block w-100 @error('visit_date') is-invalid @enderror"
                                :value="old('visit_date', now()->format('Y-m-d'))"
                            />

                            <x-input-error :messages="$errors->get('visit_date')" class="mt-1" />
                        </div>

                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-4">

                        <a
                            href="{{ route('guest-passes.index') }}"
                            class="btn btn-outline-secondary"
                        >
                            Cancelar
                        </a>

                        <button
                            type="submit"
                            class="btn btn-primary"
                        >
                            <i class="bi bi-check-lg me-1"></i>Registrar pase
                        </button>

                    </div>

                </form>

            </div>
        </div>

    </div>
</x-app-layout>