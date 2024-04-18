@if (auth()->check())

    <x-app-layout pageTitle="Chnage your password">
        <div class="row">
            <div class="col-md-7 mx-auto">
                <div class="card shadow">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <x-reset-password-change-form authemail="{{ auth()->user() != null ? auth()->user()->email : '' }}"
                                    authtoken="{{ Route::current()->parameter('token') }}" />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </x-app-layout>
@else
    <x-guest-layout pageTitle="Cambia la tua password">

        <p class="p-head mb-0">{{ __('Resetta Password') }}</p>
        <x-reset-password-change-form authemail="{{ Route::current()->parameter('email') }}" authtoken="{{ Route::current()->parameter('token') }}" />
    </x-guest-layout>
@endif


