@if ($regpracdoc->state != 0)
    <tr>
        <td>
            <button type="button" class="viewFileBtn" style="outline: none; border: none; background-color: transparent;"
                data-filepath="error404greengen.png" data-bs-toggle="modal"
                data-bs-target="#viewFileModal{{ $regpracdoc->id }}"> <i class="fa fa-file-o"></i>
                <strong class="me-4 ms-2">{{ $regpracdoc->file_name }}</strong>
            </button>
            {{-- <a class="fa fa-folder" href=""></a> --}}
            <br>
            <small>{{ $regpracdoc->description }}</small>
        </td>
        <td>
            @if ($regpracdoc->updated_on != null)
                <span class="badge bg-success">CARICATO</span>
            @else
                <span class="badge bg-danger">MANCANTE</span>
            @endif
        </td>
        <td class="hideInMobile">{{ $regpracdoc->updated_on }} </td>
        <td class="hideInMobile">{{ $regpracdoc->updated_by }} </td> 
        <td class="space">
            <form action="{{ route('regprac_file_upload') }}" method="POST" enctype="multipart/form-data" id="regprac_file_upload_form_{{ $regpracdoc->status_reg_prac_id }}">
                @csrf
                <input type="text" name="regprac_id" value="{{ $regpracdoc->id }}" hidden>
                <input type="text" name="status_regprac_id" value="{{ $regpracdoc->status_reg_prac_id }}" hidden>
                <input type="text" name="orignal_name" value="{{ $regpracdoc->file_name }}" hidden>
            
                @if ($regpracdoc->updated_by == null && $regpracdoc->bydefault != 0)
                      <input type="file" autocomplete="off" class="form-control" name="fileTest" style="color:grey !important;">
                @endif
            </form>

            <div class="nav nav-tabs border-bottom-0 d-none" role="tablist" id="regprac_file_upload_spinner_{{ $regpracdoc->status_reg_prac_id }}">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        </td>
        <td>
        <div style="display: inline-flex; width: 100%;">
            {{-- email --}}
            <a class="btn btn-link btn-sm text-warning d-inline" data-bs-toggle="modal"
                data-bs-target="#bell{{ $regpracdoc->id }}Modal" href="{{ route('legg10_email') }}">
                <i class="fa fa-bell"></i>
            </a>
            @if ($regpracdoc->updated_by != null)
                {{-- download --}}
                <a class="btn btn-link btn-sm text-dark d-inline" href="{{ route('download_regprac_files', $regpracdoc->id) }}">
                    <i class="fa fa-download"></i>
                </a>
                {{-- file exchange --}}
                @if ($regpracdoc->bydefault == 1)
                    <a class="btn btn-link btn-sm text-dark d-inline" data-bs-toggle="modal"
                        data-bs-target="#replaceDocModal{{ $regpracdoc->id }}">
                        <i class="fa fa-exchange"></i>
                    </a>
                @endif


                <a class="btn btn-link btn-sm text-danger d-inline" data-bs-toggle="modal"
                    data-bs-target="#warningModal{{ $regpracdoc->id }}">
                    <i class="fa fa-trash"></i>
                </a>
            @endif
        </div>
        </td>
    </tr>

    <!-- File Preview Modal -->
    <x-file-preview-modal modelId="viewFileModal{{$regpracdoc->id}}" filepath="{{ $regpracdoc->file_path }}"/>
    <!-- End File Preview Modal -->

    <!--  Modal -->
    <x-reminder-email-model modelId="bell{{ $regpracdoc->id }}Modal" folderName="{{ $regpracdoc->folder_name }}"
        conId="{{ $regpracdoc->ConstructionSite->id }}" />
    <!-- End Modal -->

    <!-- Replace Document Modal -->
    <div class="modal fade" id="replaceDocModal{{ $regpracdoc->id }}" aria-labelledby="exampleModalLabel"
        aria-modal="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content send-email">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        <strong>Sostituisci un documento</strong>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                @if ($regpracdoc->file_name)
                    <form action="{{ route('regprac_file_upload') }}" method="POST" enctype="multipart/form-data" id="regprac_file_upload_form_{{ $regpracdoc->status_reg_prac_id }}">
                        @csrf
                        <div class="modal-body">
                            <div class="row mb-3 mt-5">
                                <img src="http://127.0.0.1:8000/assets/images/swap-img.svg" class="alert-img mx-auto">
                            </div>
                            <div class="mb-4">
                                <h6 class="text-center">Trascina qui sotto il
                                    documento oppure selezionalo dal tuo PC
                                </h6>
                            </div>

                            <input type="text" name="regprac_id" value="{{ $regpracdoc->id }}" hidden>

                            <input type="text" name="status_regprac_id"
                                value="{{ $regpracdoc->status_reg_prac_id }}" hidden>

                            <input type="text" name="orignal_name" value="{{ $regpracdoc->file_name }}" hidden>

                            @if ($regpracdoc->updated_by == null && $regpracdoc->bydefault != 0)
                                <input type="file" autocomplete="off" class="form-control" id=""
                                    name="file" style="color:grey !important;" onchange="regprac_file_upload('{{ $regpracdoc->status_reg_prac_id }}')">
                            @endif

                            <div class="mb-4">
                                <input type="file" autocomplete="off" class="form-control" id=""
                                    name="file" class="form-control file-uploader">
                            </div>
                        </div>
                        <div class="modal-footer mb-3">
                            <button type="button" class="btn btn-secondary"
                                data-bs-dismiss="modal">Indietro</button>
                            <button type="submit" class="btn btn-green">Rimpiazza</button>
                        </div>
                    </form>

                    <div class="nav nav-tabs border-bottom-0 d-none" role="tablist" id="regprac_file_upload_spinner_{{ $regpracdoc->status_reg_prac_id }}">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <!-- End Replace Document Modal -->
    <!-- Delete Modal -->
    <div class="modal fade" id="warningModal{{ $regpracdoc->id }}" aria-labelledby="exampleModalLabel"
        aria-modal="true" role="dialog">
        <div class="modal-dialog modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Attenzione
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                @if ($regpracdoc->bydefault == 1)
                    <form action="{{ route('regprac_file_destroy') }}" method="Post">
                    @else
                        <form action="{{ route('regprac_file_delete') }}" method="Post">
                @endif
                @csrf
                <input type="hidden" name="id" value="{{ $regpracdoc->id }}">
                <div class="modal-body">
                    <p class="text-center m-0">Sei sicuro di voler procedere?
                    </p>
                </div>
                <div class="modal-footer mb-3">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Indietro</button>
                    <button type="submit" name="reset-pass" class="btn btn-danger">Procedi</button>
                </div>
                </form>
            </div>
        </div>
    </div>
@endif
