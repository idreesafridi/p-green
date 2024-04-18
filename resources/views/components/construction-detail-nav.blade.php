@if (auth()->user()->hasrole('admin') ||
        auth()->user()->hasrole('user'))
    <!-- Start Tab List -->
    <nav class="site-detail-menu nav nav-tabs px-0 my-2 border-bottom-0" id="nav-tab" role="tablist">

        <a class="{{ request()->route()->pagename == 'Cliente' ? 'text-dark' : '' }}"
            href="{{ route('construction_detail', ['id' => $constructionid, 'pagename' => 'Cliente']) }}#customer"
            role="tab">
            <li class="nav-link my-1 me-2 border-0 {{ request()->route()->pagename == 'Cliente' ? 'active' : '' }}">
                <i class="fa  fa-user me-2"></i>Cliente
            </li>
        </a>
        <a class="{{ request()->route()->pagename == 'Cantiere' ? 'text-dark' : '' }}"
            href="{{ route('construction_detail', ['id' => $constructionid, 'pagename' => 'Cantiere']) }}#building-site"
            role="tab">
            <li class="nav-link my-1 me-2 border-0 {{ request()->route()->pagename == 'Cantiere' ? 'active' : '' }}">
                <i class="fa fa-info-circle me-2"></i>Cantiere
            </li>
        </a>
        <a class="{{ request()->route()->pagename == 'Materiali' ? 'text-dark' : '' }}"
            href="{{ route('construction_detail', ['id' => $constructionid, 'pagename' => 'Materiali']) }}#materials"
            role="tab">
            <li class="nav-link my-1 me-2 border-0 {{ request()->route()->pagename == 'Materiali' ? 'active' : '' }}">
                <i class="fa fa-wrench me-2"></i>Materiali
            </li>
        </a>
        <a class="{{ request()->route()->pagename == 'Assistenze' ? 'text-dark' : '' }}"
            href="{{ route('construction_detail', ['id' => $constructionid, 'pagename' => 'Assistenze']) }}#assistances"
            role="tab">
            <li class="nav-link my-1 me-2 border-0 {{ request()->route()->pagename == 'Assistenze' ? 'active' : '' }}">
                <i class="fa fa-calendar-check-o me-2"></i>Assistenze
            </li>
        </a>

        <li
            class="nav-link my-1 me-2 border-0 {{ request()->route()->getName() == 'show_preNoti_doc'? 'active': '' }}">
            @if ($conststatus->StatusPrNoti != null)
                <a class="{{ request()->route()->getName() == 'show_preNoti_doc'? 'text-dark': '' }}"
                    href="{{ route('show_preNoti_doc', $conststatus->StatusPrNoti->id) }}#paper" role="tab">
                    <i class="fa fa-file me-2"></i>Documenti
                </a>
            @else
                <a class="{{ request()->route()->getName() == 'show_preNoti_doc'? 'text-dark': '' }}" href="#"
                    role="tab">
                    <i class="fa fa-file me-2"></i>Carte
                </a>
            @endif

            <div class="dropdown-toggle d-inline-block" data-bs-toggle="dropdown" role="button">
                <i class="fa fa-arrow"></i>
            </div>

            <ul class="dropdown-menu mt-2 border-0 shadow">
                <li class="nav-item">
                    <a class="dropdown-item" href="{{ route('doc_tecnico', ['id' => $constructionid]) }}" role="tab">
                        Tecnico
                        <i class="fa fa-arrow-right ms-3 me-2"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="dropdown-item" href="{{ route('doc_commercialista', ['id' => $constructionid]) }}" role="tab">
                        Commercialista
                        <i class="fa fa-arrow-right ms-3 me-2"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="dropdown-item" href="{{ route('doc_fotovoltaico', ['id' => $constructionid]) }}" role="tab">
                        Ing. Fotovoltaico
                        <i class="fa  fa-arrow-right ms-3 me-2"></i>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="dropdown-item" href="{{ route('doc_chiavetta', ['id' => $constructionid]) }}" role="tab">
                        Chiavetta
                        <i class="fa  fa-arrow-right ms-3 me-2"></i>
                    </a>
                </li>
            </ul>
        </li>
        <li class="nav-link my-1 me-2 border-0 {{ request()->route()->pagename == 'Immagini' ? 'active' : '' }} dropdown-toggle d-inline-block"
            data-bs-toggle="dropdown" role="button">
            <i class="fa fa-image me-2"></i>Immagini
        </li>
        <ul class="dropdown-menu border-0 mt-2 shadow">
            <li class="nav-item">
                <a class="dropdown-item"
                    href="{{ route('construction_detail', ['id' => $constructionid, 'pagename' => 'Immagini', 'image' => 'ante']) }}#image"
                    role="tab">
                    <i class="fa fa-arrow-right me-2"></i>Ante<span
                        class="badge bg-green float-end">{{ $imagecount['ante'] }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="dropdown-item"
                    href="{{ route('construction_detail', ['id' => $constructionid, 'pagename' => 'Immagini', 'image' => 'durante']) }}#image"
                    role="tab">
                    <i class="fa  fa-arrow-right me-2"></i>Durante<span
                        class="badge bg-green float-end">{{ $imagecount['durante'] }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="dropdown-item"
                    href="{{ route('construction_detail', ['id' => $constructionid, 'pagename' => 'Immagini', 'image' => 'post']) }}#image"
                    role="tab">
                    <i class="fa  fa-arrow-right me-2"></i>Post<span
                        class="badge bg-green float-end">{{ $imagecount['post'] }}</span>
                </a>
            </li>
            <li class="nav-item">
                <a class="dropdown-item"
                    href="{{ route('construction_detail', ['id' => $constructionid, 'pagename' => 'Immagini', 'image' => 'cantiere']) }}#image"
                    role="tab">
                    <i class="fa  fa-arrow-right me-2"></i>Cantiere<span
                        class="badge bg-green float-end">{{ $imagecount['cantiere'] }}</span>
                </a>
            </li>
        </ul>


        <a class="{{ request()->route()->pagename == 'Stato' ? 'text-dark' : '' }}"
            href="{{ route('construction_detail', ['id' => $constructionid, 'pagename' => 'Stato']) }}#state"
            role="tab">
            <li class="nav-link my-1 me-2 border-0 {{ request()->route()->pagename == 'Stato' ? 'active' : '' }}">
                <i class="fa fa-history me-2"></i>Stato
            </li>
        </a>
        <a class="{{ request()->route()->pagename == 'Note' ? 'text-dark' : '' }}"
            href="{{ route('construction_detail', ['id' => $constructionid, 'pagename' => 'Note']) }}#note"
            role="tab">
            <li class="nav-link my-1 me-2 border-0 {{ request()->route()->pagename == 'Note' ? 'active' : '' }}">
                <i class="fa fa-pencil-square-o me-2"></i>Note
            </li>
        </a>

        <li class="nav-link my-1 me-2 border-0 {{ request()->route()->pagename == 'Print Documentation' ? 'active' : '' }} dropdown-toggle d-inline-block"
            data-bs-toggle="dropdown" role="button">
            <i class="fa fa-print me-2"></i>Stampa Documentazione
        </li>
        <ul class="dropdown-menu border-0 shadow" data-popper-placement="bottom-start">
            <li>
                <a class="dropdown-item"
                    href="{{ route('print_construction_stampa', ['id' => $constructionid, 'page' => '110']) }}"
                    target="_blank">
                    <button>Cantiere 110%</button>
                    <i class="fa fa-arrow-right ms-3"></i>
                </a>
            </li>
            <li>
                <a class="dropdown-item"
                    href="{{ route('print_construction_stampa', ['id' => $constructionid, 'page' => '50']) }}"
                    target="_blank">
                    <button>Cantiere 50%</button>
                    <i class="fa fa-arrow-right ms-3"></i>
                </a>
            </li>
            <li>
                <a class="dropdown-item"
                    href="{{ route('print_construction_stampa', ['id' => $constructionid, 'page' => '65']) }}"
                    target="_blank">
                    <button>Cantiere 65%</button>
                    <i class="fa  fa-arrow-right ms-3"></i>
                </a>
            </li>
            <li>
                <a class="dropdown-item"
                    href="{{ route('print_construction_stampa', ['id' => $constructionid, 'page' => '90']) }}"
                    target="_blank">
                    <button>Cantiere 90%</button>
                    <i class="fa fa-arrow-right ms-3"></i>
                </a>
            </li>
            <li>
                <a class="dropdown-item"
                    href="{{ route('print_construction_stampa', ['id' => $constructionid, 'page' => 'rcee_ba']) }}"
                    target="_blank">
                    <button>RCEE (BA)</button>
                    <i class="fa fa-arrow-right ms-3"></i>
                </a>
            </li>
            <li>
                <a class="dropdown-item"
                    href="{{ route('print_construction_stampa', ['id' => $constructionid, 'page' => 'rcee_br']) }}"
                    target="_blank">
                    <button>RCEE (BR)</button>
                    <i class="fa fa-arrow-right ms-3"></i>
                </a>
            </li>
            <li>
                <a class="dropdown-item"
                    href="{{ route('print_construction_stampa', ['id' => $constructionid, 'page' => 'rcee_le']) }}"
                    target="_blank">
                    <button>RCEE (LE)</button>
                    <i class="fa fa-arrow-right ms-3"></i>
                </a>
            </li>
            <li>
                <a class="dropdown-item"
                    href="{{ route('print_construction_stampa', ['id' => $constructionid, 'page' => 'rcee_ta']) }}"
                    target="_blank">
                    <button>RCEE (TA)</button>
                    <i class="fa fa-arrow-right ms-3"></i>
                </a>
            </li>
            <li>
                <a class="dropdown-item"
                    href="{{ route('print_construction_stampa', ['id' => $constructionid, 'page' => 'rcee_gt']) }}"
                    target="_blank">
                    <button>RCEE (Gruppi termici)</button>
                    <i class="fa fa-arrow-right ms-3"></i>
                </a>
            </li>
            <li>
                <a class="dropdown-item"
                    href="{{ route('print_construction_stampa', ['id' => $constructionid, 'page' => 'guarino']) }}"
                    target="_blank">
                    <button>Guarino</button>
                    <i class="fa fa-arrow-right ms-3"></i>
                </a>
            </li>
            <li>
                <a class="dropdown-item"
                    href="{{ route('print_construction_stampa', ['id' => $constructionid, 'page' => 'pal']) }}"
                    target="_blank">
                    <button>Palmisano</button>
                    <i class="fa fa-arrow-right ms-3"></i>
                </a>
            </li>
            <li>
                <a class="dropdown-item"
                    href="{{ route('print_construction_stampa', ['id' => $constructionid, 'page' => 'preanalisi']) }}"
                    target="_blank">
                    <button>Preanalisi</button>
                    <i class="fa fa-arrow-right ms-3"></i>
                </a>
            </li>
            <li>
                <a class="dropdown-item"
                    href="{{ route('print_construction_stampa', ['id' => $constructionid, 'page' => 'cdl']) }}"
                    target="_blank">
                    <button>Consenso Lavori</button>
                    <i class="fa fa-arrow-right ms-3"></i>
                </a>
            </li>
            <li>
                <a class="dropdown-item"
                    href="{{ route('print_construction_stampa', ['id' => $constructionid, 'page' => 'tecnico']) }}"
                    target="_blank">
                    <button>Tecnico</button>
                    <i class="fa fa-arrow-right ms-3"></i>
                </a>
            </li>
            <li>
                <a class="dropdown-item"
                    href="{{ route('print_construction_stampa', ['id' => $constructionid, 'page' => 'msr']) }}"
                    target="_blank">
                    <button>Mandato Senza Rappresentanza</button>
                    <i class="fa fa-arrow-right ms-3"></i>
                </a>
            </li>
            <li>
                <a class="dropdown-item"
                    href="{{ route('print_construction_stampa', ['id' => $constructionid, 'page' => 'upsa']) }}"
                    target="_blank">
                    <button>UPSA</button>
                    <i class="fa fa-arrow-right ms-3"></i>
                </a>
            </li>
            <li>
                <a class="dropdown-item"
                    href="{{ route('print_construction_stampa', ['id' => $constructionid, 'page' => 'dich30']) }}"
                    target="_blank">
                    <button>Dichiarazione 30%</button>
                    <i class="fa  fa-arrow-right ms-3"></i>
                </a>
            </li>
            <li>
                <a class="dropdown-item"
                    href="{{ route('print_construction_stampa', ['id' => $constructionid, 'page' => 'fascicolo']) }}"
                    target="_blank">
                    <button>Scheda sopralluogo</button>
                    <i class="fa  fa-arrow-right ms-3"></i>
                </a>
            </li>
            <li>
                <a class="dropdown-item"
                    href="{{ route('print_construction_stampa', ['id' => $constructionid, 'page' => '25super']) }}"
                    target="_blank">
                    <button>-25 superficie disperdente</button>
                    <i class="fa  fa-arrow-right ms-3"></i>
                </a>
            </li>
           <li>
                <a class="dropdown-item"
                    href="{{ route('print_construction_stampa', ['id' => $constructionid, 'page' => 'lavori']) }}"
                    target="_blank">
                    <button>Dichiarazione Fine Lavori</button>
                    <i class="fa  fa-arrow-right ms-3"></i>
                </a>
            </li>
        </ul>

        <li class="nav-link my-1 me-2 border-0 {{ request()->route()->pagename == 'Settings' ? 'active' : '' }} dropdown-toggle d-inline-block"
            data-bs-toggle="dropdown" role="button">
            <i class="fa fa-cog me-2"></i>Impostazioni
        </li>
        <ul class="dropdown-menu mt-2 border-0 shadow">
            <li class="nav-item">
                @if ($conststatus->archive == 0 || $conststatus->archive == null)
                    <a class="dropdown-item d-flex justify-content-start align-items-center"
                        href="{{ route('set_archive', ['id' => $constructionid, 'archive' => 1]) }}" role="tab">
                        <i class="fa fa-inbox me-2"></i>Archivia
                    </a>
                @else
                    <a class="dropdown-item d-flex justify-content-start align-items-center"
                        href="{{ route('set_archive', ['id' => $constructionid, 'archive' => 0]) }}" role="tab">
                        <i class="fa fa-inbox me-2"></i>Estrai
                    </a>
                @endif
            </li>
            <li class="nav-item {{ $conststatus->archive == 1 ? 'd-none' : '' }}">
                <a class="dropdown-item d-flex justify-content-start align-items-center" href="#"
                    role="tab">
                    <i class="bi bi-question-octagon-fill me-2"></i>Sposta in assistenze
                </a>
            </li>
            <li class="nav-item">
                <a class="dropdown-item d-flex justify-content-start align-items-center" data-bs-toggle="modal"
                    data-bs-target="#deleteConstruction" href="javascript:void(0)" role="tab">
                    <i class="fa  fa-trash me-2"></i>Elimina definitivamente
                </a>
            </li>
        </ul>

        <button type="button" class="btn btn-green" data-bs-toggle="modal" data-bs-target="#sendInviaEmailModal">
            <i class="fa fa-envelope me-2"></i>Invia email
        </button>
    </nav>
    <!-- End Tab List -->
    {{-- @dd($conststatus) --}}
    <!-- Invite Via Email Modal -->
    <div class="modal fade" id="sendInviaEmailModal" aria-labelledby="sendInviaEmailModal" aria-modal="true"
        role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sendInviaEmailModal">Invia email</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('sendInviaEmail') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="row send-email " id = "send-email-css">
                            <div class="col-12">
                
                                <label class="col-form-label">Destinatario</label>
                                <div class="custom-dropdown">
                                    <input type="text" id="email" name="email" class="form-control" placeholder="nome@esempio.com" required>
                                    <ul id="custom-suggestion-dropdown" class="dropdown-menu" aria-labelledby="email"></ul>
                                </div>
                                
                                    {{-- <div id="suggestion-dropdown"></div> --}}
                                    {{-- <label class="col-form-label">Destinatario</label>
                                    <select name="email" id="email" class="form-control select2" multiple="multiple" required>
                                        <!-- Options will be added dynamically -->
                                    </select> --}}

                            </div>
                            <div class="col-12">
                                <label class="col-form-label">Oggetto</label>
                                <input type="text" name="subject"
                                    value=""
                                    class="form-control" placeholder="Oggetto della email" required>
                                  
                                {{-- <input type="text" name="subject"
                                    value="RIF. {{ $conststatus->name }} {{ $conststatus->surename }} — Email da {{ auth()->user()->name }}"
                                    class="form-control" placeholder="Oggetto della email" required> --}}
                            </div>
                            <div class="col-12">
                                <label class="col-form-label">Contenuto</label>
                                <textarea required class="form-control" name="msg" id="exampleFormControlTextarea1" rows="5"
                                    placeholder="Scrivi qui il messaggio della tua email"></textarea>
                                {{-- <textarea required class="form-control" name="msg" id="exampleFormControlTextarea1" rows="5"
                                    placeholder="Scrivi qui il messaggio della tua email">!!! Non rispondere a questa email ma contatta < {{ auth()->user()->email }} >
———</textarea> --}}
                                {{-- <input type= "hidden" name ="defualtText" value = "!!! Non rispondere a questa email ma contatta < {{ auth()->user()->email }} >"> --}}
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer mb-3">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
                        <button type="submit" class="btn btn-green">Invia email</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End Invite Via Email Modal -->

    <!-- Invite Via Email Modal -->
    <div class="modal fade" id="deleteConstruction" aria-labelledby="exampleModalLabel" aria-modal="true"
        role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Attenzione</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="POST">
                    <div class="modal-body">
                        <div class="row send-email">
                            <div class="col-12">
                                <label class="col-form-label">Sei sicuro di voler procedere?</label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer mb-3">
                        <a class="btn btn-sm btn-danger" href="{{ route('delete_construction', $constructionid) }}">
                            <i class="fa  fa-trash me-2"></i>Elimina
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- End Invite Via Email Modal -->
@elseif (auth()->user()->hasrole('technician'))
    <nav class="site-detail-menu nav nav-tabs px-0 my-2 border-bottom-0" id="nav-tab" role="tablist">
        <li
            class="nav-link my-1 me-2 border-0 {{ request()->route()->getName() == 'show_preNoti_doc'? 'active': '' }}">
            @if ($conststatus->StatusPrNoti != null)
                <a class="{{ request()->route()->getName() == 'show_preNoti_doc'? 'text-dark': '' }}"
                    href="{{ route('show_preNoti_doc', $conststatus->StatusPrNoti->id) }}" role="tab">
                    <i class="fa fa-file me-2"></i>Papers
                </a>
            @else
                <a class="{{ request()->route()->getName() == 'show_preNoti_doc'? 'text-dark': '' }}" href="#"
                    role="tab">
                    <i class="fa fa-file me-2"></i>Papers
                </a>
            @endif

            <div class="dropdown-toggle d-inline-block" data-bs-toggle="dropdown" role="button">
                <i class="fa fa-arrow"></i>
            </div>

            <ul class="dropdown-menu mt-2 border-0 shadow">
                <li class="nav-item">
                    <a class="dropdown-item" href="#" role="tab">
                        Tecnico
                        <i class="fa fa-arrow-right ms-3 me-2"></i>
                    </a>
                </li>
            </ul>
        </li>
        <li class="nav-link my-1 me-2 border-0 {{ request()->route()->pagename == 'Immagini' ? 'active' : '' }}">
            <div class="dropdown-toggle d-inline-block" data-bs-toggle="dropdown" role="button">
                <i class="fa fa-image me-2"></i>Immagini
            </div>

            <ul class="dropdown-menu border-0 mt-2 shadow">
                <li class="nav-item">
                    <a class="dropdown-item"
                        href="{{ route('construction_detail', ['id' => $constructionid, 'pagename' => 'Immagini', 'image' => 'ante']) }}"
                        role="tab">
                        <i class="fa fa-arrow-right me-2"></i>Ante<span
                            class="badge bg-green float-end">{{ $imagecount['ante'] }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="dropdown-item"
                        href="{{ route('construction_detail', ['id' => $constructionid, 'pagename' => 'Immagini', 'image' => 'durante']) }}"
                        role="tab">
                        <i class="fa  fa-arrow-right me-2"></i>Durante<span
                            class="badge bg-green float-end">{{ $imagecount['durante'] }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="dropdown-item"
                        href="{{ route('construction_detail', ['id' => $constructionid, 'pagename' => 'Immagini', 'image' => 'post']) }}"
                        role="tab">
                        <i class="fa  fa-arrow-right me-2"></i>Post<span
                            class="badge bg-green float-end">{{ $imagecount['post'] }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="dropdown-item"
                        href="{{ route('construction_detail', ['id' => $constructionid, 'pagename' => 'Immagini', 'image' => 'cantiere']) }}"
                        role="tab">
                        <i class="fa  fa-arrow-right me-2"></i>Cantiere<span
                            class="badge bg-green float-end">{{ $imagecount['cantiere'] }}</span>
                    </a>
                </li>
            </ul>
        </li>
    </nav>
@elseif (auth()->user()->hasrole('business'))
    <nav class="site-detail-menu nav nav-tabs px-0 my-2 border-bottom-0" id="nav-tab" role="tablist">
        <li class="nav-link my-1 me-2 border-0 {{ request()->route()->pagename == 'Immagini' ? 'active' : '' }}">
            <div class="dropdown-toggle d-inline-block" data-bs-toggle="dropdown" role="button">
                <i class="fa fa-image me-2"></i>Immagini
            </div>

            <ul class="dropdown-menu border-0 mt-2 shadow">
                <li class="nav-item">
                    <a class="dropdown-item"
                        href="{{ route('construction_detail', ['id' => $constructionid, 'pagename' => 'Immagini', 'image' => 'ante']) }}"
                        role="tab">
                        <i class="fa fa-arrow-right me-2"></i>Ante<span
                            class="badge bg-green float-end">{{ $imagecount['ante'] }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="dropdown-item"
                        href="{{ route('construction_detail', ['id' => $constructionid, 'pagename' => 'Immagini', 'image' => 'durante']) }}"
                        role="tab">
                        <i class="fa  fa-arrow-right me-2"></i>Durante<span
                            class="badge bg-green float-end">{{ $imagecount['durante'] }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="dropdown-item"
                        href="{{ route('construction_detail', ['id' => $constructionid, 'pagename' => 'Immagini', 'image' => 'post']) }}"
                        role="tab">
                        <i class="fa  fa-arrow-right me-2"></i>Post<span
                            class="badge bg-green float-end">{{ $imagecount['post'] }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="dropdown-item"
                        href="{{ route('construction_detail', ['id' => $constructionid, 'pagename' => 'Immagini', 'image' => 'cantiere']) }}"
                        role="tab">
                        <i class="fa  fa-arrow-right me-2"></i>Cantiere<span
                            class="badge bg-green float-end">{{ $imagecount['cantiere'] }}</span>
                    </a>
                </li>
            </ul>
        </li>
        @php
            $getimpresaType = \App\Models\BusinessDetail::Where('user_id',Auth::user()->id)->value('company_type');

            if($getimpresaType == 'Infissi'){
               $PrNotDocId = $conststatus->PrNotDoc->where('folder_name', 'Conferme D Ordine')->first()->id;
            $ReliefDocument = $conststatus->ReliefDocument->where('folder_name', 'Schemi Impianti')->first()->id;
            }else
        {
            $PrNotDocId = $conststatus->PrNotDoc->where('folder_name', 'Contratto Di Subappalto Impresa')->first()->id;
            //dd($PrNotDocId);
            $ReliefDocument = $conststatus->ReliefDocument->where('folder_name', 'Schemi Impianti')->first()->id;
        }

        @endphp
        @if ($PrNotDocId)
            @if($getimpresaType == 'Infissi')
                <li
                    class="nav-link my-1 me-2 border-0 {{ request()->route()->getName() == 'show_prenoti_doc_file'? 'active': '' }}">
                    <a class="{{ request()->route()->getName() == 'show_prenoti_doc_file'? 'text-dark': '' }}"
                       href="{{ route('show_prenoti_doc_file', $PrNotDocId) }}" role="tab">
                        <i class="fa fa-file me-2"></i>Conferme D Ordine
                    </a>

                </li>
            @else
                <li
                    class="nav-link my-1 me-2 border-0 {{ request()->route()->getName() == 'show_prenoti_doc_file'? 'active': '' }}">
                    <a class="{{ request()->route()->getName() == 'show_prenoti_doc_file'? 'text-dark': '' }}"
                       href="{{ route('show_prenoti_doc_file', $PrNotDocId) }}" role="tab">
                        <i class="fa fa-file me-2"></i>Contratto Di Subappalto Impresa
                    </a>

                <li
                    class="nav-link my-1 me-2 border-0 {{ request()->route()->getName() == 'show_relief_doc_file'? 'active': '' }}">
                    <a class="{{ request()->route()->getName() == 'show_relief_doc_file'? 'text-dark': '' }}"
                       href="{{ route('show_relief_doc_file', $ReliefDocument) }}" role="tab">
                        <i class="fa fa-file me-2"></i>Schemi Impianti
                    </a>

                </li>
                @endif
{{--            <li--}}
{{--                class="nav-link my-1 me-2 border-0 {{ request()->route()->getName() == 'show_prenoti_doc_file'? 'active': '' }}">--}}
{{--                <a class="{{ request()->route()->getName() == 'show_prenoti_doc_file'? 'text-dark': '' }}"--}}
{{--                    href="{{ route('show_prenoti_doc_file', $PrNotDocId) }}" role="tab">--}}
{{--                    <i class="fa fa-file me-2"></i>Conferme D Ordine--}}
{{--                </a>--}}

{{--            </li>--}}
        @else
            <li class="nav-link my-1 me-2 border-0 {{ request()->route()->pagename == 'Immagini' ? 'active' : '' }}">
                <a class="{{ request()->route()->getName() == 'show_preNoti_doc'? 'text-dark': '' }}" href="#"
                    role="tab">
                    <i class="fa fa-file me-2"></i>Contratto Di Subappalto Impresa
                </a>
            </li>
        @endif

    </nav>
@elseif (auth()->user()->hasrole('worker'))
    <nav class="site-detail-menu nav nav-tabs px-0 my-2 border-bottom-0" id="nav-tab" role="tablist">
        <a class="{{ request()->route()->pagename == 'Materiali' ? 'text-dark' : '' }}"
            href="{{ route('construction_detail', ['id' => $constructionid, 'pagename' => 'Materiali']) }}#materials"
            role="tab">
            <li class="nav-link my-1 me-2 border-0 {{ request()->route()->pagename == 'Materiali' ? 'active' : '' }}">
                <i class="fa fa-wrench me-2"></i>Materiali
            </li>
        </a>
        <a class="{{ request()->route()->pagename == 'Assistenze' ? 'text-dark' : '' }}"
            href="{{ route('construction_detail', ['id' => $constructionid, 'pagename' => 'Assistenze']) }}#assistances"
            role="tab">
            <li
                class="nav-link my-1 me-2 border-0 {{ request()->route()->pagename == 'Assistenze' ? 'active' : '' }}">
                <i class="fa fa-calendar-check-o me-2"></i>Assistenze
            </li>
        </a>
        <li class="nav-link my-1 me-2 border-0 {{ request()->route()->pagename == 'Immagini' ? 'active' : '' }}">
            <div class="dropdown-toggle d-inline-block" data-bs-toggle="dropdown" role="button">
                <i class="fa fa-image me-2"></i>Immagini
            </div>

            <ul class="dropdown-menu border-0 mt-2 shadow">
                <li class="nav-item">
                    <a class="dropdown-item"
                        href="{{ route('construction_detail', ['id' => $constructionid, 'pagename' => 'Immagini', 'image' => 'ante']) }}"
                        role="tab">
                        <i class="fa fa-arrow-right me-2"></i>Ante<span
                            class="badge bg-green float-end">{{ $imagecount['ante'] }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="dropdown-item"
                        href="{{ route('construction_detail', ['id' => $constructionid, 'pagename' => 'Immagini', 'image' => 'durante']) }}"
                        role="tab">
                        <i class="fa  fa-arrow-right me-2"></i>Durante<span
                            class="badge bg-green float-end">{{ $imagecount['durante'] }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="dropdown-item"
                        href="{{ route('construction_detail', ['id' => $constructionid, 'pagename' => 'Immagini', 'image' => 'post']) }}"
                        role="tab">
                        <i class="fa  fa-arrow-right me-2"></i>Post<span
                            class="badge bg-green float-end">{{ $imagecount['post'] }}</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="dropdown-item"
                        href="{{ route('construction_detail', ['id' => $constructionid, 'pagename' => 'Immagini', 'image' => 'cantiere']) }}"
                        role="tab">
                        <i class="fa  fa-arrow-right me-2"></i>Cantiere<span
                            class="badge bg-green float-end">{{ $imagecount['cantiere'] }}</span>
                    </a>
                </li>
            </ul>
        </li>

        @php
            //$ReliefDocument = $conststatus->ReliefDocument->where('folder_name', 'Schemi Impianti')->first()->id;
            $ReliefDocument = null;
            $reliefDocumentQuery = $conststatus->ReliefDocument->where('folder_name', 'Schemi Impianti');
            if ($reliefDocumentQuery->count() > 0) {
                $ReliefDocument = $reliefDocumentQuery->first()->id;
            }
        @endphp
        @if ($ReliefDocument)
            <li
                class="nav-link my-1 me-2 border-0 {{ request()->route()->getName() == 'show_relief_doc_file'? 'active': '' }}">
                <a class="{{ request()->route()->getName() == 'show_relief_doc_file'? 'text-dark': '' }}"
                    href="{{ route('show_relief_doc_file', $ReliefDocument) }}" role="tab">
                    <i class="fa fa-file me-2"></i>Schemi Impianti
                </a>

            </li>
        @else
            <li class="nav-link my-1 me-2 border-0">
                <a class="{{ request()->route()->getName() == 'show_preNoti_doc'? 'text-dark': '' }}" href="#"
                    role="tab">
                    <i class="fa fa-file me-2"></i>Schemi Impianti
                </a>
            </li>
        @endif

    </nav>
@elseif (auth()->user()->hasrole('businessconsultant'))
    <nav class="site-detail-menu nav nav-tabs px-0 my-2 border-bottom-0" id="nav-tab" role="tablist">

        <a class="{{ request()->route()->pagename == 'Cliente' ? 'text-dark' : '' }}"
            href="{{ route('construction_detail', ['id' => $constructionid, 'pagename' => 'Cliente']) }}#customer"
            role="tab">
            <li class="nav-link my-1 me-2 border-0 {{ request()->route()->pagename == 'Cliente' ? 'active' : '' }}">
                <i class="fa  fa-user me-2"></i>Cliente
            </li>
        </a>


        <a class="{{ request()->route()->pagename == 'Cantiere' ? 'text-dark' : '' }}"
            href="{{ route('construction_detail', ['id' => $constructionid, 'pagename' => 'Cantiere']) }}#building-site"
            role="tab">
            <li class="nav-link my-1 me-2 border-0 {{ request()->route()->pagename == 'Cantiere' ? 'active' : '' }}">
                <i class="fa fa-info-circle me-2"></i>Cantiere
            </li>
        </a>


        <li
        class="nav-link my-1 me-2 border-0 {{ request()->route()->getName() == 'show_preNoti_doc'? 'active': '' }}">
        @if ($conststatus->StatusPrNoti != null)
            <a class="{{ request()->route()->getName() == 'show_preNoti_doc'? 'text-dark': '' }}"
                href="{{ route('show_preNoti_doc', $conststatus->StatusPrNoti->id) }}#paper" role="tab">
                <i class="fa fa-file me-2"></i>Documenti
            </a>
        @else
            <a class="{{ request()->route()->getName() == 'show_preNoti_doc'? 'text-dark': '' }}" href="#"
                role="tab">
                <i class="fa fa-file me-2"></i>Carte
            </a>
        @endif

        <div class="dropdown-toggle d-inline-block" data-bs-toggle="dropdown" role="button">
            <i class="fa fa-arrow"></i>
        </div>

        <ul class="dropdown-menu mt-2 border-0 shadow">
            <li class="nav-item">
                <a class="dropdown-item" href="#" role="tab">
                    Tecnico
                    <i class="fa fa-arrow-right ms-3 me-2"></i>
                </a>
            </li>
            <li class="nav-item">
                <a class="dropdown-item" href="#" role="tab">
                    Commercialista
                    <i class="fa fa-arrow-right ms-3 me-2"></i>
                </a>
            </li>
            <li class="nav-item">
                <a class="dropdown-item" href="#" role="tab">
                    Ing. Fotovoltaico
                    <i class="fa  fa-arrow-right ms-3 me-2"></i>
                </a>
            </li>
            <li class="nav-item">
                <a class="dropdown-item" href="#" role="tab">
                    Chiavetta
                    <i class="fa  fa-arrow-right ms-3 me-2"></i>
                </a>
            </li>
        </ul>
         </li>
    </nav>
@endif
