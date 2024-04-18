<div class="column-header mb-4">
    <h1 class="h4 m-0 d-inline-block">Cantieri</h1>
    <div class="position-relative hideInMobile">
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#CreateShipping"
            onclick="getShippingList()">
            <i class="fa fa-truck"></i> Crea spedizioni
        </button>

        <!-- Modal -->
        <div class="modal fade" id="CreateShipping" data-bs-backdrop="static"  
            aria-labelledby="CreateShippingLabel" aria-hidden="true" >
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="CreateShippingLabel">Crea spedizioni</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="centriHeaderBadg"></div>

                        <div class="step-app" id="stepsWizard">
                            <ul class="nav nav-tabs" role="tablist">
                                <li class="nav-link active border-0 list-item-1 green-class" data-bs-toggle="tab"
                                    data-bs-target="#step1" id="stepnav1">
                                    <span>1</span> Scegli cantiere
                                </li>
                                <li class="nav-link border-0 list-item-2 grey-class " data-bs-toggle="tab"
                                    data-bs-target="#step2" id="stepnav2">
                                    <span>2</span> Seleziona materiali
                                </li>
                            </ul>

                            <div class="tab-content site-registration">

                                <!-- Start Step 1 -->
                                <div class="tab-pane fade show active" id="step1">
                                    <div class="card border-0">
                                        <div class="card-body">
                                            <div class="row mb-3">
                                                <div class="col-md-12">
                                                    <div class="input-group" id="create_shipping_centri_span">
                                                        <select class="form-control" name="create_shipping_centri"
                                                            id="create_shipping_centri" onchange="addCentrie()">
                                                            <option selected value="">Inizia a digitare per trovare un cantiere...</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div id="centryListShipping"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <div class="tab-footer text-end">
                                                <button type="button" class="step-btn next"
                                                    onclick="showStep2()">Avanti</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- End Step 1 -->

                                <!-- Start Step 2 -->
                                <div class="tab-pane fade" id="step2">
                                    <div id="centriMaterialList"></div>
                                </div>
                                <!-- End Step 2 -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    
        <button class="btn btn-primary dropdown-toggle show" data-bs-toggle="dropdown" role="button"
            aria-expanded="true">
            <i class="fa fa-print me-2"></i>
            Stampa modello documentazione
        </button>
        <ul class="dropdown-menu border-0 shadow w-100">
            <li>
                <a class="dropdown-item" href="{{ route('construction_stampa', '110') }}" target="_blank">
                    <button>Cantiere 110%</button>
                    <i class="fa fa-arrow-right ms-3"></i>
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ route('construction_stampa', '50') }}" target="_blank">
                    <button>Cantiere 50%</button>
                    <i class="fa fa-arrow-right ms-3"></i>
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ route('construction_stampa', '65') }}" target="_blank">
                    <button>Cantiere 65%</button>
                    <i class="fa  fa-arrow-right ms-3"></i>
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ route('construction_stampa', '90') }}" target="_blank">
                    <button>Cantiere 90%</button>
                    <i class="fa fa-arrow-right ms-3"></i>
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ route('construction_stampa', 'RCEE') }}" target="_blank">
                    <button>RCEE</button>
                    <i class="fa fa-arrow-right ms-3"></i>
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ route('construction_stampa', 'Guarino') }}" target="_blank">
                    <button>Guarino</button>
                    <i class="fa fa-arrow-right ms-3"></i>
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ route('construction_stampa', 'Preanalisi') }}" target="_blank">
                    <button>Preanalisi</button>
                    <i class="fa fa-arrow-right ms-3"></i>
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ route('construction_stampa', 'Tecnico') }}" target="_blank">
                    <button>Tecnico</button>
                    <i class="fa fa-arrow-right ms-3"></i>
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ route('construction_stampa', 'MAN') }}" target="_blank">
                    <button>Mandato Senza Rappresentanza</button>
                    <i class="fa fa-arrow-right ms-3"></i>
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ route('construction_stampa', 'UPSA') }}" target="_blank">
                    <button>UPSA</button>
                    <i class="fa fa-arrow-right ms-3"></i>
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ route('construction_stampa', 'DICH30') }}" target="_blank">
                    <button>Dichiarazione 30%</button>
                    <i class="fa  fa-arrow-right ms-3"></i>
                </a>
            </li>
            <li>
                <a class="dropdown-item" href="{{ route('construction_stampa', 'DICH30') }}" target="_blank">
                    <button>Dichiarazione Fine Lavori</button>
                    <i class="fa  fa-arrow-right ms-3"></i>
                </a>
            </li>
        </ul>
    </div>
</div>