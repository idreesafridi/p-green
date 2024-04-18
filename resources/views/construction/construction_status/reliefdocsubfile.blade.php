@if ($prnotdoc->state != 0)
    <tr>
        <td>
            <button type="button" class="viewFileBtn" style="outline: none; border: none; background-color: transparent;"
                data-filepath="error404greengen.png" data-bs-toggle="modal"
                data-bs-target="#viewFileModal{{ $prnotdoc->id }}"> <i class="fa fa-file-o"></i>
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

        <td class="hideInMobile">{{ $prnotdoc->updated_on }}</td>
        <td class="hideInMobile">{{ $prnotdoc->updated_by }}</td>
        <td class="space">
        </td>
        <td>
        <div style="display: inline-flex; width: 100%;">
            @if ($prnotdoc->updated_by != null)
                <button type="button" class="btn btn-link btn-sm text-warning d-inline" data-bs-toggle="modal"
                    data-bs-target="#bell{{ $prnotdoc->id }}Modal">
                    <i class="fa fa-bell"></i>
                </button>

                <button type="button" class="btn btn-link btn-sm text-dark d-inline"
                    onclick="location.href='{{ route('download_relief_sub_file', $prnotdoc->id) }}'">
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

    <!-- File Preview Modal -->
    <x-file-preview-modal modelId="viewFileModal{{$prnotdoc->id}}" filepath="{{ $prnotdoc->file_path }}"/>
    <!-- End File Preview Modal -->

    <!--  Modal -->
    <x-reminder-email-model modelId="bell{{ $prnotdoc->id }}Modal" folderName="{{ $prnotdoc->folder_name }}"
        conId="{{ $prnotdoc->ConstructionSite->id }}" />
    <!-- End Modal -->

    <!-- Delete Modal -->
    <div class="modal fade" id="warningModal{{ $prnotdoc->id }}" aria-labelledby="exampleModalLabel" aria-modal="true"
        role="dialog">
        <div class="modal-dialog modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Attenzione
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('rel_doc_sub_file_delete') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p class="text-center m-0">Sei sicuro di voler
                            procedere?</p>
                    </div>
                    <input type="hidden" name="id" value="{{ $prnotdoc->id }}">
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
