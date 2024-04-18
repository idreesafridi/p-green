@if ($legge10file_data->state != 0)
    <tr>
        <td>
            <button type="button" class="viewFileBtn" style="outline: none; border: none; background-color: transparent;"
                data-filepath="error404greengen.png" data-bs-toggle="modal"
                data-bs-target="#viewFileModal{{ $legge10file_data->id }}">
                <i class="fa fa-file-o"></i>
                <strong class="me-4 ms-2">{{ $legge10file_data->file_name }}</strong>
            </button>
            {{-- <a class="fa fa-folder" href=""></a> --}}
            <br>
            <small>{{ $legge10file_data->description }}</small>
        </td>
        <td>
            @if ($legge10file_data->updated_on != null)
                <span class="badge bg-success">CARICATO</span>
            @else
                <span class="badge bg-danger">MANCANTE</span>
            @endif
        </td>
        <td class="hideInMobile">{{ $legge10file_data->updated_on }}</td>
        <td class="hideInMobile">{{ $legge10file_data->updated_by }}</td> 
        <td class="space">
            @if ($legge10file_data->file_name)
                <form action="{{ route('replace_rel_doc_files') }}" method="POST" enctype="multipart/form-data" id="legge10_file_upload_legg10files_form_{{ $legge10file_data->id }}">
                    @csrf


                    <input type="text" name="relief_doc_id" value="{{ $legge10file_data->relief_doc_id }}" hidden>
                    <input type="text" name="bydefault" value="{{ $legge10file_data->bydefault }}" hidden>
                    <input type="text" name="relief_doc_f_name" value="{{ $legge10file_data->ref_folder_name }}" hidden>
                    <input type="text" name="orignal_name" value="{{ $legge10file_data->file_name }}" hidden>
                    <input type="text" name="file_id" value="{{ $legge10file_data->id }}" hidden>

                    <input type="text" name="orignal_name" value="{{ $legge10file_data->file_name }}" hidden>
                    @if ($legge10file_data->updated_on == null)
                        <input type="file" autocomplete="off" class="form-control" id="" name="file"
                            style="color:grey !important;" onchange="legge10_file_upload_legg10files('{{ $legge10file_data->id }}')">
                    @endif
                </form>
                <div class="nav nav-tabs border-bottom-0 d-none" role="tablist" id="legge10_file_upload_legg10files_spinner_{{ $legge10file_data->id }}">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            @endif
        </td>
        <td>
        <div style="display: inline-flex; width: 100%;">
            {{-- email --}}
            <a class="btn btn-link btn-sm text-warning d-inline" data-bs-toggle="modal"
                data-bs-target="#bell{{ $legge10file_data->id }}Modal" href="#">
                <i class="fa fa-bell"></i>
            </a>
            @if ($legge10file_data->updated_on != null)
                {{-- download --}}
                <a class="btn btn-link btn-sm text-dark d-inline"
                    href="{{ route('download_legg10_file', $legge10file_data->id) }}">
                    <i class="fa fa-download"></i>
                </a>
                {{-- file exchange --}}
                @if ($legge10file_data->bydefault != 0)
                    <a class="btn btn-link btn-sm text-dark d-inline" data-bs-toggle="modal"
                        data-bs-target="#replaceDocModal{{ $legge10file_data->id }}">
                        <i class="fa fa-exchange"></i>
                    </a>
                @endif

                {{-- delete --}}

                <a class="btn btn-link btn-sm text-danger d-inline" data-bs-toggle="modal"
                    data-bs-target="#warningModal{{ $legge10file_data->id }}">
                    <i class="fa fa-trash"></i>
                </a>
            @endif
        </div>
        </td>
    </tr>

    <!--  Modal -->
    <x-reminder-email-model modelId="bell{{ $legge10file_data->id }}Modal"
        folderName="{{ $legge10file_data->folder_name }}" conId="{{ $legge10file_data->ConstructionSite->id }}" />
    <!-- End Modal -->

    <!-- File Preview Modal -->
    <x-file-preview-modal modelId="viewFileModal{{$legge10file_data->id}}" filepath="{{ $legge10file_data->file_path }}"/>
    <!-- End File Preview Modal -->
    
    <!-- Replace Document Modal -->
    <div class="modal fade" id="replaceDocModal{{ $legge10file_data->id }}" aria-labelledby="exampleModalLabel"
        aria-modal="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content send-email">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        <strong>Sostituisci un documento</strong>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                @if ($legge10file_data->file_name)
                    <form action="{{ route('replace_rel_doc_files') }}" method="POST" enctype="multipart/form-data">
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

                            <<input type="text" name="relief_doc_id" value="{{ $legge10file_data->relief_doc_id }}" hidden>
                            <input type="text" name="bydefault" value="{{ $legge10file_data->bydefault }}" hidden>
                            <input type="text" name="relief_doc_f_name" value="{{ $legge10file_data->ref_folder_name }}" hidden>
                            <input type="text" name="orignal_name" value="{{ $legge10file_data->file_name }}" hidden>
                            <input type="text" name="file_id" value="{{ $legge10file_data->id }}" hidden>
        
                            <input type="text" name="orignal_name" value="{{ $legge10file_data->file_name }}" hidden>

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
                @endif
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div class="modal fade" id="warningModal{{ $legge10file_data->id }}" aria-labelledby="exampleModalLabel"
        aria-modal="true" role="dialog">
        <div class="modal-dialog modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Attenzione
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                @if ($legge10file_data->bydefault == 1)
                <form action="{{ route('rec_file_destroy') }}" method="Post">
                    @else
                        <form action="{{ route('rec_file_delete') }}" method="Post">
                @endif
                @csrf

                <input type="text" name="id" value="{{ $legge10file_data->id }}" hidden>

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
    <!-- End Delete Modal -->
@endif
