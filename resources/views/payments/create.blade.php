<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="mb-0">Registrar Pago / Adquirir Membresía</h2>
            <small class="text-muted">
                Simula y registra el pago de membresía para un socio
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

        <div class="row g-4">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <strong class="mb-0">Datos de la Transacción</strong>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('payments.store') }}" id="paymentForm">
                            @csrf

                            <!-- Selector de Socio -->
                            <div class="mb-3">
                                <label for="member_id" class="form-label fw-bold">1. Seleccionar Socio</label>
                                <select class="form-select @error('member_id') is-invalid @enderror" id="member_id" name="member_id" required>
                                    <option value="">-- Seleccionar Socio --</option>
                                    @foreach ($members as $user)
                                    <option value="{{ $user->member?->user_id }}" @selected(old('member_id', $selectedMemberId)==$user->member?->user_id)>
                                        {{ $user->first_name }} {{ $user->last_name }} ({{ $user->email }})
                                    </option>
                                    @endforeach
                                </select>
                                @error('member_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Selector de Plan -->
                            <div class="mb-3"> <label for="plan_id" class="form-label fw-bold"> 2. Seleccionar Plan de Membresía </label> <select class="form-select @error('plan_id') is-invalid @enderror" id="plan_id" name="plan_id" required>
                                    <option value="" data-price="0" data-duration="0" data-plan-name="" data-period="" data-discount="0"> -- Seleccionar Plan -- </option>
                                    @foreach ($plans as $plan) @php [$name, $period] = array_pad(explode(' - ', $plan->name), 2, ''); $names = [ 'Basic' => 'Básico', 'Premium' => 'Premium', 'Elite' => 'Élite', ]; $periods = [ 'Monthly' => 'Mensual', 'Quarterly' => 'Trimestral', 'Annual' => 'Anual', ]; $discounts = [ 'Monthly' => 0, 'Quarterly' => 10, 'Annual' => 20, ];
                                    @endphp
                                    <option value="{{ $plan->id }}" data-price="{{ $plan->price }}" data-duration="{{ $plan->duration_months }}" data-plan-name="{{ $names[$name] ?? $name }}" data-period="{{ $periods[$period] ?? $period }}" data-discount="{{ $discounts[$period] ?? 0 }}" @selected(old('plan_id', request('plan_id'))==$plan->id)> {{ $names[$name] ?? $name }} - {{ $periods[$period] ?? $period }} - Q{{ number_format($plan->price, 2) }} </option>
                                    @endforeach
                                </select> @error('plan_id') <div class="invalid-feedback">{{ $message }}</div> @enderror </div>

                            <!-- Selector de Método de Pago (Simulado) -->
                            <div class="mb-3">
                                <label for="payment_method_id" class="form-label fw-bold">3. Método de Pago (Simulado)</label>
                                <select class="form-select @error('payment_method_id') is-invalid @enderror" id="payment_method_id" name="payment_method_id" required>
                                    <option value="">-- Seleccionar Método --</option>
                                    @foreach ($paymentMethods as $method)
                                    <option value="{{ $method->id }}" @selected(old('payment_method_id')==$method->id)>
                                        {{ $method->name }} - {{ $method->description }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('payment_method_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Selector de Promoción (Opcional, de rastreo) -->
                            <div class="mb-3">
                                <label for="promotion_id" class="form-label fw-bold">4. Promoción (Opcional)</label>
                                <select class="form-select @error('promotion_id') is-invalid @enderror" id="promotion_id" name="promotion_id">
                                    <option value="">Ninguna promoción seleccionada</option>
                                    @foreach ($promotions as $promotion)
                                    <option value="{{ $promotion->id }}" @selected(old('promotion_id')==$promotion->id)>
                                        {{ $promotion->name }} (Tipo: {{ $promotion->type->label() }}, Valor: {{ $promotion->value }})
                                    </option>
                                    @endforeach
                                </select>
                                @error('promotion_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('memberships.index') }}" class="btn btn-secondary">
                                    Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    Registrar Pago y Activar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Panel de Resumen del Pago -->
            <div class="col-md-4">
                <div class="card shadow-sm border-primary">
                    <div class="card-header bg-primary text-white py-3">
                        <strong class="mb-0">Resumen del Cobro</strong>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Plan seleccionado:</span>
                            <span id="summaryPlanName" class="fw-semibold">Ninguno</span>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Duración:</span>
                            <span id="summaryDuration" class="fw-semibold">-</span>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Descuento:</span>
                            <span id="summaryDiscount" class="fw-semibold">-</span>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fs-5 fw-bold text-dark">Total a pagar:</span>
                            <span id="summaryPrice" class="fs-4 fw-bold text-primary">$0.00</span>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script simple para actualizar resumen en tiempo real sin recargar -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const plan = document.getElementById('plan_id');
            const summary = {
                name: document.getElementById('summaryPlanName'),
                duration: document.getElementById('summaryDuration'),
                discount: document.getElementById('summaryDiscount'),
                price: document.getElementById('summaryPrice')
            };

            function updateSummary() {
                const option = plan.options[plan.selectedIndex];
                if (!plan.value) {
                    summary.name.textContent = 'Ninguno';
                    summary.duration.textContent = '-';
                    summary.discount.textContent = '-';
                    summary.price.textContent = 'Q0.00';
                    return;
                }
                summary.name.textContent = option.dataset.planName;
                summary.duration.textContent = `${option.dataset.period} (${option.dataset.duration} mes(es))`;
                summary.discount.textContent = `${option.dataset.discount}%`;
                summary.price.textContent = `Q${parseFloat(option.dataset.price).toFixed(2)}`;
            }
            plan.addEventListener('change', updateSummary);
            updateSummary();
        });
    </script>
</x-app-layout>
