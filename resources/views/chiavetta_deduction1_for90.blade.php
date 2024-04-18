<tr>
    @if ($data90['deduction1_for90']->folder_name != null)
        <td>
            <a class="fa fa-folder"
                href="{{ route('type_of_deduc_sub1', [$data90['deduction1_for90']->pr_not_doc_id, $data90['deduction1_for90']->id, $folder_name]) }}"></a>
            <a href="{{ route('type_of_deduc_sub1', [$data90['deduction1_for90']->pr_not_doc_id, $data90['deduction1_for90']->id, $folder_name]) }}"
                class="me-4 ms-2">
                <strong>{{ $data90['deduction1_for90']->folder_name }}</strong>
            </a><br>
            <small>{{$data90['deduction1_for90']->description}}</small>
        </td>
        <td>
            @php
                $count = 0;
                foreach ($data90['deduction1_for90']->TypeOfDedectionSub2 as $file) {
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
                data-bs-target="#viewFileModal{{ $data90['deduction1_for90']->id }}"
                style="outline: none; border: none; background-color: transparent;"
                data-filepath="error404greengen.png"> <i class="fa fa-file-o"></i>
                <strong class="me-4 ms-2">{{ $data90['deduction1_for90']->file_name }}</strong>
            </button>
            <small>{{$data90['deduction1_for90']->description}}</small>
        </td>
        <td>
            @if ($data90['deduction1_for90']->updated_on != null)
                <span class="badge bg-success">CARICATO</span>
            @else
                <span class="badge bg-danger">MANCANTE</span>
            @endif
        </td>
    @endif
    <td class="hideInMobile">
        {{ $data90['deduction1_for90']->updated_on }}
    </td>
    <td class="hideInMobile">
        {{ $data90['deduction1_for90']->updated_by }}
    </td>
    <td class="space">
        <form action="{{ route('replace_sub1_file') }}" method="POST" enctype="multipart/form-data" id="replace_sub1_file_deduction1_for90_form_{{ $data90['deduction1_for90']->id }}">
            @csrf

            <input type="text" name="bydefault" value="{{ $data90['deduction1_for90']->bydefault }}" hidden>
            <input type="text" name="pr_not_doc_id" value="{{ $data90['deduction1_for90']->pr_not_doc_id }}" hidden>
            <input type="text" name="file_id" value="{{ $data90['deduction1_for90']->id }}" hidden>
            <input type="text" name="parent1_folder_name" value="{{ $folder_name }}" hidden>
            <input type="text" name="orignal_name" value="{{ $data90['deduction1_for90']->file_name }}" hidden>
            <input type="text" name="type_of_dedection_sub1_id" value="{{ $data90['deduction1_for90']->id }}" hidden>
            @if ($data90['deduction1_for90']->updated_by == null && $data90['deduction1_for90']->file_name != null)
                <input type="file" autocomplete="off" class="form-control" id="" name="file"
                    style="color:grey !important;" onchange="replace_sub1_file_deduction1_for90('{{ $data90['deduction1_for90']->id }}')">
            @endif
        </form>
        <div class="nav nav-tabs border-bottom-0 d-none" role="tablist" id="replace_sub1_file_deduction1_for90_spinner_{{ $data90['deduction1_for90']->id }}">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    </td>
    <td>
    <div style="display: inline-flex; width: 100%;">
        {{-- start sub1 --}}
        <button type="button" class="btn btn-link btn-sm text-warning d-inline" data-bs-toggle="modal"
            data-bs-target="#bell{{ $data90['deduction1_for90']->id }}Modal">
            <i class="fa fa-bell"></i>
        </button>
        @if ($data90['deduction1_for90']->updated_by != null)
            @if ($data90['deduction1_for90']->file_name != null)
                <button type="button" class="btn btn-link btn-sm text-dark d-inline"
                    onclick="location.href='{{ route('download_sub1', $data90['deduction1_for90']->id) }}'">
                    <i class="fa fa-download"></i>
                </button>
            @else
                <button type="button" class="btn btn-link btn-sm text-dark d-inline"
                    onclick="location.href='{{ route('download_sub1_folder', [$folder_name, $data90['deduction1_for90']->folder_name]) }}'">
                    <i class="fa fa-download"></i>
                </button>
            @endif

            {{-- file exchange --}}
            @if ($data90['deduction1_for90']->file_name != null && $data90['deduction1_for90']->bydefault != 0)
                <a class="btn btn-link btn-sm text-dark d-inline" data-bs-toggle="modal"
                    data-bs-target="#replaceDocModal{{ $data90['deduction1_for90']->id }}">
                    <i class="fa fa-exchange"></i>
                </a>
            @endif


            @if ($data90['deduction1_for90']->file_name != null)
                <a class="btn btn-link btn-sm text-danger d-inline" data-bs-toggle="modal"
                    data-bs-target="#warningModal{{ $data90['deduction1_for90']->id }}">
                    <i class="fa fa-trash"></i>
                </a>
            @endif
        @endif
        {{-- end sub1 --}}
    </div>

    </td>
</tr>

<!-- File Preview Modal -->
<x-file-preview-modal modelId="viewFileModal{{ $data90['deduction1_for90']->id }}" filepath="{{ $data90['deduction1_for90']->file_path }}"/>
<!-- End File Preview Modal -->

<!--  Modal -->
<x-reminder-email-model modelId="bell{{ $data90['deduction1_for90']->id }}Modal"
    folderName="{{ isset($data90['deduction1_for90']->folder_name) ? ($data90['deduction1_for90']->folder_name == null ? $data90['deduction1_for90']->file_name : $data90['deduction1_for90']->folder_name) : $data90['deduction1_for90']->file_name }}"
    conId="{{ $construct_id }}" />
<!-- End Modal -->

<!-- Replace Document Modal -->
<div class="modal fade" id="replaceDocModal{{ $data90['deduction1_for90']->id }}" aria-labelledby="exampleModalLabel"
    aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog">
        <div class="modal-content send-email">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    <strong>Sostituisci un documento</strong>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('replace_sub1_file') }}" method="POST" enctype="multipart/form-data">
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

                    <input type="text" name="bydefault" value="{{ $data90['deduction1_for90']->bydefault }}"
                        hidden>

                    <input type="text" name="pr_not_doc_id"
                        value="{{ $data90['deduction1_for90']->pr_not_doc_id }}" hidden>
                    <input type="text" name="file_id" value="{{ $data90['deduction1_for90']->id }}" hidden>
                    <input type="text" name="parent1_folder_name" value="{{ $folder_name }}" hidden>

                    <input type="text" name="orignal_name" value="{{ $data90['deduction1_for90']->file_name }}"
                        hidden>

                    <input type="text" name="type_of_dedection_sub1_id"
                        value="{{ $data90['deduction1_for90']->id }}" hidden>

                    <div class="mb-4">
                        <input type="file" autocomplete="off" class="form-control file-uploader" id=""
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
<!-- Delete Modal -->
<div class="modal fade" id="warningModal{{ $data90['deduction1_for90']->id }}" aria-labelledby="exampleModalLabel"
    aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Attenzione
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            @if ($data90['deduction1_for90']->bydefault == 1)
                <form action="{{ route('destroy_sub1') }}" method="Post">
                @else
                    <form action="{{ route('delete_files_sub1') }}" method="Post">
            @endif
            @csrf
            <input type="hidden" name="id" value="{{ $data90['deduction1_for90']->id }}">
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
<!-- End Delete Modal -->
