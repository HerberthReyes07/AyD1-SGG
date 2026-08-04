<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" href="{{ asset('images/header2.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased d-flex flex-column min-vh-100" style="font-family: 'Figtree', sans-serif; background-color: #FDFDFC;">

    <header class="container py-3">
        @if (Route::has('login'))
            <nav class="d-flex justify-content-end gap-3">
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn btn-outline-dark btn-sm">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-link btn-sm text-dark">
                        Iniciar sesión
                    </a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-outline-dark btn-sm">
                            Registrarse
                        </a>
                    @endif
                @endauth
            </nav>
        @endif
    </header>

    <main class="flex-grow-1 d-flex align-items-center">
        <div class="container" style="max-width: 700px;">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4 p-lg-5">
                    <h1 class="h4 fw-medium mb-2">Let's get started</h1>
                    <p class="text-secondary mb-4">
                        With so many options available to you, we suggest you start with the following:
                    </p>

                    <ul class="list-unstyled d-flex flex-column gap-3 mb-4">
                        <li class="d-flex align-items-center gap-3">
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light border" style="width: 1.5rem; height: 1.5rem;">
                                <i class="bi bi-book text-secondary" style="font-size: 0.8rem;"></i>
                            </span>
                            <span>
                                Read the
                                <a href="https://laravel.com/docs" target="_blank" class="fw-medium text-decoration-underline" style="color: #F53003;">
                                    Documentation <i class="bi bi-box-arrow-up-right" style="font-size: 0.7rem;"></i>
                                </a>
                            </span>
                        </li>
                        <li class="d-flex align-items-center gap-3">
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light border" style="width: 1.5rem; height: 1.5rem;">
                                <i class="bi bi-play-circle text-secondary" style="font-size: 0.8rem;"></i>
                            </span>
                            <span>
                                Watch video tutorials at
                                <a href="https://laracasts.com" target="_blank" class="fw-medium text-decoration-underline" style="color: #F53003;">
                                    Laracasts <i class="bi bi-box-arrow-up-right" style="font-size: 0.7rem;"></i>
                                </a>
                            </span>
                        </li>
                    </ul>

                    <a href="https://cloud.laravel.com" target="_blank" class="btn btn-dark btn-sm">
                        Deploy now
                    </a>

                    <p class="text-secondary small mt-4 mb-0">
                        v{{ app()->version() }}
                        <a href="https://github.com/laravel/framework/blob/13.x/CHANGELOG.md" target="_blank" class="fw-medium text-decoration-underline ms-1" style="color: #F53003;">
                            View changelog <i class="bi bi-box-arrow-up-right" style="font-size: 0.7rem;"></i>
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </main>

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
