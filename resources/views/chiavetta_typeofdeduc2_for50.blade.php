@php
    $pr_not_doc_id = $data50['pr_not_doc_id']->pr_not_doc_id;
    $docname = 'Documenti 50';
    $parent_folder_name = 'Documents SAL 50';
@endphp
<tr>
    @if ($prnotdoc->folder_name != null)
        <td>
            <a class="fa fa-folder"
                href="{{ route('type_of_deduc_sub2', [$pr_not_doc_id, $prnotdoc->type_of_dedection_sub1_id, $prnotdoc->id, $docname, $parent_folder_name]) }}"></a>
            <a href="{{ route('type_of_deduc_sub2', [$pr_not_doc_id, $prnotdoc->type_of_dedection_sub1_id, $prnotdoc->id, $docname, $parent_folder_name]) }}"
                class="me-4 ms-2">
                <strong>{{ $prnotdoc->folder_name }}</strong>
            </a><br>
            <small>{{ $prnotdoc->description }}</small>
        </td>
        <td>

            @php
                $count = 0;
                foreach ($prnotdoc->TypeOfDedectionFiles as $file) {
                    if ($file['updated_on'] != null && $file['file_name'] && $file['state'] == 1) {
                        $count++;
                    }
                }
            @endphp
            @if ($count > 0)
                <span class="badge bg-success">{{ $count }}</span>
            @else
                <span class="badge bg-danger">{{ $count }}</span>
            @endif
        </td>
    @else
        <td>
            <button type="button" class="viewFileBtn" data-bs-toggle="modal"
                data-bs-target="#viewFileModal{{ $prnotdoc->id }}"
                style="outline: none; border: none; background-color: transparent;"
                data-filepath="error404greengen.png"> <i class="fa fa-file-o"></i>
                <strong class="me-4 ms-2">{{ $prnotdoc->file_name }}</strong>
            </button>
        </td>
        <td>
            @if ($prnotdoc->updated_by == null)
                <span class="badge bg-danger">MANCANTE</span>
            @else
                <span class="badge bg-success">CARICATO</span>
            @endif
        </td>
    @endif
    <td class="hideInMobile">{{ $prnotdoc->updated_on }}</td>
    <td class="hideInMobile">{{ $prnotdoc->updated_by }}</td>
    <td class="space">
        <form action="{{ route('replace_sub2_file') }}" method="POST" enctype="multipart/form-data" id="replace_sub2_file_typeofdeduc2_for50_form_{{ $prnotdoc->id }}">
            @csrf

            <input type="text" name="bydefault" value="{{ $prnotdoc->bydefault }}" hidden>

            <input type="text" name="pr_not_doc_id" value="{{ $pr_not_doc_id }}" hidden>
            <input type="text" name="file_id" value="{{ $prnotdoc->id }}" hidden>
            <input type="text" name="type_of_dedection_sub1_id" value="{{ $prnotdoc->type_of_dedection_sub1_id }}"
                hidden>

            <input type="text" name="parent1_folder_name" value="{{ $docname }}" hidden>

            <input type="text" name="parent2_folder_name" value="{{ $parent_folder_name }}" hidden>


            <input type="text" name="orignal_name" value="{{ $prnotdoc->file_name }}" hidden>


            @if ($prnotdoc->updated_by == null && $prnotdoc->file_name != null)
                <input type="file" autocomplete="off" class="form-control" id="" name="file"
                    style="color:grey !important;" onchange="replace_sub2_file_typeofdeduc2_for50('{{ $prnotdoc->id }}')">
            @endif

        </form>
        <div class="nav nav-tabs border-bottom-0 d-none" role="tablist" id="replace_sub2_file_typeofdeduc2_for50_spinner_{{ $prnotdoc->id }}">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </td>
    <td>
    <div style="display: inline-flex; width: 100%;">
        <button type="button" class="btn btn-link btn-sm text-warning d-inline" data-bs-toggle="modal"
            data-bs-target="#bell{{ $prnotdoc->id }}Modal">
            <i class="fa fa-bell"></i>
        </button>
        @if ($prnotdoc->updated_by != null)
            @if ($prnotdoc->file_name != null)
                <button type="button" class="btn btn-link btn-sm text-dark d-inline"
                    onclick="location.href='{{ route('download_sub2', $prnotdoc->id) }}'">
                    <i class="fa fa-download"></i>
                </button>
            @else
                <button type="button" class="btn btn-link btn-sm text-dark d-inline"
                    onclick="location.href='{{ route('download_sub2_folder', [$docname, $parent_folder_name, $prnotdoc->folder_name]) }}'">
                    <i class="fa fa-download"></i>
                </button>
            @endif

            @if ($prnotdoc->file_name != null && $prnotdoc->bydefault != 0)
                <a class="btn btn-link btn-sm text-dark d-inline" data-bs-toggle="modal"
                    data-bs-target="#replaceDocModal{{ $prnotdoc->id }}">
                    <i class="fa fa-exchange"></i>
                </a>
            @endif


            @if ($prnotdoc->file_name != null)
                <a class="btn btn-link btn-sm text-danger d-inline" data-bs-toggle="modal"
                    data-bs-target="#warningModal{{ $prnotdoc->id }}">
                    <i class="fa fa-trash"></i>
                </a>
            @endif
        @endif
    </div>
    </td>
</tr>

<!-- File Preview Modal -->
<x-file-preview-modal modelId="viewFileModal{{$prnotdoc->id}}" filepath="{{ $prnotdoc->file_path }}"/>
<!-- End File Preview Modal -->

<!--  Modal -->
<x-reminder-email-model modelId="bell{{ $prnotdoc->id }}Modal"
    folderName="{{ $prnotdoc->folder_name == null ? $prnotdoc->file_name : $prnotdoc->folder_name }}"
    conId="{{ $construct_id }}" />
<!-- End Modal -->

<!-- Replace Document Modal -->
<div class="modal fade" id="replaceDocModal{{ $prnotdoc->id }}" aria-labelledby="exampleModalLabel" aria-modal="true"
    role="dialog">
    <div class="modal-dialog modal-dialog">
        <div class="modal-content send-email">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    <strong>Sostituisci un documento</strong>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('replace_sub2_file') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row mb-3 mt-5">
                        <img src="{{ asset('assets/images/swap-img.svg') }}" class="alert-img mx-auto">
                    </div>
                    <div class="mb-4">
                        <h6 class="text-center">Trascina qui sotto il
                            documento
                            oppure selezionalo dal tuo PC</h6>
                    </div>

                    <input type="text" name="bydefault" value="{{ $prnotdoc->bydefault }}" hidden>

                    <input type="text" name="pr_not_doc_id" value="{{ $pr_not_doc_id }}" hidden>
                    <input type="text" name="file_id" value="{{ $prnotdoc->id }}" hidden>
                    <input type="text" name="type_of_dedection_sub1_id"
                        value="{{ $prnotdoc->type_of_dedection_sub1_id }}" hidden>

                    <input type="text" name="parent1_folder_name" value="{{ $docname }}" hidden>

                    <input type="text" name="parent2_folder_name" value="{{ $parent_folder_name }}" hidden>


                    <input type="text" name="orignal_name" value="{{ $prnotdoc->file_name }}" hidden>

                    <div class="mb-4">
                        <input type="file" autocomplete="off" class="form-control file-uploader" name="file"
                            style="color:grey !important;" onchange="this.form.submit();">
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
<!-- Delete Modal -->
<div class="modal fade" id="warningModal{{ $prnotdoc->id }}" aria-labelledby="exampleModalLabel" aria-modal="true"
    role="dialog">
    <div class="modal-dialog modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    Attenzione
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            @if ($prnotdoc->bydefault == 1)
                <form action="{{ route('destroysub2') }}" method="Post">
                @else
                    <form action="{{ route('deletefiles_sub2') }}" method="Post">
            @endif
            @csrf

            <input type="text" name="id" value="{{ $prnotdoc->id }}" hidden>

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
<!-- End Notification Modal -->
