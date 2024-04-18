<x-app-layout pageTitle="Add technician details">
    @section('styles')
    @endsection

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Aggiungi dettagli tecnico') }}</div>

                    <div class="card-body">
                        @if (session()->has('success'))
                            {{ session()->get('success') }}
                        @endif

                        @if (session()->has('error'))
                            {{ session()->get('error') }}
                        @endif

                        <form action="{{ route('store_techno_details') }}" method="post">
                            @csrf

                            <input type="text" name="professional_title" id="professional_title" class="form-control mt-2"
                                placeholder="Titolo Professionale">
                            @if ($errors->has('professional_title'))
                                {{ $errors->first('professional_title') }}
                            @endif

                            <input type="text" name="professional_id" id="professional_id" class="form-control mt-2"
                                placeholder="Id Professionale">
                            @if ($errors->has('professional_id'))
                                {{ $errors->first('professional_id') }}
                            @endif

                            <input type="text" name="city" id="city" class="form-control mt-2" placeholder="Comune">
                            @if ($errors->has('city'))
                                {{ $errors->first('city') }}
                            @endif

                            <input type="text" name="birth_province" id="birth_province" class="form-control mt-2"
                                placeholder="Provincia di nascita">
                            @if ($errors->has('birth_province'))
                                {{ $errors->first('birth_province') }}
                            @endif

                            <input type="text" name="ccn" id="ccn" class="form-control mt-2" placeholder="ccn">
                            @if ($errors->has('ccn'))
                                {{ $errors->first('ccn') }}
                            @endif

                            <input type="text" name="address" id="address" class="form-control mt-2"
                                placeholder="Residenza">
                            @if ($errors->has('address'))
                                {{ $errors->first('address') }}
                            @endif

                            <input type="text" name="current_province mt-2" id="current_province" class="form-control mt-2"
                                placeholder="Provincia di residenza">
                            @if ($errors->has('current_province'))
                                {{ $errors->first('current_province') }}
                            @endif

                            <input type="submit" value="Salva" class="btn btn-sm btn-outline-primary mt-2">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @section('scripts')
    @endsection
</x-app-layout>
