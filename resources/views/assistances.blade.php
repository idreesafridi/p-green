<x-app-layout pageTitle="Assistence">
    @section('styles')
    @endsection

    <div class="main py-4 calender">
        <div class="container">
            <div class="body-header d-flex py-3">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <h1 class="fs-4 mt-1 mb-0">Calendario</h1>
                        <small class="text-muted">Qui puoi visualizzare tutte le assitenze.</small>
                    </div>
                    <div class="col d-flex justify-content-lg-end mt-2 mt-md-0">
                        <div class="p-2 me-md-3">
                            <div class="d-flex justify-content-center">
                                <span class="h4 mb-0 fw-bold">{{ $to_be_completed }}</span>
                            </div>
                            <small class="text-muted text-uppercase">DA COMPLEATARE</small>
                        </div>
                        <div class="p-2 me-md-3">
                            <div class="d-flex justify-content-center">
                                <span class="h4 mb-0 fw-bold">{{ $Completato }}</span>
                            </div>
                            <small class="text-muted text-uppercase">COMPLETATE</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card p-3">
                @php
                    // Check if a date was submitted in the form
                    if (isset($_GET['date'])) {
                        // Get the selected date value from the form
                        $selectedDate = $_GET['date'];
                    } else {
                        // Use a default date value if no date was submitted
                        // $selectedDate = date('Y-m-d', strtotime('01-01-2023'));
                        $selectedDate = date('Y-m-d');
                    
                        // Format the default date for display
                        $formattedDate = date('F j, Y', strtotime($selectedDate));
                    }
                    
                    // Format the selected date for display
                    $formattedDate = date('F j, Y', strtotime($selectedDate));
                @endphp

                <!-- HTML code for the form with the date input field -->
                <form action="{{ route('view_assistanse') }}" method="GET">
                    <div class="card-head px-2">
                        <h6 class="mb-0 text-center fw-bold text-uppercase">
                            <span class="color-green bg-white fw-bold border-0">{{ $formattedDate }}</span>
                        </h6>
                        <div class="d-grid gap-2 d-flex justify-content-end px-2 pt-3">
                            <button class="btn btn-light me-md-2 fa fa-arrow-left" type="submit"></button>
                            <input class="btn btn-light me-md-2" type="date" name="date"
                                value="{{ $selectedDate }}" onchange="this.form.submit();">
                            <button class="btn btn-light me-md-2 fa fa-arrow-right" type="submit"></button>
                        </div>
                        <hr>
                    </div>
                </form>


                {{-- old --}}
                {{-- <form action="{{ route('view_assistanse') }}" method="GET">
                    <div class="card-head px-2">
                        <h6 class="mb-0 text-center fw-bold text-uppercase">
                            <span class="color-green bg-white fw-bold border-0">{{ $formattedDate }}</span>
                        </h6>
                        <div class="d-grid gap-2 d-flex justify-content-end px-2 pt-3">
                            <button class="btn btn-light me-md-2 fa fa-arrow-left" type="submit"></button>
                            <input class="btn btn-light me-md-2" type="date" name="date" value="01-01-2023"
                                onchange="this.form.submit();">
                            <button class="btn btn-light me-md-2 fa fa-arrow-right" type="submit"></button>
                        </div>
                        <hr>
                    </div>
                </form> --}}
                {{--  --}}
                <div class="card-body px-4 py-0">
                    <div class="row mb-5 d-flex justify-content-center justify-content-sm-start">
                  
                        @if ($data)
                            @foreach ($data as $assistanse)
                                <div class="col-10 col-sm-6 col-lg-3 mb-2">
                                    <div class="card p-3">
                                        <div class="card-body">
                                            <div class="row align-items-center">
                                                <span
                                                    class="col text-muted text-center fw-bold">{{ \Carbon\Carbon::parse($assistanse->expiry_date)->formatLocalized('%A %d %B') }}</span>
                                                <div class="col-auto">
                                                    <div class="dropdown">
                                                        <a href="#" class="dropdown-toggle" role="button"
                                                            data-bs-toggle="dropdown" aria-haspopup="true"
                                                            data-expanded="false" aria-expanded="false">
                                                            <i class="fa fa-ellipsis-h"></i>
                                                        </a>
                                                        <div class="dropdown-menu dropdown-menu-end">
                                                            <a data-bs-toggle="modal" data-bs-target="#changeDate"
                                                                class="dropdown-item">
                                                                <i class="fa fa-clock-o"></i> Cambia data
                                                            </a>
                                                            <a data-bs-toggle="modal" data-bs-target="#skip"
                                                                class="dropdown-item">
                                                                <i class="fa fa-fast-forward"></i> Salta per quest'anno
                                                            </a>
                                                            <a data-bs-toggle="modal" data-bs-target="#delete-{{ $assistanse->id }}"
                                                                class="dropdown-item">
                                                                <i class="fa fa-trash"></i> Elimina Definitivamente
                                                            </a>
                                                            <a data-bs-toggle="modal" data-bs-target="#markasdone"
                                                                class="dropdown-item">
                                                                <i class="fa fa-check"></i> Contrassegna come completato
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-3">

                                            <span>
                                                <h5 class="text-center">
                                                    {{ $assistanse->ConstructionSite->name . ' ' . $assistanse->ConstructionSite->surename }}
                                                </h5>
                                                {{-- <h5 class="text-center">{{ $assistanse->updated_by }}</h5> --}}
                                            </span>
                                            <div
                                                class="d-flex text-muted flex-wrap justify-content-between small text-Sentencecase">
                                                @if ($assistanse->state == 'Completato')
                                                    effettuare il:
                                                @else
                                                    Da effettuare il:
                                                @endif

                                                <span> {{ substr($assistanse->expiry_date, 0, 10) }}</span>
                                            </div>
                                            <div
                                                class="d-flex text-muted flex-wrap justify-content-between small text-Sentencecase">
                                                Tipo
                                                <span>{{ $assistanse->notes }}</span>
                                            </div>
                                            <div
                                                class="d-flex text-muted flex-wrap justify-content-between text-Sentencecase">
                                                Stato:
                                                @if ($assistanse->state == 'Completato')
                                                    <span class="badge bg-green">{{ $assistanse->state }}</span>
                                                @else
                                                    <span class="badge bg-warning">{{ $assistanse->state }}</span>
                                                @endif

                                            </div>
                                        </div>
                                    </div>
                                </div>
                                {{-- modals here --}}
                                {{-- date change modal --}}
                                <div class="modal fade" id="changeDate" aria-labelledby="exampleModalLabel"
                                    aria-modal="true" role="dialog">
                                    <div class="modal-dialog">
                                        <form action="{{ route('change_date') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="assistanse_id" value="{{ $assistanse->id }}">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLabel">Cambia data</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body send-email">
                                                    <p style="font-size: 13px;" class="text-center">
                                                        Cambia la data dell'assistenza da effettuare per:
                                                        <b>{{ $assistanse->ConstructionMaterial == null ? '' : $assistanse->ConstructionMaterial->MaterialList->MaterialTypeBelongs->name }}</b>
                                                        di
                                                        <b>{{ $assistanse->ConstructionSite->name }}
                                                            {{ $assistanse->ConstructionSite->surename }}</b><br>
                                                        <u>*verranno spostate anche le assistenze degli anni
                                                            successivi</u>
                                                    </p>
                                                    <input name="change_date" class="form-control text-center"
                                                        type="date">
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Chiudi</button>
                                                    <button type="submit" class="btn btn-green">Cambia</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                {{-- skip this year modal --}}
                                <div class="modal fade" id="skip" aria-labelledby="exampleModalLabel"
                                    aria-modal="true" role="dialog">
                                    <div class="modal-dialog">
                                        <form action="{{ route('skip_this_year') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="assistanse_id"
                                                value="{{ $assistanse->id }}">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLabel">Salta per
                                                        quest'anno
                                                        </h1>
                                                        <button type="button" class="btn-close"
                                                            data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p style="font-size: 13px;" class="text-center">
                                                        Sei sicuro di voler saltare l'iter di assistenza del
                                                        <b>{{ $assistanse->ConstructionMaterial == null ? '' : $assistanse->ConstructionMaterial->MaterialList->MaterialTypeBelongs->name }}</b>
                                                        di <b>{{ $assistanse->ConstructionSite->name }}
                                                            {{ $assistanse->ConstructionSite->surename }}</b>?<br>
                                                        <u>*l'assistenza verrà spostata al
                                                            <b>{{ $assistanse->expiry_date }}</b></u>
                                                    </p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Chiudi</button>
                                                    <button type="submit"
                                                        class="btn btn-warning text-white">Cambia</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                {{-- permanently delete --}}
                                <div class="modal fade" id="delete-{{ $assistanse->id }}" aria-labelledby="exampleModalLabel"
                                    aria-modal="true" role="dialog">
                                    <div class="modal-dialog">
                                        <form action="{{ route('delete_assistance') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="assistanse_id"
                                                value="{{ $assistanse->id }}">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLabel">Salta per
                                                        quest'anno
                                                        </h1>
                                                        <button type="button" class="btn-close"
                                                            data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p style="font-size: 13px;" class="text-center">
                                                        Sei sicuro di voler eliminare
                                                        <b>{{ $assistanse->state }}</b> l'assistenza per
                                                        <b>{{ $assistanse->ConstructionMaterial == null ? '' : $assistanse->ConstructionMaterial->MaterialList->MaterialTypeBelongs->name }}</b>
                                                        di <b>{{ $assistanse->ConstructionSite->name }}
                                                            {{ $assistanse->ConstructionSite->surename }}</b>?<br>
                                                        <u>*non ci saranno nuove assistenze per questo materiale</u>
                                                    </p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Chiudi</button>
                                                    <button type="submit"
                                                        class="btn btn-danger text-white">Elimina</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                {{-- mark is completed --}}
                                <div class="modal fade" id="markasdone" aria-labelledby="exampleModalLabel"
                                    aria-modal="true" role="dialog">
                                    <div class="modal-dialog">
                                        <form action="{{ route('completed') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="assistanse_id"
                                                value="{{ $assistanse->id }}">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLabel">Salta per
                                                        quest'anno
                                                        </h1>
                                                        <button type="button" class="btn-close"
                                                            data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p style="font-size: 13px;" class="text-center">
                                                        Sei sicuro di voler contrassegnare come
                                                        <b>{{ $assistanse->state }}</b> l'assistenza per
                                                        <b>{{ $assistanse->ConstructionMaterial == null ? '' : $assistanse->ConstructionMaterial->MaterialList->MaterialTypeBelongs->name }}</b>
                                                        di <b>{{ $assistanse->ConstructionSite->name }}
                                                            {{ $assistanse->ConstructionSite->surename }}</b>?<br>
                                                        <u>*ricorda di inserire la relativa fattura e rapportino</u>
                                                    </p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Chiudi</button>
                                                    <button type="submit"
                                                        class="btn btn-green text-white">Contrassegna</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                        <!-- Card 1 -->
                        <!-- End Card 1 -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    @section('scripts')
    @endsection
</x-app-layout>
