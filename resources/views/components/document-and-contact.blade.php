<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Document and Contacts step2') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <form action="{{ route('document_store') }}"class="row g-3 needs-validation" method="post">
                        @csrf
                        <div class="col-md-6 position-relative">
                            <label for="document_number" class="form-label">Numero Documento</label>
                            <input type="text" name="document_number" class="form-control" id="document_number"
                                placeholder="AF1236548">
                            @if ($errors->has('document_number'))
                                {{ $errors->first('document_number') }}
                            @endif
                        </div>
                        <div class="col-md-3 position-relative">
                            <label for="issued_by" class="form-label">Rilasciato da</label>
                            <input type="text" name="issued_by" class="form-control" id="issued_by"
                                placeholder="Town of Castellana">
                            @if ($errors->has('issued_by'))
                                {{ $errors->first('issued_by') }}
                            @endif
                        </div>
                        <div class="col-md-3 position-relative">
                            <label for="release_date" class="form-label">Data di rilascio</label>
                            <input type="date" name="release_date" class="form-control" id="release_date">
                            @if ($errors->has('release_date'))
                                {{ $errors->first('release_date') }}
                            @endif
                        </div>
                        {{--  --}}
                        <div class="col-md-6 position-relative">
                            <label for="expiration_date" class="form-label">Data di scadenza</label>
                            <input type="date" name="expiration_date" class="form-control" id="expiration_date">

                            @if ($errors->has('expiration_date'))
                                {{ $errors->first('expiration_date') }}
                            @endif
                        </div>
                        <div class="col-md-3 position-relative">
                            <label for="fiscal_document_number" class="form-label">Codice Fiscale code</label>
                            <input type="text" name="fiscal_document_number" class="form-control"
                                id="fiscal_document_number" placeholder="Document number">
                            @if ($errors->has('fiscal_document_number'))
                                {{ $errors->first('fiscal_document_number') }}
                            @endif
                        </div>
                        <div class="col-md-3 position-relative">
                            <label for="vat_number" class="form-label">Partita IVA</label>
                            <input type="number" name="vat_number" class="form-control" id="vat_number"
                                placeholder="863345197557">
                            @if ($errors->has('vat_number'))
                                {{ $errors->first('vat_number') }}
                            @endif
                        </div>
                        {{-- 3rd line --}}
                        <div class="col-md-6 position-relative">
                            <label for="contact_email" class="form-label">Contatto</label>
                            <input type="email" name="contact_email" class="form-control" id="contact_email"
                                placeholder="Email address">
                            @if ($errors->has('contact_email'))
                                {{ $errors->first('contact_email') }}
                            @endif
                        </div>
                        <div class="col-md-3 position-relative">
                            <label for="contact_number" class="form-label">contact number</label>
                            <input type="number" name="contact_number" class="form-control" id="contact_number"
                                placeholder="Phone number">
                            @if ($errors->has('contact_number'))
                                {{ $errors->first('contact_number') }}
                            @endif
                        </div>
                        <div class="col-md-3 position-relative">
                            <label for="alt_refrence_name" class="form-label">alt refrence name</label>
                            <input type="text" name="alt_refrence_name" class="form-control" id="alt_refrence_name">
                            @if ($errors->has('alt_refrence_name'))
                                {{ $errors->first('alt_refrence_name') }}
                            @endif
                        </div>
                        <div class="col-md-3 position-relative">
                            <label for="alt_contact_number" class="form-label">alt contact number</label>
                            <input type="number" name="alt_contact_number" class="form-control" id="alt_contact_number"
                                placeholder="Phone number">
                            @if ($errors->has('alt_contact_number'))
                                {{ $errors->first('alt_contact_number') }}
                            @endif
                        </div>
                        <div class="col-12">
                            <button class="btn btn-primary" type="submit">Submit form</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
