<x-guest-layout pageTitle="Update Password">
    @section('styles')
    @endsection

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Resetta Password') }}</div>

                    <div class="card-body">
                        <x-reset-password-change-form authemail="{{ Route::current()->parameter('email') }}"
                            authtoken="{{ Route::current()->parameter('token') }}" />
                    </div>
                </div>
            </div>
        </div>
    </div>

    @section('scripts')
    @endsection
    </x-app-layout>
