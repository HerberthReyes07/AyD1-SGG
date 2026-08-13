<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'SGG') }} - Sistema de Gestión de Gimnasio</title>
    <link rel="icon" href="{{ asset('images/header2.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="antialiased" style="font-family: 'Figtree', sans-serif;">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-sm navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="/">
                <x-application-logo style="height: 3.5rem;" />
                {{ config('app.name', 'SGG') }}
            </a>
            @if (Route::has('login'))
            <div class="d-flex">
                @auth
                <a href="{{ url('/dashboard') }}" class="btn btn-outline-light btn-sm">
                    Ir al panel
                </a>
                @else
                <a href="{{ route('login') }}" class="btn btn-warning btn-sm fw-medium">
                    Iniciar sesión
                </a>
                @endauth
            </div>
            @endif
        </div>
    </nav>

    <!-- Hero -->
    <header class="bg-dark text-white py-5">
        <div class="container py-4">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <span class="badge bg-warning text-dark mb-3">Uso interno del gimnasio</span>
                    <h1 class="display-5 fw-bold mb-3">
                        Toda la operación del gimnasio, en un solo lugar.
                    </h1>
                    <p class="lead text-white-50 mb-4">
                        Membresías, clases grupales, seguimiento con entrenador y nutrición —
                        administrado desde una sola plataforma, para el equipo y para cada socio.
                    </p>
                    @if (Route::has('login'))
                    <a href="{{ route('login') }}" class="btn btn-warning btn-lg fw-medium">
                        Iniciar sesión <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                    @endif
                </div>
                <div class="col-lg-5 d-none d-lg-block text-center">
                    <i class="bi bi-clipboard2-pulse" style="font-size: 12rem; color: rgba(255,255,255,0.08);"></i>
                </div>
            </div>
        </div>
    </header>

    <!-- Módulos -->
    <main class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="h3 fw-bold">Qué gestiona el sistema</h2>
                <p class="text-secondary">Cuatro módulos centrales, pensados para cada tipo de usuario.</p>
            </div>

            <div class="row g-4">
                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-warning bg-opacity-25 mb-3"
                                style="width: 3.5rem; height: 3.5rem;">
                                <i class="bi bi-person-badge fs-4 text-warning"></i>
                            </div>
                            <h3 class="h6 fw-semibold">Membresías</h3>
                            <p class="text-secondary small mb-0">
                                Planes Básico, Premium y Élite, con pagos, congelamientos y renovaciones controladas.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-warning bg-opacity-25 mb-3"
                                style="width: 3.5rem; height: 3.5rem;">
                                <i class="bi bi-people fs-4 text-warning"></i>
                            </div>
                            <h3 class="h6 fw-semibold">Clases grupales</h3>
                            <p class="text-secondary small mb-0">
                                Inscripción con control de cupo, lista de espera y calificación de cada clase.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-warning bg-opacity-25 mb-3"
                                style="width: 3.5rem; height: 3.5rem;">
                                <i class="bi bi-clipboard2-pulse fs-4 text-warning"></i>
                            </div>
                            <h3 class="h6 fw-semibold">Entrenador personal</h3>
                            <p class="text-secondary small mb-0">
                                Rutinas, mediciones periódicas y seguimiento de progreso para socios Élite.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-lg-3">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-warning bg-opacity-25 mb-3"
                                style="width: 3.5rem; height: 3.5rem;">
                                <i class="bi bi-egg-fried fs-4 text-warning"></i>
                            </div>
                            <h3 class="h6 fw-semibold">Nutrición</h3>
                            <p class="text-secondary small mb-0">
                                Registro de comidas, meta calórica diaria y desglose de macronutrientes.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    @include('layouts.footer')

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
