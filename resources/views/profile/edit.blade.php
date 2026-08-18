<x-app-layout>
    <x-slot name="header">
        <h2 class="fw-semibold fs-4"><i class="bi bi-person-circle me-2"></i>{{ __('Perfil') }}</h2>
    </x-slot>

    <div class="py-4">
        <div class="container-xl">
            <div class="row g-3">

                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            @include('profile.partials.profile-summary')
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="d-flex flex-column gap-3">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                                @include('profile.partials.update-profile-information-form')
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                                @include('profile.partials.update-password-form')
                            </div>
                        </div>

                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                                @include('profile.partials.delete-user-form')
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
