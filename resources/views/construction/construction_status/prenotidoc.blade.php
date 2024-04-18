@if ($prnotdoc->state == 1)
    <tr>
        <td>
            <a class="fa fa-folder" href="{{ route('show_prenoti_doc_file', $prnotdoc->id) }}"></a>
            <a href="{{ route('show_prenoti_doc_file', $prnotdoc->id) }}" class="me-4 ms-2">
                <strong>{{ $prnotdoc->folder_name }}</strong>
            </a><br>
            <small>{{ $prnotdoc->description }}</small>
        </td>
        <td>
            @php
                
                $count = $prnotdoc->FilesCounting();

            @endphp
            @if ($count > 0)
                <span class="badge bg-success">{{ $count }}</span>
            @else
                <span class="badge bg-danger">{{ $count }}</span>
            @endif
        </td>
        <td class="hideInMobile">{{$count > 0 && $prnotdoc->getLatestUpdate() ? $prnotdoc->getLatestUpdate()->updated_on : '' }}</td>
        <td class="hideInMobile">{{ $count > 0 && $prnotdoc->getLatestUpdate() ? $prnotdoc->getLatestUpdate()->updated_by : '' }}</td>

        <td class="space"></td>
        <td>
            <div style="display: inline-flex; width: 100%;">
                <button type="button" class="btn btn-link btn-sm text-warning d-inline" data-bs-toggle="modal"
                    data-bs-target="#bell{{ $prnotdoc->id }}Modal">
                    <i class="fa fa-bell"></i>
                </button>
                @if ($count > 0)

                    @if ($prnotdoc->file_name != null)
                        <button type="button" class="btn btn-link btn-sm text-dark d-inline"
                            onclick="location.href='{{ route('download_prenotidoc', [$prnotdoc->id, $var->id]) }}'">
                            <i class="fa fa-download"></i>
                        </button>
                        <button type="button" class="btn btn-link btn-sm text-danger d-inline"
                        onclick="location.href='{{ route('delete_prinotdoc', [$prnotdoc->id]) }}'">
                        <i class="fa fa-trash"></i>
                         </button>
                    @else
                        <button type="button" class="btn btn-link btn-sm text-dark d-inline"
                            onclick="location.href='{{ route('download_prenotidoc_folder', [$prnotdoc->folder_name, $var->id]) }}'">
                            <i class="fa fa-download"></i>
                        </button>
                        <button type="button" class="btn btn-link btn-sm text-danger d-inline"
                        onclick="location.href='{{ route('DeleteAllPrinotdoc', [$prnotdoc->id]) }}'">
                        <i class="fa fa-trash"></i>
                         </button>
                    @endif


                @endif
            </div>
        </td>

    </tr>

@endif
