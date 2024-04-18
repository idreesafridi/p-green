@if (session('status'))
<div class="alert alert-success" role="alert">

    {{ session('status') }}

</div>
@endif

<form method="POST" action="{{ route('password.email') }}" class="reset-form">
    @csrf

    <div class="row ">
        <div class="col-md-12">
            <label for="email" class="col-12 col-form-label ">{{ __('Indirizzo Email') }}</label>

            <div class="col-12 ">
                <input id="email " type="email" {{ $authemail != null ? 'readonly' : '' }} class="reset-input form-control @error('email') is-invalid @enderror" name="email" value="{{ $authemail }}" required autocomplete="email" autofocus>

                @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>
    </div>
    <br>

    <div class="row mb-0 ">
        <div class="col-12 d-flex justify-content-center">
            <button type="submit" class="btn btn-primary rounded reset-button">
                {{ __('Invia link per reset password') }}
            </button>
        </div>
    </div>
    <br>
</form>