@if ($prnotdoc->state == 1)
    <p>regprac call</>
        <br>
        <tr>
            <td>
                <a class="fa fa-folder" href="{{ route('regprac_prac', $var->statusRegPrac->id) }}"></a>
                <a href="{{ route('regprac_prac', $var->statusRegPrac->id) }}" class="me-4 ms-2">
                    <strong>Pratiche Comunali</strong>
                </a><br>
            </td>
            <td>
                @php
                    $count = 0;
                    $updated_on = '';
                    $updated_by = '';
                    foreach ($var->statusRegPrac->RegPracDoc as $file) {
                        if ($file['updated_on'] != null && $file['file_name'] && [($file['state'] = 'MANCANTE' || $file['state'] == 1)]) {
                            $updated_on = $file->updated_on;
                            $updated_by = $file->updated_by;
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
            <td class="hideInMobile">{{ $updated_on }}</td>
            <td class="hideInMobile">{{ $updated_by }}</td>
            <td class="space"></td>
            <td>
            <div style="display: inline-flex; width: 100%;">
                <button type="button" class="btn btn-link btn-sm text-warning d-inline" data-bs-toggle="modal"
                    data-bs-target="#bell{{ $var->statusRegPrac->id }}Modal">
                    <i class="fa fa-bell"></i>
                </button>
               
                @if ($var->statusRegPrac->updated_by != null)
                    @if ($var->statusRegPrac->file_name != null)
                        <button type="button" class="btn btn-link btn-sm text-dark d-inline"
                            onclick="location.href='{{ route('download_prenotidoc', [$var->statusRegPrac->id, $var->id  ]) }}'">
                            <i class="fa fa-download"></i>
                        </button>
                    @else
                        <button type="button" class="btn btn-link btn-sm text-dark d-inline"
                            onclick="location.href='{{ route('download_prenotidoc_folder', [$var->statusRegPrac->folder_name, $var->id]) }}'">
                            <i class="fa fa-download"></i>
                        </button>
                    @endif
                @endif
            </div>
            </td>
        </tr>

@endif
