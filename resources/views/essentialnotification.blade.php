@if ($relief_doc_file->state != 0 && $relief_doc_file->file_name != null || $relief_doc_file->folder_name != null)
<tr>
                <td>
                    <button type="button" class="viewFileBtn"
                        style="outline: none; border: none; background-color: transparent;"
                        data-filepath="error404greengen.png" data-bs-toggle="modal"
                        data-bs-target="#viewFileModal2{{ $relief_doc_file->id }}"> <i class="fa fa-file-o"></i>
                        <strong class="me-4 ms-2 text-start">{{ $relief_doc_file->file_name }}</strong>
                    </button>

                    <small >{{$relief_doc_file->file_name == "Notifica Preliminare" ? "Prima notifica preliminare" :  $relief_doc_file->description}}</small>
                </td>
                <td>
                    @if ($relief_doc_file->updated_by == null)
                        <span class="badge bg-danger">MANCANTE</span>
                    @else
                        <span class="badge bg-success">CARICATO</span>
                    @endif
                </td>

            <td class="hideInMobile">{{ $relief_doc_file->updated_on }}</td>
            <td class="hideInMobile">{{ $relief_doc_file->updated_by }}</td>
            <td class="space">
                @if ($relief_doc_file->file_name)
                    <form action="{{ route('replace_rel_doc_files') }}" method="POST" enctype="multipart/form-data" id="replace_rel_doc_files_form_{{ $relief_doc_file->id }}">
                        @csrf
                    
                        <input type="text" name="relief_doc_id" value="{{ $relief_doc_file->relief_doc_id }}" hidden>

                        <input type="text" name="bydefault" value="{{ $relief_doc_file->bydefault }}" hidden>

                        <input type="text" name="relief_doc_f_name" value="{{ $relief_doc_file->ref_folder_name }}" hidden>

                        <input type="text" name="orignal_name" value="{{ $relief_doc_file->file_name }}" hidden>

                        <input type="text" name="file_id" value="{{ $relief_doc_file->id }}" hidden>

                        <input type="text" name="orignal_name" value="{{ $relief_doc_file->file_name }}" hidden>
                        @if ($relief_doc_file->updated_on == null)
                            <input type="file" autocomplete="off" class="form-control" name="file"
                                style="color:grey !important;" onchange="replace_rel_doc_files('{{ $relief_doc_file->id }}')">
                        @endif
                    </form>
                    <div class="spinner-border d-none" role="status" id="replace_rel_doc_files_spinner_{{ $relief_doc_file->id }}">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                @endif
            </td>
            <td>
            <div style="display: inline-flex; width: 100%;">
                <button type="button" class="btn btn-link btn-sm text-warning d-inline" data-bs-toggle="modal"
                    data-bs-target="#bell{{ $relief_doc_file->id }}Modal">
                    <i class="fa fa-bell"></i>
                </button>
    
                @if ($relief_doc_file->updated_by != null)
               
                    @if ($relief_doc_file->file_name != null)
                      
                        <button type="button" class="btn btn-link btn-sm text-dark d-inline"
                            onclick="location.href='{{ route('download_relief_file', $relief_doc_file->id) }}'">
                            <i class="fa fa-download"></i>
                        </button>
                    @else
                    
                        <button type="button" class="btn btn-link btn-sm text-dark d-inline"
                            onclick="location.href='{{ route('download_relief_folder', $relief_doc_file->folder_name) }}'">
                            <i class="fa fa-download"></i>
                        </button>
                    @endif

                    @if ($relief_doc_file->file_name != null && $relief_doc_file->bydefault != 0)
                        <a class="btn btn-link btn-sm text-dark d-inline" data-bs-toggle="modal"
                            data-bs-target="#replaceDocModal{{ $relief_doc_file->id }}">
                            <i class="fa fa-exchange"></i>
                        </a>
                    @endif
                    @if ($relief_doc_file->file_name != null)
                        <a class="btn btn-link btn-sm text-danger d-inline" data-bs-toggle="modal"
                            data-bs-target="#warningModal{{ $relief_doc_file->id }}">
                            <i class="fa fa-trash"></i>
                        </a>
                    @endif
                @endif
            </div>
            </td>
        </tr>
        <!-- File Preview Modal -->
        <x-file-preview-modal modelId="viewFileModal2{{ $relief_doc_file->id }}" filepath="{{ $relief_doc_file->file_path }}">
            @slot('filename', $relief_doc_file->file_name)
        </x-file-preview-modal>
        <!-- End File Preview Modal -->

        <!--  Modal -->
        <x-reminder-email-model modelId="bell{{ $relief_doc_file->id }}Modal"
            folderName="{{ $relief_doc_file->file_name == null ? $relief_doc_file->folder_name : $relief_doc_file->file_name }}"
            conId="{{ $relief_doc_file->ConstructionSite->id }}" />
        <!-- End Modal -->

        <!-- Replace Document Modal -->
        <div class="modal fade" id="replaceDocModal{{ $relief_doc_file->id }}" aria-labelledby="exampleModalLabel"
            aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content send-email">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">
                            <strong>Sostituisci un documento</strong>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    @if ($relief_doc_file->file_name)
                        <form action="{{ route('replace_rel_doc_files') }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="modal-body">
                                <div class="row mb-3 mt-5">
                                    <img src="https://greengen.crm-labloid.com/assets/images/swap-img.svg" class="alert-img mx-auto">
                                </div>
                                <div class="mb-4">
                                    <h6 class="text-center">Trascina qui sotto
                                        il
                                        documento oppure selezionalo dal tuo PC
                                    </h6>
                                </div>
                                <input type="text" name="relief_doc_id" value="{{ $relief_doc_file->relief_doc_id }}" hidden>

                                <input type="text" name="bydefault" value="{{ $relief_doc_file->bydefault }}" hidden>

                                <input type="text" name="relief_doc_f_name"
                                    value="{{ $relief_doc_file->ref_folder_name }}" hidden>


                                <input type="text" name="orignal_name" value="{{ $relief_doc_file->file_name }}" hidden>

                                <input type="text" name="file_id" value="{{ $relief_doc_file->id }}" hidden>

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
        <!-- End Replace Document Modal -->
        <!-- Delete Modal -->
        <div class="modal fade" id="warningModal{{ $relief_doc_file->id }}" aria-labelledby="exampleModalLabel"
            aria-modal="true" role="dialog">
            <div class="modal-dialog modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">
                            Attenzione
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    @if ($relief_doc_file->bydefault == 1)
                        <form action="{{ route('rec_file_destroy') }}" method="Post">
                        @else
                            <form action="{{ route('rec_file_delete') }}" method="Post">
                    @endif
                    @csrf
                    <input type="hidden" name="id" value="{{ $relief_doc_file->id }}">
                    <div class="modal-body">
                        <p class="text-center m-0">Sei sicuro di voler
                            procedere?
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
