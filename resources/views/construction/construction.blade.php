<x-app-layout pageTitle="Construction">
    @section('styles')
    <style>
        .select2-container--default .select2-selection--single {
            border: none;
        }
    .select2-container .select2-selection--single .select2-selection__rendered {
        border: 1px solid #7eb93f !important;
        display: block;
        width: 100%;
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        font-weight: 400;
        line-height: 1.5;
        color: #212529;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid #ced4da;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        border-radius: 0.375rem;
        transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        top: 5px;
    }
    </style>
    @endsection
    <div class="card shadow">
        <div class="card-header">
            @if ($data == null)
                <h3>Nuovo Cantiere</h3>
            @else
                <h3>Nuovo Condomino di {{ $data->name }} {{ $data->surename }}
                </h3>
            @endif
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="site-registration"> 
                        <form action="{{ route('construction_store') }}" method="POST" onsubmit="return handleSubmit(this);">
                            @csrf
                            <input type="hidden" name="fk_id" value="{{ $data == null ? '' : $data->id }}">
                            <div class="step-app" id="stepsWizard">
                                <ul class="nav nav-tabs" role="tablist">
                                    <li class="nav-link active border-0 list-item-1" data-bs-toggle="tab"
                                        data-bs-target="#step1">
                                        <span>1</span> 1
                                        Anagrafica Cliente
                                    </li>
                                    <li class="nav-link border-0 list-item-2" data-bs-toggle="tab"
                                        data-bs-target="#step2">
                                        <span>2</span> Documenti e Contatti
                                    </li>
                                    <li class="nav-link border-0 list-item-3" data-bs-toggle="tab"
                                        data-bs-target="#step3">
                                        <span>3</span> Dati Immobile
                                    </li>
                                    <li class="nav-link border-0 list-item-4" data-bs-toggle="tab"
                                        data-bs-target="#step4">
                                        <span>4</span> Impostazioni Cantiere
                                    </li>
                                </ul>
                                <div class="tab-content site-registration">

                                    <!-- Start Step 1 -->
                                    <div class="tab-pane fade show active" id="step1">
                                        <div class="row g-3 my-3">
                                            <p class="mb-0"><strong>Dati anagrafici</strong></p>
                                            <div class="col-md-4 col-12">
                                                <label for="name" class="form-label">Nome</label>
                                                <input type="text" name="name" class="form-control"
                                                    id="name">
                                                @if ($errors->has('name'))
                                                    <span class="text-danger">{{ $errors->first('name') }}</span>
                                                @endif
                                            </div>
                                            <div class="col-md-4 col-12">
                                                <label for="surename" class="form-label">Cognome</label>
                                                <input type="text" name="surename" class="form-control"
                                                    id="surename">
                                                @if ($errors->has('surename'))
                                                    <span class="text-danger">{{ $errors->first('surename') }}</span>
                                                @endif
                                            </div>
                                            <div class="col-md-4 col-12">
                                                <label for="date_of_birth" class="form-label">Data di nascita</label>
                                                <input type="date" name="date_of_birth" class="form-control"
                                                    id="date_of_birth">
                                                @if ($errors->has('date_of_birth'))
                                                    <span
                                                        class="text-danger">{{ $errors->first('date_of_birth') }}</span>
                                                @endif
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <label for="town_of_birth" class="form-label">Comune di Nascita</label>
                                                <input type="text" name="town_of_birth" class="form-control"
                                                    id="town_of_birth">
                                                @if ($errors->has('town_of_birth'))
                                                    <span
                                                        class="text-danger">{{ $errors->first('town_of_birth') }}</span>
                                                @endif
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <label for="province" class="form-label">Provincia</label>
                                                <input type="text" name="province" class="form-control"
                                                    id="province">
                                                @if ($errors->has('province'))
                                                    <span class="text-danger">{{ $errors->first('province') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="row g-3 my-3">
                                            <p class="mb-0"><strong>Indirizzo di residenza</strong></p>
                                            <div class="col-md-4 col-12">
                                                <label for="residence_street" class="form-label">via (strada)</label>
                                                <input type="text" name="residence_street" class="form-control"
                                                    id="residence_street">
                                                @if ($errors->has('residence_street'))
                                                    <span
                                                        class="text-danger">{{ $errors->first('residence_street') }}</span>
                                                @endif
                                            </div>
                                            <div class="col-md-4 col-12">
                                                <label for="residence_house_number" class="form-label">Numero civico
                                                    (house number)
                                                </label>
                                                <input type="text" name="residence_house_number" class="form-control"
                                                    id="residence_house_number">
                                                @if ($errors->has('residence_house_number'))
                                                    <span
                                                        class="text-danger">{{ $errors->first('residence_house_number') }}</span>
                                                @endif
                                            </div>
                                            {{-- <div class="col-md-4 col-12">
                                                <label for="residence_postal_code" class="form-label">CAP (codice
                                                    postale)</label>
                                                <input type="text" name="residence_postal_code" class="form-control"
                                                    id="residence_postal_code">
                                                @if ($errors->has('residence_postal_code'))
                                                    <span
                                                        class="text-danger">{{ $errors->first('residence_postal_code') }}</span>
                                                @endif
                                            </div> --}}
                                            <div class="col-md-4 col-12">
                                                <label for="residence_common" class="form-label">Comune
                                                    (Common)</label>
                                                <input type="text" name="residence_common" class="form-control"
                                                    id="residence_common">
                                                @if ($errors->has('residence_common'))
                                                    <span
                                                        class="text-danger">{{ $errors->first('residence_common') }}</span>
                                                @endif
                                            </div>
                                            <div class="col-md-4 col-12">
                                                <label for="residence_province" class="form-label">Provincia
                                                    (Provence)</label>
                                                <input type="text" name="residence_province" class="form-control"
                                                    id="residence_province">
                                                @if ($errors->has('residence_province'))
                                                    <span
                                                        class="text-danger">{{ $errors->first('residence_province') }}</span>
                                                @endif
                                            </div>
                                            <div class="col-md-4 col-12">
                                                <label for="residence_postal_code" class="form-label">CAP (Postal Code)</label>
                                                <input type="text" name="residence_postal_code" class="form-control"
                                                    id="residence_postal_code">
                                                @if ($errors->has('residence_postal_code'))
                                                    <span
                                                        class="text-danger">{{ $errors->first('residence_postal_code') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Step 1 -->

                                    <!-- Start Step 2 -->
                                    <div class="tab-pane fade" id="step2">
                                        <div class="row my-3 g-3">
                                            <p class="mb-0"><strong>Carta d'indentità e D.F.</strong></p>
                                            <div class="col-md-6 col-12">
                                                <label for="document_number" class="form-label">Numero
                                                    Documento</label>
                                                <input type="text" name="document_number" class="form-control"
                                                    id="document_number" placeholder="AF1236548">
                                                @if ($errors->has('document_number'))
                                                    {{ $errors->first('document_number') }}
                                                @endif
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <label for="issued_by" class="form-label">Rilasciato da</label>
                                                <input type="text" name="issued_by" class="form-control"
                                                    id="issued_by" placeholder="Town of Castellana">
                                                @if ($errors->has('issued_by'))
                                                    {{ $errors->first('issued_by') }}
                                                @endif
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <label for="release_date" class="form-label">Data di rilascio</label>
                                                <input type="date" name="release_date" class="form-control"
                                                    id="release_date">
                                                @if ($errors->has('release_date'))
                                                    {{ $errors->first('release_date') }}
                                                @endif
                                            </div>
                                            <div class="col-md-6 col-12">
                                                <label for="expiration_date" class="form-label">Data di
                                                    scadenza</label>
                                                <input type="date" name="expiration_date" class="form-control"
                                                    id="expiration_date">

                                                @if ($errors->has('expiration_date'))
                                                    {{ $errors->first('expiration_date') }}
                                                @endif
                                            </div>
                                            <div class="col-md-4 col-12">
                                                <label for="fiscal_document_number" class="form-label">Codice Fiscale
                                                    code</label>
                                                <input type="text" name="fiscal_document_number"
                                                    class="form-control" id="fiscal_document_number"
                                                    placeholder="Numero documento">
                                                @if ($errors->has('fiscal_document_number'))
                                                    {{ $errors->first('fiscal_document_number') }}
                                                @endif
                                            </div>
                                            <div class="col-md-4 col-12">
                                                <label for="vat_number" class="form-label">
                                                    Partita IVA</label>
                                                <input type="number" name="vat_number" class="form-control"
                                                    id="vat_number" placeholder="863345197557">
                                                @if ($errors->has('vat_number'))
                                                    {{ $errors->first('vat_number') }}
                                                @endif
                                            </div>
                                        </div>
                                        <div class="row my-3 g-3">
                                            <label class="col-form-label"><strong>Contatto</strong></label>
                                            <div class="col-md-4 col-12">
                                                <label for="contact_email" class="form-label">contatto email</label>
                                                <input type="email" name="contact_email" class="form-control"
                                                    id="contact_email" placeholder="Indirizzo email">
                                                @if ($errors->has('contact_email'))
                                                    {{ $errors->first('contact_email') }}
                                                @endif
                                            </div>
                                            <div class="col-md-4 col-12">
                                                <label for="contact_number" class="form-label">numero di
                                                    contatto</label>
                                                <input type="tel" name="contact_number"
                                                    class="form-control phone" id="contact_number"
                                                    placeholder="Numero di telefono">
                                                @if ($errors->has('contact_number'))
                                                    {{ $errors->first('contact_number') }}
                                                @endif
                                            </div>
                                        </div>
                                        <div class="row my-3 g-3">
                                            <label class="col-form-label"><strong>Contatto Alternativo</strong></label>
                                            <div class="col-md-4 col-12">
                                                <label for="alt_refrence_name" class="form-label">Nome di riferimento
                                                    alternativo</label>
                                                <input type="text" name="alt_refrence_name" class="form-control"
                                                    id="alt_refrence_name" placeholder="numero di contatto">
                                                @if ($errors->has('alt_refrence_name'))
                                                    {{ $errors->first('alt_refrence_name') }}
                                                @endif
                                            </div>
                                            <div class="col-md-4 col-12">
                                                <label for="alt_contact_number" class="form-label">numero di
                                                    contatto</label>
                                                <input type="tel" name="alt_contact_number"
                                                    class="form-control phone1" id="alt_contact_number"
                                                    placeholder="Phone number">
                                                @if ($errors->has('alt_contact_number'))
                                                    {{ $errors->first('alt_contact_number') }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Step 2 -->

                                    <!-- Start Step 3 -->
                                    <div class="tab-pane fade" id="step3">
                                        <div class="row g-3 my-3">
                                            <label class="col-form-label">
                                                <strong>Indirizzo Immobile</strong>
                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                                <input type="checkbox" id="Copy_data"
                                                    class="form-check-input btn-sm">
                                                &nbsp;Copia dati da "Anagrafica Cliente"
                                            </label>
                                            <div class="col-md-4 col-12">
                                                <label for="property_street" class="form-label">
                                                    Via</label>
                                                <input type="text" name="property_street"
                                                    value="{{ $data == null ? '' : $data->PropertyData->property_street }}"
                                                    class="form-control" id="property_street">
                                                @if ($errors->has('property_street'))
                                                    {{ $errors->first('property_street') }}
                                                @endif
                                            </div>
                                            <div class="col-md-4 col-12">
                                                <label for="property_house_number" class="form-label">Numero
                                                    civico</label>
                                                <input type="text" name="property_house_number"
                                                    class="form-control"
                                                    value="{{ $data == null ? '' : $data->PropertyData->property_house_number }}"
                                                    id="property_house_number">
                                                @if ($errors->has('property_house_number'))
                                                    {{ $errors->first('property_house_number') }}
                                                @endif
                                            </div>
                                            <div class="col-md-4 col-12">
                                                <label for="property_postal_code" class="form-label">CAP</label>
                                                <input type="text" name="property_postal_code"
                                                    class="form-control"
                                                    value="{{ $data == null ? '' : $data->PropertyData->property_postal_code }}"
                                                    id="property_postal_code">
                                                @if ($errors->has('property_postal_code'))
                                                    {{ $errors->first('property_postal_code') }}
                                                @endif
                                            </div>
                                            <div class="col-md-4 col-12">
                                                <label for="property_common" class="form-label">Comune</label>
                                                <input type="text" name="property_common"
                                                    value="{{ $data == null ? '' : $data->PropertyData->property_common }}"
                                                    class="form-control" id="property_common">

                                                @if ($errors->has('property_common'))
                                                    {{ $errors->first('property_common') }}
                                                @endif
                                            </div>
                                            <div class="col-md-4 col-12">
                                                <label for="property_province" class="form-label">Provincia</label>
                                                <input type="text" name="property_province"
                                                    value="{{ $data == null ? '' : $data->PropertyData->property_province }}"
                                                    class="form-control" id="property_province">
                                                @if ($errors->has('property_province'))
                                                    {{ $errors->first('property_province') }}
                                                @endif
                                            </div>
                                        </div>
                                        <div class="row g-3 my-3">
                                            <label class="col-form-label"><strong>Dati Catastali</strong></label>
                                            <div class="col-md-4 col-12">
                                                <label for="cadastral_dati" class="form-label">Sezione
                                                    catastale</label>
                                                <input type="text" name="cadastral_dati"
                                                    value="{{ $data == null ? '' : $data->PropertyData->cadastral_dati }}"
                                                    class="form-control" id="cadastral_dati">
                                                @if ($errors->has('cadastral_dati'))
                                                    {{ $errors->first('cadastral_dati') }}
                                                @endif
                                            </div>
                                            <div class="col-md-4 col-12">
                                                <label for="cadastral_section" class="form-label">Foglio</label>
                                                <input type="text" name="cadastral_section"
                                                    value="{{ $data == null ? '' : $data->PropertyData->cadastral_section }}"
                                                    class="form-control" id="cadastral_section">
                                                @if ($errors->has('cadastral_section'))
                                                    {{ $errors->first('cadastral_section') }}
                                                @endif
                                            </div>
                                            <div class="col-md-4 col-12">
                                                <label for="cadastral_particle" class="form-label">
                                                    Particella catastale</label>
                                                <input type="text" name="cadastral_particle"
                                                    value="{{ $data == null ? '' : $data->PropertyData->cadastral_particle }}"
                                                    class="form-control" id="cadastral_particle">
                                                @if ($errors->has('cadastral_particle'))
                                                    {{ $errors->first('cadastral_particle') }}
                                                @endif
                                            </div>
                                            <div class="col-md-4 col-12">
                                                <label for="sub_ordinate" class="form-label">Subalterni</label>
                                                <input type="text" name="sub_ordinate"
                                                    value="{{ $data == null ? '' : $data->PropertyData->sub_ordinate }}"
                                                    class="form-control" id="sub_ordinate">
                                                @if ($errors->has('sub_ordinate'))
                                                    {{ $errors->first('sub_ordinate') }}
                                                @endif
                                            </div>
                                            <div class="col-md-4 col-12">
                                                <label for="sub_ordinate" class="form-label">Piano</label>

                                                <select name="Piano[]" class="form-control bg-white select2" multiple>
                                                    <option value="" selected disabled>Seleziona</option>
                                                    <option value="S1" {{ $data != null && $data->PropertyData->Piano == 'S1' ? 'selected' : '' }}>S1</option>
                                                    <option value="T" {{ $data != null && $data->PropertyData->Piano == 'T' ? 'selected' : '' }}>T</option>
                                                    <option value="R" {{ $data != null && $data->PropertyData->Piano == 'R' ? 'selected' : '' }}>R</option>
                                                    <option value="P1" {{ $data != null && $data->PropertyData->Piano == 'P1' ? 'selected' : '' }}>P1</option>
                                                    <option value="P2" {{ $data != null && $data->PropertyData->Piano == 'P2' ? 'selected' : '' }}>P2</option>
                                                    <option value="P3" {{ $data != null && $data->PropertyData->Piano == 'P3' ? 'selected' : '' }}>P3</option>
                                                    <option value="P4" {{ $data != null && $data->PropertyData->Piano == 'P4' ? 'selected' : '' }}>P4</option>
                                                   
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row g-3 my-3">
                                            <label class="col-form-label"><strong>Codice POD</strong></label>
                                            <div class="col-md-4 col-12">
                                                <label for="pod_code" class="form-label">Codice POD</label>
                                                <input type="text" name="pod_code"
                                                    value="{{ $data == null ? '' : $data->PropertyData->pod_code }}"
                                                    class="form-control" id="pod_code">
                                                @if ($errors->has('pod_code'))
                                                    {{ $errors->first('pod_code') }}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Step 3 -->

                                    <!-- Start Step 4 -->
                                    <div class="tab-pane fade" id="step4">
                                        <div class="col-md-4 col-12 my-3">
                                            <label for="type_of_property" class="form-label">Tipologia di
                                                immobile</label>
                                            <select name="type_of_property" class="form-select"
                                                id="type_of_property">
                                                <option selected="">Seleziona</option>
                                                <option value="Condominio">Condominio</option>
                                                <option value="Unifamiliare">Unifamiliare</option>
                                                <option value="Plurifamiliare">Plurifamiliare</option>
                                            </select>
                                            @if ($errors->has('type_of_property'))
                                                {{ $errors->first('type_of_property') }}
                                            @endif
                                        </div>

                                        <div class="col-md-4 col-12 my-3">
                                            <label class="col-form-label">Tipologia di cantiere</label>
                                            <div class="p-1 w-75">
                                                <input type="radio" class="btn-check" name="type_of_construction"
                                                    value="0" id="Internal" checked>
                                                <label class="btn btn-outline-primary" for="Internal">Interno</label>
                                                <input type="radio" class="btn-check" name="type_of_construction"
                                                    value="1" id="External">
                                                <label class="btn btn-outline-primary" for="External">Esterno</label>
                                            </div>
                                        </div>
                                        <div class="col-12 my-3">
                                            <label class="col-form-label">Tipologia di detrazione</label>
                                            <div class="input-group">
                                                <div class="input-group-text">
                                                    <input name="type_of_deduction[]" class="form-check-input mt-0"
                                                        type="checkbox" value="110"> &nbsp;110%
                                                </div>
                                                &nbsp;
                                                <div class="input-group-text">
                                                    <input name="type_of_deduction[]" class="form-check-input mt-0"
                                                        type="checkbox" value="50">&nbsp;50%
                                                </div>
                                                &nbsp;
                                                <div class="input-group-text">
                                                    <input name="type_of_deduction[]" class="form-check-input mt-0"
                                                        type="checkbox" value="65">&nbsp;65%
                                                </div>
                                                &nbsp;
                                                <div class="input-group-text">
                                                    <input name="type_of_deduction[]" class="form-check-input mt-0"
                                                        type="checkbox" value="90">&nbsp;90%
                                                </div>
                                                &nbsp;
                                                <div class="input-group-text">
                                                    <input name="type_of_deduction[]" class="form-check-input mt-0"
                                                        type="checkbox" value="Fotovoltaico">&nbsp;Fotovoltaico
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- End Step 4 -->

                                    <hr>
                                    <div class="tab-footer text-end">

                                        <button type="button" class="step-btn previous">Indietro</button>
                                        <button type="button" class="step-btn next">Avanti</button>
                                        <button type="submit" name="" class="step-btn save" id ="submitButton">Salva e
                                            Chiudi</button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        {{-- <x-construction-site :constructionData="$data" /> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    @section('scripts')
        <script>
            
            function handleSubmit(form) {
            var submitButton = document.getElementById('submitButton');
            if (submitButton) {
            
                submitButton.disabled = true;
            }

            // Make sure the form is being submitted
            return true;
            }



            // document.getElementById('submitButton').addEventListener('click', function() {
            //         this.disabled = true;
            //     });
            // function geocodeUserInput() {
            //     var userInput = document.getElementById('userInput').value;

            //     var geocoder = new google.maps.Geocoder();

            //     geocoder.geocode({ address: userInput }, function(results, status) {
            //         if (status === 'OK') {
            //         if (results[0]) {
            //             // Extract the town or city name from the first result
            //             var addressComponents = results[0].address_components;
            //             var townName = extractTownName(addressComponents);

            //             // Use the town name as desired (e.g., display it on the page)
            //             document.getElementById('townName').innerHTML = townName;
            //         }
            //         } else {
            //         console.log('Geocode was not successful for the following reason:', status);
            //         }
            //     });
            // }

            // Extract the town or city name from the address components
            // function extractTownName(addressComponents) {
            //     for (var i = 0; i < addressComponents.length; i++) {
            //         var types = addressComponents[i].types;
            //         if (types.includes('locality') || types.includes('administrative_area_level_3')) {
            //         return addressComponents[i].long_name;
            //         }
            //     }
            //     return null;
            // }

            $(document).ready(function() {

                // var location = new google.maps.places.Autocomplete((document.getElementById('town_of_birth')), {
                //     types: ['locality'],
                //     componentRestrictions: {
                //         country: 'pak'
                //     }
                // })

                $('.previous').addClass('d-none');
                $('.save').addClass('d-none');

                $('.list-item-1').click(function(val) {
                    $('.list-item-1').removeClass('completed');
                    $('.list-item-2').removeClass('completed');
                    $('.list-item-3').removeClass('completed');
                    $('.previous').addClass('d-none');
                    $('.save').addClass('d-none');
                });
                $('.list-item-2').click(function(val) {
                    $('.list-item-1').addClass('completed');
                    $('.list-item-2').removeClass('completed');
                    $('.list-item-3').removeClass('completed');
                    $('.previous').removeClass('d-none');
                    $('.save').addClass('d-none');
                });
                $('.list-item-3').click(function(val) {
                    $('.list-item-1').addClass('completed');
                    $('.list-item-2').addClass('completed');
                    $('.list-item-3').removeClass('completed');
                    $('.previous').removeClass('d-none');
                    $('.save').addClass('d-none');
                });
                $('.list-item-4').click(function(val) {
                    $('.list-item-1').addClass('completed');
                    $('.list-item-2').addClass('completed');
                    $('.list-item-3').addClass('completed');
                    $('.save').removeClass('d-none');
                });

                $('.next').click(function() {
                    $('.nav-tabs > .active').next('li').trigger('click');
                });

                $('.previous').click(function() {
                    $('.nav-tabs > .active').prev('li').trigger('click');
                });

                const phoneInputField = document.querySelector("#alt_contact_number");
                const phoneInput = window.intlTelInput(phoneInputField, {
                    utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
                    separateDialCode: true,
                    initialCountry: "IT",
                });
                $('#alt_contact_number').inputmask('999 999 99 99', {
                    placeholder: ""
                });
            });

            $("#Copy_data").click(function() {
                if (this.checked) {
                    $("#property_street").val($("#residence_street").val());
                    $("#property_house_number").val($("#residence_house_number").val());
                    $("#property_postal_code").val($("#residence_postal_code").val());
                    $("#property_common").val($("#residence_common").val());
                    $("#property_province").val($("#residence_province").val());
                } else {
                    $("#property_street").val("{{ $data == null ? '' : $data->PropertyData->property_street }}");
                    $("#property_house_number").val(
                        "{{ $data == null ? '' : $data->PropertyData->property_house_number }}");
                    $("#property_postal_code").val(
                        "{{ $data == null ? '' : $data->PropertyData->property_postal_code }}");
                    $("#property_common").val("{{ $data == null ? '' : $data->PropertyData->property_common }}");
                    $("#property_province").val("{{ $data == null ? '' : $data->PropertyData->property_province }}");
                }

            });
        </script>
    @endsection
</x-app-layout>
