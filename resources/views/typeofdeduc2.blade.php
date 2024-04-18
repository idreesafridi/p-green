@if ($prnotdoc->state != 0)
    <tr>

        @if ($prnotdoc->folder_name != null)
            <td>
                <a class="fa fa-folder"
                    href="{{ route('type_of_deduc_sub2', [$pr_not_doc_id, $prnotdoc->type_of_dedection_sub1_id, $prnotdoc->id, $docname, $parent_folder_name]) }}"></a>
                <a href="{{ route('type_of_deduc_sub2', [$pr_not_doc_id, $prnotdoc->type_of_dedection_sub1_id, $prnotdoc->id, $docname, $parent_folder_name]) }}"
                    class="me-4 ms-2">
                    <strong>{{ $prnotdoc->folder_name }}</strong>
                </a><br>
                <small>{{ $prnotdoc->description }}</small>
            </td>
            <td>

                @php
                    // $countsub = 0;
                    //  $countsub3 = 0;
                    $count = 0;
                    //    dd($prnotdoc);
                    foreach ($prnotdoc->TypeOfDedectionFiles as $typeOfDedectionFiles) {
                        if (!empty($typeOfDedectionFiles['updated_on'] && $typeOfDedectionFiles['updated_by'] &&  $typeOfDedectionFiles['file_name'] )  && $typeOfDedectionFiles['state'] == 1 ) {
                            $count++;
                        }
                        foreach ($typeOfDedectionFiles->TypeOfDedectionFiles2 as $typeOfDedectionFiles2) {
                            if (!empty( $typeOfDedectionFiles2['updated_on']  && $typeOfDedectionFiles2['updated_by']  && $typeOfDedectionFiles2['file_name']) && $typeOfDedectionFiles2['state'] == 1  ) {
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
                <small>{{ $prnotdoc->description }}</small>
            </td>
            <td>
                @if ($prnotdoc->updated_by == null)
                    <span class="badge bg-danger">MANCANTE</span>
                @else
                    <span class="badge bg-success">CARICATO</span>
                @endif
            </td>
        @endif


        @php
            // if($prnotdoc ->folder_name =='Visto Di Conformita Sal 110'){
            //     dd($prnotdoc->TypeOfDedectionFiles()->latest('updated_on')->where('state', 1)->get());
            // }

            $latestUpdate = $prnotdoc
                ->TypeOfDedectionFiles()
                ->latest('updated_on')
                ->where('state', 1)
                ->first(['updated_by', 'updated_on']);
        @endphp
        {{-- @dd( $latestUpdate); --}}
        @if ($prnotdoc->file_name != null)
            <td class="hideInMobile">{{ $prnotdoc->updated_on }}</td>
            <td class="hideInMobile">{{ $prnotdoc->updated_by }}</td>
            {{-- @elseif($prnotdoc->folder_name='Visto Di Conformita Sal 110' )
        @dd($latestUpdate->updated_by);
        <td class="hideInMobile">{{ isset($count) && $count > 0  ? $latestUpdate->updated_on : '' }}</td>
        <td class="hideInMobile">{{ isset($count) && $count > 0  ? $latestUpdate->updated_by : '' }}</td> --}}
        @elseif($prnotdoc->folder_name != null)
            <td class="hideInMobile">{{ isset($count) && $count > 0 ? $latestUpdate->updated_on : '' }}</td>
            <td class="hideInMobile">{{ isset($count) && $count > 0 ? $latestUpdate->updated_by : '' }}</td>
        @endif
        {{-- @dd($latestUpdate->updated_by) --}}

        {{-- <td class="hideInMobile">{{ isset($count) && $count > 0 || $prnotdoc->file_name != null ? $prnotdoc->updated_on : '' }}</td>
        <td class="hideInMobile">{{isset($count) && $count > 0 || $prnotdoc->file_name != null ?  $prnotdoc->updated_by : '' }}</td> --}}

        <td class="space">
            <form action="{{ route('replace_sub2_file') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <input type="text" name="bydefault" value="{{ $prnotdoc->bydefault }}" hidden>

                <input type="text" name="pr_not_doc_id" value="{{ $pr_not_doc_id }}" hidden>
                <input type="text" name="file_id" value="{{ $prnotdoc->id }}" hidden>
                <input type="text" name="type_of_dedection_sub1_id"
                    value="{{ $prnotdoc->type_of_dedection_sub1_id }}" hidden>

                <input type="text" name="parent1_folder_name" value="{{ $docname }}" hidden>

                <input type="text" name="parent2_folder_name" value="{{ $parent_folder_name }}" hidden>


                <input type="text" name="orignal_name" value="{{ $prnotdoc->file_name }}" hidden>


                @if ($prnotdoc->updated_by == null && $prnotdoc->file_name != null)
                    <input type="file" autocomplete="off" class="form-control" id="" name="file"
                        style="color:grey !important;" onchange="this.form.submit();">
                @endif

            </form>
        </td>
        <td>
            <div style="display: inline-flex; width: 100%;">
                <button type="button" class="btn btn-link btn-sm text-warning d-inline" data-bs-toggle="modal"
                    data-bs-target="#bell{{ $prnotdoc->id }}Modal">
                    <i class="fa fa-bell"></i>
                </button>
                @if (($prnotdoc->file_name != null && $prnotdoc->updated_by != null) || (isset($count) && $count > 0))
                    @if ($prnotdoc->file_name != null)
                        <button type="button" class="btn btn-link btn-sm text-dark d-inline"
                            onclick="location.href='{{ route('download_sub2', $prnotdoc->id) }}'">
                            <i class="fa fa-download"></i>
                        </button>
                    @else
                        <button type="button" class="btn btn-link btn-sm text-dark d-inline"
                            onclick="location.href='{{ route('download_sub2_folder', [$docname, $parent_folder_name, $prnotdoc->folder_name]) }}'">
                            <i class="fa fa-download"></i>
                        </button>
                        <button type="button" class="btn btn-link btn-sm text-danger d-inline"
                            onclick="location.href='{{ route('delete_sub2_folder', $prnotdoc->id) }}'">
                            <i class="fa fa-trash"></i>
                        </button>
                    @endif

                    @if ($prnotdoc->file_name != null && $prnotdoc->bydefault != 0)
                        <a class="btn btn-link btn-sm text-dark d-inline" data-bs-toggle="modal"
                            data-bs-target="#replaceDocModal{{ $prnotdoc->id }}">
                            <i class="fa fa-exchange"></i>
                        </a>
                    @endif


                    @if ($prnotdoc->file_name != null)
                        <a class="btn btn-link btn-sm text-danger d-inline" data-bs-toggle="modal"
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
                    @if ($prnotdoc->file_path == null)
                        <p>File non caricato. Per favore carica prima.</p>
                    @else
                        <iframe id="view-file-frame" src="{{ asset('construction-assets/' . $prnotdoc->file_path) }}"
                            width="100%" height="600px"></iframe>
                    @endif
                </div>
            </div>
        </div>
    </div> --}}

    <x-file-preview-modal modelId="viewFileModal{{$prnotdoc->id}}" filepath="{{ $prnotdoc->file_path }}"/>
    <!-- End File Preview Modal -->

    <!--  Modal -->
    <x-reminder-email-model modelId="bell{{ $prnotdoc->id }}Modal"
        folderName="{{ $prnotdoc->file_name == null ? $prnotdoc->folder_name : $prnotdoc->file_name }}"
        conId="{{ $prnotdoc->ConstructionSite->id }}" />
    <!-- End Modal -->

    <!-- End Notification Modal -->
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
                <form action="{{ route('replace_sub2_file') }}" method="POST" enctype="multipart/form-data">
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

                        <input type="text" name="pr_not_doc_id" value="{{ $pr_not_doc_id }}" hidden>
                        <input type="text" name="file_id" value="{{ $prnotdoc->id }}" hidden>
                        <input type="text" name="type_of_dedection_sub1_id"
                            value="{{ $prnotdoc->type_of_dedection_sub1_id }}" hidden>

                        <input type="text" name="parent1_folder_name" value="{{ $docname }}" hidden>

                        <input type="text" name="parent2_folder_name" value="{{ $parent_folder_name }}" hidden>


                        <input type="text" name="orignal_name" value="{{ $prnotdoc->file_name }}" hidden>

                        <div class="mb-4">
                            <input type="file" autocomplete="off" class="form-control file-uploader"
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
    <div class="modal fade" id="warningModal{{ $prnotdoc->id }}" aria-labelledby="exampleModalLabel"
        aria-modal="true" role="dialog">
        <div class="modal-dialog modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Attenzione</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('deletefiles_sub2') }}" method="Post">
                    @csrf

                    <input type="text" name="id" value="{{ $prnotdoc->id }}" hidden>

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
@endif
