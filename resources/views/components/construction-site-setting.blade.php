<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Construction Site Setting step4') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form action="{{ route('construction_setting_data_store') }}"class="row g-3 needs-validation"
                        method="post">
                        @csrf
                        <div class="col-md-3">
                            <label for="validationDefault04" class="form-label">Indirizzo dell'immobile</label>
                            <select name="type_of_property" class="form-select" id="validationDefault04">
                                <option selected disabled value="">Scegli...</option>
                                <option value="1">.1.</option>
                                <option value="2">.2.</option>
                                <option value="3">.3.</option>
                            </select>
                            @if ($errors->has('type_of_property'))
                                {{ $errors->first('type_of_property') }}
                            @endif
                        </div>

                        <div class="col-md-6 position-relative">
                            <label for="type_of_construction" class="form-label">Indirizzo dell'immobile</label>
                            <input type="text" name="type_of_construction" class="form-control"
                                id="type_of_construction">
                           @if ($errors->has('type_of_construction'))
                            {{ $errors->first('type_of_construction') }}
                            @endif
                        </div>
                        <div class="col-md-6 position-relative">
                            <label for="type_of_construction" class="form-label">tipo di detrazione</label>
                        </div>
                        <div class="form-check mb-3">
                            <input type="checkbox" name="type_of_deduction[]" value="110%" class="form-check-input"
                                id="validationFormCheck1">
                            <label class="form-check-label" for="validationFormCheck1">110%</label>
                            <div class="invalid-feedback">Esempio di testo di feedback non valido</div>
                        </div>
                        <div class="form-check mb-3">
                            <input type="checkbox" name="type_of_deduction[]" value="50%" class="form-check-input"
                                id="validationFormCheck1">
                            <label class="form-check-label" for="validationFormCheck1">50%</label>
                            <div class="invalid-feedback">Esempio di testo di feedback non valido</div>
                        </div>
                        <div class="form-check mb-3">
                            <input type="checkbox"name="type_of_deduction[]" value="65%"class="form-check-input"
                                id="validationFormCheck1">
                            <label class="form-check-label" for="validationFormCheck1">65%</label>
                            <div class="invalid-feedback">Esempio di testo di feedback non valido</div>
                        </div>
                        <div class="form-check mb-3">
                            <input type="checkbox" name="type_of_deduction[]" value="90%"class="form-check-input"
                                id="validationFormCheck1">
                            <label class="form-check-label" for="validationFormCheck1">90%</label>
                            <div class="invalid-feedback">Esempio di testo di feedback non valido</div>
                        </div>
                        <div class="form-check mb-3">
                            <input type="checkbox" name="type_of_deduction[]" class="form-check-input"
                                id="validationFormCheck1">
                            <label class="form-check-label"
                                value="photovoltaic"for="validationFormCheck1">Photovoltaic</label>
                            <div class="invalid-feedback">Esempio di testo di feedback non valido</div>
                        </div>
                        <div class="col-12">
                            <button class="btn btn-primary" type="submit">Inviare il modulo</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
