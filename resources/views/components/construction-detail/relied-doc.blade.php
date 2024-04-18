<div class="card-head document-page-header py-4">
    <div class="d-flex align-items-center">
        <a href="{{ URL::previous() }}">
        <i class="fa fa-arrow-left me-3  back"></i></a>
        <h6 class="heading fw-bold mb-0">Tutti i Documenti</h6>
    </div>
    <form>
        <div class="row">
            <div class="col-12 mt-4 col-lg-4 d-flex align-items-center">
                <input type="text" class="form-control head-input" placeholder="Cerca tra i documenti">
            </div>
            <div class="col-12 col-lg-8 mt-4 d-md-flex justify-content-between align-items-center">
                <div>

                    <nav class="d-inline-block filterList">
                        <div class="nav nav-tabs border-bottom-0" role="tablist">
                            <a id="filter1" class="active btn btn-light text-black btn-sm me-2 mb-2" type="button"
                                role="tab" data-bs-toggle="tab" href="#filterTab1">Tutti</a>

                            <a id="filter2" class="btn btn-light text-black btn-sm 50-filter me-2 mb-2" type="button"
                                role="tab" data-bs-toggle="tab" href="#filterTab2">50</a>

                            <a id="filter3" type="button" class="btn btn-danger me-2 mb-2" role="tab"
                                data-bs-toggle="tab" href="#filterTab3">Essenziali</a>

                            <a id="filter4" type="button" class="btn btn-info me-2 mb-2" role="tab"
                                data-bs-toggle="tab" href="#filterTab4">Chiavetta</a>
                        </div>
                    </nav>

                </div>
                <div class="text-end">
                    <button type="" class="btn btn-green">
                        <i class="fa fa-download me-2"></i>
                        Scarica tutto
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
<div class="card-body p-0">
    <div class="tab-content">
        <div class="tab-pane fade active show" id="filterTab1">
            <table class="table document-table table-hover">
                <thead>
                    <tr>
                        <th scope="col">Nome Documento</th>
                        <th scope="col">Stato</th>
                        <th scope="col" class="hideInMobile">Aggiornato il</th>
                        <th scope="col" class="hideInMobile">Aggiornato Da</th>
                        <th scope="col" class=""></th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <a class="fa fa-folder"></a>
                            <a href="" class="me-4 ms-2">
                                <strong>Altri Documenti Interni</strong>
                            </a><br>
                            <small>Documenti vari interni</small>
                        </td>
                        <td>
                            <span class="badge bg-success">2</span>
                        </td>
                        <td class="hideInMobile">09/12/22</td>
                        <td class="hideInMobile">PASQUALE</td>
                        <td class="space"></td>
                        <td>
                            <button type="button" class="btn btn-link btn-sm text-dark">
                                <i class="fa fa-download"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-warning" data-bs-toggle="modal"
                                data-bs-target="#bellBtnModal">
                                <i class="fa fa-bell"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-danger" data-bs-toggle="modal"
                                data-bs-target="#warningModal">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <a class="fa fa-folder"></a>
                            <a href="" class="me-4 ms-2">
                                <strong>Conferme d'ordine</strong>
                            </a><br>
                            <small>Infissi - imprese - ecc.</small>
                        </td>
                        <td>
                            <span class="badge bg-danger">0</span>
                        </td>
                        <td class="hideInMobile"></td>
                        <td class="hideInMobile"></td>
                        <td class="space"></td>
                        <td>
                            <button type="button" class="btn btn-link btn-sm text-dark">
                                <i class="fa fa-download"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-warning" data-bs-toggle="modal"
                                data-bs-target="#bellBtnModal">
                                <i class="fa fa-bell"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-danger" data-bs-toggle="modal"
                                data-bs-target="#warningModal">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <a class="fa fa-folder"></a>
                            <a href="" class="me-4 ms-2">
                                <strong>Contratto di subappalto impresa</strong>
                            </a><br>
                            <small>Firmato - con allegato lavorazioni</small>
                        </td>
                        <td>
                            <span class="badge bg-danger">0</span>
                        </td>
                        <td class="hideInMobile"></td>
                        <td class="hideInMobile"></td>
                        <td class="space"></td>
                        <td>
                            <button type="button" class="btn btn-link btn-sm text-dark">
                                <i class="fa fa-download"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-warning" data-bs-toggle="modal"
                                data-bs-target="#bellBtnModal">
                                <i class="fa fa-bell"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-danger" data-bs-toggle="modal"
                                data-bs-target="#warningModal">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <a class="fa fa-folder"></a>
                            <a href="" class="me-4 ms-2">
                                <strong>DICO</strong>
                            </a><br>
                            <small>Completo di impaginazione, timbro</small>
                        </td>
                        <td>
                            <span class="badge bg-danger">0</span>
                        </td>
                        <td class="hideInMobile"></td>
                        <td class="hideInMobile"></td>
                        <td class="space"></td>
                        <td>
                            <button type="button" class="btn btn-link btn-sm text-dark">
                                <i class="fa fa-download"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-warning" data-bs-toggle="modal"
                                data-bs-target="#bellBtnModal">
                                <i class="fa fa-bell"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-danger" data-bs-toggle="modal"
                                data-bs-target="#warningModal">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <a class="fa fa-folder"></a>
                            <a href="" class="me-4 ms-2">
                                <strong>Documenti 50</strong>
                            </a><br>
                            <small>Completa e firmata</small>
                        </td>
                        <td>
                            <span class="badge bg-danger">0</span>
                        </td>
                        <td class="hideInMobile"></td>
                        <td class="hideInMobile"></td>
                        <td class="space"></td>
                        <td>
                            <button type="button" class="btn btn-link btn-sm text-dark">
                                <i class="fa fa-download"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-warning" data-bs-toggle="modal"
                                data-bs-target="#bellBtnModal">
                                <i class="fa fa-bell"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-danger" data-bs-toggle="modal"
                                data-bs-target="#warningModal">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <a class="fa fa-folder"></a>
                            <a href="" class="me-4 ms-2">
                                <strong>Documenti Clienti</strong>
                            </a><br>
                            <small>Atto di provenienza, carta d'identità, codice fiscale e visura catastale</small>
                        </td>
                        <td>
                            <span class="badge bg-danger">0</span>
                        </td>
                        <td class="hideInMobile"></td>
                        <td class="hideInMobile"></td>
                        <td class="space"></td>
                        <td>
                            <button type="button" class="btn btn-link btn-sm text-dark">
                                <i class="fa fa-download"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-warning" data-bs-toggle="modal"
                                data-bs-target="#bellBtnModal">
                                <i class="fa fa-bell"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-danger" data-bs-toggle="modal"
                                data-bs-target="#warningModal">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <a class="fa fa-folder"></a>
                            <a href="" class="me-4 ms-2">
                                <strong>Documenti Co-intestatari</strong>
                            </a><br>
                            <small>Carta d'identità e consenso lavori</small>
                        </td>
                        <td>
                            <span class="badge bg-danger">0</span>
                        </td>
                        <td class="hideInMobile"></td>
                        <td class="hideInMobile"></td>
                        <td class="space"></td>
                        <td>
                            <button type="button" class="btn btn-link btn-sm text-dark">
                                <i class="fa fa-download"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-warning" data-bs-toggle="modal"
                                data-bs-target="#bellBtnModal">
                                <i class="fa fa-bell"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-danger" data-bs-toggle="modal"
                                data-bs-target="#warningModal">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <a class="fa fa-folder"></a>
                            <a href="" class="me-4 ms-2">
                                <strong>Documenti Conformità</strong>
                            </a><br>
                        </td>
                        <td>
                            <span class="badge bg-danger">0</span>
                        </td>
                        <td class="hideInMobile"></td>
                        <td class="hideInMobile"></td>
                        <td class="space"></td>
                        <td>
                            <button type="button" class="btn btn-link btn-sm text-dark">
                                <i class="fa fa-download"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-warning" data-bs-toggle="modal"
                                data-bs-target="#bellBtnModal">
                                <i class="fa fa-bell"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-danger" data-bs-toggle="modal"
                                data-bs-target="#warningModal">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <a class="fa fa-folder"></a>
                            <a href="" class="me-4 ms-2">
                                <strong>Documenti Fine Lavori</strong>
                            </a><br>
                            <small>Verbali consegna chiavetta e lavori, Sopralluogo fine lavori...</small>
                        </td>
                        <td>
                            <span class="badge bg-danger">0</span>
                        </td>
                        <td class="hideInMobile"></td>
                        <td class="hideInMobile"></td>
                        <td class="space"></td>
                        <td>
                            <button type="button" class="btn btn-link btn-sm text-dark">
                                <i class="fa fa-download"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-warning" data-bs-toggle="modal"
                                data-bs-target="#bellBtnModal">
                                <i class="fa fa-bell"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-danger" data-bs-toggle="modal"
                                data-bs-target="#warningModal">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <a class="fa fa-folder"></a>
                            <a href="" class="me-4 ms-2">
                                <strong>Documenti Libretto Impianti</strong>
                            </a><br>
                            <small>e catasto impianti</small>
                        </td>
                        <td>
                            <span class="badge bg-success">1</span>
                        </td>
                        <td class="hideInMobile">09/12/22</td>
                        <td class="hideInMobile">PASQUALE</td>
                        <td class="space"></td>
                        <td>
                            <button type="button" class="btn btn-link btn-sm text-dark">
                                <i class="fa fa-download"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-warning" data-bs-toggle="modal"
                                data-bs-target="#bellBtnModal">
                                <i class="fa fa-bell"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-danger" data-bs-toggle="modal"
                                data-bs-target="#warningModal">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <a class="fa fa-folder"></a>
                            <a href="" class="me-4 ms-2">
                                <strong>Documenti Rilevanti</strong>
                            </a><br>
                            <small>Documenti condivisi rilevanti</small>
                        </td>
                        <td>
                            <span class="badge bg-danger">0</span>
                        </td>
                        <td class="hideInMobile"></td>
                        <td class="hideInMobile"></td>
                        <td class="space"></td>
                        <td>
                            <button type="button" class="btn btn-link btn-sm text-dark">
                                <i class="fa fa-download"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-warning" data-bs-toggle="modal"
                                data-bs-target="#bellBtnModal">
                                <i class="fa fa-bell"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-danger" data-bs-toggle="modal"
                                data-bs-target="#warningModal">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <a class="fa fa-folder"></a>
                            <a href="" class="me-4 ms-2">
                                <strong>Documenti Rilievo</strong>
                            </a><br>
                            <small>Scheda dati ante opera e interventi</small>
                        </td>
                        <td>
                            <span class="badge bg-danger">0</span>
                        </td>
                        <td class="hideInMobile"></td>
                        <td class="hideInMobile"></td>
                        <td class="space"></td>
                        <td>
                            <button type="button" class="btn btn-link btn-sm text-dark">
                                <i class="fa fa-download"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-warning" data-bs-toggle="modal"
                                data-bs-target="#bellBtnModal">
                                <i class="fa fa-bell"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-danger" data-bs-toggle="modal"
                                data-bs-target="#warningModal">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <a class="fa fa-folder"></a>
                            <a href="" class="me-4 ms-2">
                                <strong>Documenti Sicurezza</strong>
                            </a><br>
                            <small>PSC, POS e allegati</small>
                        </td>
                        <td>
                            <span class="badge bg-danger">0</span>
                        </td>
                        <td class="hideInMobile"></td>
                        <td class="hideInMobile"></td>
                        <td class="space"></td>
                        <td>
                            <button type="button" class="btn btn-link btn-sm text-dark">
                                <i class="fa fa-download"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-warning" data-bs-toggle="modal"
                                data-bs-target="#bellBtnModal">
                                <i class="fa fa-bell"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-danger" data-bs-toggle="modal"
                                data-bs-target="#warningModal">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <a class="fa fa-folder"></a>
                            <a href="" class="me-4 ms-2">
                                <strong>Documentazione Varia</strong>
                            </a><br>
                        </td>
                        <td>
                            <span class="badge bg-danger">0</span>
                        </td>
                        <td class="hideInMobile"></td>
                        <td class="hideInMobile"></td>
                        <td class="space"></td>
                        <td>
                            <button type="button" class="btn btn-link btn-sm text-dark">
                                <i class="fa fa-download"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-warning" data-bs-toggle="modal"
                                data-bs-target="#bellBtnModal">
                                <i class="fa fa-bell"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-danger" data-bs-toggle="modal"
                                data-bs-target="#warningModal">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <a class="fa fa-folder"></a>
                            <a href="" class="me-4 ms-2">
                                <strong>Schemi Impianti</strong>
                            </a><br>
                            <small>Pianta lastrico solare e pianta imp. Termico</small>
                        </td>
                        <td>
                            <span class="badge bg-danger">0</span>
                        </td>
                        <td class="hideInMobile"></td>
                        <td class="hideInMobile"></td>
                        <td class="space"></td>
                        <td>
                            <button type="button" class="btn btn-link btn-sm text-dark">
                                <i class="fa fa-download"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-warning" data-bs-toggle="modal"
                                data-bs-target="#bellBtnModal">
                                <i class="fa fa-bell"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-danger" data-bs-toggle="modal"
                                data-bs-target="#warningModal">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="tab-pane fade" id="filterTab2">
            <table class="table document-table table-hover">
                <thead>
                    <tr>
                        <th scope="col">Nome Documento</th>
                        <th scope="col">Stato</th>
                        <th scope="col" class="hideInMobile">Aggiornato il</th>
                        <th scope="col" class="hideInMobile">Aggiornato Da</th>
                        <th scope="col" class=""></th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <a class="fa fa-folder"></a>
                            <a href="" class="me-4 ms-2">
                                <strong>Documenti 50</strong>
                            </a><br>
                            <small>Completa e firmata</small>
                        </td>
                        <td>
                            <span class="badge bg-danger">0</span>
                        </td>
                        <td class="hideInMobile"></td>
                        <td class="hideInMobile"></td>
                        <td class="space"></td>
                        <td>
                            <button type="button" class="btn btn-link btn-sm text-dark">
                                <i class="fa fa-download"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-warning" data-bs-toggle="modal"
                                data-bs-target="#bellBtnModal">
                                <i class="fa fa-bell"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-danger" data-bs-toggle="modal"
                                data-bs-target="#warningModal">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <a class="fa fa-folder"></a>
                            <a href="" class="me-4 ms-2">
                                <strong>Schemi Impianti</strong>
                            </a><br>
                            <small>Pianta lastrico solare e pianta imp. Termico</small>
                        </td>
                        <td>
                            <span class="badge bg-danger">0</span>
                        </td>
                        <td class="hideInMobile"></td>
                        <td class="hideInMobile"></td>
                        <td class="space"></td>
                        <td>
                            <button type="button" class="btn btn-link btn-sm text-dark">
                                <i class="fa fa-download"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-warning" data-bs-toggle="modal"
                                data-bs-target="#bellBtnModal">
                                <i class="fa fa-bell"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-danger" data-bs-toggle="modal"
                                data-bs-target="#warningModal">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="tab-pane fade" id="filterTab3">
            <table class="table document-table table-hover">
                <thead>
                    <tr>
                        <th scope="col">Nome Documento</th>
                        <th scope="col">Stato</th>
                        <th scope="col" class="hideInMobile">Aggiornato il</th>
                        <th scope="col" class="hideInMobile">Aggiornato Da</th>
                        <th scope="col" class=""></th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <button type="button" class="btn text-start">
                                <i class="fa fa-file-o"></i>
                                <strong class="me-4 ms-2">Atto di Provenienza</strong>
                            </button><br>
                            <small>Dell'immobile (NO NOTA DI TRASCRIZIONE)</small>
                        </td>
                        <td>
                            <span class="badge bg-danger hideInDesktop">0/0</span>
                            <span class="badge bg-danger hideInMobile">MANCANTE</span>
                        </td>
                        <td class="hideInMobile"></td>
                        <td class="hideInMobile"></td>
                        <td>
                            <input type="file" autocomplete="off" class="form-control file-uploader">
                        </td>
                        <td>
                            <button type="button" class="btn btn-link btn-sm text-dark">
                                <i class="fa fa-download"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-dark" data-bs-toggle="modal"
                                data-bs-target="#replaceDocModal">
                                <i class="fa fa-exchange"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-warning" data-bs-toggle="modal"
                                data-bs-target="#bellBtnModal">
                                <i class="fa fa-bell"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-danger" data-bs-toggle="modal"
                                data-bs-target="#warningModal">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <button type="button" class="btn text-start">
                                <i class="fa fa-file-o"></i>
                                <strong class="me-4 ms-2">Bolletta Luce</strong>
                            </button><br>
                            <small>Dell'immobile (prime 2 pagine)</small>
                        </td>
                        <td>
                            <span class="badge bg-danger hideInDesktop">0/0</span>
                            <span class="badge bg-danger hideInMobile">MANCANTE</span>
                        </td>
                        <td class="hideInMobile"></td>
                        <td class="hideInMobile"></td>
                        <td>
                            <input type="file" autocomplete="off" class="form-control file-uploader">
                        </td>
                        <td>
                            <button type="button" class="btn btn-link btn-sm text-dark">
                                <i class="fa fa-download"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-dark" data-bs-toggle="modal"
                                data-bs-target="#replaceDocModal">
                                <i class="fa fa-exchange"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-warning" data-bs-toggle="modal"
                                data-bs-target="#bellBtnModal">
                                <i class="fa fa-bell"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-danger" data-bs-toggle="modal"
                                data-bs-target="#warningModal">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <button type="button" class="btn text-start">
                                <i class="fa fa-file-o"></i>
                                <strong class="me-4 ms-2">Carta d'identità</strong>
                            </button><br>
                            <small>(Fronte - retro) in corso di validità</small>
                        </td>
                        <td>
                            <span class="badge bg-danger hideInDesktop">0/0</span>
                            <span class="badge bg-danger hideInMobile">MANCANTE</span>
                        </td>
                        <td class="hideInMobile"></td>
                        <td class="hideInMobile"></td>
                        <td>
                            <input type="file" autocomplete="off" class="form-control file-uploader">
                        </td>
                        <td>
                            <button type="button" class="btn btn-link btn-sm text-dark">
                                <i class="fa fa-download"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-dark" data-bs-toggle="modal"
                                data-bs-target="#replaceDocModal">
                                <i class="fa fa-exchange"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-warning" data-bs-toggle="modal"
                                data-bs-target="#bellBtnModal">
                                <i class="fa fa-bell"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-danger" data-bs-toggle="modal"
                                data-bs-target="#warningModal">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <button type="button" class="btn text-start">
                                <i class="fa fa-file-o"></i>
                                <strong class="me-4 ms-2">Carta d'identità intestatario bollette</strong>
                            </button><br>
                            <small>(Fronte - retro) in corso di validità</small>
                        </td>
                        <td>
                            <span class="badge bg-danger hideInDesktop">0/0</span>
                            <span class="badge bg-danger hideInMobile">MANCANTE</span>
                        </td>
                        <td class="hideInMobile"></td>
                        <td class="hideInMobile"></td>
                        <td>
                            <input type="file" autocomplete="off" class="form-control file-uploader">
                        </td>
                        <td>
                            <button type="button" class="btn btn-link btn-sm text-dark">
                                <i class="fa fa-download"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-dark" data-bs-toggle="modal"
                                data-bs-target="#replaceDocModal">
                                <i class="fa fa-exchange"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-warning" data-bs-toggle="modal"
                                data-bs-target="#bellBtnModal">
                                <i class="fa fa-bell"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-danger" data-bs-toggle="modal"
                                data-bs-target="#warningModal">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <button type="button" class="btn text-start">
                                <i class="fa fa-file-o"></i>
                                <strong class="me-4 ms-2">Cila Protocollata 50-65-90</strong>
                            </button><br>
                            <small>Unico file completo ufficiale</small>
                        </td>
                        <td>
                            <span class="badge bg-danger hideInDesktop">0/0</span>
                            <span class="badge bg-danger hideInMobile">MANCANTE</span>
                        </td>
                        <td class="hideInMobile"></td>
                        <td class="hideInMobile"></td>
                        <td>
                            <input type="file" autocomplete="off" class="form-control file-uploader">
                        </td>
                        <td>
                            <button type="button" class="btn btn-link btn-sm text-dark">
                                <i class="fa fa-download"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-dark" data-bs-toggle="modal"
                                data-bs-target="#replaceDocModal">
                                <i class="fa fa-exchange"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-warning" data-bs-toggle="modal"
                                data-bs-target="#bellBtnModal">
                                <i class="fa fa-bell"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-danger" data-bs-toggle="modal"
                                data-bs-target="#warningModal">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <button type="button" class="btn text-start">
                                <i class="fa fa-file-o"></i>
                                <strong class="me-4 ms-2">Codice Fiscale</strong>
                            </button><br>
                            <small>(Fronte - retro) in corso di validità</small>
                        </td>
                        <td>
                            <span class="badge bg-danger hideInDesktop">0/0</span>
                            <span class="badge bg-danger hideInMobile">MANCANTE</span>
                        </td>
                        <td class="hideInMobile"></td>
                        <td class="hideInMobile"></td>
                        <td>
                            <input type="file" autocomplete="off" class="form-control file-uploader">
                        </td>
                        <td>
                            <button type="button" class="btn btn-link btn-sm text-dark">
                                <i class="fa fa-download"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-dark" data-bs-toggle="modal"
                                data-bs-target="#replaceDocModal">
                                <i class="fa fa-exchange"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-warning" data-bs-toggle="modal"
                                data-bs-target="#bellBtnModal">
                                <i class="fa fa-bell"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-danger" data-bs-toggle="modal"
                                data-bs-target="#warningModal">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <button type="button" class="btn text-start">
                                <i class="fa fa-file-o"></i>
                                <strong class="me-4 ms-2">Estratto di Mappa</strong><br>
                                <small>Aggiornato</small>
                            </button>
                        </td>
                        <td>
                            <span class="badge bg-danger hideInDesktop">0/0</span>
                            <span class="badge bg-danger hideInMobile">MANCANTE</span>
                        </td>
                        <td class="hideInMobile"></td>
                        <td class="hideInMobile"></td>
                        <td>
                            <input type="file" autocomplete="off" class="form-control file-uploader">
                        </td>
                        <td>
                            <button type="button" class="btn btn-link btn-sm text-dark">
                                <i class="fa fa-download"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-dark" data-bs-toggle="modal"
                                data-bs-target="#replaceDocModal">
                                <i class="fa fa-exchange"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-warning" data-bs-toggle="modal"
                                data-bs-target="#bellBtnModal">
                                <i class="fa fa-bell"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-danger" data-bs-toggle="modal"
                                data-bs-target="#warningModal">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <button type="button" class="btn text-start">
                                <i class="fa fa-file-o"></i>
                                <strong class="me-4 ms-2">Legge 10</strong>
                            </button>
                        </td>
                        <td>
                            <span class="badge bg-danger hideInDesktop">0/0</span>
                            <span class="badge bg-danger hideInMobile">MANCANTE</span>
                        </td>
                        <td class="hideInMobile"></td>
                        <td class="hideInMobile"></td>
                        <td>
                            <input type="file" autocomplete="off" class="form-control file-uploader">
                        </td>
                        <td>
                            <button type="button" class="btn btn-link btn-sm text-dark">
                                <i class="fa fa-download"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-dark" data-bs-toggle="modal"
                                data-bs-target="#replaceDocModal">
                                <i class="fa fa-exchange"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-warning" data-bs-toggle="modal"
                                data-bs-target="#bellBtnModal">
                                <i class="fa fa-bell"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-danger" data-bs-toggle="modal"
                                data-bs-target="#warningModal">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <button type="button" class="btn text-start">
                                <i class="fa fa-file-o"></i>
                                <strong class="me-4 ms-2">Notifica Preliminare</strong>
                            </button><br>
                            <small>Con GREENGEN appaltatrice + TUTTE le aziende presenti in cantiere</small>
                        </td>
                        <td>
                            <span class="badge bg-danger hideInDesktop">0/0</span>
                            <span class="badge bg-danger hideInMobile">MANCANTE</span>
                        </td>
                        <td class="hideInMobile"></td>
                        <td class="hideInMobile"></td>
                        <td>
                            <input type="file" autocomplete="off" class="form-control file-uploader">
                        </td>
                        <td>
                            <button type="button" class="btn btn-link btn-sm text-dark">
                                <i class="fa fa-download"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-dark" data-bs-toggle="modal"
                                data-bs-target="#replaceDocModal">
                                <i class="fa fa-exchange"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-warning" data-bs-toggle="modal"
                                data-bs-target="#bellBtnModal">
                                <i class="fa fa-bell"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-danger" data-bs-toggle="modal"
                                data-bs-target="#warningModal">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <button type="button" class="btn text-start">
                                <i class="fa fa-file-o"></i>
                                <strong class="me-4 ms-2">Protocollo Cila 50-65-90</strong>
                            </button><br>
                            <small>No foto - solo doc ufficiale (PEC o ricevuta)</small>
                        </td>
                        <td>
                            <span class="badge bg-danger hideInDesktop">0/0</span>
                            <span class="badge bg-danger hideInMobile">MANCANTE</span>
                        </td>
                        <td class="hideInMobile"></td>
                        <td class="hideInMobile"></td>
                        <td>
                            <input type="file" autocomplete="off" class="form-control file-uploader">
                        </td>
                        <td>
                            <button type="button" class="btn btn-link btn-sm text-dark">
                                <i class="fa fa-download"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-dark" data-bs-toggle="modal"
                                data-bs-target="#replaceDocModal">
                                <i class="fa fa-exchange"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-warning" data-bs-toggle="modal"
                                data-bs-target="#bellBtnModal">
                                <i class="fa fa-bell"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-danger" data-bs-toggle="modal"
                                data-bs-target="#warningModal">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <button type="button" class="btn text-start">
                                <i class="fa fa-file-o"></i>
                                <strong class="me-4 ms-2">Sopralluogo fine lavori</strong>
                            </button>
                        </td>
                        <td>
                            <span class="badge bg-danger hideInDesktop">0</span>
                            <span class="badge bg-danger hideInMobile">MANCANTE</span>
                        </td>
                        <td class="hideInMobile"></td>
                        <td class="hideInMobile"></td>
                        <td>
                            <input type="file" autocomplete="off" class="form-control file-uploader">
                        </td>
                        <td>
                            <button type="button" class="btn btn-link btn-sm text-dark">
                                <i class="fa fa-download"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-dark" data-bs-toggle="modal"
                                data-bs-target="#replaceDocModal">
                                <i class="fa fa-exchange"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-warning" data-bs-toggle="modal"
                                data-bs-target="#bellBtnModal">
                                <i class="fa fa-bell"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-danger" data-bs-toggle="modal"
                                data-bs-target="#warningModal">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <button type="button" class="btn text-start">
                                <i class="fa fa-file-o"></i>
                                <strong class="me-4 ms-2">Visura Catastale</strong>
                            </button><br>
                            <small>Aggiornata</small>
                        </td>
                        <td>
                            <span class="badge bg-danger hideInDesktop">0/0</span>
                            <span class="badge bg-danger hideInMobile">MANCANTE</span>
                        </td>
                        <td class="hideInMobile"></td>
                        <td class="hideInMobile"></td>
                        <td>
                            <input type="file" autocomplete="off" class="form-control file-uploader">
                        </td>
                        <td>
                            <button type="button" class="btn btn-link btn-sm text-dark">
                                <i class="fa fa-download"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-dark" data-bs-toggle="modal"
                                data-bs-target="#replaceDocModal">
                                <i class="fa fa-exchange"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-warning" data-bs-toggle="modal"
                                data-bs-target="#bellBtnModal">
                                <i class="fa fa-bell"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-danger" data-bs-toggle="modal"
                                data-bs-target="#warningModal">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>

        <div class="tab-pane fade" id="filterTab4">
            <table class="table document-table table-hover">
                <thead>
                    <tr>
                        <th scope="col">Nome Documento</th>
                        <th scope="col">Stato</th>
                        <th scope="col" class="hideInMobile">Aggiornato il</th>
                        <th scope="col" class="hideInMobile">Aggiornato Da</th>
                        <th scope="col" class=""></th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <a class="fa fa-folder"></a>
                            <a href="" class="me-4 ms-2">
                                <strong>Asseverazione SAL 50</strong>
                            </a><br>
                            <small>Documento ufficiale</small>
                        </td>
                        <td>
                            <span class="badge bg-danger">0</span>
                        </td>
                        <td class="hideInMobile"></td>
                        <td class="hideInMobile"></td>
                        <td></td>
                        <td>
                            <button type="button" class="btn btn-link btn-sm text-dark">
                                <i class="fa fa-download"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-warning" data-bs-toggle="modal"
                                data-bs-target="#bellBtnModal">
                                <i class="fa fa-bell"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-danger" data-bs-toggle="modal"
                                data-bs-target="#warningModal">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <a class="fa fa-folder"></a>
                            <a href="" class="me-4 ms-2">
                                <strong>Asseverazione SALDO 50</strong>
                            </a><br>
                            <small>Documento ufficiale</small>
                        </td>
                        <td>
                            <span class="badge bg-danger">0</span>
                        </td>
                        <td class="hideInMobile"></td>
                        <td class="hideInMobile"></td>
                        <td></td>
                        <td>
                            <button type="button" class="btn btn-link btn-sm text-dark">
                                <i class="fa fa-download"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-warning" data-bs-toggle="modal"
                                data-bs-target="#bellBtnModal">
                                <i class="fa fa-bell"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-danger" data-bs-toggle="modal"
                                data-bs-target="#warningModal">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <a class="fa fa-folder"></a>
                            <a href="" class="me-4 ms-2">
                                <strong>Computo SALDO 50</strong>
                            </a><br>
                            <small>Completo di impaginazione, timbro e riepilogo SAL</small>
                        </td>
                        <td>
                            <span class="badge bg-danger">0</span>
                        </td>
                        <td class="hideInMobile"></td>
                        <td class="hideInMobile"></td>
                        <td></td>
                        <td>
                            <button type="button" class="btn btn-link btn-sm text-dark">
                                <i class="fa fa-download"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-warning" data-bs-toggle="modal"
                                data-bs-target="#bellBtnModal">
                                <i class="fa fa-bell"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-danger" data-bs-toggle="modal"
                                data-bs-target="#warningModal">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <a class="fa fa-folder"></a>
                            <a href="" class="me-4 ms-2">
                                <strong>Fattura SAL 50</strong>
                            </a><br>
                            <small>Completo di impaginazione, timbro</small>
                        </td>
                        <td>
                            <span class="badge bg-danger">0</span>
                        </td>
                        <td class="hideInMobile"></td>
                        <td class="hideInMobile"></td>
                        <td></td>
                        <td>
                            <button type="button" class="btn btn-link btn-sm text-dark">
                                <i class="fa fa-download"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-warning" data-bs-toggle="modal"
                                data-bs-target="#bellBtnModal">
                                <i class="fa fa-bell"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-danger" data-bs-toggle="modal"
                                data-bs-target="#warningModal">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <a class="fa fa-folder"></a>
                            <a href="" class="me-4 ms-2">
                                <strong>Fattura SALDO 50</strong>
                            </a><br>
                            <small>Fattura ufficiale</small>
                        </td>
                        <td>
                            <span class="badge bg-danger">0</span>
                        </td>
                        <td class="hideInMobile"></td>
                        <td class="hideInMobile"></td>
                        <td></td>
                        <td>
                            <button type="button" class="btn btn-link btn-sm text-dark">
                                <i class="fa fa-download"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-warning" data-bs-toggle="modal"
                                data-bs-target="#bellBtnModal">
                                <i class="fa fa-bell"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-danger" data-bs-toggle="modal"
                                data-bs-target="#warningModal">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <a class="fa fa-folder"></a>
                            <a href="" class="me-4 ms-2">
                                <strong>Schemi Impianti</strong>
                            </a><br>
                            <small>Pianta lastrico solare e pianta imp. Termico</small>
                        </td>
                        <td>
                            <span class="badge bg-danger">0</span>
                        </td>
                        <td class="hideInMobile"></td>
                        <td class="hideInMobile"></td>
                        <td></td>
                        <td>
                            <button type="button" class="btn btn-link btn-sm text-dark">
                                <i class="fa fa-download"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-warning" data-bs-toggle="modal"
                                data-bs-target="#bellBtnModal">
                                <i class="fa fa-bell"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-danger" data-bs-toggle="modal"
                                data-bs-target="#warningModal">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <button type="button" class="btn text-start">
                                <i class="fa fa-file-o"></i>
                                <strong class="me-4 ms-2">Ape regionale</strong>
                            </button><br>
                            <small>Documento ufficiale</small>
                        </td>
                        <td>
                            <span class="badge bg-danger hideInDesktop">0</span>
                            <span class="badge bg-danger hideInMobile">MANCANTE</span>
                        </td>
                        <td class="hideInMobile"></td>
                        <td class="hideInMobile"></td>
                        <td>
                            <input type="file" autocomplete="off" class="form-control file-uploader">
                        </td>
                        <td>
                            <button type="button" class="btn btn-link btn-sm text-dark">
                                <i class="fa fa-download"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-dark" data-bs-toggle="modal"
                                data-bs-target="#replaceDocModal">
                                <i class="fa fa-exchange"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-warning" data-bs-toggle="modal"
                                data-bs-target="#bellBtnModal">
                                <i class="fa fa-bell"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-danger" data-bs-toggle="modal"
                                data-bs-target="#warningModal">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <button type="button" class="btn text-start">
                                <i class="fa fa-file-o"></i>
                                <strong class="me-4 ms-2">Carta d'identità</strong>
                            </button><br>
                            <small>(Fronte - retro) in corso di validità</small>
                        </td>
                        <td>
                            <span class="badge bg-danger hideInDesktop">0/0</span>
                            <span class="badge bg-danger hideInMobile">MANCANTE</span>
                        </td>
                        <td class="hideInMobile"></td>
                        <td class="hideInMobile"></td>
                        <td>
                            <input type="file" autocomplete="off" class="form-control file-uploader">
                        </td>
                        <td>
                            <button type="button" class="btn btn-link btn-sm text-dark">
                                <i class="fa fa-download"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-dark" data-bs-toggle="modal"
                                data-bs-target="#replaceDocModal">
                                <i class="fa fa-exchange"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-warning" data-bs-toggle="modal"
                                data-bs-target="#bellBtnModal">
                                <i class="fa fa-bell"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-danger" data-bs-toggle="modal"
                                data-bs-target="#warningModal">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <button type="button" class="btn text-start">
                                <i class="fa fa-file-o"></i>
                                <strong class="me-4 ms-2">Cila Protocollata 50-65-90</strong>
                            </button><br>
                            <small>Unico file completo ufficiale</small>
                        </td>
                        <td>
                            <span class="badge bg-danger hideInDesktop">0</span>
                            <span class="badge bg-danger hideInMobile">MANCANTE</span>
                        </td>
                        <td class="hideInMobile"></td>
                        <td class="hideInMobile"></td>
                        <td>
                            <input type="file" autocomplete="off" class="form-control file-uploader">
                        </td>
                        <td>
                            <button type="button" class="btn btn-link btn-sm text-dark">
                                <i class="fa fa-download"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-dark" data-bs-toggle="modal"
                                data-bs-target="#replaceDocModal">
                                <i class="fa fa-exchange"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-warning" data-bs-toggle="modal"
                                data-bs-target="#bellBtnModal">
                                <i class="fa fa-bell"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-danger" data-bs-toggle="modal"
                                data-bs-target="#warningModal">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <button type="button" class="btn text-start">
                                <i class="fa fa-file-o"></i>
                                <strong class="me-4 ms-2">Contratto 50</strong>
                            </button>
                        </td>
                        <td>
                            <span class="badge bg-danger hideInDesktop">0</span>
                            <span class="badge bg-danger hideInMobile">MANCANTE</span>
                        </td>
                        <td class="hideInMobile"></td>
                        <td class="hideInMobile"></td>
                        <td>
                            <input type="file" autocomplete="off" class="form-control file-uploader">
                        </td>
                        <td>
                            <button type="button" class="btn btn-link btn-sm text-dark">
                                <i class="fa fa-download"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-dark" data-bs-toggle="modal"
                                data-bs-target="#replaceDocModal">
                                <i class="fa fa-exchange"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-warning" data-bs-toggle="modal"
                                data-bs-target="#bellBtnModal">
                                <i class="fa fa-bell"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-danger" data-bs-toggle="modal"
                                data-bs-target="#warningModal">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <button type="button" class="btn text-start">
                                <i class="fa fa-file-o"></i>
                                <strong class="me-4 ms-2">DICO Impianto elettrico</strong>
                            </button>
                        </td>
                        <td>
                            <span class="badge bg-danger hideInDesktop">0</span>
                            <span class="badge bg-danger hideInMobile">MANCANTE</span>
                        </td>
                        <td class="hideInMobile"></td>
                        <td class="hideInMobile"></td>
                        <td>
                            <input type="file" autocomplete="off" class="form-control file-uploader">
                        </td>
                        <td>
                            <button type="button" class="btn btn-link btn-sm text-dark">
                                <i class="fa fa-download"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-dark" data-bs-toggle="modal"
                                data-bs-target="#replaceDocModal">
                                <i class="fa fa-exchange"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-warning" data-bs-toggle="modal"
                                data-bs-target="#bellBtnModal">
                                <i class="fa fa-bell"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-danger" data-bs-toggle="modal"
                                data-bs-target="#warningModal">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <button type="button" class="btn text-start">
                                <i class="fa fa-file-o"></i>
                                <strong class="me-4 ms-2">DICO Impianto idrico-fognante</strong>
                            </button>
                        </td>
                        <td>
                            <span class="badge bg-danger hideInDesktop">0</span>
                            <span class="badge bg-danger hideInMobile">MANCANTE</span>
                        </td>
                        <td class="hideInMobile"></td>
                        <td class="hideInMobile"></td>
                        <td>
                            <input type="file" autocomplete="off" class="form-control file-uploader">
                        </td>
                        <td>
                            <button type="button" class="btn btn-link btn-sm text-dark">
                                <i class="fa fa-download"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-dark" data-bs-toggle="modal"
                                data-bs-target="#replaceDocModal">
                                <i class="fa fa-exchange"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-warning" data-bs-toggle="modal"
                                data-bs-target="#bellBtnModal">
                                <i class="fa fa-bell"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-danger" data-bs-toggle="modal"
                                data-bs-target="#warningModal">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <button type="button" class="btn text-start">
                                <i class="fa fa-file-o"></i>
                                <strong class="me-4 ms-2">DICO Impianto termico</strong>
                            </button>
                        </td>
                        <td>
                            <span class="badge bg-danger hideInDesktop">0</span>
                            <span class="badge bg-danger hideInMobile">MANCANTE</span>
                        </td>
                        <td class="hideInMobile"></td>
                        <td class="hideInMobile"></td>
                        <td>
                            <input type="file" autocomplete="off" class="form-control file-uploader">
                        </td>
                        <td>
                            <button type="button" class="btn btn-link btn-sm text-dark">
                                <i class="fa fa-download"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-dark" data-bs-toggle="modal"
                                data-bs-target="#replaceDocModal">
                                <i class="fa fa-exchange"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-warning" data-bs-toggle="modal"
                                data-bs-target="#bellBtnModal">
                                <i class="fa fa-bell"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-danger" data-bs-toggle="modal"
                                data-bs-target="#warningModal">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <button type="button" class="btn text-start">
                                <i class="fa fa-file-o"></i>
                                <strong class="me-4 ms-2">Legge 10</strong>
                            </button>
                        </td>
                        <td>
                            <span class="badge bg-danger hideInDesktop">0/0</span>
                            <span class="badge bg-danger hideInMobile">MANCANTE</span>
                        </td>
                        <td class="hideInMobile"></td>
                        <td class="hideInMobile"></td>
                        <td>
                            <input type="file" autocomplete="off" class="form-control file-uploader">
                        </td>
                        <td>
                            <button type="button" class="btn btn-link btn-sm text-dark">
                                <i class="fa fa-download"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-dark" data-bs-toggle="modal"
                                data-bs-target="#replaceDocModal">
                                <i class="fa fa-exchange"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-warning" data-bs-toggle="modal"
                                data-bs-target="#bellBtnModal">
                                <i class="fa fa-bell"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-danger" data-bs-toggle="modal"
                                data-bs-target="#warningModal">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <button type="button" class="btn text-start">
                                <i class="fa fa-file-o"></i>
                                <strong class="me-4 ms-2">Libretto impianti ante</strong>
                            </button><br>
                            <small>Se presente o redatto da Greengen</small>
                        </td>
                        <td>
                            <span class="badge bg-danger hideInDesktop">0/0</span>
                            <span class="badge bg-danger hideInMobile">MANCANTE</span>
                        </td>
                        <td class="hideInMobile"></td>
                        <td class="hideInMobile"></td>
                        <td>
                            <input type="file" autocomplete="off" class="form-control file-uploader">
                        </td>
                        <td>
                            <button type="button" class="btn btn-link btn-sm text-dark">
                                <i class="fa fa-download"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-dark" data-bs-toggle="modal"
                                data-bs-target="#replaceDocModal">
                                <i class="fa fa-exchange"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-warning" data-bs-toggle="modal"
                                data-bs-target="#bellBtnModal">
                                <i class="fa fa-bell"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-danger" data-bs-toggle="modal"
                                data-bs-target="#warningModal">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <button type="button" class="btn text-start">
                                <i class="fa fa-file-o"></i>
                                <strong class="me-4 ms-2">Libretto impianti post</strong>
                            </button>
                        </td>
                        <td>
                            <span class="badge bg-danger hideInDesktop">0</span>
                            <span class="badge bg-danger hideInMobile">MANCANTE</span>
                        </td>
                        <td class="hideInMobile"></td>
                        <td class="hideInMobile"></td>
                        <td>
                            <input type="file" autocomplete="off" class="form-control file-uploader">
                        </td>
                        <td>
                            <button type="button" class="btn btn-link btn-sm text-dark">
                                <i class="fa fa-download"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-dark" data-bs-toggle="modal"
                                data-bs-target="#replaceDocModal">
                                <i class="fa fa-exchange"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-warning" data-bs-toggle="modal"
                                data-bs-target="#bellBtnModal">
                                <i class="fa fa-bell"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-danger" data-bs-toggle="modal"
                                data-bs-target="#warningModal">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <button type="button" class="btn text-start">
                                <i class="fa fa-file-o"></i>
                                <strong class="me-4 ms-2">Protocollo Cila 50-65-90</strong>
                            </button><br>
                            <small>No foto - solo doc ufficiale (PEC o ricevuta)</small>
                        </td>
                        <td>
                            <span class="badge bg-danger hideInDesktop">0</span>
                            <span class="badge bg-danger hideInMobile">MANCANTE</span>
                        </td>
                        <td class="hideInMobile"></td>
                        <td class="hideInMobile"></td>
                        <td>
                            <input type="file" autocomplete="off" class="form-control file-uploader">
                        </td>
                        <td>
                            <button type="button" class="btn btn-link btn-sm text-dark">
                                <i class="fa fa-download"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-dark" data-bs-toggle="modal"
                                data-bs-target="#replaceDocModal">
                                <i class="fa fa-exchange"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-warning" data-bs-toggle="modal"
                                data-bs-target="#bellBtnModal">
                                <i class="fa fa-bell"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-danger" data-bs-toggle="modal"
                                data-bs-target="#warningModal">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <button type="button" class="btn text-start">
                                <i class="fa fa-file-o"></i>
                                <strong class="me-4 ms-2">Ricevuta Ape Regione</strong>
                            </button><br>
                            <small>Ricevuta invio Ape Regione</small>
                        </td>
                        <td>
                            <span class="badge bg-danger hideInDesktop">0</span>
                            <span class="badge bg-danger hideInMobile">MANCANTE</span>
                        </td>
                        <td class="hideInMobile"></td>
                        <td class="hideInMobile"></td>
                        <td>
                            <input type="file" autocomplete="off" class="form-control file-uploader">
                        </td>
                        <td>
                            <button type="button" class="btn btn-link btn-sm text-dark">
                                <i class="fa fa-download"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-dark" data-bs-toggle="modal"
                                data-bs-target="#replaceDocModal">
                                <i class="fa fa-exchange"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-warning" data-bs-toggle="modal"
                                data-bs-target="#bellBtnModal">
                                <i class="fa fa-bell"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-danger" data-bs-toggle="modal"
                                data-bs-target="#warningModal">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                    <tr>
                        <td>
                            <button type="button" class="btn text-start">
                                <i class="fa fa-file-o"></i>
                                <strong class="me-4 ms-2">Visura Catastale</strong>
                            </button><br>
                            <small>Aggiornata</small>
                        </td>
                        <td>
                            <span class="badge bg-danger hideInDesktop">0/0</span>
                            <span class="badge bg-danger hideInMobile">MANCANTE</span>
                        </td>
                        <td class="hideInMobile"></td>
                        <td class="hideInMobile"></td>
                        <td>
                            <input type="file" autocomplete="off" class="form-control file-uploader">
                        </td>
                        <td>
                            <button type="button" class="btn btn-link btn-sm text-dark">
                                <i class="fa fa-download"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-dark" data-bs-toggle="modal"
                                data-bs-target="#replaceDocModal">
                                <i class="fa fa-exchange"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-warning" data-bs-toggle="modal"
                                data-bs-target="#bellBtnModal">
                                <i class="fa fa-bell"></i>
                            </button>
                            <button type="button" class="btn btn-link btn-sm text-danger" data-bs-toggle="modal"
                                data-bs-target="#warningModal">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>
</div>
