<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="mb-0"><i class="bi bi-card-checklist me-2"></i>Planes de Membresía Disponibles</h2>
            <small class="text-muted">
                Selecciona un plan y asócialo a un socio para registrar su pago y activar su membresía.
            </small>
        </div>
    </x-slot>

    <div class="container-xl py-4">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Cerrar"></button>
            </div>
        @endif

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

        <!-- Grid de Planes -->
        <div class="row g-4 mb-5">
            @foreach ($plans as $plan)
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm border-primary border-top border-4">
                        <div class="card-body d-flex flex-column">
                            <h3 class="card-title text-primary text-center fw-bold mb-3">{{ $plan->name }}</h3>
                            <div class="text-center mb-4">
                                <span class="display-5 fw-bold text-dark">${{ number_format($plan->price, 2) }}</span>
                                <span class="text-muted">/ {{ $plan->duration_months }} mes(es)</span>
                            </div>
                            <p class="card-text text-muted flex-grow-1">{{ $plan->description }}</p>

                            <ul class="list-unstyled mb-4">
                                <li class="mb-2">
                                    <i class="bi bi-check-circle-fill text-success me-2"></i>
                                    Duración: {{ $plan->duration_months }} mes(es)
                                </li>
                                <li class="mb-2">
                                    @if ($plan->includes_group_classes)
                                        <span class="text-dark">
                                            <i class="bi bi-check-lg text-success me-1"></i>Clases grupales incluidas
                                            @if ($plan->weekly_class_limit)
                                                (Límite: {{ $plan->weekly_class_limit }} por semana)
                                            @else
                                                (Ilimitadas)
                                            @endif
                                        </span>
                                    @else
                                        <span class="text-muted"><i class="bi bi-x-lg me-1"></i>No incluye clases grupales</span>
                                    @endif
                                </li>
                                <li class="mb-2">
                                    @if ($plan->includes_trainer)
                                        <span class="text-dark"><i class="bi bi-check-lg text-success me-1"></i>Entrenador personal incluido</span>
                                    @else
                                        <span class="text-muted"><i class="bi bi-x-lg me-1"></i>No incluye entrenador</span>
                                    @endif
                                </li>
                                <li class="mb-2">
                                    @if ($plan->has_waitlist_priority)
                                        <span class="text-dark"><i class="bi bi-check-lg text-success me-1"></i>Prioridad en lista de espera</span>
                                    @else
                                        <span class="text-muted"><i class="bi bi-x-lg me-1"></i>Sin prioridad en lista de espera</span>
                                    @endif
                                </li>
                            </ul>

                            <hr>

                            <!-- Formulario GET para proceder al pago con el plan y el socio seleccionado -->
                            <form method="GET" action="{{ route('payments.create') }}">
                                <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                                
                                <div class="mb-3">
                                    <x-input-label class="small fw-bold" :value="__('Seleccionar Socio:')" />
                                    <select name="member_id" class="form-select form-select-sm" required>
                                        <option value="">-- Seleccionar --</option>
                                        @foreach ($members as $user)
                                            <option value="{{ $user->member?->user_id }}">
                                                {{ $user->first_name }} {{ $user->last_name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-arrow-right-circle me-1"></i>Proceder al Pago
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

    </div>
</x-app-layout>
