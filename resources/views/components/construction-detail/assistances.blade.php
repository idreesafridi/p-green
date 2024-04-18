@php
$construct_id = $materialAssist->id;
$construction_data = \App\Models\ConstructionSite::find($construct_id);
$data = $construction_data->MaterialsAsisstance;
@endphp
<form action="{{ route('update_assistance') }}" method="post">
    @csrf
    <div class="card-head" id="assistances">
        <div class="col-12 d-flex align-items-center justify-content-between">
            <div class="d-inline-block">
                <h6 class="fw-bold mb-0">Cronologia Assistenze</h6>
                <small>Tutte le assistenze avvenute.</small>
            </div>
            <div class="d-flex justify-content-end hideInMobile hideInTablet">
                <button type="button" class="btn btn-outline-green m-1" data-bs-toggle="modal" data-bs-target="#addAssistanceModel">
                    <strong><i class="fa fa-plus me-2"></i>Aggiungi materiale</strong>
                </button>
                <button type="button" class="btn btn-green m-1 edit-a">
                    <strong><i class="fa fa-pencil me-2"></i>Modifica</strong>
                </button>
                <button type="submit" class="btn btn-outline-green m-1 save-a" disabled="disabled">
                    <strong><i class="fa fa-check me-2"></i>Salva</strong>
                </button>
                <a href="{{ route('construction_assistance_print', $materialAssist->id) }}" target="_blank" class="btn btn-outline-green m-1">
                    <strong><i class="fa fa-print"></i></strong>
                </a>
            </div>
        </div>
    </div>
    <div class="card-body p-0 my-4">
        <table class="table assistance-page-table table-borderless dt-responsive" style="width: 100% !important;">
            <thead>
                <tr>
                    <th scope="col">Modello Macchina</th>
                    <th scope="col" class="hideInMobile hideInTablet">Matricola</th>
                    <th scope="col dropdown">
                        <div class="dropdown-toggle" role="button" data-bs-toggle="dropdown" aria-expanded="true">
                            Data Assistenza
                            <i class="fa fa-filter"></i>
                        </div>
                        <div class="dropdown-menu">
                            <span class="dropdown-item disabled">FILTER PER DATA</span>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item">DATA X</a>
                            <a class="dropdown-item">DATA X</a>
                            <a class="dropdown-item">DATA X</a>
                            <a class="dropdown-item">DATA X</a>
                            <a class="dropdown-item">DATA X</a>
                        </div>
                    </th>
                    <th scope="col" class="hideInMobile hideInTablet">Fattura</th>
                    <th scope="col" class="hideInMobile hideInTablet">Rapportino</th>
                    <th scope="col" class="hideInMobile">Note</th>
                    <th scope="col" class="hideInMobile">Stato</th>
                    <th scope="col" class="hideInMobile"></th>
                </tr>
            </thead>
            <tbody>
                @if ($data != null)
                @foreach ($data as $data)
                <tr>
                    <input class="bg-white" name="assistanse_id[]" value="{{ $data->id }}" type="hidden">
                    <td>
                        <input class="bg-white" name="machine_model[]" value="{{ $data->machine_model }}" disabled="disabled" type="text">
                    </td>
                    <td class="hideInMobile hideInTablet">
                        <input class="bg-white" name="freshman[]" value="{{ $data->freshman }}" disabled="disabled" type="text">
                    </td>
                    <td>
                        <input class="bg-white" name="start_date[]" value="{{ $data->start_date }}" disabled="disabled" type="hidden">

                        <input class="bg-white" name="expiry_date[]" value="{{ $data->expiry_date }}" disabled="disabled" type="date">
                    </td>

                    @php

                    $year = \Carbon\Carbon::parse($data->expiry_date)->format('Y');

                    $folder_name = $data->machine_model . ' ' . $year;

                    @endphp
                    <td class="hideInMobile hideInTablet">
                        <a href="{{Route('assistance_document', [$data->construction_site_id, $folder_name])}}">
                            <i class="fa fa-money fa-2x"></i>
                        </a>
                    </td>
                    <td class="hideInMobile hideInTablet">
                        <a href="{{Route('assistance_document', [$data->construction_site_id, $folder_name])}}">
                            <i class="fa fa-list fa-2x"></i>
                        </a>
                    </td>
                    <td class="hideInMobile">
                        <input class="bg-white" name="notes[]" value="{{ $data->notes }}" disabled="disabled" type="text">
                    </td>
                    <td class="hideInMobile">
                        <select class="bg-gray text-dark  p-2" type="" name="state[]" disabled="disabled" style="background:{{ $data->state == 'Da completare' ? '#FFBA33' : '#198754'}};" onchange="stateChangeValue('{{$data->id}}', 'state-{{$data->id}}')" id="state-{{$data->id}}">
                            <option value="Da completare" {{ $data->state == 'Da completare' ? 'selected' : '' }}>
                                Da completare
                            </option>
                            <option value="Completato" {{ $data->state == 'Completato' ? 'selected' : '' }}>
                                Completato
                            </option>
                        </select>
                        <input type="hidden" id="tempState-{{$data->id}}" value="{{ $data->state }}">
                    </td>
                    <td class="hideInMobile">
                        <button type="button" onclick="assist_id('{{ $data->id }}')" class="p-0 btn btn-link btn-sm text-danger">
                            <i class="fa fa-trash f-17 me-2"></i>
                        </button>
                    </td>
                </tr>
                @endforeach

                @endif
            </tbody>
        </table>
    </div>
</form>

<div class="modal fade" id="deleteModal" aria-labelledby="exampleModalLabel" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    <strong>Elimina lavorazione</strong>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('delete_assistance') }}" method="POST">
                @csrf
                <input type="hidden" id="assistanse_id" name="assistanse_id">
                <div class="modal-body text-center">
                    <div class="row mb-3 mt-5">
                        <img src="{{ asset('assets/images/alertgreengen.png') }}" class="alert-img mx-auto">
                    </div>
                    <div class="row mb-4">
                        <h6>Sei sicuro di voler eliminare questa lavorazione?</h6>
                    </div>
                </div>
                <div class="modal-footer mb-1">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Indietro</button>
                    <button type="submit" class="btn btn-danger">Confermo</button>
                </div>
            </form>
        </div>
    </div>
</div>

</select>
<!-- addAssistanceModel -->
<div class="modal fade" id="addAssistanceModel" aria-labelledby="exampleModalLabel" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><strong>Aggiungi assistenza</strong></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('add_assistance') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="modal2-content py-3">
                        <div class="row mb-2">
                            <label class="text-center">Aggiungi un'assistenza extraordinaria</label>
                        </div>
                        <div class="row">
                            <div class="col-md-4 col-12">
                                <label class="col-form-label">Modello</label>
                                {{-- <select class="dark" disabled="disabled" name="material_list_id[]"> --}}
                                <select class="form-control" name="machine_model" id="machine_model" placeholder="Carrier 30AWH015HD" required>
                                    <option value="" selected>Seleziona</option>
                                    @foreach ($materialAssist->ConstructionMaterial as $checkAssist)
                                    @if ($checkAssist->MaterialList != null)
                                    @if ($checkAssist->MaterialList->MaterialTypeBelongs->name == 'Pompa di Calore o Caldaia' || $checkAssist->MaterialList->MaterialTypeBelongs->name == 'Inverter')
                                    <option value="{{ $checkAssist->id }}">
                                        {{ $checkAssist->MaterialList->name }}
                                    </option>
                                    @else
                                    {{-- <input type="hidden" name="avvio[]" class="form-control avvio" disabled> --}}
                                    @endif
                                    @endif
                                    @endforeach
                                </select>

                            </div>
                            <div class="col-md-4 col-12">
                                <label class="col-form-label">Matricola</label>
                                <input type="text" name="freshman" class="form-control" placeholder="LAT1000AAE">
                            </div>
                            <div class="col-md-4 col-12">
                                <label class="col-form-label">Data</label>
                                <input type="date" name="start_date" class="form-control" placeholder="mm/dd/yyyy" s>
                            </div>
                            <input type = "hidden" name="construction_site_id" value="{{$construct_id }}">
                        </div>
                        <div class="row mt-3">
                            <div class="col-lg-6 col-12 mx-auto">
                                <div class="text-center">
                                    <label class="col-form-label">Note</label>
                                </div>
                                <input type="text" name="notes" class="form-control" placeholder="es. Guasto scheda madre">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer mb-1">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Indietro</button>
                    <button type="submit" class="btn btn-green">Aggiungi</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- End addAssistanceModel -->

@section('scripts')
<script>
    function get_all_assistance() {
        $.ajax({
            method: "GET",
            url: "{{ route('get_assistance') }}",
            dataType: "json",
            success: function(response) {

                $("#assitance").html(response.data);
            }
        });
    }
    //assist_id
    function assist_id($id) {
        $("#deleteModal").modal('show');
        $('#assistanse_id').val($id);
    }
    $(document).ready(function() {
        get_all_assistance();
    });
    //Assistance Page edit button
    $('.edit-a').click(function() {
        $(this).addClass('bg-orange');
        $('.assistance-page-table input').removeAttr('disabled');
        $('.assistance-page-table select').removeAttr('disabled');
        $('.save-a').removeAttr('disabled');
    });

    $('.assistance-page-table').DataTable({
        "responsive": {
            details: {
                type: 'column',
                target: -1
            }
        },
        "language": {
            emptyTable: "Nessun dato disponibile nella tabella"
        },
        "paging": false,
        "ordering": false,
        "info": false,
        "searching": false,
        "columnDefs": [{
            className: 'dtr-control arrow-right',
            orderable: false,
            target: -1
        }]
    });

    function stateChangeValue(id, stateId) {
        var enteredValue = $('#tempState-' + id).val();
        var state = $('#' + stateId).val();
        if (enteredValue == 'Completato' && state == 'Completato') {
            $('#' + stateId).val('Da completare');
            $('#tempState-' + id).val('Da completare');
        } else if (enteredValue == 'Da completare' && state == 'Da completare') {
            $('#' + stateId).val('Completato');
            $('#tempState-' + id).val('Completato');
        }
    }
</script>
@endsection