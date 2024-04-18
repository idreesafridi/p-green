<div class="row g-3 my-3">
    @if (session('status'))
        <div class="alert alert-success" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <form action="{{ route('construction_store') }}"class="row g-3 needs-validation" method="post">
        @csrf
        <div class="col-md-6 position-relative">
            <label for="name" class="form-label">Nome</label>
            <input type="text" name="name"
                value="{{ $constructionData != null ? $constructionData->name : '' }}"class="form-control"
                id="name">
            @if ($errors->has('name'))
                <span class="text-danger">{{ $errors->first('name') }}</span>
            @endif
        </div>
        <div class="col-md-3 position-relative">
            <label for="surename" class="form-label">Cognome</label>
            <input type="text" name="surename"
                value="{{ $constructionData != null ? $constructionData->surename : '' }}" class="form-control"
                id="surename">
            @if ($errors->has('surename'))
                <span class="text-danger">{{ $errors->first('surename') }}</span>
            @endif
        </div>

        <div class="col-md-6 position-relative">
            <label for="date_of_birth" class="form-label">Data di nascita</label>
            <input type="date" name="date_of_birth"
                value="{{ $constructionData != null ? $constructionData->date_of_birth : '' }}" class="form-control"
                id="date_of_birth">
            @if ($errors->has('date_of_birth'))
                <span class="text-danger">{{ $errors->first('date_of_birth') }}</span>
            @endif
        </div>
        <div class="col-md-3 position-relative">
            <label for="town_of_birth" class="form-label">Comune di Nascita</label>
            <input type="text" name="town_of_birth"
                value="{{ $constructionData != null ? $constructionData->town_of_birth : '' }}" class="form-control"
                id="town_of_birth">
            @if ($errors->has('town_of_birth'))
                <span class="text-danger">{{ $errors->first('town_of_birth') }}</span>
            @endif
        </div>
        <div class="col-md-3 position-relative">
            <label for="province" class="form-label">Provincia</label>
            <input type="text" name="province"
                value="{{ $constructionData != null ? $constructionData->province : '' }}" class="form-control"
                id="province">
            @if ($errors->has('province'))
                <span class="text-danger">{{ $errors->first('province') }}</span>
            @endif
        </div>
        {{-- 3rd line --}}
        <div class="col-md-6 position-relative">
            <label for="residence_address" class="form-label">Indirizzo di residenza</label>
            <input type="text" name="residence_address"
                value="{{ $constructionData != null ? $constructionData->residence_address : '' }}"
                class="form-control" id="residence_address">
            @if ($errors->has('residence_address'))
                <span class="text-danger">{{ $errors->first('residence_address') }}</span>
            @endif
        </div>
        <div class="col-md-6 position-relative">
            <label for="residence_street" class="form-label">residence street</label>
            <input type="text" name="residence_street"
                value="{{ $constructionData != null ? $constructionData->residence_street : '' }}" class="form-control"
                id="residence_street">
            @if ($errors->has('residence_street'))
                <span class="text-danger">{{ $errors->first('residence_street') }}</span>
            @endif
        </div>
        <div class="col-md-3 position-relative">
            <label for="residence_zip" class="form-label">residence zip</label>
            <input type="text" name="residence_zip" class="form-control"
                value="{{ $constructionData != null ? $constructionData->residence_zip : '' }}" id="residence_zip">
            @if ($errors->has('residence_zip'))
                <span class="text-danger">{{ $errors->first('residence_zip') }}</span>
            @endif
        </div>
        <div class="col-md-3 position-relative">
            <label for="residence_common" class="form-label">residence common</label>
            <input type="text" name="residence_common" class="form-control"
                value="{{ $constructionData != null ? $constructionData->residence_common : '' }}"
                id="residence_common">
            @if ($errors->has('residence_common'))
                <span class="text-danger">{{ $errors->first('residence_common') }}</span>
            @endif
        </div>
        <div class="col-md-3 position-relative">
            <label for="residence_province" class="form-label">residence province</label>
            <input type="text" name="residence_province"
                value="{{ $constructionData != null ? $constructionData->residence_province : '' }}"class="form-control"
                id="residence_province">
            @if ($errors->has('residence_province'))
                <span class="text-danger">{{ $errors->first('residence_province') }}</span>
            @endif
        </div>
        <div class="col-12">
            <button class="btn btn-sm btn-outline-primary" type="submit">Inviare il modulo</button>
        </div>
    </form>
</div>
