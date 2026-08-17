@php
$roleName = Auth::user()->role?->name;

$navbarThemes = [
'admin' => [
'navbar' => 'navbar-dark bg-primary border-primary',
'dropdown' => 'btn btn-sm rounded-pill px-3 py-1 fw-semibold bg-white text-dark border-0 shadow-sm',
'mobileName' => 'text-white',
'mobileEmail' => 'text-white-50',
'mobileBorder' => 'border-light',
],
'receptionist' => [
'navbar' => 'navbar-dark bg-success border-success',
'dropdown' => 'btn btn-sm rounded-pill px-3 py-1 fw-semibold bg-white text-dark border-0 shadow-sm',
'mobileName' => 'text-white',
'mobileEmail' => 'text-white-50',
'mobileBorder' => 'border-light',
],
'trainer' => [
'navbar' => 'navbar-dark bg-info border-info',
'dropdown' => 'btn btn-sm rounded-pill px-3 py-1 fw-semibold bg-white text-dark border-0 shadow-sm',
'mobileName' => 'text-white',
'mobileEmail' => 'text-white-50',
'mobileBorder' => 'border-light',
],
'member' => [
'navbar' => 'navbar-dark bg-warning border-warning',
'dropdown' => 'btn btn-sm rounded-pill px-3 py-1 fw-semibold bg-white text-dark border-0 shadow-sm',
'mobileName' => 'text-white',
'mobileEmail' => 'text-white-50',
'mobileBorder' => 'border-light',
],
];

$theme = $navbarThemes[$roleName] ?? [
'navbar' => 'navbar-light bg-white border-bottom',
'dropdown' => 'btn btn-sm rounded-pill px-3 py-1 fw-semibold bg-light text-dark border shadow-sm',
'mobileName' => 'text-dark',
'mobileEmail' => 'text-muted',
'mobileBorder' => 'border-bottom',
];

$isReportsActive = request()->routeIs('physical-progress.*') || request()->routeIs('group-class-reports.*') || request()->routeIs('class-attendance-reports.*') || request()->routeIs('guest-pass-reports.*') || request()->routeIs('reports.*');
@endphp

{{--
    navbar-expand-xxl (no -sm): con roles como admin que tienen muchos enlaces,
    colapsar solo bajo "sm" (576px) los amontonaba sin quebrar, y a letra
    normal ni "lg" (992px) ni "xl" (1200px) alcanzaban para que los 7 enlaces +
    Reportes + usuario entraran en una fila sin partirse o desbordar. Con
    "xxl" (1400px) el menu hamburguesa cubre mobile, tablet y laptops
    normales, y arriba de eso hay espacio de sobra para letra a tamaño normal.
--}}
<style>
    .navbar .navbar-nav .nav-link {
        font-size: 0.95rem;
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }
</style>

<nav class="navbar navbar-expand-xxl {{ $theme['navbar'] }}">
    <div class="container-fluid px-3">
        <!-- Logo -->
        <a class="navbar-brand" href="{{ route('dashboard') }}">
            <x-application-logo style="height: 3.5rem;" />
        </a>

        <!-- Hamburger -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent"
            aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarContent">
            <!-- Navigation Links -->
            <div class="navbar-nav me-auto">

                @if (in_array(Auth::user()->role?->name, ['admin', 'receptionist']))
                <x-nav-link
                    :href="route('memberships.index')"
                    :active="request()->routeIs('memberships.*')">
                    <i class="bi bi-card-checklist me-1"></i>Membresías
                </x-nav-link>
                <x-nav-link
                    :href="route('attendance.index')"
                    :active="request()->routeIs('attendance.*')">
                    <i class="bi bi-door-open me-1"></i>Asistencia
                </x-nav-link>
                @endif

                @if (Auth::user()->role?->name === 'admin')
                <x-nav-link :href="route('membership-plans.index')" :active="request()->routeIs('membership-plans.*')">
                    <i class="bi bi-card-heading me-1"></i>Planes
                </x-nav-link>

                <x-nav-link :href="route('promotions.index')" :active="request()->routeIs('promotions.*')">
                    <i class="bi bi-tags me-1"></i>Promociones
                </x-nav-link>

                <x-nav-link
                    :href="route('foods.index')"
                    :active="request()->routeIs('foods.*')"
                >
                    <i class="bi bi-egg-fried me-1"></i>Catálogo de alimentos
                </x-nav-link>

                <x-nav-link :href="route('employees.index')" :active="request()->routeIs('employees.*')">
                    <i class="bi bi-person-workspace me-1"></i>Empleados
                </x-nav-link>

                <x-nav-link :href="route('members.index')" :active="request()->routeIs('members.*')">
                    <i class="bi bi-people me-1"></i>Miembros
                </x-nav-link>

                <x-nav-link :href="route('trainer-assignments.index')"
                    :active="request()->routeIs('trainer-assignments.*')">
                    <i class="bi bi-person-badge me-1"></i>Asignaciones de entrenadores
                </x-nav-link>

                <x-nav-link :href="route('group-classes.index')" :active="request()->routeIs('group-classes.*')">
                    <i class="bi bi-calendar-event me-1"></i>Clases grupales
                </x-nav-link>

                <div class="nav-item d-flex align-items-center" style="margin-left: 0.5rem">
                    <x-dropdown align="right" width="48" menuClass="shadow-sm">
                        <x-slot name="trigger">
                            <button class="btn btn-sm d-flex align-items-center gap-1 rounded-pill px-3 py-1 fw-semibold {{ $isReportsActive ? 'bg-white text-primary border-0 shadow-sm' : 'bg-primary text-black border border-black border-opacity-50' }}">
                                <i class="bi bi-graph-up"></i>
                                Reportes
                                <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <x-dropdown-link :href="route('reports.income.index')"
                                :active="request()->routeIs('reports.income.*')">
                                <i class="bi bi-cash-stack me-1"></i>Ingresos
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('reports.membership-expiration.index')"
                                :active="request()->routeIs('reports.membership-expiration.*')">
                                <i class="bi bi-calendar-x me-1"></i>Vencimiento de membresías
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('physical-progress.index')"
                                :active="request()->routeIs('physical-progress.*')">
                                <i class="bi bi-clipboard2-pulse me-1"></i>Progreso físico
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('group-class-reports.index')"
                                :active="request()->routeIs('group-class-reports.*')">
                                <i class="bi bi-calendar-event me-1"></i>Clases grupales
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('class-attendance-reports.index')"
                                :active="request()->routeIs('class-attendance-reports.*')">
                                <i class="bi bi-clipboard2-check me-1"></i>Asistencia por clase
                            </x-dropdown-link>
                            <x-dropdown-link :href="route('guest-pass-reports.index')"
                                :active="request()->routeIs('guest-pass-reports.*')">
                                <i class="bi bi-ticket-perforated me-1"></i>Pases de invitado
                            </x-dropdown-link>
                        </x-slot>
                    </x-dropdown>
                </div>
                @endif

                @if (in_array(Auth::user()->role?->name, ['receptionist']))
                <x-nav-link
                    :href="route('payments.index')"
                    :active="request()->routeIs('payments.*')">
                    <i class="bi bi-cash-coin me-1"></i>Pagos
                </x-nav-link>
                <x-nav-link
                    :href="route('guest-passes.index')"
                    :active="request()->routeIs('guest-passes.*')">
                    <i class="bi bi-ticket-perforated me-1"></i>Pases de invitado
                </x-nav-link>
                @endif

                @if (Auth::user()->role?->name === 'member')

                <x-nav-link :href="route('member-classes.index')" :active="request()->routeIs('member-classes.index')">
                    <i class="bi bi-calendar-check me-1"></i>Clases disponibles
                </x-nav-link>

                <x-nav-link :href="route('member-classes.history')"
                    :active="request()->routeIs('member-classes.history')">
                    <i class="bi bi-clock-history me-1"></i>Historial de clases
                </x-nav-link>

                <x-nav-link :href="route('member-training.index')" :active="request()->routeIs('member-training.*')">
                    <i class="bi bi-clipboard2-pulse me-1"></i>Mi Entrenamiento
                </x-nav-link>

                <x-nav-link :href="route('member-meals.index')" :active="request()->routeIs('member-meals.*')">
                    <i class="bi bi-basket me-1"></i>Mis comidas
                </x-nav-link>

                    <x-nav-link
                        :href="route('calorie-goals.edit')"
                        :active="request()->routeIs('calorie-goals.*')"
                    >
                        <i class="bi bi-bullseye me-1"></i>Meta calorica
                    </x-nav-link>

                    <x-nav-link
                        :href="route('nutrition-history.index')"
                        :active="request()->routeIs('nutrition-history.*')"
                    >
                        <i class="bi bi-graph-up-arrow me-1"></i>Historial nutricional
                    </x-nav-link>
                    <x-nav-link
                        :href="route('member-memberships.index')"
                        :active="request()->routeIs('member-memberships.*')"
                    >
                        <i class="bi bi-card-checklist me-1"></i>Membresías
                    </x-nav-link>

                    <x-nav-link
                        :href="route('payments.index')"
                        :active="request()->routeIs('payments.*')"
                    >
                        <i class="bi bi-cash-coin me-1"></i>Mis pagos
                    </x-nav-link>

                @endif

                @if (Auth::user()->role?->name === 'trainer')
                <x-nav-link
                    :href="route('trainer-classes.index')"
                    :active="request()->routeIs('trainer-classes.*')">
                    <i class="bi bi-calendar3 me-1"></i>Mis clases
                </x-nav-link>

                <x-nav-link :href="route('assignments.index')" :active="request()->routeIs('assignments.*')">
                    <i class="bi bi-person-lines-fill me-1"></i>Mis socios asignados
                </x-nav-link>
                @endif

            </div>

            <!-- Settings Dropdown (desktop) -->
            <div class="navbar-nav ms-auto d-none d-xxl-flex">
                <x-dropdown align="right">
                    <x-slot name="trigger">
                        <button class="{{ $theme['dropdown'] }} d-flex align-items-center">
                            <i class="bi bi-person-circle me-1"></i>
                            {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}
                            <svg class="ms-1" width="16" height="16" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            <i class="bi bi-person-circle me-1"></i>{{ __('Mi Perfil') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                <i class="bi bi-box-arrow-right me-1"></i>{{ __('Cerrar sesión') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Responsive Settings Options (mobile, dentro del collapse) -->
            <div class="d-xxl-none pt-3 mt-3 border-top {{ $theme['mobileBorder'] }}">
                <div class="px-2">
                    <div class="fw-medium {{ $theme['mobileName'] }}">{{ Auth::user()->first_name }} {{
                        Auth::user()->last_name }}</div>
                    <div class="small {{ $theme['mobileEmail'] }}">{{ Auth::user()->email }}</div>
                </div>

                <div class="navbar-nav mt-2">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        <i class="bi bi-person-circle me-1"></i>{{ __('Mi Perfil') }}
                    </x-responsive-nav-link>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                            this.closest('form').submit();">
                            <i class="bi bi-box-arrow-right me-1"></i>{{ __('Cerrar sesión') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>
