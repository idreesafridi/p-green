<tr>
    <td>
        <a class="fa fa-folder" href="{{ route('show_relief_doc_file', $common_schemi['Schemi']->id) }}"></a>
        <a href="{{ route('show_relief_doc_file', $common_schemi['Schemi']->id) }}" class="me-4 ms-2">
            <strong>{{ $common_schemi['Schemi']->folder_name }}</strong>
        </a><br>
        <small>{{ $common_schemi['Schemi']->description }}</small>
    </td>
    <td>
        @php
            $count = 0;
            foreach ($common_schemi['Schemi']->ReliefDocumentFile as $file) {
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

    @php
        // dd($prnotdoc->TypeOfDedectionSub2);
        $latestUpdate = $common_schemi['Schemi']
            ->ReliefDocumentFile()
            ->where('state', 1)
            ->latest('updated_on')
            ->first(['updated_by', 'updated_on']);
    @endphp


    {{-- <td class="hideInMobile">{{ $common_schemi['Schemi']->updated_on }}</td>
    <td class="hideInMobile">{{ $common_schemi['Schemi']->updated_by }}</td> --}}
    @if ($common_schemi['Schemi']->file_name != null)
        <td class="hideInMobile">{{ $common_schemi['Schemi']->updated_on }}</td>
        <td class="hideInMobile">{{ $common_schemi['Schemi']->updated_by }}</td>
    @elseif($common_schemi['Schemi']->folder_name != null)
        <td class="hideInMobile">{{ isset($count) && $count > 0 ? $latestUpdate->updated_on : '' }}</td>
        <td class="hideInMobile">{{ isset($count) && $count > 0 ? $latestUpdate->updated_by : '' }}</td>
    @endif
    <td class="space"></td>
    <td>
        {{-- <div style="display: inline-flex; width: 100%;">

            <button type="button" class="btn btn-sm text-warning border-0 d-inline" data-bs-toggle="modal"
                data-bs-target="#bell{{ $common_schemi['Schemi']->id }}Modal">
                <i class="fa fa-bell"></i>
            </button>

            @if ($common_schemi['Schemi']->updated_by != null)
                @if ($common_schemi['Schemi']->file_name != null)
                    <button type="button" class="btn btn-link btn-sm text-dark d-inline"
                        onclick="location.href='{{ route('download_reliefdoc', $common_schemi['Schemi']->id) }}'">
                        <i class="fa fa-download"></i>
                    </button>
                @else
                    <button type="button" class="btn btn-link btn-sm text-dark d-inline"
                        onclick="location.href='{{ route('download_reliefdoc_folder', $common_schemi['Schemi']->folder_name) }}'">
                        <i class="fa fa-download"></i>
                    </button>
                @endif
            @endif

        </div> --}}

        <div style="display: inline-flex; width: 100%;">
            {{-- start --}}
            <button type="button" class="btn btn-sm text-warning border-0 d-inline" data-bs-toggle="modal"
                data-bs-target="#bell{{ $common_schemi['Schemi']->id }}Modal">
                <i class="fa fa-bell"></i>
            </button>
            @if (isset($count) && $count > 0 || $common_schemi['Schemi'] != null)
    
    
            {{-- @if ($common_schemi['Schemi']->updated_by != null) --}}
                @if ($common_schemi['Schemi']->file_name != null)
                    <button type="button" class="btn btn-link btn-sm text-dark d-inline"
                        onclick="location.href='{{ route('download_reliefdoc', $common_schemi['Schemi']->id) }}'">
                        <i class="fa fa-download"></i>
                    </button>
                @else
                    <button type="button" class="btn btn-link btn-sm text-dark d-inline"
                        onclick="location.href='{{ route('download_reliefdoc_folder', $common_schemi['Schemi']->folder_name) }}'">
                        <i class="fa fa-download"></i>
                    </button>
                    <button type="button" class="btn btn-link btn-sm text-danger d-inline"
                    onclick="location.href='{{ route('DeleteAllReliefdoc', [$common_schemi['Schemi']->id]) }}'">
                    <i class="fa fa-trash"></i>
                </button>
                </a>
                @endif
            @endif
            {{-- end --}}
        </div>
    
    </td>
</tr>

<!--  Modal -->
<x-reminder-email-model modelId="bell{{ $common_schemi['Schemi']->id }}Modal"
    folderName="{{ $common_schemi['Schemi']->fodler_name == null ? $common_schemi['Schemi']->file_name : $common_schemi['Schemi']->fodler_name }}"
    conId="{{ $construct_id }}" />
<!-- End Modal -->
