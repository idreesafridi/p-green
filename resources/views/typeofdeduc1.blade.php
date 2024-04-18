<tr>
  
    @if ($prnotdoc->folder_name != null)


        <td>
            <a class="fa fa-folder"
                href="{{ route('type_of_deduc_sub1', [$prnotdoc->pr_not_doc_id, $prnotdoc->id, $folder_name]) }}"></a>
            <a href="{{ route('type_of_deduc_sub1', [$prnotdoc->pr_not_doc_id, $prnotdoc->id, $folder_name]) }}"
                class="me-4 ms-2">
                <strong>{{ $prnotdoc->folder_name }}</strong>
            </a><br>
            <small>{{ $prnotdoc->description }}</small>

        </td>
        <td>
            @php
            $count =  $prnotdoc->FilesCounting();
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

             <small>{{ $prnotdoc->description }}</small>

        </td>
        <td>
            @if ($prnotdoc->updated_on != null)
                <span class="badge bg-success">CARICATO</span>
            @else
                <span class="badge bg-danger">MANCANTE</span>
            @endif
        </td>
    @endif

    @php
    // dd($prnotdoc->TypeOfDedectionSub2);
    $latestUpdate = $prnotdoc->TypeOfDedectionSub2()
        ->where('state', 1)
        ->latest('updated_on')
        ->first(['updated_by', 'updated_on']);
@endphp

    @if($prnotdoc->file_name != null)
    <td class="hideInMobile">{{ $prnotdoc->updated_on  }}</td>
    <td class="hideInMobile">{{  $prnotdoc->updated_by  }}</td>
    @elseif($prnotdoc->folder_name != null)
    <td class="hideInMobile">{{ isset($count) && $count > 0  ? $latestUpdate->updated_on : '' }}</td>
    <td class="hideInMobile">{{ isset($count) && $count > 0  ? $latestUpdate->updated_by : '' }}</td>
    @endif
    {{-- <td class="hideInMobile">{{isset($count) && $count > 0 ||  $prnotdoc->file_name != null ? $prnotdoc->updated_on : '' }}</td>
    <td class="hideInMobile">{{ isset($count) && $count > 0 ||  $prnotdoc->file_name != null ? $prnotdoc->updated_by : ''}}</td> --}}
    <td class="space">
        <form action="{{ route('replace_sub1_file') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <input type="text" name="bydefault" value="{{ $prnotdoc->bydefault }}" hidden>

            <input type="text" name="pr_not_doc_id" value="{{ $prnotdoc->pr_not_doc_id }}" hidden>
            <input type="text" name="file_id" value="{{ $prnotdoc->id }}" hidden>
            <input type="text" name="parent1_folder_name" value="{{ $folder_name }}" hidden>

            <input type="text" name="orignal_name" value="{{ $prnotdoc->file_name }}" hidden>

            <input type="text" name="type_of_dedection_sub1_id" value="{{ $prnotdoc->id }}" hidden>
            @if ($prnotdoc->updated_by == null && $prnotdoc->file_name != null)
                <input type="file" autocomplete="off" class="form-control" id="" name="file"
                    style="color:grey !important;" onchange="this.form.submit();">
            @endif

        </form>
    </td>
    <td>
    <div  style="display: inline-flex;
    width: 100%;">
        <button type="button" class="btn btn-link btn-sm text-warning" data-bs-toggle="modal"
            data-bs-target="#bell{{ $prnotdoc->id }}Modal">
            <i class="fa fa-bell"></i>
        </button>

        @if (isset($count) && $count > 0 || $prnotdoc->file_name != null)
            @if ($prnotdoc->file_name != null)
                <button type="button" class="btn btn-link btn-sm text-dark"
                    onclick="location.href='{{ route('download_sub1', $prnotdoc->id) }}'">
                    <i class="fa fa-download"></i>
                </button>
                {{-- <button type="button" class="btn btn-link btn-sm text-danger"
                onclick="location.href='{{ route('delete_sub1', $prnotdoc->id) }}'">
                <i class="fa fa-trash"></i>
                 </button> --}}
            @else
                <button type="button" class="btn btn-link btn-sm text-dark"
                    onclick="location.href='{{ route('download_sub1_folder', [$folder_name, $prnotdoc->folder_name]) }}'">
                    <i class="fa fa-download"></i>
                </button>
                <button type="button" class="btn btn-link btn-sm text-danger"
                onclick="location.href='{{ route('Delete_All_sub1', $prnotdoc->id) }}'">
                <i class="fa fa-trash"></i>
                 </button>
            @endif

            {{-- file exchange --}}
            @if ($prnotdoc->file_name != null && $prnotdoc->bydefault != 0)
                <a class="btn btn-link btn-sm text-dark" data-bs-toggle="modal"
                    data-bs-target="#replaceDocModal{{ $prnotdoc->id }}">
                    <i class="fa fa-exchange"></i>
                </a>
            @endif

            @if ($prnotdoc->file_name != null)
                <a class="btn btn-link btn-sm text-danger" data-bs-toggle="modal"
                    data-bs-target="#warningModal{{ $prnotdoc->id }}">
                    <i class="fa fa-trash"></i>
                </a>
            @endif
        @endif
    </div>
    </td>
</tr>

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

  <!-- File Preview Modal -->
  <x-file-preview-modal modelId="viewFileModal{{$prnotdoc->id}}" filepath="{{ $prnotdoc->file_path }}"/>
    <!-- End File Preview Modal -->
<!-- End File Preview Modal -->

<!--  Modal -->
<x-reminder-email-model modelId="bell{{ $prnotdoc->id }}Modal"
    folderName="{{ $prnotdoc->file_name == null ? $prnotdoc->folder_name : $prnotdoc->file_name }}"
    conId="{{ $prnotdoc->ConstructionSite->id }}" />
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
            <form action="{{ route('replace_sub1_file') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row mb-3 mt-5">
                        <img src="{{ asset('assets/images/swap-img.svg') }}" class="alert-img mx-auto">
                    </div>
                    <div class="mb-4">
                        <h6 class="text-center">Trascina qui sotto il documento
                            oppure selezionalo dal tuo PC</h6>
                    </div>

                    <input type="text" name="bydefault" value="{{ $prnotdoc->bydefault }}" hidden>

                    <input type="text" name="pr_not_doc_id" value="{{ $prnotdoc->pr_not_doc_id }}" hidden>
                    <input type="text" name="file_id" value="{{ $prnotdoc->id }}" hidden>
                    <input type="text" name="parent1_folder_name" value="{{ $folder_name }}" hidden>

                    <input type="text" name="orignal_name" value="{{ $prnotdoc->file_name }}" hidden>

                    <input type="text" name="type_of_dedection_sub1_id" value="{{ $prnotdoc->id }}" hidden>

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
<div class="modal fade" id="warningModal{{ $prnotdoc->id }}" aria-labelledby="exampleModalLabel" aria-modal="true"
    role="dialog">
    <div class="modal-dialog modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Attenzione</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

              <form action="{{ route('destroy_sub1') }}" method="Post">

            @csrf
            <input type="hidden" name="id" value="{{ $prnotdoc->id }}">
            <div class="modal-body">
                <p class="text-center m-0">Sei sicuro di voler procedere?</p>
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
