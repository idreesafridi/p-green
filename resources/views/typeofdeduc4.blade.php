@if ($prnotdoc->state != 0)

    <tr>
        <td>
            @if ($prnotdoc->file_name != null)
                <button type="button" class="viewFileBtn" data-bs-toggle="modal"
                    data-bs-target="#viewFileModal{{ $prnotdoc->id }}"
                    style="outline: none; border: none; background-color: transparent;"
                    data-filepath="error404greengen.png"> <i class="fa fa-file-o"></i>
                    <strong class="me-4 ms-2">{{ $prnotdoc->file_name }}</strong>
                </button>
            @endif
        </td>
        <td>
            @if ($prnotdoc->updated_by != null)
                <span class="badge bg-success">CARICATO</span>
            @endif
        </td>
        <td class="hideInMobile">{{ $prnotdoc->updated_on }}</td>
        <td class="hideInMobile">{{ $prnotdoc->updated_by }}</td>
        <td class="space">

        </td>
        <td>
            <div style="display: inline-flex; width: 100%;">
            <button type="button" class="btn btn-link btn-sm text-warning d-inline" data-bs-toggle="modal"
                data-bs-target="#bell{{ $prnotdoc->id }}Modal">
                <i class="fa fa-bell"></i>
            </button>
            @if ($prnotdoc->updated_by != null)
                <button type="button" class="btn btn-link btn-sm text-dark d-inline"
                    onclick="location.href='{{ route('download_chiled_file2', $prnotdoc->id) }}'">
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
    {{-- <div class="modal fade" id="viewFileModal{{ $prnotdoc->id }}" aria-labelledby="exampleModalLabel" aria-modal="true"
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
    </div> --}}
    <x-file-preview-modal modelId="viewFileModal{{$prnotdoc->id}}" filepath="{{ $prnotdoc->file_path }}"/>
    <!-- End File Preview Modal -->
    <!-- Replace Document Modal -->
    <div class="modal fade" id="replaceDocModal{{ $prnotdoc->id }}" aria-labelledby="exampleModalLabel"
        aria-modal="true" role="dialog">
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
                            <h6 class="text-center">Trascina qui sotto il
                                documento
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
                    <h5 class="modal-title" id="exampleModalLabel">Attenzione
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('delete_file2') }}" method="Post">
                    @csrf

                    <input type="text" name="id" value="{{ $prnotdoc->id }}" hidden>

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
