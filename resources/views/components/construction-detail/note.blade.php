<div class="card-header p-4" id="note">
    <h5 class="font-weight-bold">NUOVA NOTA</h5>
    <form action="{{ route('note_store') }}" method="POST">
        @csrf
        <input type="hidden" name="construction_site_id" value="{{ request()->route()->id }}">
        <div class="form-floating mb-3 note">
            <textarea type="text" name="notes" class="form-control" style="height: 71px; color: grey !important;"></textarea>
            <label>Scrivi una nota qui...</label>
        </div>
        <button type="submit" class="btn btn-success">Pubblica</button>
        <button type="reset" class="btn btn-secondary">Cancella</button>
    </form>
</div>
<div class="card-body p-0 note">
    <div class="pt-5">
        <h5 class="font-weight-bold pt-2">NOTE</h5>
        <input id="searchNotes" class="form-control me-2" type="search" placeholder="Cerca tra le note"
            aria-label="Search" style="color: grey !important;">
    </div>

    <div id="response_note">
        {{-- @dd($notes); --}}
        @forelse ($notes as $note)
            @if ($note->status == 0)
                <div class="d-flex align-items-center mt-3 pt-3 border-top">
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
                        <span class="text-muted">{{ $note->notes }}</span><br><br>
                        <p>
                            <span
                                class="autore"><strong>{{ $note->User == null ? '' : $note->User->name }}</strong></span>
                            <small class="msg-time">{{ date('d/m/Y', strtotime($note->created_at)) }}</small>
                        </p>
                    </div>
                </div>

                <!-- Warning Modal -->
                <div class="modal fade" id="warningModal" aria-labelledby="exampleModalLabel" aria-modal="true"
                    role="dialog">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Attenzione</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <form action="" method="POST">
                                <div class="modal-body">
                                    <p class="text-center m-0">Sei sicuro di voler procedere?</p>
                                </div>
                                <div class="modal-footer mb-3">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Indietro</button>
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
    </div>
</div>

@section('scripts')
    <script>
        $('#searchNotes').on('input', function() {
            var searchNotes = $(this).val()

            $.ajax({
                method: 'POST',
                url: "{{ route('search_note') }}",
                data: {
                    searchNotes: searchNotes,
                    "_token": token
                },
                success: function(result) {
                    $('#response_note').html(result)
                }
            })
        })
    </script>
@endsection
