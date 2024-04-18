<x-guest-layout pageTitle="Reset Password">
    @section('styles')
    @endsection

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Reset Password') }}</div>

                    <div class="card-body">
                        <x-reset-password-form />
                    </div>
                </div>
            </div>
        </div>
    </div>

    @section('scripts')
    @endsection
    </x-app-layout>
