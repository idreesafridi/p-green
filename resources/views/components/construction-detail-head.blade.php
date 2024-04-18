{{-- @dd($headdata->ConstructionNotesFirst->notes) --}}
<div class="card">
    <div class="card-body p-4">
        <div class="d-flex align-items-center">
            <h5 class="d-inline-block mb-0">Gestione Cantiere {{ $headdata->surename . ' ' . $headdata->name }} </h5>
            <span class="badge bg-dark-green rounded-pill ms-3">
                @if ($headdata->ConstructionSiteSetting != null)
                    @if ($headdata->ConstructionSiteSetting->type_of_construction != null)
                        @if ($headdata->ConstructionSiteSetting->type_of_construction == 1)
                            Esterno
                        @else
                            Interno
                        @endif
                    @endif
                @endif
            </span>
        </div>
        <div class="">
            <div class="mt-4" style="display: flex; flex-wrap: wrap;">
                <div class="me-2 me-sm-3 {{ $headdata->PropertyData == null ? 'd-none' : ($headdata->PropertyData->property_common == null ? 'd-none' : '') }}">
                    <strong class="color-green">Comune</strong>
                    <div class="text-muted ">
                        {{-- @dd($headdata->PropertyData); --}}
                        {{ $headdata->PropertyData == null ? '' : $headdata->PropertyData->property_common }}</div>
                </div>
                <div class="me-2 me-sm-3 {{ $headdata->PropertyData == null ? 'd-none' : ($headdata->PropertyData->property_street != null || $headdata->PropertyData->property_house_number != null ? '' : 'd-none')  }}">
                    <strong class="color-green">Via Cantiere</strong>
                    @if ($headdata->PropertyData != null)
                        <div>
                            @if ($headdata->pin_location == null)
                                @php
                                    $link = 'https://www.google.it/maps/search/' . $headdata->PropertyData->property_street . '+' . $headdata->PropertyData->property_house_number . '+' . $headdata->PropertyData->property_common . '/data=!3m1!1e3';
                                @endphp
                                <a class="btn btn-outline-green fw-bold text-start" target="_blank" href="{{ $link }}">
                                    <i class="fa  fa-map-marker me-2"></i>
                                    {{ $headdata->PropertyData->property_street . ' n. ' . $headdata->PropertyData->property_house_number }}
                                </a>
                            @else
                                <a class="btn btn-outline-green fw-bold text-start"
                                    href="{{ $headdata->pin_location == null ? '#' : $headdata->pin_location }}"
                                    target="_blank">
                                    <i class="fa  fa-map-marker me-2"></i>
                                    {{ $headdata->PropertyData->property_street . ' ' . $headdata->PropertyData->property_house_number }}
                                </a>
                            @endif
                        </div>
                        <div>
                            <a href="#" data-bs-toggle="modal" data-bs-target="#addPinLocation">
                                {{--{{ $headdata->pin_location == null ? 'Aggiorna' : 'Edit' }} posizione --}} 
                                Aggiorna posizione</a>
                        </div>
                    @endif
                </div>

                @if ($headdata->DocumentAndContact != null)
                    @if ($headdata->DocumentAndContact->contact_number !=null)
                        <div class="me-2 me-sm-3">
                            <strong class="color-green">Telefono</strong>
                            <div>
                                <span class="text-muted mobileShowPhone">
                                    <i class="fa fa-phone me-2"></i>
                                    {{ $headdata->DocumentAndContact->contact_number }}</span>

                                <a class="btn btn-outline-green mobileHidePhone"
                                    href="tel:{{ $headdata->DocumentAndContact->contact_number }}">
                                    <i class="fa fa-phone me-2"></i>
                                    {{ $headdata->DocumentAndContact->contact_number }}
                                </a>
                            </div>
                        </div> 
                    @endif      
                @endif
                @if (auth()->user()->hasrole('admin') || auth()->user()->hasrole('user'))
                    @if ($headdata->latest_status != null)
                        <div class="me-2 me-sm-3">
                            <strong class="color-green">Stato</strong>
                            <div>
                                <a class="btn btn-outline-green fw-bold"
                                    href="{{ route('construction_detail', ['id' => $headdata->id, 'pagename' => 'Stato']) }}"
                                    role="tab">
                                    <i class="fa fa-history me-2"></i>
                                    {{ $headdata->latest_status }}
                                </a>
                            </div>
                        </div>
                    
                        {{-- <div class="me-2 me-sm-3 {{ $headdata->DocumentAndContact->contact_number == null ? 'd-none' : '' }}">
                            <strong class="color-green">Stato</strong>
                            <div>
                                <a class="btn btn-outline-green fw-bold"
                                    href="{{ route('construction_detail', ['id' => $headdata->id, 'pagename' => 'Stato']) }}"
                                    role="tab">
                                    <i class="fa fa-history me-2"></i>
                                    {{ $headdata->latest_status }}
                                </a>
                            </div>
                        </div>  --}}
                    @endif

                    @if ($headdata->ConstructionNotesFirst != null)
                        <div class="me-2 me-sm-3">
                            <strong class="color-green">Nota in evidenza</strong>
                            <a href="{{ route('construction_detail', ['id' => $headdata->id, 'pagename' => 'Note']) }}">
                                <div class="bg-light p-3 rounded">
                                    <span
                                        class="text-muted">{{ $headdata->ConstructionNotesFirst != null? Str::limit($headdata->ConstructionNotesFirst->notes, 30, '.....'): Str::limit($headdata->ConstructionNotes()->orderBy('created_at', 'desc')->pluck('notes')->first(),30,'.....') }}</span>
                                </div>
                            </a>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="addPinLocation" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="addPinLocationLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content"> 
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="addPinLocationLabel">Aggiornamento posizione Cantiere</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('construction_pin_location') }}" method="post">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="recipient-name" class="col-form-label">Incolla link posizione aggiornata</label>
                        <input type="hidden" class="form-control" value="{{ $headdata->id }}" name="fk_const">

                        <input type="text" class="form-control" value="{{ $headdata->pin_location }}"
                            name="pin_location">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-outline-primary">Aggiorna</button>
                </div>
            </form>
        </div>
    </div>
</div>
