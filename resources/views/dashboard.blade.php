<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-semibold fs-4">{{ __('Dashboard') }}</h2>
    </x-slot>

    <style>
        .dashboard-stat-icon {
            width: 52px;
            height: 52px;
        }

        .dashboard-tile {
            transition: transform .15s ease, box-shadow .15s ease;
        }

        .dashboard-tile:hover {
            transform: translateY(-3px);
            box-shadow: 0 .75rem 1.5rem rgba(0, 0, 0, .1) !important;
        }
    </style>

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
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body d-flex align-items-center gap-3">
                        <i class="bi bi-clipboard-heart fs-3 text-primary"></i>
                        <div>
                            <p class="small text-secondary mb-0">{{ __('Tu especialidad') }}</p>
                            <p class="mb-0 fw-medium">{{ $user->trainer->specialty?->name ?? __('Sin especialidad asignada') }}</p>
                        </div>
                    </div>
                </div>
            @elseif ($roleName === 'member' && $user->member)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body d-flex align-items-center gap-3">
                        <i class="bi bi-heart-pulse fs-3 text-primary"></i>
                        <div>
                            <p class="small text-secondary mb-0">{{ __('Socio desde') }}</p>
                            <p class="mb-0 fw-medium">{{ $user->created_at?->format('d/m/Y') }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- ============================== ADMIN ============================== --}}
            @if ($roleName === 'admin')

                <div class="row g-3 mb-4">
                    <div class="col-6 col-lg-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="dashboard-stat-icon rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary">
                                    <i class="bi bi-people fs-4"></i>
                                </div>
                                <div>
                                    <p class="mb-0 fs-4 fw-bold">{{ $stats['totalMembers'] }}</p>
                                    <small class="text-secondary">{{ __('Socios registrados') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="dashboard-stat-icon rounded-circle d-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success">
                                    <i class="bi bi-card-checklist fs-4"></i>
                                </div>
                                <div>
                                    <p class="mb-0 fs-4 fw-bold">{{ $stats['activeMemberships'] }}</p>
                                    <small class="text-secondary">{{ __('Membresías activas') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="dashboard-stat-icon rounded-circle d-flex align-items-center justify-content-center bg-info bg-opacity-10 text-info">
                                    <i class="bi bi-person-badge fs-4"></i>
                                </div>
                                <div>
                                    <p class="mb-0 fs-4 fw-bold">{{ $stats['guestPassesThisMonth'] }}</p>
                                    <small class="text-secondary">{{ __('Pases de invitado (mes)') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="dashboard-stat-icon rounded-circle d-flex align-items-center justify-content-center bg-warning bg-opacity-10 text-warning">
                                    <i class="bi bi-calendar-week fs-4"></i>
                                </div>
                                <div>
                                    <p class="mb-0 fs-4 fw-bold">{{ $stats['upcomingSessions'] }}</p>
                                    <small class="text-secondary">{{ __('Clases próximos 7 días') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-0 py-3">
                                <h5 class="mb-0 fw-bold"><i class="bi bi-graph-up-arrow text-primary me-2"></i>{{ __('Ingresos últimos 6 meses') }}</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="admin-income-chart" height="110"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-white border-0 py-3">
                                <h5 class="mb-0 fw-bold"><i class="bi bi-pie-chart text-primary me-2"></i>{{ __('Membresías por estado') }}</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="admin-membership-chart" height="180"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-0 py-3">
                                <h5 class="mb-0 fw-bold"><i class="bi bi-bar-chart-line text-primary me-2"></i>{{ __('Asistencia últimos 7 días') }}</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="admin-attendance-chart" height="80"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <h5 class="fw-bold mb-3">{{ __('Accesos rápidos') }}</h5>
                <div class="row g-3 mb-4">
                    @foreach ([
                        ['route' => 'members.index', 'icon' => 'bi-people', 'label' => __('Miembros'), 'desc' => __('Gestionar socios')],
                        ['route' => 'employees.index', 'icon' => 'bi-person-workspace', 'label' => __('Empleados'), 'desc' => __('Recepcionistas y entrenadores')],
                        ['route' => 'payments.index', 'icon' => 'bi-cash-coin', 'label' => __('Pagos'), 'desc' => __('Registro de pagos')],
                        ['route' => 'memberships.index', 'icon' => 'bi-card-list', 'label' => __('Membresías'), 'desc' => __('Planes contratados')],
                        ['route' => 'group-classes.index', 'icon' => 'bi-calendar2-event', 'label' => __('Clases grupales'), 'desc' => __('Horarios y sesiones')],
                        ['route' => 'foods.index', 'icon' => 'bi-egg-fried', 'label' => __('Catálogo de alimentos'), 'desc' => __('Base nutricional')],
                        ['route' => 'trainer-assignments.index', 'icon' => 'bi-person-video3', 'label' => __('Asignación de entrenadores'), 'desc' => __('Socios plan Elite')],
                        ['route' => 'reports.income.index', 'icon' => 'bi-graph-up', 'label' => __('Reporte de ingresos'), 'desc' => __('Ver reporte')],
                        ['route' => 'reports.membership-expiration.index', 'icon' => 'bi-calendar-x', 'label' => __('Vencimientos'), 'desc' => __('Ver reporte')],
                        ['route' => 'guest-pass-reports.index', 'icon' => 'bi-ticket-perforated', 'label' => __('Pases de invitado'), 'desc' => __('Ver reporte')],
                        ['route' => 'class-attendance-reports.index', 'icon' => 'bi-clipboard-data', 'label' => __('Asistencia a clases'), 'desc' => __('Ver reporte')],
                        ['route' => 'physical-progress.index', 'icon' => 'bi-graph-up-arrow', 'label' => __('Progreso físico'), 'desc' => __('Ver reporte')],
                    ] as $tile)
                        <div class="col-6 col-md-4 col-lg-3">
                            <a href="{{ route($tile['route']) }}" class="text-decoration-none">
                                <div class="card border-0 shadow-sm h-100 dashboard-tile">
                                    <div class="card-body d-flex align-items-center gap-3">
                                        <div class="dashboard-stat-icon rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary flex-shrink-0">
                                            <i class="bi {{ $tile['icon'] }} fs-4"></i>
                                        </div>
                                        <div>
                                            <p class="mb-0 fw-semibold text-dark">{{ $tile['label'] }}</p>
                                            <small class="text-secondary">{{ $tile['desc'] }}</small>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>

            @endif

            {{-- ========================== RECEPTIONIST ========================== --}}
            @if ($roleName === 'receptionist')

                <div class="row g-3 mb-4">
                    <div class="col-6 col-lg-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="dashboard-stat-icon rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary">
                                    <i class="bi bi-door-open fs-4"></i>
                                </div>
                                <div>
                                    <p class="mb-0 fs-4 fw-bold">{{ $stats['todayCheckIns'] }}</p>
                                    <small class="text-secondary">{{ __('Check-ins hoy') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="dashboard-stat-icon rounded-circle d-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success">
                                    <i class="bi bi-person-walking fs-4"></i>
                                </div>
                                <div>
                                    <p class="mb-0 fs-4 fw-bold">{{ $stats['currentlyInGym'] }}</p>
                                    <small class="text-secondary">{{ __('En el gimnasio ahora') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="dashboard-stat-icon rounded-circle d-flex align-items-center justify-content-center bg-info bg-opacity-10 text-info">
                                    <i class="bi bi-person-badge fs-4"></i>
                                </div>
                                <div>
                                    <p class="mb-0 fs-4 fw-bold">{{ $stats['guestPassesToday'] }}</p>
                                    <small class="text-secondary">{{ __('Pases de invitado hoy') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="dashboard-stat-icon rounded-circle d-flex align-items-center justify-content-center bg-warning bg-opacity-10 text-warning">
                                    <i class="bi bi-cash-coin fs-4"></i>
                                </div>
                                <div>
                                    <p class="mb-0 fs-4 fw-bold">{{ $stats['paymentsToday'] }}</p>
                                    <small class="text-secondary">{{ __('Pagos registrados hoy') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white border-0 py-3">
                                <h5 class="mb-0 fw-bold"><i class="bi bi-bar-chart-line text-primary me-2"></i>{{ __('Asistencia por hora (hoy)') }}</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="receptionist-hourly-chart" height="80"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <h5 class="fw-bold mb-3">{{ __('Accesos rápidos') }}</h5>
                <div class="row g-3 mb-4">
                    @foreach ([
                        ['route' => 'attendance.index', 'icon' => 'bi-door-open', 'label' => __('Asistencia'), 'desc' => __('Check-in / check-out')],
                        ['route' => 'guest-passes.index', 'icon' => 'bi-person-badge', 'label' => __('Pases de invitado'), 'desc' => __('Registrar visita')],
                        ['route' => 'payments.index', 'icon' => 'bi-cash-coin', 'label' => __('Pagos'), 'desc' => __('Registro de pagos')],
                        ['route' => 'memberships.index', 'icon' => 'bi-card-list', 'label' => __('Membresías'), 'desc' => __('Planes contratados')],
                    ] as $tile)
                        <div class="col-6 col-md-4 col-lg-3">
                            <a href="{{ route($tile['route']) }}" class="text-decoration-none">
                                <div class="card border-0 shadow-sm h-100 dashboard-tile">
                                    <div class="card-body d-flex align-items-center gap-3">
                                        <div class="dashboard-stat-icon rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary flex-shrink-0">
                                            <i class="bi {{ $tile['icon'] }} fs-4"></i>
                                        </div>
                                        <div>
                                            <p class="mb-0 fw-semibold text-dark">{{ $tile['label'] }}</p>
                                            <small class="text-secondary">{{ $tile['desc'] }}</small>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>

            @endif

            {{-- ============================= TRAINER ============================= --}}
            @if ($roleName === 'trainer' && $user->trainer)

                <div class="row g-3 mb-4">
                    <div class="col-6 col-lg-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="dashboard-stat-icon rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary">
                                    <i class="bi bi-people fs-4"></i>
                                </div>
                                <div>
                                    <p class="mb-0 fs-4 fw-bold">{{ $stats['activeAssignments'] }}</p>
                                    <small class="text-secondary">{{ __('Socios asignados') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="dashboard-stat-icon rounded-circle d-flex align-items-center justify-content-center bg-info bg-opacity-10 text-info">
                                    <i class="bi bi-clock-history fs-4"></i>
                                </div>
                                <div>
                                    <p class="mb-0 fs-4 fw-bold">{{ $stats['pastAssignments'] }}</p>
                                    <small class="text-secondary">{{ __('Asignaciones finalizadas') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-lg-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="dashboard-stat-icon rounded-circle d-flex align-items-center justify-content-center bg-warning bg-opacity-10 text-warning">
                                    <i class="bi bi-calendar-week fs-4"></i>
                                </div>
                                <div>
                                    <p class="mb-0 fs-4 fw-bold">{{ $stats['upcomingSessionsCount'] }}</p>
                                    <small class="text-secondary">{{ __('Sesiones próximos 7 días') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($stats['upcomingSessions']->isNotEmpty())
                    @php
                        $sessionChartData = $stats['upcomingSessions']->map(fn ($session) => [
                            'label' => ($session->groupClass->name ?? __('Clase')).' - '.$session->starts_at->format('d/m H:i'),
                            'count' => $session->enrollments_count,
                            'capacity' => $session->groupClass->max_participants ?? 0,
                        ]);
                    @endphp
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-white border-0 py-3">
                                    <h5 class="mb-0 fw-bold"><i class="bi bi-bar-chart-line text-primary me-2"></i>{{ __('Ocupación de tus próximas sesiones') }}</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="trainer-sessions-chart" height="90"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <h5 class="fw-bold mb-3">{{ __('Accesos rápidos') }}</h5>
                <div class="row g-3 mb-4">
                    @foreach ([
                        ['route' => 'assignments.index', 'icon' => 'bi-people', 'label' => __('Mis asignaciones'), 'desc' => __('Socios activos')],
                        ['route' => 'assignments.history', 'icon' => 'bi-clock-history', 'label' => __('Historial'), 'desc' => __('Asignaciones pasadas')],
                        ['route' => 'trainer-classes.index', 'icon' => 'bi-calendar2-event', 'label' => __('Mis clases grupales'), 'desc' => __('Sesiones programadas')],
                    ] as $tile)
                        <div class="col-6 col-md-4 col-lg-3">
                            <a href="{{ route($tile['route']) }}" class="text-decoration-none">
                                <div class="card border-0 shadow-sm h-100 dashboard-tile">
                                    <div class="card-body d-flex align-items-center gap-3">
                                        <div class="dashboard-stat-icon rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary flex-shrink-0">
                                            <i class="bi {{ $tile['icon'] }} fs-4"></i>
                                        </div>
                                        <div>
                                            <p class="mb-0 fw-semibold text-dark">{{ $tile['label'] }}</p>
                                            <small class="text-secondary">{{ $tile['desc'] }}</small>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>

            @endif

            {{-- ============================= MEMBER ============================= --}}
            @if ($roleName === 'member' && $user->member)

                <div class="row g-3 mb-4">
                    <div class="col-md-6 col-lg-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center gap-3 mb-1">
                                    <div class="dashboard-stat-icon rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary">
                                        <i class="bi bi-egg-fried fs-4"></i>
                                    </div>
                                    <div>
                                        <p class="mb-0 fs-5 fw-bold">{{ number_format($stats['todayConsumed']) }} kcal</p>
                                        <small class="text-secondary">{{ __('Consumido hoy') }}</small>
                                    </div>
                                </div>
                                @if ($stats['comparison'])
                                    <span @class([
                                        'badge mt-2',
                                        'bg-success' => $stats['comparison']['status'] === 'within',
                                        'bg-warning text-dark' => $stats['comparison']['status'] === 'above',
                                        'bg-info text-dark' => $stats['comparison']['status'] === 'below',
                                    ])>
                                        {{ match ($stats['comparison']['status']) {
                                            'within' => __('Dentro de tu meta'),
                                            'above' => __('Por arriba de tu meta'),
                                            'below' => __('Por debajo de tu meta'),
                                        } }}
                                        ({{ $stats['comparison']['percentage'] }}%)
                                    </span>
                                @else
                                    <a href="{{ route('calorie-goals.edit') }}" class="small">{{ __('Define tu meta calórica') }}</a>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="dashboard-stat-icon rounded-circle d-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success">
                                    <i class="bi bi-door-open fs-4"></i>
                                </div>
                                <div>
                                    <p class="mb-0 fs-4 fw-bold">{{ $stats['weeklyAttendance'] }}</p>
                                    <small class="text-secondary">{{ __('Visitas esta semana') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="dashboard-stat-icon rounded-circle d-flex align-items-center justify-content-center bg-info bg-opacity-10 text-info">
                                    <i class="bi bi-calendar2-check fs-4"></i>
                                </div>
                                <div>
                                    <p class="mb-0 fs-4 fw-bold">{{ $stats['upcomingClasses']->count() }}</p>
                                    <small class="text-secondary">{{ __('Próximas clases inscritas') }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-3">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body d-flex align-items-center gap-3">
                                <div class="dashboard-stat-icon rounded-circle d-flex align-items-center justify-content-center bg-warning bg-opacity-10 text-warning">
                                    <i class="bi bi-card-checklist fs-4"></i>
                                </div>
                                <div>
                                    @if ($stats['membership'])
                                        <p class="mb-0 fs-6 fw-bold">{{ $stats['membership']->plan?->name }}</p>
                                        <small class="text-secondary">
                                            {{ __('Vence') }} {{ $stats['membership']->end_date?->format('d/m/Y') }}
                                        </small>
                                    @else
                                        <p class="mb-0 fs-6 fw-bold text-danger">{{ __('Sin membresía activa') }}</p>
                                        <a href="{{ route('member-memberships.index') }}" class="small">{{ __('Ver opciones') }}</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($stats['goal'])
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-white border-0 py-3">
                                    <h5 class="mb-0 fw-bold"><i class="bi bi-graph-up text-primary me-2"></i>{{ __('Calorías últimos 7 días') }}</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="member-calorie-chart" height="90"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($stats['upcomingClasses']->isNotEmpty())
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-white border-0 py-3">
                                    <h5 class="mb-0 fw-bold"><i class="bi bi-calendar2-week text-primary me-2"></i>{{ __('Tus próximas clases') }}</h5>
                                </div>
                                <ul class="list-group list-group-flush">
                                    @foreach ($stats['upcomingClasses'] as $enrollment)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span>
                                                <i class="bi bi-calendar2-event text-primary me-2"></i>
                                                {{ $enrollment->classSession->groupClass->name ?? __('Clase') }}
                                            </span>
                                            <span class="badge bg-light text-dark border">
                                                {{ $enrollment->classSession->starts_at->format('d/m/Y H:i') }}
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <h5 class="fw-bold mb-3">{{ __('Accesos rápidos') }}</h5>
                <div class="row g-3 mb-4">
                    @php
                        $memberTiles = [
                            ['route' => 'member-meals.create', 'icon' => 'bi-egg-fried', 'label' => __('Registrar comida'), 'desc' => __('Log de hoy')],
                            ['route' => 'member-classes.index', 'icon' => 'bi-calendar2-event', 'label' => __('Mis clases'), 'desc' => __('Inscribirme')],
                            ['route' => 'calorie-goals.edit', 'icon' => 'bi-bullseye', 'label' => __('Mi meta calórica'), 'desc' => __('Ajustar meta')],
                            ['route' => 'nutrition-history.index', 'icon' => 'bi-clock-history', 'label' => __('Historial nutricional'), 'desc' => __('Ver tendencia')],
                            ['route' => 'member-memberships.index', 'icon' => 'bi-card-list', 'label' => __('Mi membresía'), 'desc' => __('Ver / renovar')],
                        ];

                        if ($user->member->hasActiveTrainerAssignment()) {
                            $memberTiles[] = ['route' => 'member-training.index', 'icon' => 'bi-person-video3', 'label' => __('Mi entrenamiento'), 'desc' => __('Rutinas y progreso')];
                        }
                    @endphp
                    @foreach ($memberTiles as $tile)
                        <div class="col-6 col-md-4 col-lg-3">
                            <a href="{{ route($tile['route']) }}" class="text-decoration-none">
                                <div class="card border-0 shadow-sm h-100 dashboard-tile">
                                    <div class="card-body d-flex align-items-center gap-3">
                                        <div class="dashboard-stat-icon rounded-circle d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary flex-shrink-0">
                                            <i class="bi {{ $tile['icon'] }} fs-4"></i>
                                        </div>
                                        <div>
                                            <p class="mb-0 fw-semibold text-dark">{{ $tile['label'] }}</p>
                                            <small class="text-secondary">{{ $tile['desc'] }}</small>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>

            @endif

        </div>
    </div>

    @if (in_array($roleName, ['admin', 'receptionist', 'trainer', 'member']))
        @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const baseGridColor = 'rgba(0,0,0,0.06)';

                @if ($roleName === 'admin')
                    const incomeTrend = @json($stats['incomeTrend']);
                    new Chart(document.getElementById('admin-income-chart').getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: incomeTrend.map(p => p.label),
                            datasets: [{
                                label: 'Ingresos (Q)',
                                data: incomeTrend.map(p => p.total),
                                backgroundColor: '#0d6efd',
                                borderRadius: 4,
                            }],
                        },
                        options: {
                            responsive: true,
                            plugins: { legend: { display: false } },
                            scales: {
                                y: { beginAtZero: true, grid: { color: baseGridColor } },
                                x: { grid: { display: false } },
                            },
                        },
                    });

                    const membershipsByStatus = @json($stats['membershipsByStatus']);
                    new Chart(document.getElementById('admin-membership-chart').getContext('2d'), {
                        type: 'doughnut',
                        data: {
                            labels: membershipsByStatus.map(s => s.label),
                            datasets: [{
                                data: membershipsByStatus.map(s => s.value),
                                backgroundColor: ['#198754', '#0dcaf0', '#ffc107', '#dc3545'],
                            }],
                        },
                        options: {
                            responsive: true,
                            plugins: { legend: { position: 'bottom' } },
                        },
                    });

                    const attendanceTrend = @json($stats['attendanceTrend']);
                    new Chart(document.getElementById('admin-attendance-chart').getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: attendanceTrend.map(a => a.label),
                            datasets: [{
                                label: 'Check-ins',
                                data: attendanceTrend.map(a => a.count),
                                borderColor: '#0d6efd',
                                backgroundColor: 'rgba(13,110,253,0.1)',
                                tension: 0.3,
                                fill: true,
                            }],
                        },
                        options: {
                            responsive: true,
                            plugins: { legend: { display: false } },
                            scales: {
                                y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: baseGridColor } },
                                x: { grid: { display: false } },
                            },
                        },
                    });
                @endif

                @if ($roleName === 'receptionist')
                    const hourlyAttendance = @json($stats['hourlyAttendance']);
                    new Chart(document.getElementById('receptionist-hourly-chart').getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: hourlyAttendance.map(h => h.label),
                            datasets: [{
                                label: 'Check-ins',
                                data: hourlyAttendance.map(h => h.count),
                                backgroundColor: '#198754',
                                borderRadius: 4,
                            }],
                        },
                        options: {
                            responsive: true,
                            plugins: { legend: { display: false } },
                            scales: {
                                y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: baseGridColor } },
                                x: { grid: { display: false } },
                            },
                        },
                    });
                @endif

                @if ($roleName === 'trainer' && isset($sessionChartData))
                    const sessionData = @json($sessionChartData);
                    const sessionCanvas = document.getElementById('trainer-sessions-chart');
                    if (sessionCanvas) {
                        new Chart(sessionCanvas.getContext('2d'), {
                            type: 'bar',
                            data: {
                                labels: sessionData.map(s => s.label),
                                datasets: [
                                    {
                                        label: 'Inscritos',
                                        data: sessionData.map(s => s.count),
                                        backgroundColor: '#0d6efd',
                                        borderRadius: 4,
                                    },
                                    {
                                        label: 'Capacidad',
                                        data: sessionData.map(s => s.capacity),
                                        backgroundColor: 'rgba(13,110,253,0.15)',
                                        borderRadius: 4,
                                    },
                                ],
                            },
                            options: {
                                responsive: true,
                                scales: {
                                    y: { beginAtZero: true, ticks: { stepSize: 1 }, grid: { color: baseGridColor } },
                                    x: { grid: { display: false } },
                                },
                            },
                        });
                    }
                @endif

                @if ($roleName === 'member' && ($stats['goal'] ?? null))
                    const calorieTrend = @json($stats['calorieTrend']);
                    const goalCalories = {{ (float) $stats['goal']->daily_calories }};
                    new Chart(document.getElementById('member-calorie-chart').getContext('2d'), {
                        type: 'line',
                        data: {
                            labels: calorieTrend.map(d => d.label),
                            datasets: [
                                {
                                    label: 'Consumido (kcal)',
                                    data: calorieTrend.map(d => d.calories),
                                    borderColor: '#0d6efd',
                                    backgroundColor: 'rgba(13,110,253,0.1)',
                                    tension: 0.3,
                                    fill: true,
                                },
                                {
                                    label: 'Meta (kcal)',
                                    data: calorieTrend.map(() => goalCalories),
                                    borderColor: '#dc3545',
                                    borderDash: [6, 4],
                                    pointRadius: 0,
                                    fill: false,
                                },
                            ],
                        },
                        options: {
                            responsive: true,
                            scales: {
                                y: { beginAtZero: true, grid: { color: baseGridColor } },
                                x: { grid: { display: false } },
                            },
                        },
                    });
                @endif
            });
        </script>
        @endpush
    @endif
</x-app-layout>
