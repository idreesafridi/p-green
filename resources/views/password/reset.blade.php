@if (auth()->check())
<x-app-layout pageTitle="Reset your password">
    <div class="row">
        <div class="col-md-7 mx-auto">
            <div class="card shadow">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <x-reset-password-form authemail="{{ auth()->user() != null ? auth()->user()->email : '' }}" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
@else
<x-guest-layout pageTitle="Change your password">
    <p class="p-head mb-0">{{ __('Resetta Password') }}</p>
    <x-reset-password-form authemail="" />
</x-guest-layout>
@endif