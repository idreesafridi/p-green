@php

    isset($docname) ? '' : ($docname = 'Documenti 110');
    isset($sub_chiled_f_name) ? '' : ($sub_chiled_f_name = $prnotdoc->TypeOfDedectionSub2->folder_name);
    isset($prntfname) ? '' : ($prntfname = 'Documenti SAL 110');

@endphp
<tr>
    @if ($prnotdoc->folder_name != null)
        <td>
            <a class="fa fa-folder"
                href="{{ route('type_of_deduc_files1', [$prnotdoc->id, $docname, $sub_chiled_f_name, $prntfname, $prnotdoc->folder_name]) }}">
            </a>
            <a href="{{ route('type_of_deduc_files1', [$prnotdoc->id, $docname, $sub_chiled_f_name, $prntfname, $prnotdoc->folder_name]) }}"
                class="me-4 ms-2">
                <strong>{{ $prnotdoc->folder_name }}</strong>
            </a><br>
            {{-- <small>Documenti vari interni</small> --}}
        </td>
        <td>
            @php
                $count = 0;
                if ($prnotdoc->TypeOfDedectionFiles2) {
                    foreach ($prnotdoc->TypeOfDedectionFiles2 as $file) {
                        if ($file['updated_on'] != null && $file['file_name'] && $file['state'] == 1) {
                            $count++;
                        }
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
    </td>

    @php
        // dd($prnotdoc->TypeOfDedectionSub2);
        $latestUpdate = $prnotdoc
            ->TypeOfDedectionFiles2()
            ->where('state', 1)
            ->latest('updated_on')
            ->first(['updated_by', 'updated_on']);
    @endphp

    {{-- <td class="hideInMobile">{{ $prnotdoc->updated_on }}</td>
        <td class="hideInMobile">{{ $prnotdoc->updated_by }}</td> --}}

    @if ($prnotdoc->file_name != null)
        <td class="hideInMobile">{{ $prnotdoc->updated_on }}</td>
        <td class="hideInMobile">{{ $prnotdoc->updated_by }}</td>
    @elseif($prnotdoc->folder_name != null)
        <td class="hideInMobile">{{ isset($count) && $count > 0 ? $latestUpdate->updated_on : '' }}</td>

        <td class="hideInMobile">{{ isset($count) && $count > 0 ? $latestUpdate->updated_by : '' }}</td>
    @endif

    <td class="space">

    </td>
    <td>
        {{-- <div style="display: inline-flex; width: 100%;">
            <button type="button" class="btn btn-link btn-sm text-warning d-inline" data-bs-toggle="modal"
                data-bs-target="#bell{{ $prnotdoc->id }}Modal">
                <i class="fa fa-bell"></i>
            </button>
            @if ($prnotdoc->updated_by != null)
                <button type="button" class="btn btn-link btn-sm text-dark d-inline"
                    onclick="location.href='{{ route('download_sub2_chiled_file', $prnotdoc->id) }}'">
                    <i class="fa fa-download"></i>
                </button>

                <a class="btn btn-link btn-sm text-danger d-inline" data-bs-toggle="modal"
                    data-bs-target="#warningModal{{ $prnotdoc->id }}">
                    <i class="fa fa-trash"></i>
                </a>
            @endif
        </div> --}}
        <div style="display: inline-flex; width: 100%;">
            <button type="button" class="btn btn-link btn-sm text-warning d-inline" data-bs-toggle="modal"
                data-bs-target="#bell{{ $prnotdoc->id }}Modal">
                <i class="fa fa-bell"></i>
            </button>
            @if ((isset($count) && $count > 0) || $prnotdoc->file_name != null && $prnotdoc->updated_by != null)
                <button type="button" class="btn btn-link btn-sm text-dark d-inline"
                    onclick="location.href='{{ route('download_sub2_chiled_file', $prnotdoc->id) }}'">
                    <i class="fa fa-download"></i>
                </button>


                <a class="btn btn-link btn-sm text-danger d-inline" data-bs-toggle="modal"
                    data-bs-target="#warningModal{{ $prnotdoc->id }}">
                    <i class="fa fa-trash"></i>
                </a>
            @endif
        </div>

    </td>
</tr>

<!--  Modal -->
<x-reminder-email-model modelId="bell{{ $prnotdoc->id }}Modal"
    folderName="{{ $prnotdoc->file_name == null ? $prnotdoc->folder_name : $prnotdoc->file_name }}"
    conId="{{ $prnotdoc->ConstructionSite->id }}" />
<!-- End Modal -->

<!-- File Preview Modal -->
<div class="modal fade" id="viewFileModal{{ $prnotdoc->id }}" aria-labelledby="exampleModalLabel" aria-modal="true"
    role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Anteprima
                    Documento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <iframe id="view-file-frame" src="{{ asset('construction-assets/' . $prnotdoc->file_path) }}"
                    width="100%" height="600px"></iframe>
            </div>
        </div>
    </div>
</div>
<!-- End File Preview Modal -->
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
            <form action="" method="POST">
                <div class="modal-body">
                    <div class="row mb-3 mt-5">
                        <img src="{{ asset('assets/images/swap-img.svg') }}" class="alert-img mx-auto">
                    </div>
                    <div class="mb-4">
                        <h6 class="text-center">Trascina qui sotto il documento
                            oppure selezionalo dal tuo PC</h6>
                    </div>
                    <div class="mb-4">
                        <input type="file" autocomplete="off" class="form-control file-uploader">
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
<!-- End Delete Modal -->
<div class="modal fade" id="warningModal{{ $prnotdoc->id }}" aria-labelledby="exampleModalLabel" aria-modal="true"
    role="dialog">
    <div class="modal-dialog modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Attenzione</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('delete_sub2_file') }}" method="Post">
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
