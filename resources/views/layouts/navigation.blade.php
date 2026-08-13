@php
    $roleName = Auth::user()->role?->name;

    $navbarThemes = [
        'admin' => [
            'navbar' => 'navbar-dark bg-primary border-primary',
            'dropdown' => 'badge text-bg-light',
            'mobileName' => 'text-white',
            'mobileEmail' => 'text-white-50',
            'mobileBorder' => 'border-light',
        ],
        'receptionist' => [
            'navbar' => 'navbar-dark bg-success border-success',
            'dropdown' => 'badge text-bg-light',
            'mobileName' => 'text-white',
            'mobileEmail' => 'text-white-50',
            'mobileBorder' => 'border-light',
        ],
        'trainer' => [
            'navbar' => 'navbar-dark bg-info border-info',
            'dropdown' => 'badge text-bg-light',
            'mobileName' => 'text-white',
            'mobileEmail' => 'text-white-50',
            'mobileBorder' => 'border-light',
        ],
        'member' => [
            'navbar' => 'navbar-dark bg-warning border-warning',
            'dropdown' => 'badge text-bg-light',
            'mobileName' => 'text-white',
            'mobileEmail' => 'text-white-50',
            'mobileBorder' => 'border-light',
        ],
    ];

    $theme = $navbarThemes[$roleName] ?? [
        'navbar' => 'navbar-light bg-white border-bottom',
        'dropdown' => 'btn btn-light',
        'mobileName' => 'text-dark',
        'mobileEmail' => 'text-muted',
        'mobileBorder' => 'border-bottom',
    ];
@endphp

<nav class="navbar navbar-expand-sm {{ $theme['navbar'] }}">
    <div class="container-fluid px-4">
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

                <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    Dashboard
                </x-nav-link>

                @if (Auth::user()->role?->name === 'admin')
                <x-nav-link :href="route('foods.index')" :active="request()->routeIs('foods.*')">
                    Catalogo de Alimentos
                </x-nav-link>

                <x-nav-link :href="route('employees.index')" :active="request()->routeIs('employees.*')">
                    Empleados
                </x-nav-link>

                <x-nav-link :href="route('members.index')" :active="request()->routeIs('members.*')">
                    Miembros
                </x-nav-link>

                <x-nav-link :href="route('trainer-assignments.index')" :active="request()->routeIs('trainer-assignments.*')">
                    Asignaciones de entrenadores
                </x-nav-link>

                @endif

                @if (in_array(Auth::user()->role?->name, ['admin', 'receptionist']))
                <x-nav-link :href="route('guest-passes.index')" :active="request()->routeIs('guest-passes.*')">
                    Pases de invitado
                </x-nav-link>
                @endif

                @if (Auth::user()->role?->name === 'admin')
                <x-nav-link :href="route('group-classes.index')" :active="request()->routeIs('group-classes.*')">
                    Clases grupales
                </x-nav-link>

                <x-nav-link :href="route('group-class-reports.index')"
                    :active="request()->routeIs('group-class-reports.*')">
                    Reportes de clases
                </x-nav-link>
                @endif


                @if (Auth::user()->role?->name === 'member')

                <x-nav-link :href="route('member-classes.index')" :active="request()->routeIs('member-classes.index')">
                    Clases disponibles
                </x-nav-link>

                <x-nav-link :href="route('member-classes.history')"
                    :active="request()->routeIs('member-classes.history')">
                    Historial de clases
                </x-nav-link>

                    <x-nav-link
                        :href="route('member-meals.index')"
                        :active="request()->routeIs('member-meals.*')"
                    >
                        Mis comidas
                    </x-nav-link>

                @endif

                @if (Auth::user()->role?->name === 'trainer')
                <x-nav-link :href="route('trainer-classes.index')" :active="request()->routeIs('trainer-classes.*')">
                    Mis clases
                </x-nav-link>

                <x-nav-link :href="route('assignments.index')" :active="request()->routeIs('assignments.*')">
                    Mis socios asignados
                </x-nav-link>
                @endif

            </div>

            <!-- Settings Dropdown (desktop) -->
            <div class="navbar-nav ms-auto d-none d-sm-flex">
                <x-dropdown align="right">
                    <x-slot name="trigger">
                        <button class="{{ $theme['dropdown'] }} d-flex align-items-center">
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
                            {{ __('Mi Perfil') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Cerrar sesión') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Responsive Settings Options (mobile, dentro del collapse) -->
            <div class="d-sm-none pt-3 mt-3 border-top {{ $theme['mobileBorder'] }}">
                <div class="px-2">
                    <div class="fw-medium {{ $theme['mobileName'] }}">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</div>
                    <div class="small {{ $theme['mobileEmail'] }}">{{ Auth::user()->email }}</div>
                </div>

                <div class="navbar-nav mt-2">
                    <x-responsive-nav-link :href="route('profile.edit')">
                        {{ __('Profile') }}
                    </x-responsive-nav-link>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <x-responsive-nav-link :href="route('logout')" onclick="event.preventDefault();
                                            this.closest('form').submit();">
                            {{ __('Log Out') }}
                        </x-responsive-nav-link>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>
