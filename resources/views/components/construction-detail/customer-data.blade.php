<div class="card-head" id="customer">
    <h3 class="mb-0">Dati Cliente</h3>
</div>
<div class="card-body p-0">
    <form action="{{ route('construction_update', $cusdata->id) }}" method="post">
        @csrf
        @method('put')
        <ul class="resume-box">
            <li>
                <div class="icon text-center">
                    <i class="fa fa-user icon-fixed"></i>
                </div>
                <div class="fw-medium mb-0">Dati anagrafici</div>
                <div class="d-flex flex-row flex-wrap align-items-center mb-3 mt-2">
                    <div class="me-3 me-md-5">
                        <small class="text-muted">Cognome</small>
                        <div>
                            <input class="mb-0 bg-white" name="surename" type="text" value="{{ $cusdata->surename }}"
                                disabled="disabled">
                        </div>
                    </div>
                    <div class="me-3 me-md-5">
                        <small class="text-muted">Nome</small>
                        <div class="w-200">
                            <input class="mb-0 bg-white w-100" name="name" type="text"
                                value="{{ $cusdata->name }}" disabled="disabled">
                        </div>
                    </div>
                    <div class="me-3 me-md-5">
                        <small class="text-muted">Data di nascita</small>
                        <div>
                            <input class="mb-0 bg-white" name="date_of_birth" type="date"
                                value="{{ $cusdata->date_of_birth }}" disabled="disabled">
                        </div>
                    </div>
                    <div class="me-3 me-md-5">
                        <small class="text-muted">Comune di Nascita</small>
                        <div>
                            <input class="bg-white" type="text" name="town_of_birth"
                                value="{{ $cusdata->town_of_birth }}" disabled="disabled">
                        </div>
                    </div>
                    <div>
                        <small class="text-muted">Provincia</small>
                        <div>
                            <input class="bg-white" type="text" name="province" value="{{ $cusdata->province }}"
                                disabled="disabled">
                        </div>
                    </div>
                </div>
            </li>
            <li>
                <div class="icon text-center">
                    <i class="fa fa-map-marker icon-fixed"></i>
                </div>
                <div class="fw-medium mb-0">Indirizzo residenza</div>
                <div class="d-flex flex-row flex-wrap align-items-center mb-3 mt-2">
                    <div class="me-3 me-md-5">
                        <small class="text-muted">Via (Street)</small>
                        <div class="w-200">
                            <input class="mb-0 bg-white w-100" name="residence_street" type="text"
                                value="{{ $cusdata->residence_street }}" disabled="disabled">
                        </div>
                    </div>
                    <div class="me-3 me-md-5">
                        <small class="text-muted">Numero civico (House Number)</small>
                        <div>
                            <input class="mb-0 bg-white" name="residence_house_number" type="text"
                                value="{{ $cusdata->residence_house_number }}" disabled="disabled">
                        </div>
                    </div>
                    <div class="me-3 me-md-5">
                        <small class="text-muted">Comune</small>
                        <div>
                            <input class="mb-0 bg-white" name="residence_common" type="text"
                                value="{{ $cusdata->residence_common }}" disabled="disabled">
                        </div>
                    </div>
                    <div class="me-3 me-md-5">
                        <small class="text-muted">CAP (Postal Code)</small>
                        <div>
                            <input class="bg-white" type="text" name="residence_postal_code"
                                value="{{ $cusdata->residence_postal_code }}" disabled="disabled">
                        </div>
                    </div>
                    <div>
                        <small class="text-muted">Provincia (Province)</small>
                        <div>
                            <input class="bg-white" type="text" name="residence_province"
                                value="{{ $cusdata->residence_province }}" disabled="disabled">
                        </div>
                    </div>
                </div>
            </li>
            <li>
                <div class="icon text-center">
                    <i class="fa fa-id-card icon-fixed"></i>
                </div>
                <div class="fw-medium mb-0">Documenti</div>
                <div class="d-flex flex-row flex-wrap align-items-center mb-3 mt-2">
                    <div class="me-3 me-md-5">
                        <small class="text-muted">N° Carta d'identità</small><br>
                        <div>
                            <input class="bg-white" type="text"
                                value="{{ $cusdata->DocumentAndContact == null ? '' : $cusdata->DocumentAndContact->document_number }}"
                                name="document_number" disabled="disabled">
                        </div>
                    </div>
                    <div class="w-200 me-3 me-md-5">
                        <small class="text-muted">Rilasciato da</small><br>
                        <div>
                            <input class="bg-white w-100" type="text"
                                value="{{ $cusdata->DocumentAndContact == null ? '' : $cusdata->DocumentAndContact->issued_by }}"
                                name="issued_by" disabled="disabled">
                        </div>
                    </div>
                    <div class="me-3 me-md-5">
                        <small class="text-muted">Data rilascio</small><br>
                        <div>
                      
                            <input class="bg-white" type="date"
                                value="{{ $cusdata->DocumentAndContact == null ? '' : ($cusdata->DocumentAndContact->release_date ? date('Y-m-d', strtotime($cusdata->DocumentAndContact->release_date)) : null)  }}"
                                name="release_date" disabled="disabled">
                        </div>
                    </div>

                    <div class="me-3 me-md-5">
                        <small class="text-muted">Data di scadenza</small><br>
                        <div>
                          
                            <input class="bg-white" type="date" disabled="disabled" name="expiration_date"
                                value="{{ $cusdata->DocumentAndContact == null ? '' : ($cusdata->DocumentAndContact->expiration_date ? date('Y-m-d', strtotime($cusdata->DocumentAndContact->expiration_date)) : null)  }}">
                        </div>
                    </div>
                    <div class="me-3 me-md-5">
                        <small class="text-muted">Codice fiscale</small><br>
                        <div>
                            <input class="bg-white" type="text" disabled="disabled" name="fiscal_document_number"
                                value="{{ $cusdata->DocumentAndContact == null ? '' : $cusdata->DocumentAndContact->fiscal_document_number }}">
                        </div>
                    </div>
                    <div class="me-3 me-md-5">
                        <small class="text-muted">Partita IVA</small><br>
                        <div>
                            <input class="bg-white" type="text" disabled="disabled" name="vat_number"
                                value="{{ $cusdata->DocumentAndContact == null ? '' : $cusdata->DocumentAndContact->vat_number }}">
                        </div>
                    </div>
                </div>
            </li>
            <li>
                <div class="icon text-center">
                    <i class="fa fa-phone icon-fixed"></i>
                </div>
                <div class="fw-medium mb-0">Contatti</div>
                <div class="d-flex flex-row flex-wrap align-items-center mb-3 mt-2">
                    <div class="me-3 me-md-5">
                        <small class="text-muted">Numero di telefono</small><br>
                        <div>
                            <input class="bg-white" disabled="disabled" type="text" name="contact_number"
                                value="{{ $cusdata->DocumentAndContact == null ? '' : $cusdata->DocumentAndContact->contact_number }}">
                        </div>
                    </div>
                    <div class="w-250">
                        <small class="text-muted">Indirizzo email</small><br>
                        <div>
                            <input class="bg-white w-100" disabled="disabled" type="email" name="contact_email"
                                value="{{ $cusdata->DocumentAndContact == null ? '' : $cusdata->DocumentAndContact->contact_email }}">
                        </div>
                    </div>
                </div>
                <div class="fw-medium mb-0">Contatto Alternativo</div>
                <div class="d-flex flex-row flex-wrap align-items-center mb-3 mt-2">
                    <div class="me-3 me-md-5">
                        <small class="text-muted">Riferimento Contatto</small><br>
                        <div>
                            <input class="bg-white" type="text"
                                value="{{ $cusdata->DocumentAndContact == null ? '' : $cusdata->DocumentAndContact->alt_refrence_name }}"
                                name="alt_refrence_name" disabled="disabled">
                        </div>
                    </div>
                    <div class="me-3 me-md-5">
                        <small class="text-muted">Numero di telefono</small><br>
                        <div>
                            <input class="bg-white" disabled="disabled"
                                value="{{ $cusdata->DocumentAndContact == null ? '' : $cusdata->DocumentAndContact->alt_contact_number }}"
                                name="alt_contact_number" type="text">
                        </div>
                    </div>
                </div>
            </li>
        </ul>
        <div class="d-flex justify-content-end">
            <button type="button" class="btn btn-green m-1 edit">
                <strong><i class="fa fa-pencil me-2"></i>Modifica</strong>
            </button>
            <button type="submit" class="btn btn-outline-green m-1 save" disabled="disabled">
                <strong><i class="fa fa-check me-2"></i>Salva</strong>
            </button>
        </div>
    </form>
</div>
