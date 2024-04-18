@forelse ($notes as $note)
    @if ($note->status == 0)
        <div class="d-flex  mt-3 pt-3 border-top">
            <a class="btn btn-link btn-sm text-danger" onclick="return confirm('Are you sure?')"
                href="{{ route('destroy', $note->id) }}">
                <i class="fa fa-trash"></i></a>

            <button type="button" class="btn btn-link btn-sm text-warning"
                onclick="location.href='{{ route('click_on_start', $note->id) }}'">
                @if ($note->priority == 1)
                    <i class="fa fa-star"></i>
                @else
                    <i class="fa fa-star-o"></i>
                @endif
            </button>
            <div class="flex-fill ms-3 text-truncate">
                {{ $note->notes }}<br><br>
                <p>
                    <span class="autore"><strong>{{ $note->User->name }}</strong></span>
                    <small class="msg-time">{{ date('d/m/Y', strtotime($note->created_at)) }}</small>
                </p>
            </div>
        </div>

        <!-- Warning Modal -->
        <div class="modal fade" id="warningModal" aria-labelledby="exampleModalLabel" aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
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
    @endif
@empty
@endforelse
