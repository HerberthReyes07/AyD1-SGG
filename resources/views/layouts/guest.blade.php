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
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-light">

    <div class="min-vh-100 d-flex align-items-center justify-content-center py-5">

        <div class="container">
            <div class="row justify-content-center">

                <div class="col-12 col-sm-10 col-md-7 col-lg-5 col-xl-4">

                    <!-- Logo y título -->
                    <div class="text-center mb-4">

                        <a href="/" class="text-decoration-none">
                            <x-application-logo style="width: 90px; height: 90px;" class="mb-3" />

                            <h4 class="fw-bold text-dark mb-1">
                                SGG
                            </h4>

                            <p class="text-secondary mb-0">
                                Sistema de Gestión de Gimnasio
                            </p>
                        </a>

                    </div>

                    <!-- Tarjeta -->
                    <div class="card border-0 shadow-sm rounded-4">

                        <div class="card-body p-4 p-md-6">
                            {{ $slot }}
                        </div>

                    </div>

                </div>

            </div>
        </div>

    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
