<div class="modal fade" id="{{$modelId}}"
    aria-labelledby="exampleModalLabel" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog">
        <div class="modal-content send-email">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    <strong>Invia una email di sollecito</strong>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <form action="{{ route('reminder_emails') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p class="text-center">A chi vorresti inviare un sollecito?</p>
                    <p> <strong>FILE DI RIFERIMENTO:</strong> {{$folderName}}</p>

                    <input type="hidden" name="folder" value="{{$folderName}}">
                    <input type="hidden" name="conId" value="{{$conId}}">
                    <input type="email" class="form-control mt-3" name="to_mail" placeholder="Inserisci un indirizzo email">
                </div>
                <div class="modal-footer mb-3">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">Indietro</button>
                    <button type="submit" name="reset-pass"
                        class="btn btn-warning text-white">Invia</button>
                </div>
            </form>
        </div>
    </div>
</div>