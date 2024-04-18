
        @foreach ($prenoti_doc['prenoti_doc']->PrNotDoc as $prnotdoc)
            @if ($prnotdoc->state == 1)
            
                @if ($prdoc=='all')
                    @include('construction.construction_status.prenotidoc')
                @else
                    @if ($prnotdoc->folder_name == $prdoc)
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
                                    
                                    if ($prnotdoc->folder_name == $prdoc) {
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
                                    <span class="badge bg-success">{{ $count }}</span>
                                @else
                                    <span class="badge bg-danger">{{ $count }}</span>
                                @endif
                            </td>
                            @if ($prnotdoc->file_name != null)
                            <td class="hideInMobile">{{ $prnotdoc->updated_on }}</td>
                            <td class="hideInMobile">{{ $prnotdoc->updated_by }}</td>
                            @elseif($prnotdoc->folder_name != null)
                            <td class="hideInMobile">{{ $prnotdoc->getLatestUpdate() ?  $prnotdoc->getLatestUpdate()->updated_on : '' }}</td>
                            <td class="hideInMobile">{{ $prnotdoc->getLatestUpdate()?  $prnotdoc->getLatestUpdate()->updated_by : '' }}</td>
                            @endif
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
            @endif

            <!--  Modal -->
            <x-reminder-email-model modelId="bell{{ $prnotdoc->id }}Modal"
                folderName="{{ $prnotdoc->folder_name }}" conId="{{ $var->id }}">
            </x-reminder-email-model>
            <!-- End Modal -->
        @endforeach

        @foreach ($prenoti_doc['prenoti_and_relif']->unique('folder_name') as $reliefdocument)
     
            @if ($reliefdocument->state == 1) 
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
                            <span class="badge bg-success">{{ $count }}</span>
                        @else
                            <span class="badge bg-danger">{{ $count }}</span>
                        @endif
            
                    </td>
                    
                    @if($reliefdocument->file_name != null)
                    <td class="hideInMobile">{{ $reliefdocument->updated_on }}</td>
                    <td class="hideInMobile">{{ $reliefdocument->updated_by }}</td>
                    @elseif($reliefdocument->folder_name != null)
                    <td class="hideInMobile">{{$reliefdocument->ReliefLatestUpdated() ? $reliefdocument->ReliefLatestUpdated()->updated_on : '' }}</td>
                    <td class="hideInMobile">{{$reliefdocument->ReliefLatestUpdated() ? $reliefdocument->ReliefLatestUpdated()->updated_by : '' }}</td>
                    @endif
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

            <!--  Modal -->
            <x-reminder-email-model modelId="bell{{ $reliefdocument->id }}Modal"
                folderName="{{ $reliefdocument->folder_name }}" conId="{{ $var->id }}">
            </x-reminder-email-model>
            <!-- End Modal -->
        @endforeach