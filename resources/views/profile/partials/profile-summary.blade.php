<div class="text-center mb-4">
    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary bg-opacity-10 text-primary fw-bold mx-auto mb-3"
         style="width: 80px; height: 80px; font-size: 1.75rem;">
        {{ mb_strtoupper(mb_substr($user->first_name, 0, 1).mb_substr($user->last_name, 0, 1)) }}
    </div>
    <h2 class="h5 mb-1">{{ $user->first_name }} {{ $user->last_name }}</h2>
    <hr>
    <span class="badge text-bg-primary d-inline-block text-wrap" style="max-width: 100%; white-space: normal; word-break: break-word;">
        {{ $user->role?->description ?: ($user->role?->name ? ucfirst($user->role->name) : __('Sin rol')) }}
    </span>
</div>

<dl class="row mb-0 small">
    <dt class="col-5 text-secondary">{{ __('Correo') }}</dt>
    <dd class="col-7 text-break">{{ $user->email }}</dd>

    <dt class="col-5 text-secondary">{{ __('Teléfono') }}</dt>
    <dd class="col-7">{{ $user->phone_number ?? __('No registrado') }}</dd>

    @if ($user->trainer)
    <dt class="col-5 text-secondary">{{ __('Especialidad') }}</dt>
    <dd class="col-7">{{ $user->trainer->specialty?->name ?? __('Sin asignar') }}</dd>
    @endif

    @if ($user->member)
    <dt class="col-5 text-secondary">{{ __('Nacimiento') }}</dt>
    <dd class="col-7">{{ $user->member->birth_date?->format('d/m/Y') ?? __('No registrada') }}</dd>

    <dt class="col-5 text-secondary">{{ __('Membresía') }}</dt>
    <dd class="col-7">{{ $membership?->plan?->name ?? __('Sin membresía vigente') }}</dd>

    @if ($membership)
        @php
            $membershipBadgeClass = match ($membership->status->value) {
                'active' => 'text-bg-success',
                'frozen' => 'text-bg-info',
                'expired' => 'text-bg-warning',
                'cancelled' => 'text-bg-danger',
                default => 'text-bg-secondary',
            };
        @endphp
        <dt class="col-5 text-secondary">{{ __('Estado') }}</dt>
        <dd class="col-7">
            <span class="badge {{ $membershipBadgeClass }}">{{ $membership->status->label() }}</span>
        </dd>
    @endif
    @endif
</dl>
