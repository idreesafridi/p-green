<x-app-layout :pageTitle="request()->route()->pagename">
    @section('styles')
    @endsection

    <x-construction-detail-head :consId="$construct_id"  />
    <x-construction-detail-nav :constructionid="$construct_id" />
  
    <div class="tab-content">
        <div class="card p-4 border-0 site-detail-card">
            <div class="card-head document-page-header py-4">
                <div class="d-flex align-items-center">
                    <a href="{{ url()->previous() }}">
                        <i class="fa fa-arrow-left me-3 back"></i>
                    </a>
                    <h6 class="heading fw-bold mb-0">Documenti Fotovoltaico</h6>
                </div>
                <div class="row">
                    <div class="col-12 mt-4 col-lg-4 d-flex align-items-center">
                        <input type="text" id="searchfield" class="form-control head-input"
                            placeholder="Cerca tra i documenti">
                    </div>
                    <div class="col-12 col-lg-8 mt-4 d-md-flex justify-content-between align-items-center">
                        <div>
                            <div style="float: right;">
                            </div>

                        </div>
                        <div class="text-end">

                            @php
                                $slug = 'all';
                            @endphp
                            <a class="btn btn-green" href="{{ route('zip_doc_files', $slug) }}">
                                <i class="fa fa-download me-2"> Scarica tutto</i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="tab-content">
                    <div class="tab-pane fade active show table-responsive" id="filterTab1">
                        <table class="table document-table table-hover ">
                            <thead>
                                <tr>
                                    <th scope="col">Nome Documento</th>
                                    <th scope="col">Stato</th>
                                    <th scope="col" class="hideInMobile">Aggiornato il</th>
                                    <th scope="col" class="hideInMobile">Aggiornato Da</th>
                                    <th scope="col" class=""></th>
                                    <th scope="col"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($prenoti_doc['prenoti_and_relif']->unique('folder_name') as $reliefdocument)
                                    @if ($reliefdocument->state == 1)
                                        @if ($reliefdocument->folder_name == 'Diagnosi Energetica' || $reliefdocument->folder_name == 'Schemi Impianti' || $reliefdocument->folder_name == 'Documenti Clienti' || $reliefdocument->folder_name == 'Documenti Fotovoltaico' || $reliefdocument->folder_name == 'Documenti Co-intestatari')
                                            <tr>
                                                <td>
                                                    <a class="fa fa-folder" href="{{ route('show_relief_doc_file', $reliefdocument->id) }}"></a>
                                                    <a href="{{ route('show_relief_doc_file', $reliefdocument->id) }}" class="me-4 ms-2">
                                                        <strong>{{ $reliefdocument->folder_name }} </strong>
                                                    </a><br>
                                                
                                                    <small>{{$reliefdocument->description}}</small>
                                                </td>
                                                <td>
                                                    @php
                                                        $count = 0;
                                                        // dd($reliefdocument);
                                                        foreach ($reliefdocument->ReliefDocumentFile->unique('file_name') as $file) {
                                                            if ($file['updated_on'] != null && $file['file_name'] && $file['state'] == 1) {
                                                                $count++;
                                                            }
                                                            
                                                            // foreach($file->RelifDocFileSub1 as $RelifDocFileSub1){
                                                            //     if ($RelifDocFileSub1['updated_on'] != null && $RelifDocFileSub1['file_name'] && $RelifDocFileSub1['state'] == 1) {
                                                            //     $count++;
                                                                
                                                            // }
                                                            //  dd($RelifDocFileSub1);
                                                            // }
                                                        
                                                        }
                                                    @endphp
                                                    @if ($count > 0)
                                                        <span class="badge bg-success">File Caricati</span>
                                                    @else
                                                        <span class="badge bg-danger">Vuoto</span>
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
                                        @endif
                                    @endif

                                    <!--  Modal -->
                                    <x-reminder-email-model modelId="bell{{ $reliefdocument->id }}Modal"
                                        folderName="{{ $reliefdocument->folder_name }}" conId="{{ $var->id }}">
                                    </x-reminder-email-model>
                                    <!-- End Modal -->
                                @endforeach

                                @foreach ($prenoti_doc['prenoti_doc']->PrNotDoc as $prnotdoc)
                                    @if ($prnotdoc->state == 1)
                                        @if ($prnotdoc->folder_name == 'Dico' || $prnotdoc->folder_name == 'Documentazione Varia')
                                            <tr>
                                                <td>
                                                    <a class="fa fa-folder" href=""></a>
                                                    <a href="{{ route('show_prenoti_doc_file', $prnotdoc->id) }}" class="me-4 ms-2">
                                                        <strong>{{ $prnotdoc->folder_name }}</strong>
                                                    </a><br>
                                                    <small>{{ $prnotdoc->description }}</small>
                                                </td>
                                                <td>
                                                    @php
                                                        $count = 0;
                                                        
                                                        if ($prnotdoc->folder_name == 'Documenti 110' || $prnotdoc->folder_name == 'Documenti 90' || $prnotdoc->folder_name == 'Documenti 65' || $prnotdoc->folder_name == 'Documenti 50' ||  $prnotdoc->folder_name == 'Documenti Sicurezza') {
                                                            foreach ($prnotdoc->PrNotDocFile as $PrNotDocFile) {
                                                                if ($PrNotDocFile['updated_on'] != null && $PrNotDocFile['file_name'] && $PrNotDocFile['state'] == 1 && $PrNotDocFile['file_name'] != null) {
                                                                    $count++;
                                                                } 
                                                            }
                                                            
                                                            foreach ($prnotdoc->TypeOfDedectionSub1 as $file) {
                                                                if ($file['updated_on'] != null && $file['updated_by'] != null  && $file['state'] == 1 && $file['file_name'] != null) {
                                                                    $count++;
                                                                }
                                                        
                                                                foreach ($file->TypeOfDedectionSub2 as $TypeOfDedectionSub2) {
                                                                    if ($TypeOfDedectionSub2['updated_on'] !== null && $TypeOfDedectionSub2['updated_by'] !== null && $TypeOfDedectionSub2['state'] == 1 && $TypeOfDedectionSub2['file_name'] != null) {
                                                                        $count++;
                                                                    }
                                                                    foreach ($TypeOfDedectionSub2->TypeOfDedectionFiles as $TypeOfDedectionFiles) {
                                                                        if ($TypeOfDedectionFiles['updated_on'] !== null && $TypeOfDedectionFiles['updated_by'] !== null && $TypeOfDedectionFiles['state'] == 1 && $TypeOfDedectionFiles['file_name'] != null) {
                                                                            $count++;
                                                                        }
                                                                        foreach ($TypeOfDedectionFiles->TypeOfDedectionFiles2 as $TypeOfDedectionFiles2) {
                                                                            if ($TypeOfDedectionFiles2['updated_on'] !== null && $TypeOfDedectionFiles2['updated_by'] !== null && $TypeOfDedectionFiles2['state'] == 1 && $TypeOfDedectionFiles2['file_name'] != null) {
                                        
                                                                                $count++;
                                                                            }
                                                                        }
                                                                
                                                                    }
                                                                }
                                                            }
                                                        } 
                                                        else {
                                                            foreach ($prnotdoc->PrNotDocFile as $file) {
                                                                if ($file['updated_on'] != null && $file['updated_by'] != null && $file['state'] == 1 && $file['file_name'] != null) {
                                                                    $count++;
                                                                }
                                                            }
                                                        }
                                        
                                        
                                                    @endphp
                                                    @if ($count > 0)
                                                        <span class="badge bg-success">File Caricati</span>
                                                    @else
                                                        <span class="badge bg-danger">Vuoto</span>
                                                    @endif
                                                </td>
                                                <td class="hideInMobile">{{ $prnotdoc->updated_on }}</td>
                                                <td class="hideInMobile">{{ $prnotdoc->updated_by }}</td>
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
                                    @endif

                                    <!--  Modal -->
                                    <x-reminder-email-model modelId="bell{{ $prnotdoc->id }}Modal"
                                        folderName="{{ $prnotdoc->folder_name }}" conId="{{ $var->id }}">
                                    </x-reminder-email-model>
                                    <!-- End Modal -->
                                @endforeach

                                @if ($data['Files'] != null)
                                    @foreach ($data['Files'] as $prnotdoc)
                                        @if ($prnotdoc->state != 0)
                                            @php
                                                $parent2 = \App\Models\TypeOfDedectionSub1::where('id', $prnotdoc->type_of_dedection_sub1_id)->first();
                                                $parent1 = \App\Models\PrNotDoc::where('id', $parent2->pr_not_doc_id)->first();
                                            @endphp
                                            <tr>
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
                                                <td class="hideInMobile">{{ $prnotdoc->updated_on }}</td>
                                                <td class="hideInMobile">{{ $prnotdoc->updated_by }}</td>
                                                <td class="space">
                                                    <form action="{{ route('replace_sub2_file') }}" method="POST" enctype="multipart/form-data">
                                                        @csrf
                                        
                                                        <input type="text" name="bydefault" value="{{ $prnotdoc->bydefault }}" hidden>
                                        
                                                        <input type="text" name="pr_not_doc_id" value="{{ $parent2->pr_not_doc_id }}" hidden>
                                                        <input type="text" name="file_id" value="{{ $prnotdoc->id }}" hidden>
                                                        <input type="text" name="type_of_dedection_sub1_id"
                                                            value="{{ $prnotdoc->type_of_dedection_sub1_id }}" hidden>
                                        
                                                        <input type="text" name="parent1_folder_name" value="{{ $parent1->folder_name }}" hidden>
                                        
                                                        <input type="text" name="parent2_folder_name" value="{{ $parent2->folder_name }}" hidden>
                                        
                                        
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
                                                        @if ($prnotdoc->updated_by != null)
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


                                                <!--  Modal -->
                                                <x-reminder-email-model modelId="bell{{ $prnotdoc->id }}Modal"
                                                    folderName="{{ $prnotdoc->file_name == null ? $prnotdoc->folder_name : $prnotdoc->file_name }}"
                                                    conId="{{ $prnotdoc->ConstructionSite->id }}" />
                                                <!-- End Modal -->

                                                <x-file-preview-modal
                                                    modelId="viewFileModal{{ $prnotdoc->id }}"
                                                    filepath="{{ $prnotdoc->file_path }}" />

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

                                                                    <input type="text" name="pr_not_doc_id" value="{{ $parent2->pr_not_doc_id }}" hidden>
                                                                    <input type="text" name="file_id" value="{{ $prnotdoc->id }}" hidden>
                                                                    <input type="text" name="type_of_dedection_sub1_id"
                                                                        value="{{ $prnotdoc->type_of_dedection_sub1_id }}" hidden>

                                                                    <input type="text" name="parent1_folder_name" value="{{ $parent1->folder_name }}" hidden>

                                                                    <input type="text" name="parent2_folder_name" value="{{ $parent2->folder_name }}" hidden>


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
                                                            @if ($prnotdoc->bydefault == 1)
                                                                <form action="{{ route('destroysub2') }}" method="Post">
                                                                @else
                                                                    <form action="{{ route('deletefiles_sub2') }}" method="Post">
                                                            @endif
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
                                            </tr>
                                        @endif
                                    @endforeach
                                @endif

                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>

    @section('scripts')
        <script>
            function sortTable() {
                var tbody = document.querySelector('#myTable tbody');
                var rows = Array.from(tbody.querySelectorAll('tr'));

                rows.sort(function(a, b) {
                    var textA = a.cells[0].textContent.toLowerCase();
                    var textB = b.cells[0].textContent.toLowerCase();
                    if (textA < textB) return -1;
                    if (textA > textB) return 1;
                    return 0;
                });

                rows.forEach(function(row) {
                    tbody.appendChild(row);
                });
            }

            // Call the sort function when the page loads
            window.addEventListener('load', sortTable);
        </script>
    @endsection
</x-app-layout>
