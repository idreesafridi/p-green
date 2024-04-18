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
                    <h6 class="heading fw-bold mb-0">Documenti Commercialista</h6>
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
                                @foreach ($prenoti_doc['prenoti_doc']->PrNotDoc as $prnotdoc)
                                    @if ($prnotdoc->state == 1)
                                        @if ($prnotdoc->folder_name == 'Documenti Rilevanti' || $prnotdoc->folder_name == 'Documenti 110' || $prnotdoc->folder_name == 'Documenti 50' || $prnotdoc->folder_name == 'Documenti 65' || $prnotdoc->folder_name == 'Documenti 90')
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

                                @foreach ($prenoti_doc['prenoti_and_relif']->unique('folder_name') as $reliefdocument)
                                    @if ($reliefdocument->state == 1)
                                        @if ($reliefdocument->folder_name == 'Documenti Clienti' || $reliefdocument->folder_name == 'Documenti Co-intestatari' || $reliefdocument->folder_name == 'Pratiche Comunali')
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
