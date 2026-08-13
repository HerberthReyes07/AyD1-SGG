<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-semibold fs-4">{{ __('Dashboard') }}</h2>
    </x-slot>

    <div class="py-4">
        <div class="container-xl">

            @php
                $roleName = $user->role?->name;

                $hour = now()->hour;
                $greeting = match (true) {
                    $hour < 12 => __('Buenos días'),
                    $hour < 19 => __('Buenas tardes'),
                    default => __('Buenas noches'),
                };

                $initials = mb_strtoupper(mb_substr($user->first_name, 0, 1).mb_substr($user->last_name, 0, 1));
            @endphp

            {{-- Banner de bienvenida --}}
            <div class="card border-0 shadow-sm mb-4 overflow-hidden">
                <div class="card-body p-4 p-md-5 text-white"
                     style="background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);">
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <div class="d-flex align-items-center justify-content-center rounded-circle bg-white text-primary fw-bold"
                             style="width: 72px; height: 72px; font-size: 1.5rem;">
                            {{ $initials }}
                        </div>

                        <div>
                            <p class="mb-1 opacity-75">{{ $greeting }}</p>
                            <h1 class="h3 mb-2">{{ $user->first_name }} {{ $user->last_name }}</h1>
                            <span class="badge bg-white text-primary">
                                {{ $user->role?->description ?: ($roleName ? ucfirst($roleName) : __('Sin rol asignado')) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Un dato corto y propio del rol, solo informativo (sin links a otras features) --}}
            @if ($roleName === 'trainer' && $user->trainer)
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        <i class="bi bi-clipboard-heart fs-3 text-primary"></i>
                        <div>
                            <p class="small text-secondary mb-0">{{ __('Tu especialidad') }}</p>
                            <p class="mb-0 fw-medium">{{ $user->trainer->specialty?->name ?? __('Sin especialidad asignada') }}</p>
                        </div>
                    </div>
                </div>
            @elseif ($roleName === 'member' && $user->member)
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-flex align-items-center gap-3">
                        <i class="bi bi-heart-pulse fs-3 text-primary"></i>
                        <div>
                            <p class="small text-secondary mb-0">{{ __('Socio desde') }}</p>
                            <p class="mb-0 fw-medium">{{ $user->created_at?->format('d/m/Y') }}</p>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
