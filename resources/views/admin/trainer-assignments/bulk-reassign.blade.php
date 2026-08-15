<x-app-layout>
    <x-slot name="header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-0">{{ __('Reasignación de socios') }}</h2>
                <small class="text-muted">
                    {{ __('Reasigna múltiples socios a un nuevo entrenador') }}
                </small>
            </div>

            <a href="{{ route('trainer-assignments.index') }}" class="btn btn-secondary">
                {{ __('Volver al listado') }}
            </a>
        </div>
    </x-slot>

    <div class="container py-4">

        <div class="container-xl">

            @if ($errors->any())
            <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif

            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <strong>{{ __('Formulario de reasignación') }}</strong>
                </div>

                <div class="card-body">
                    <ul class="nav nav-pills mb-4" id="wizard-steps">
                        <li class="nav-item">
                            <button class="nav-link active" id="step1-tab" data-bs-toggle="pill" data-bs-target="#step1"
                                type="button">
                                1. Entrenador actual
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="step2-tab" data-bs-toggle="pill" data-bs-target="#step2"
                                type="button" disabled>
                                2. Socios a reasignar
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="step3-tab" data-bs-toggle="pill" data-bs-target="#step3"
                                type="button" disabled>
                                3. Nuevo entrenador y motivo
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" id="step4-tab" data-bs-toggle="pill" data-bs-target="#step4"
                                type="button" disabled>
                                4. Confirmar
                            </button>
                        </li>
                    </ul>

                    <form method="POST" action="{{ route('trainer-assignments.bulk-reassign.store') }}">
                        @csrf
                        <input type="hidden" name="old_trainer_id" id="old_trainer_id">

                        <div class="tab-content">

                            {{-- PASO 1: Seleccionar entrenador viejo --}}
                            <div class="tab-pane fade show active" id="step1">
                                <table class="table table-hover align-middle">
                                    <thead>
                                        <tr>
                                            <th>Entrenador</th>
                                            <th>Especialidad</th>
                                            <th>Socios activos</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($trainers as $trainer)
                                        <tr>
                                            <td>{{ $trainer->user->first_name }} {{ $trainer->user->last_name }}</td>
                                            <td>{{ $trainer->specialty->name ?? '—' }}</td>
                                            <td>{{ $trainer->active_members_count }}</td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-outline-primary select-old-trainer"
                                                    data-id="{{ $trainer->user_id }}"
                                                    data-name="{{ $trainer->user->first_name }} {{ $trainer->user->last_name }}"
                                                    {{ $trainer->active_members_count === 0 ? 'disabled' : '' }}>
                                                    Seleccionar
                                                </button>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-secondary">No hay entrenadores
                                                registrados.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            {{-- PASO 2: Seleccionar socios de ese entrenador --}}
                            <div class="tab-pane fade" id="step2">
                                @foreach ($trainers as $trainer)
                                <div class="old-trainer-assignments" data-trainer-id="{{ $trainer->user_id }}"
                                    style="display: none;">
                                    <table class="table table-hover align-middle">
                                        <thead>
                                            <tr>
                                                <th><input type="checkbox" class="select-all-assignments"
                                                        data-trainer-id="{{ $trainer->user_id }}" checked></th>
                                                <th>Socio</th>
                                                <th>Objetivo</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($trainer->trainerAssignments as $assignment)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" name="assignment_ids[]"
                                                        value="{{ $assignment->id }}" class="assignment-checkbox"
                                                        data-trainer-id="{{ $trainer->user_id }}" checked>
                                                </td>
                                                <td>{{ $assignment->member->user->first_name }} {{
                                                    $assignment->member->user->last_name }}</td>
                                                <td>{{ $assignment->goal ?? '—' }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @endforeach
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-outline-secondary" id="back-to-step1">&larr;
                                        Volver</button>
                                    <button type="button" class="btn btn-primary" id="next-to-step3">Continuar
                                        &rarr;</button>
                                </div>
                            </div>

                            {{-- PASO 3: Nuevo entrenador y motivo --}}
                            <div class="tab-pane fade" id="step3">
                                <div class="mb-3">
                                    <label class="form-label">Nuevo entrenador</label>
                                    <select name="new_trainer_id" id="new_trainer_id" class="form-select" required>
                                        <option value="">Selecciona un entrenador</option>
                                        @foreach ($trainers as $trainer)
                                        <option value="{{ $trainer->user_id }}" class="new-trainer-option"
                                            data-trainer-id="{{ $trainer->user_id }}">
                                            {{ $trainer->user->first_name }} {{ $trainer->user->last_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Motivo de la reasignación</label>
                                    <textarea name="reassignment_reason" id="reassignment_reason" class="form-control"
                                        rows="3" required></textarea>
                                </div>
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-outline-secondary" id="back-to-step2">&larr;
                                        Volver</button>
                                    <button type="button" class="btn btn-primary" id="next-to-step4">Continuar
                                        &rarr;</button>
                                </div>
                            </div>

                            {{-- PASO 4: Confirmación --}}
                            <div class="tab-pane fade" id="step4">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Resumen</h5>
                                        <p><strong>Entrenador actual:</strong> <span id="summary-old-trainer"></span>
                                        </p>
                                        <p><strong>Entrenador nuevo:</strong> <span id="summary-new-trainer"></span></p>
                                        <p><strong>Motivo:</strong> <span id="summary-reason"></span></p>
                                        <p><strong>Socios afectados:</strong></p>
                                        <ul id="summary-members"></ul>
                                    </div>
                                </div>
                                <div class="mt-3 d-flex gap-2">
                                    <button type="button" class="btn btn-outline-secondary" id="back-to-step3">&larr;
                                        Volver</button>
                                    <button type="submit" class="btn btn-success">Confirmar reasignación masiva</button>
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
            const tabs = {
                1: new bootstrap.Tab(document.getElementById('step1-tab')),
                2: new bootstrap.Tab(document.getElementById('step2-tab')),
                3: new bootstrap.Tab(document.getElementById('step3-tab')),
                4: new bootstrap.Tab(document.getElementById('step4-tab')),
            };
            const tabBtns = {
                2: document.getElementById('step2-tab'),
                3: document.getElementById('step3-tab'),
                4: document.getElementById('step4-tab'),
            };

            const step4TabBtn = document.getElementById('step4-tab');

            let oldTrainerName = '';

            const clearRowSelection = () => {
                document.querySelectorAll('#step1 tbody tr').forEach(row => {
                    row.classList.remove('table-primary');
                    row.querySelector('.select-old-trainer').textContent = '{{ __("Seleccionar") }}';
                    row.querySelector('.select-old-trainer').disabled = false;
                });
            };

            const verifyStep4 = () => {
                const oldTrainerId = document.getElementById('old_trainer_id').value;

                const trainerSelect = document.getElementById('new_trainer_id');
                const reason = document.getElementById('reassignment_reason').value.trim();

                if (!trainerSelect.value || !reason) {
                    alert('Completa el entrenador y el motivo.');
                    tabs[3].show();
                    return false;
                }

                const checked = document.querySelectorAll(`.assignment-checkbox[data-trainer-id="${oldTrainerId}"]:checked`);
                if (checked.length === 0) {
                    alert('Selecciona al menos un socio.');
                    tabs[2].show();
                    return false;
                }

                document.getElementById('summary-old-trainer').textContent = oldTrainerName;
                document.getElementById('summary-new-trainer').textContent = trainerSelect.options[trainerSelect.selectedIndex].text;
                document.getElementById('summary-reason').textContent = reason;

                const membersList = document.getElementById('summary-members');
                membersList.innerHTML = '';
                document.querySelectorAll(`.assignment-checkbox[data-trainer-id="${oldTrainerId}"]:checked`).forEach(cb => {
                    const li = document.createElement('li');
                    li.textContent = cb.closest('tr').children[1].textContent;
                    membersList.appendChild(li);
                });
                return true;
            };

            // Paso 1 -> selecciona entrenador viejo
            document.querySelectorAll('.select-old-trainer').forEach(btn => {
                btn.addEventListener('click', function () {
                    const trainerId = this.dataset.id;
                    oldTrainerName = this.dataset.name;

                    document.getElementById('old_trainer_id').value = trainerId;

                    // Muestra solo la tabla de socios de ese entrenador
                    document.querySelectorAll('.old-trainer-assignments').forEach(div => {
                        div.style.display = div.dataset.trainerId === trainerId ? 'block' : 'none';
                    });

                    // En el paso 3, oculta al entrenador viejo como opción de "nuevo entrenador"
                    document.querySelectorAll('.new-trainer-option').forEach(opt => {
                        opt.hidden = opt.dataset.trainerId === trainerId;
                    });

                    clearRowSelection();
                    this.closest('tr').classList.add('table-primary');
                    this.textContent = '{{ __("Seleccionado") }}';
                    this.disabled = true;

                    tabBtns[2].disabled = false;
                    tabs[2].show();
                });
            });

            document.getElementById('back-to-step1').addEventListener('click', () => tabs[1].show());

            // Paso 2 -> seleccionar socios
            document.querySelectorAll('.select-all-assignments').forEach(cb => {
                cb.addEventListener('change', function () {
                    document.querySelectorAll(`.assignment-checkbox[data-trainer-id="${this.dataset.trainerId}"]`)
                        .forEach(chk => chk.checked = this.checked);
                });
            });

            document.getElementById('next-to-step3').addEventListener('click', function () {
                const oldTrainerId = document.getElementById('old_trainer_id').value;
                const checked = document.querySelectorAll(`.assignment-checkbox[data-trainer-id="${oldTrainerId}"]:checked`);
                if (checked.length === 0) {
                    alert('Selecciona al menos un socio.');
                    return;
                }
                tabBtns[3].disabled = false;
                tabs[3].show();
            });

            document.getElementById('back-to-step2').addEventListener('click', () => tabs[2].show());

            // Paso 3 -> nuevo entrenador y motivo
            document.getElementById('next-to-step4').addEventListener('click', function () {
                if (!verifyStep4()) {
                    return;
                }
                tabBtns[4].disabled = false;
                tabs[4].show();
            });

            step4TabBtn.addEventListener('click', () => {
                if (!verifyStep4()) {
                    return;
                }
            });

            document.getElementById('back-to-step3').addEventListener('click', () => tabs[3].show());
        });
    </script>
    @endpush
</x-app-layout>
