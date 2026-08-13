<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-0">{{ __('Reasignar entrenador') }}</h2>
                <small class="text-muted">
                    {{ __('Cambia el entrenador activo y registra el motivo de la reasignación') }}
                </small>
            </div>

            <a href="{{ route('trainer-assignments.index') }}" class="btn btn-secondary">
                {{ __('Volver al listado') }}
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

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <strong>{{ __('Asignación actual') }}</strong>
                </div>

                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <span class="text-muted d-block">{{ __('Socio') }}</span>
                            <strong>{{ $trainerAssignment->member->user->first_name }} {{
                                $trainerAssignment->member->user->last_name }}</strong>
                        </div>

                        <div class="col-md-6">
                            <span class="text-muted d-block">{{ __('Entrenador actual') }}</span>
                            <strong>{{ $trainerAssignment->trainer->user->first_name }} {{
                                $trainerAssignment->trainer->user->last_name }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <strong>{{ __('Formulario de reasignación') }}</strong>
                </div>

                <div class="card-body">
                    <ul class="nav nav-pills mb-4" id="wizard-steps">
                        <li class="nav-item">
                            <button class="nav-link active" id="step1-tab" data-bs-toggle="pill" data-bs-target="#step1"
                                type="button">
                                1. {{ __('Nuevo entrenador') }}
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="step2-tab" data-bs-toggle="pill" data-bs-target="#step2"
                                type="button" disabled>
                                2. {{ __('Motivo') }}
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="step3-tab" data-bs-toggle="pill" data-bs-target="#step3"
                                type="button" disabled>
                                3. {{ __('Confirmar') }}
                            </button>
                        </li>
                    </ul>

                    <form method="POST" action="{{ route('trainer-assignments.reassign.store', $trainerAssignment) }}">
                        @csrf
                        <input type="hidden" name="new_trainer_id" id="selected_trainer_id">

                        <div class="tab-content">

                            {{-- PASO 1: Nuevo entrenador --}}
                            <div class="tab-pane fade show active" id="step1">
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
                                            @forelse ($availableTrainers as $trainer)
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
                                                        {{ __('Seleccionar') }}
                                                    </button>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-4 text-muted">{{ __('No hay otros
                                                    entrenadores disponibles.') }}</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- PASO 2: Motivo --}}
                            <div class="tab-pane fade" id="step2">
                                <div class="mb-3">
                                    <label for="reassignment_reason" class="form-label">{{ __('Motivo de la
                                        reasignación') }}</label>
                                    <textarea class="form-control @error('reassignment_reason') is-invalid @enderror"
                                        id="reassignment_reason" name="reassignment_reason" rows="4"
                                        placeholder="{{ __('Ej. El entrenador anterior renunció, o considera que otro colega tiene más experiencia en el objetivo del socio') }}">{{ old('reassignment_reason') }}</textarea>
                                    <x-input-error :messages="$errors->get('reassignment_reason')" class="mt-2" />
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-outline-secondary" id="back-to-step1">&larr; {{
                                        __('Volver') }}</button>
                                    <button type="button" class="btn btn-primary" id="next-to-step3">{{ __('Continuar')
                                        }} &rarr;</button>
                                </div>
                            </div>

                            {{-- PASO 3: Confirmación --}}
                            <div class="tab-pane fade" id="step3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">{{ __('Resumen de la reasignación') }}</h5>
                                        <p><strong>{{ __('Socio') }}:</strong> {{
                                            $trainerAssignment->member->user->first_name }} {{
                                            $trainerAssignment->member->user->last_name }}</p>
                                        <p><strong>{{ __('Entrenador anterior') }}:</strong> {{
                                            $trainerAssignment->trainer->user->first_name }}
                                            {{ $trainerAssignment->trainer->user->last_name }}</p>
                                        <p><strong>{{ __('Entrenador nuevo') }}:</strong> <span
                                                id="summary-trainer"></span></p>
                                        <p><strong>{{ __('Especialidad') }}:</strong> <span
                                                id="summary-specialty"></span></p>
                                        <p><strong>{{ __('Motivo') }}:</strong> <span id="summary-reason"></span></p>
                                        <hr>
                                        <div class="mb-3">
                                            <label for="goal" class="form-label"><strong>{{ __('Actualizar objetivo')
                                                    }}</strong> <small class="text-muted">{{ __('Dejar en blanco si no
                                                    se desea establecer un objetivo') }}</small></label>
                                            <textarea class="form-control @error('goal') is-invalid @enderror" id="goal"
                                                name="goal" rows="4"
                                                placeholder="{{ __('Ej. Mejorar la condición física del socio') }}">{{ $trainerAssignment->goal ?? '' }}</textarea>
                                            <x-input-error :messages="$errors->get('goal')" class="mt-2" />
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3 d-flex gap-2">
                                    <button type="button" class="btn btn-outline-secondary" id="back-to-step2">&larr; {{
                                        __('Volver') }}</button>
                                    <button type="submit" class="btn btn-success">{{ __('Confirmar reasignación')
                                        }}</button>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
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

            let selectedTrainerName = '';
            let selectedTrainerSpecialty = '';

            const clearRowSelection = () => {
                document.querySelectorAll('#step1 tbody tr').forEach(row => {
                    row.classList.remove('table-primary');
                    row.querySelector('.select-trainer').textContent = '{{ __("Seleccionar") }}';
                    row.querySelector('.select-trainer').disabled = false;
                });
            };

            const verifyStep3 = () => {

                const reason = document.getElementById('reassignment_reason').value.trim();

                if (!reason) {
                    alert('Por favor ingresa el motivo de la reasignación.');
                    return false;
                }

                document.getElementById('summary-trainer').textContent = selectedTrainerName;
                document.getElementById('summary-specialty').textContent = selectedTrainerSpecialty;
                document.getElementById('summary-reason').textContent = reason;
                return true;
            };

            document.querySelectorAll('.select-trainer').forEach(btn => {
                btn.addEventListener('click', function () {
                    document.getElementById('selected_trainer_id').value = this.dataset.id;
                    selectedTrainerName = this.dataset.name;
                    selectedTrainerSpecialty = this.dataset.specialty;

                    clearRowSelection();
                    this.closest('tr').classList.add('table-primary');
                    this.textContent = '{{ __("Seleccionado") }}';
                    this.disabled = true;

                    step2TabBtn.disabled = false;
                    step2Tab.show();
                });
            });

            document.getElementById('next-to-step3').addEventListener('click', function () {
                if (!verifyStep3()) {
                    return;
                }
                step3TabBtn.disabled = false;
                step3Tab.show();
            });

            step3TabBtn.addEventListener('click', () => {
                if (!verifyStep3()) {
                    step2Tab.show();
                    return;
                }
            });

            document.getElementById('back-to-step1').addEventListener('click', () => step1Tab.show());
            document.getElementById('back-to-step2').addEventListener('click', () => step2Tab.show());
        });
    </script>
    @endpush
</x-app-layout>
