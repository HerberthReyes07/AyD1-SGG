<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h2 class="mb-0"><i class="bi bi-person-badge me-2"></i>{{ __('Asignar entrenador a socio') }}</h2>
                <small class="text-muted">
                    {{ __('Selecciona un socio elegible y el entrenador que lo acompañará') }}
                </small>
            </div>

            <a href="{{ route('trainer-assignments.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i>{{ __('Volver al listado') }}
            </a>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="container-xl">
            @if ($errors->any() || session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') ?? $errors->first() }}

                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
            @endif

            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <strong>{{ __('Formulario de asignación') }}</strong>
                </div>

                <div class="card-body">
                    <p class="text-muted mb-4">
                        {{ __('Selecciona primero un socio, luego un entrenador y confirma la asignación.') }}
                    </p>

                    {{-- Stepper visual --}}
                    <ul class="nav nav-pills mb-4" id="wizard-steps">
                        <li class="nav-item">
                            <button class="nav-link active" id="step1-tab" data-bs-toggle="pill" data-bs-target="#step1"
                                type="button">
                                1. {{ __('Seleccionar socio') }}
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="step2-tab" data-bs-toggle="pill" data-bs-target="#step2"
                                type="button" disabled>
                                2. {{ __('Seleccionar entrenador') }}
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="step3-tab" data-bs-toggle="pill" data-bs-target="#step3"
                                type="button" disabled>
                                3. {{ __('Confirmar') }}
                            </button>
                        </li>
                    </ul>

                    <form method="POST" action="{{ route('trainer-assignments.store') }}" id="assignment-form">
                        @csrf
                        <input type="hidden" name="member_id" id="selected_member_id">
                        <input type="hidden" name="trainer_id" id="selected_trainer_id">

                        <div class="tab-content">

                            {{-- PASO 1: Socios elegibles --}}
                            <div class="tab-pane fade show active" id="step1">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="px-4 py-3">{{ __('Nombre') }}</th>
                                                <th class="py-3">{{ __('Email') }}</th>
                                                <th class="py-3">{{ __('Plan') }}</th>
                                                <th class="px-4 py-3 text-end"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($eligibleMembers as $member)
                                            <tr>
                                                <td class="px-4 py-3 fw-medium">{{ $member->user->first_name }} {{
                                                    $member->user->last_name }}</td>
                                                <td>{{ $member->user->email }}</td>
                                                <td>{{ $member->memberships->first()?->plan->name }}</td>
                                                <td class="px-4 py-3 text-end">
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-primary select-member"
                                                        data-id="{{ $member->user_id }}"
                                                        data-name="{{ $member->user->first_name }} {{ $member->user->last_name }}">
                                                        <i class="bi bi-check-lg me-1"></i>{{ __('Seleccionar') }}
                                                    </button>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-4 text-muted">
                                                    <i class="bi bi-inbox text-muted d-block mb-2" style="font-size: 2.5rem;"></i>
                                                    {{ __('No hay socios elegibles (deben tener Plan Élite sin
                                                    entrenador asignado).') }}
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- PASO 2: Entrenadores --}}
                            <div class="tab-pane fade" id="step2">
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="px-4 py-3">{{ __('Nombre') }}</th>
                                                <th class="py-3">{{ __('Especialidad') }}</th>
                                                <th class="py-3">{{ __('Socios activos') }}</th>
                                                <th class="px-4 py-3 text-end"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($trainers as $trainer)
                                            <tr>
                                                <td class="px-4 py-3 fw-medium">{{ $trainer->user->first_name }} {{
                                                    $trainer->user->last_name }}</td>
                                                <td>{{ $trainer->specialty->name }}</td>
                                                <td>{{ $trainer->active_members_count }}</td>
                                                <td class="px-4 py-3 text-end">
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-primary select-trainer"
                                                        data-id="{{ $trainer->user_id }}"
                                                        data-name="{{ $trainer->user->first_name }} {{ $trainer->user->last_name }}"
                                                        data-specialty="{{ $trainer->specialty->name }}">
                                                        <i class="bi bi-check-lg me-1"></i>{{ __('Seleccionar') }}
                                                    </button>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-4 text-muted">
                                                    <i class="bi bi-inbox text-muted d-block mb-2" style="font-size: 2.5rem;"></i>
                                                    {{ __('No hay
                                                    entrenadores disponibles.') }}</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                                <button type="button" class="btn btn-outline-secondary mt-3" id="back-to-step1">
                                    <i class="bi bi-arrow-left me-1"></i>{{ __('Volver') }}
                                </button>
                            </div>

                            {{-- PASO 3: Confirmación --}}
                            <div class="tab-pane fade" id="step3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ __('Resumen de la asignación') }}</h5>
                                        <p><strong>{{ __('Socio') }}:</strong> <span id="summary-member"></span></p>
                                        <p><strong>{{ __('Entrenador') }}:</strong> <span id="summary-trainer"></span>
                                        </p>
                                        <p><strong>{{ __('Especialidad') }}:</strong> <span
                                                id="summary-specialty"></span></p>
                                        <hr>
                                        <div class="mb-3">
                                            <label for="goal" class="form-label"><strong>{{ __('Objetivo de la asignación')
                                                    }}</strong> <small class="text-muted">{{ __('Dejar en blanco si no se desea establecer un objetivo') }}</small></label>
                                            <textarea
                                                class="form-control @error('goal') is-invalid @enderror"
                                                id="goal" name="goal" rows="4"
                                                placeholder="{{ __('Ej. Mejorar la condición física del socio') }}">{{ old('goal') }}</textarea>
                                            <x-input-error :messages="$errors->get('goal')"
                                                class="mt-2" />
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3 d-flex gap-2">
                                    <button type="button" class="btn btn-outline-secondary" id="back-to-step2">
                                        <i class="bi bi-arrow-left me-1"></i>{{ __('Volver') }}
                                    </button>
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-check-lg me-1"></i>{{ __('Confirmar asignación') }}
                                    </button>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>

        @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
            const step1Tab = new bootstrap.Tab(document.getElementById('step1-tab'));
            const step2Tab = new bootstrap.Tab(document.getElementById('step2-tab'));
            const step3Tab = new bootstrap.Tab(document.getElementById('step3-tab'));

            const step2TabBtn = document.getElementById('step2-tab');
            const step3TabBtn = document.getElementById('step3-tab');

            const clearRowSelection = (selector) => {
                document.querySelectorAll(selector).forEach(row => {
                    row.classList.remove('table-primary');
                    const selectBtn = row.querySelector('button');
                    if (selectBtn) {
                        selectBtn.innerHTML = '<i class="bi bi-check-lg me-1"></i>{{ __("Seleccionar") }}';
                        selectBtn.disabled = false;
                    }
                });
            };

            // Paso 1 -> selecciona socio
            document.querySelectorAll('.select-member').forEach(btn => {
                btn.addEventListener('click', function () {
                    document.getElementById('selected_member_id').value = this.dataset.id;
                    document.getElementById('summary-member').textContent = this.dataset.name;

                    clearRowSelection('#step1 tbody tr');
                    this.closest('tr').classList.add('table-primary');
                    this.innerHTML = '<i class="bi bi-check-lg me-1"></i>{{ __("Seleccionado") }}';
                    this.disabled = true;

                    step2TabBtn.disabled = false;
                    step2Tab.show();
                });
            });

            // Paso 2 -> selecciona entrenador
            document.querySelectorAll('.select-trainer').forEach(btn => {
                btn.addEventListener('click', function () {
                    document.getElementById('selected_trainer_id').value = this.dataset.id;
                    document.getElementById('summary-trainer').textContent = this.dataset.name;
                    document.getElementById('summary-specialty').textContent = this.dataset.specialty;

                    clearRowSelection('#step2 tbody tr');
                    this.closest('tr').classList.add('table-primary');
                    this.innerHTML = '<i class="bi bi-check-lg me-1"></i>{{ __("Seleccionado") }}';
                    this.disabled = true;

                    step3TabBtn.disabled = false;
                    step3Tab.show();
                });
            });

            // Botones de volver
            document.getElementById('back-to-step1').addEventListener('click', () => step1Tab.show());
            document.getElementById('back-to-step2').addEventListener('click', () => step2Tab.show());
        });
        </script>
        @endpush
</x-app-layout>
