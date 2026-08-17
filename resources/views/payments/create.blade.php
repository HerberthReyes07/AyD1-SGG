<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="mb-0"><i class="bi bi-credit-card me-2"></i>Registrar Pago / Adquirir Membresía</h2>
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
                                <x-input-label for="member_id" class="fw-bold" :value="__('1. Seleccionar Socio')" />
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
                            <div class="mb-3">
                                <x-input-label for="plan_id" class="fw-bold" :value="__('2. Seleccionar Plan de Membresía')" />
                                <select class="form-select @error('plan_id') is-invalid @enderror" id="plan_id" name="plan_id" required>
                                    <option value="" data-price="0" data-duration="0" data-name=""> -- Seleccionar Plan -- </option>
                                    @foreach ($plans as $plan)
                                        <option value="{{ $plan->id }}" data-price="{{ $plan->price }}" data-duration="{{ $plan->duration_months }}" data-name="{{ $plan->name }}" @selected(old('plan_id', request('plan_id')) == $plan->id)>
                                            {{ $plan->name }} - Q{{ number_format($plan->price, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('plan_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Selector de Método de Pago (Simulado) -->
                            <div class="mb-3">
                                <x-input-label for="payment_method_id" class="fw-bold" :value="__('3. Método de Pago (Simulado)')" />
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

                            <!-- Selector de Promoción (Opcional) -->
                            <div class="mb-3">
                                <x-input-label for="promotion_id" class="fw-bold" :value="__('4. Promoción (Opcional)')" />
                                <select class="form-select @error('promotion_id') is-invalid @enderror" id="promotion_id" name="promotion_id">
                                    <option value="" data-type="" data-value="0">Ninguna promoción seleccionada</option>
                                    @foreach ($promotions as $promotion)
                                    <option value="{{ $promotion->id }}" data-type="{{ $promotion->type->value }}" data-value="{{ $promotion->value }}" @selected(old('promotion_id')==$promotion->id)>
                                        {{ $promotion->name }} ({{ $promotion->type->label() }}: {{ $promotion->type->value === 'percentage' ? number_format($promotion->value, 0) . '%' : 'Q' . number_format($promotion->value, 2) }})
                                    </option>
                                    @endforeach
                                </select>
                                @error('promotion_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('memberships.index') }}" class="btn btn-outline-secondary">
                                    Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-check-lg me-1"></i>Registrar Pago y Activar
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
                            <span id="summaryDiscount" class="fw-semibold">Sin descuento</span>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fs-5 fw-bold text-dark">Total a pagar:</span>
                            <span id="summaryPrice" class="fs-4 fw-bold text-primary">Q0.00</span>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Script para actualizar el resumen en tiempo real según plan y promoción -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const planSelect = document.getElementById('plan_id');
            const promoSelect = document.getElementById('promotion_id');
            const summary = {
                name: document.getElementById('summaryPlanName'),
                duration: document.getElementById('summaryDuration'),
                discount: document.getElementById('summaryDiscount'),
                price: document.getElementById('summaryPrice')
            };

            function updateSummary() {
                const planOpt = planSelect.options[planSelect.selectedIndex];
                if (!planSelect.value) {
                    summary.name.textContent = 'Ninguno';
                    summary.duration.textContent = '-';
                    summary.discount.textContent = 'Sin descuento';
                    summary.price.textContent = 'Q0.00';
                    return;
                }

                const basePrice = parseFloat(planOpt.dataset.price) || 0;
                const duration = planOpt.dataset.duration || 0;
                summary.name.textContent = planOpt.dataset.name;
                summary.duration.textContent = `${duration} mes(es)`;

                let discountText = 'Sin descuento';
                let finalPrice = basePrice;

                const promoOpt = promoSelect.options[promoSelect.selectedIndex];
                if (promoSelect.value && promoOpt) {
                    const type = promoOpt.dataset.type;
                    const val = parseFloat(promoOpt.dataset.value) || 0;

                    if (type === 'percentage') {
                        const discountAmt = basePrice * (val / 100);
                        finalPrice = Math.max(0, basePrice - discountAmt);
                        discountText = `${val}% (-Q${discountAmt.toFixed(2)})`;
                    } else if (type === 'fixed_amount') {
                        finalPrice = Math.max(0, basePrice - val);
                        discountText = `-Q${val.toFixed(2)}`;
                    }
                }

                summary.discount.textContent = discountText;
                summary.price.textContent = `Q${finalPrice.toFixed(2)}`;
            }

            planSelect.addEventListener('change', updateSummary);
            promoSelect.addEventListener('change', updateSummary);
            updateSummary();
        });
    </script>
</x-app-layout>
