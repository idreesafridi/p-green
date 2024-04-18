@if ($reliefdocument->state == 1)
    <tr>
        <td>
            <a class="fa fa-folder" href="{{ route('show_relief_doc_file', $reliefdocument->id) }}"></a>
            <a href="{{ route('show_relief_doc_file', $reliefdocument->id) }}" class="me-4 ms-2">
                <strong>{{ $reliefdocument->folder_name }}</strong>
            </a><br>
            <small>{{ $reliefdocument->description }}</small>
        </td>
        <td>

            @php
                $count = 0;
                foreach ($reliefdocument->ReliefDocumentFile as $file) {
                    if ($file['updated_on'] != null && $file['file_name'] != null && $file['state'] == 1) {
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
        <td class="hideInMobile">{{ $reliefdocument->updated_on }}</td>
        <td class="hideInMobile">{{ $reliefdocument->updated_by }}</td>
        <td class="space"></td>
        <td>
        <div style="display: inline-flex; width: 100%;">
            <button type="button" class="btn btn-sm text-warning border-0 d-inline" data-bs-toggle="modal"
                data-bs-target="#bell{{ $reliefdocument->id }}Modal">
                <i class="fa fa-bell"></i>
            </button>

            @if ($reliefdocument->updated_by != null)
                @if ($reliefdocument->file_name != null)
                    <button type="button" class="btn btn-link btn-sm text-dark d-inline"
                        onclick="location.href='{{ route('download_reliefdoc', $reliefdocument->id) }}'">
                        <i class="fa fa-download"></i>
                    </button>
                @else
                    <button type="button" class="btn btn-link btn-sm text-dark d-inline"
                        onclick="location.href='{{ route('download_reliefdoc_folder', $reliefdocument->folder_name) }}'">
                        <i class="fa fa-download"></i>
                    </button>
                @endif

                {{-- <a class="btn btn-link btn-sm text-danger" data-bs-toggle="modal" data-bs-target="#warningModal">
            <i class="fa fa-trash"></i>
        </a> --}}

                {{-- <a class="btn btn-link btn-sm text-danger"
            onclick="return confirm('Are you sure?')"
            href="{{ route('delete_reliefdoc', $reliefdocument->id) }}"><i
                class="fa fa-trash"></i> --}}
            @endif
        </div>
        </td>
    </tr>

    <!--  Modal -->
    <x-reminder-email-model modelId="bell{{ $reliefdocument->id }}Modal"
        folderName="{{ $reliefdocument->folder_name }}" conId="{{ $reliefdocument->ConstructionSite->id }}" />
    <!-- End Modal -->

    <!-- Delete Modal -->
    <div class="modal fade" id="warningModal" aria-labelledby="exampleModalLabel" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Attenzione</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="POST">
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
