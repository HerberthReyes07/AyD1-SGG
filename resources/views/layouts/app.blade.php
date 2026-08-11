<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SGG') }} - Sistema de Gestión de Gimnasio</title>
    <link rel="icon" href="{{ asset('images/header2.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased" style="font-family: 'Figtree', sans-serif;">
    @php
    $roleName = Auth::user()->role?->name;

    $headerThemes = [
    'admin' => [
    'accent' => 'bg-primary',
    'tint' => 'bg-primary-subtle',
    'badge' => 'text-bg-primary',
    'label' => 'Administración',
    ],
    'receptionist' => [
    'accent' => 'bg-success',
    'tint' => 'bg-success-subtle',
    'badge' => 'text-bg-success',
    'label' => 'Recepción',
    ],
    'trainer' => [
    'accent' => 'bg-info',
    'tint' => 'bg-info-subtle',
    'badge' => 'text-bg-info',
    'label' => 'Entrenador',
    ],
    'member' => [
    'accent' => 'bg-warning',
    'tint' => 'bg-warning-subtle',
    'badge' => 'text-bg-warning',
    'label' => 'Miembro',
    ],
    ];

    $headerTheme = $headerThemes[$roleName] ?? [
    'accent' => 'bg-secondary',
    'tint' => 'bg-light',
    'badge' => 'text-bg-secondary',
    'label' => 'Panel',
    ];
    @endphp

    <div class="bg-light" style="min-height: 100vh;">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
        <header class="py-4">
            <div class="container-xl px-3">
                <div class="overflow-hidden rounded-4 border shadow-sm bg-white">
                    <div class="{{ $headerTheme['accent'] }}" style="height: .35rem;"></div>
                    <div class="{{ $headerTheme['tint'] }} px-4 py-4">
                        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                            <div class="flex-grow-1">
                                {{ $header }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        @endisset

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>

    <!-- Footer -->
    @include('layouts.footer')

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>

</html>
