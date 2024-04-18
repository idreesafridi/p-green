<x-front.links :pageTitle="$pageTitle" />
<x-front.navbar />
<x-sub-nav />

<div class="container mt-4 mb-4">
    <div class="row">
        <div class="col-md-12">
            {{ $slot }}
        </div>
    </div>
</div>

<!-- Notification Modal -->
<div class="modal fade" id="bellBtnModal" aria-labelledby="exampleModalLabel" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content send-email">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    <strong>Invia una email di sollecito</strong>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST">
                <div class="modal-body">
                    <p class="text-center m-0">A chi vorresti inviare un sollecito?</p>
                    <input type="email" name="email" class="form-control mt-5" placeholder="Inserisci un indirizzo email">
                </div>
                <div class="modal-footer mb-3">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Indietro</button>
                    <button type="submit" name="reset-pass" class="btn btn-warning text-white">Invia</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- End Notification Modal -->

<!-- Replace Document Modal -->
<div class="modal fade" id="replaceDocModal" aria-labelledby="exampleModalLabel" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content send-email">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    <strong>Sostituisci un documento</strong>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST">
                <div class="modal-body">
                    <div class="row mb-3 mt-5">
                        <img src="{{ asset('assets/images/swap-img.svg') }}" class="alert-img mx-auto">
                    </div>
                    <div class="mb-4">
                        <h6 class="text-center">Trascina qui sotto il documento oppure selezionalo dal tuo PC?</h6>
                    </div>
                    <div class="mb-4">
                        <input type="file" autocomplete="off" class="form-control file-uploader">
                    </div>
                </div>
                <div class="modal-footer mb-3">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Indietro</button>
                    <button type="submit" class="btn btn-green">Rimpiazza</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- End Replace Document Modal -->

<!-- Warning Modal -->
<div class="modal fade" id="warningModal" aria-labelledby="exampleModalLabel" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Attenzione</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="" method="POST">
                <div class="modal-body">
                    <p class="text-center m-0">Sei sicuro di voler procedere?</p>
                </div>
                <div class="modal-footer mb-3">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Indietro</button>
                    <button type="submit" name="reset-pass" class="btn btn-danger">Procedi</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- End Warning Modal -->

<x-front.footer_main />
<x-front.footer />

