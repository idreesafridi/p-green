
@if ($prnotdocfile->state == 1)

    <tr>
        <td>
            <button type="button" class="viewFileBtn" data-bs-toggle="modal"
                data-bs-target="#viewFileModal{{ $prnotdocfile->id }}"
                style="outline: none; border: none; background-color: transparent;" data-filepath="error404greengen.png">
                <i class="fa fa-file-o"></i>
                <strong class="me-4 ms-2">{{ $prnotdocfile->file_name }}
                </strong>

            </button>
            <small>{{ $prnotdocfile->description }}</small>
        </td>
        <td>
            @if ($prnotdocfile->updated_on != null)
                <span class="badge bg-success">CARICATO</span>
            @else
                <span class="badge bg-danger">MANCANTE</span>
            @endif
        </td>

        <td class="hideInMobile">{{ $prnotdocfile->updated_on }}</td>
        <td class="hideInMobile">{{ $prnotdocfile->updated_by }}</td>
        <td class="space">
            <form action="{{ route('replace_file') }}" method="POST" enctype="multipart/form-data" id="replace_file_{{ $prnotdocfile->id }}">
                @csrf
                <input type="text" name="pr_not_doc_id" value="{{ $prenoti_doc_file->id }}" hidden>

                <input type="text" name="bydefault" value="{{ $prnotdocfile->bydefault }}" hidden>

                <input type="text" name="file_id" value="{{ $prnotdocfile->id }}" hidden>

                <input type="text" name="parent_folder_name" value="{{ $prenoti_doc_file->folder_name }}" hidden>

                <input type="text" name="orignal_name" value="{{ $prnotdocfile->file_name }}" hidden>

                @if ($prnotdocfile->updated_on == null)
                    <input type="file" autocomplete="off" class="form-control" id="" name="file"
                        style="color:grey !important;" onchange="replace_file_form_submit('{{ $prnotdocfile->id }}')">
                @endif
            </form>
            <div class="spinner-border d-none" role="status" id="replace_file_spinner_{{ $prnotdocfile->id }}">
                <span class="visually-hidden">Loading...</span>
            </div>
        </td>
        <td>
        <div style="display: inline-flex; width: 100%;">
            <a class="btn btn-link btn-sm text-warning d-inline" href="{{ route('rec_email') }}" data-bs-toggle="modal"
                data-bs-target="#bell{{ $prnotdocfile->id }}Modal">
                <i class="fa fa-bell"></i>
            </a>
            @if ($prnotdocfile->updated_on != null)
                <a class="btn btn-link btn-sm text-dark d-inline"
                    href="{{ route('download_prenoti_file', $prnotdocfile->id) }}">
                    <i class="fa fa-download"></i>
                </a>
                @if ($prnotdocfile->bydefault != 0)
                    <a class="btn btn-link btn-sm text-dark d-inline" data-bs-toggle="modal"
                        data-bs-target="#replaceDocModal{{ $prnotdocfile->id }}">
                        <i class="fa fa-exchange"></i>
                    </a>
                @endif

                <a class="btn btn-link btn-sm text-danger d-inline" data-bs-toggle="modal"
                    data-bs-target="#warningModal{{ $prnotdocfile->id }}">
                    <i class="fa fa-trash"></i>
                </a>
            @endif
            </div>
        </td>
    </tr>

    <!--  Modal -->
    <x-reminder-email-model modelId="bell{{ $prnotdocfile->id }}Modal" folderName="{{ $prnotdocfile->folder_name }}"
        conId="{{ $prnotdocfile->ConstructionSite->id }}" />
    <!-- End Modal -->

    <!-- File Preview Modal -->
    <x-file-preview-modal modelId="viewFileModal{{$prnotdocfile->id}}" filepath="{{ $prnotdocfile->file_path }}"/>
    <!-- End File Preview Modal -->

    <!-- Replace Document Modal -->
    <div class="modal fade" id="replaceDocModal{{ $prnotdocfile->id }}" aria-labelledby="exampleModalLabel"
        aria-modal="true" role="dialog">
        <div class="modal-dialog modal-dialog">
            <div class="modal-content send-email">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        <strong>Sostituisci un documento</strong>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('replace_file') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <input type="text" name="bydefault" value="{{ $prnotdocfile->bydefault }}" hidden>

                    <input type="text" name="pr_not_doc_id" value="{{ $prnotdocfile->pr_not_doc_id }}" hidden>

                    <input type="text" name="file_id" value="{{ $prnotdocfile->id }}" hidden>

                    <input type="text" name="parent_folder_name" value="DICO" hidden>

                    <input type="text" name="orignal_name" value="{{ $prnotdocfile->file_name }}" hidden>

                    <div class="modal-body">
                        <div class="row mb-3 mt-5">
                            <img src="{{ asset('assets/images/swap-img.svg') }}" class="alert-img mx-auto">
                        </div>
                        <div class="mb-4">
                            <h6 class="text-center">Trascina qui sotto il
                                documento oppure selezionalo dal tuo PC</h6>
                        </div>
                        <div class="mb-4">
                            <input type="file" autocomplete="off" class="form-control file-uploader"
                                name="file" style="color:grey !important;" onchange="this.form.submit();">
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
{{-- @dd($prnotdocfile); --}}
    <!-- Delete Modal -->
    <div class="modal fade" id="warningModal{{ $prnotdocfile->id }}" aria-labelledby="exampleModalLabel"
        aria-modal="true" role="dialog">
        <div class="modal-dialog modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Attenzione
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                @if ($prnotdocfile->bydefault == 1)
                    <form action="{{ route('prenoti_file_destroy') }}" method="Post">
                    @else
                        <form action="{{ route('prenoti_file_delete') }}" method="Post">
                @endif
                @csrf
                <input type="hidden" name="id" value="{{ $prnotdocfile->id }}">
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
