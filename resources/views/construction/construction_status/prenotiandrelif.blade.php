@if ($reliefdocument->state == 1)

    <tr>
        <td>
            <a class="fa fa-folder" href="{{ route('show_relief_doc_file', $reliefdocument->id) }}"></a>
            <a href="{{ route('show_relief_doc_file', $reliefdocument->id) }}" class="me-4 ms-2">
                <strong>{{ $reliefdocument->folder_name }} </strong>
            </a><br>

            <small>{{ $reliefdocument->description }}</small>
        </td>
        <td>
            @php
                // dd($reliefdocument);
                $count = 0;
                // dd($reliefdocument->where('folder_name', "Documenti Rilievo")->first()->ReliefDocumentFile->where('folder_name', 'Scheda Dati Ante Opera')->first());
                foreach ($reliefdocument->ReliefDocumentFile as $file) {
                    if (!empty($file['updated_on'] && $file['file_name'])   && $file['state'] == 1) {
                        $count++;
                    }

                    // dd($file);
                    foreach ($file->RelifDocFileSub1 as $RelifDocFileSub1) {
                        if (!empty($RelifDocFileSub1['updated_on']  && $RelifDocFileSub1['file_name'])   && $RelifDocFileSub1['state'] == 1) {
                            $count++;
                        }
                    }
                }
            @endphp

            <span class="badge {{ $count > 0 ? 'bg-success' : 'bg-danger' }} ">{{ $count }}</span>
        </td>
        @if( $prnotdoc->folder_name!=null)
        @php
            $latestUpdate = $reliefdocument->ReliefDocumentFile()
                ->where('state', 1)
                ->latest('updated_on')
                ->first(['updated_by', 'updated_on']);
        @endphp

        @endif
        <td class="hideInMobile">{{  $latestUpdate ? $latestUpdate->updated_on : '' }}</td>
        <td class="hideInMobile">{{ $latestUpdate ? $latestUpdate->updated_by : '' }}</td>
        <td class="space"></td>
        <td>
            <div style="display: inline-flex; width: 100%;">
                <button type="button" class="btn btn-sm text-warning border-0 d-inline" data-bs-toggle="modal"
                    data-bs-target="#bell{{ $reliefdocument->id }}Modal">
                    <i class="fa fa-bell"></i>
                </button>
                @if ($count > 0)
                    @if ($reliefdocument->file_name != null)
                        <button type="button" class="btn btn-link btn-sm text-dark d-inline"
                            onclick="location.href='{{ route('download_reliefdoc', $reliefdocument->id) }}'">
                            <i class="fa fa-download"></i>
                        </button>
                        <button type="button" class="btn btn-link btn-sm text-danger d-inline"
                            onclick="location.href='{{ route('delete_reliefdoc', [$reliefdocument->id]) }}'">
                            <i class="fa fa-trash"></i>
                        </button>
                    @else
                        <button type="button" class="btn btn-link btn-sm text-dark d-inline"
                            onclick="location.href='{{ route('download_reliefdoc_folder', $reliefdocument->folder_name) }}'">
                            <i class="fa fa-download"></i>
                        </button>
                        <button type="button" class="btn btn-link btn-sm text-danger d-inline"
                            onclick="location.href='{{ route('DeleteAllReliefdoc', [$reliefdocument->id]) }}'">
                            <i class="fa fa-trash"></i>
                        </button>
                    @endif
                @endif
            </div>
        </td>
    </tr>

    <!--  Modal -->
    <x-reminder-email-model modelId="bell{{ $reliefdocument->id }}Modal"
        folderName="{{ $reliefdocument->folder_name }}" conId="{{ $var->id }}" />
    <!-- End Modal -->

@endif
