<x-app-layout :pageTitle="request()->route()->pagename">
    @section('styles')
    @endsection


    <x-construction-detail-head :consId="$construct_id" />
    <x-construction-detail-nav :constructionid="$construct_id" />

    <div class="tab-content">
        <div class="card p-4 border-0 site-detail-card">
            <div class="card-head document-page-header py-4">
                <div class="d-flex align-items-center">
                    <a href="{{ Route('essential', $construct_id) }}">
                        <i class="fa fa-arrow-left me-3 back"></i>
                    </a>
                    <h6 class="heading fw-bold mb-0">Saldo</h6>
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
                            {{-- <a class="btn btn-green" href="{{ route('zip_chiavetta_or_essential', $slug) }}">
                                <i class="fa fa-download me-2"> Scarica tutto</i></a> --}}
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="tab-content">
                    <div class="tab-pane fade active show table-responsive" id="filterTab1">
                        <table class="table document-table table-hover">
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
                                @if ($data110 != null)
                                    @if (!empty($data110['PrNotDoc']))
                                        @forelse ($data110['PrNotDoc'] as $prnotdoc)
                                            <tr>
                                                <td>
                                                    <a class="fa fa-folder"
                                                        href="{{ route('show_prenoti_doc_file', $prnotdoc->id) }}"></a>
                                                    <a href="{{ route('show_prenoti_doc_file', $prnotdoc->id) }}"
                                                        class="me-4 ms-2">
                                                        <strong>{{ $prnotdoc->folder_name }}</strong>
                                                    </a><br>
                                                    <small>{{ $prnotdoc->description }}</small>
                                                </td>
                                                <td>
                                                    @php
                                                        $count = 0;

                                                        if ($prnotdoc->folder_name == 'Documenti 110' || $prnotdoc->folder_name == 'Documenti 90' || $prnotdoc->folder_name == 'Documenti 65' || $prnotdoc->folder_name == 'Documenti 50' || $prnotdoc->folder_name == 'Documenti Sicurezza') {
                                                            foreach ($prnotdoc->PrNotDocFile as $PrNotDocFile) {
                                                                if ($PrNotDocFile['updated_on'] != null && $PrNotDocFile['file_name'] && $PrNotDocFile['state'] == 1) {
                                                                    $count++;
                                                                }
                                                            }

                                                            foreach ($prnotdoc->TypeOfDedectionSub1 as $file) {
                                                                if ($file['updated_on'] != null && $file['updated_by'] != null && $file['state'] == 1) {
                                                                    $count++;
                                                                }

                                                                foreach ($file->TypeOfDedectionSub2 as $TypeOfDedectionSub2) {
                                                                    if ($TypeOfDedectionSub2['updated_on'] !== null && $TypeOfDedectionSub2['updated_by'] !== null && $TypeOfDedectionSub2['state'] == 1) {
                                                                        $count++;
                                                                    }
                                                                    foreach ($TypeOfDedectionSub2->TypeOfDedectionFiles as $TypeOfDedectionFiles) {
                                                                        if ($TypeOfDedectionFiles['updated_on'] !== null && $TypeOfDedectionFiles['updated_by'] !== null && $TypeOfDedectionFiles['state'] == 1) {
                                                                            $count++;
                                                                        }
                                                                        foreach ($TypeOfDedectionFiles->TypeOfDedectionFiles2 as $TypeOfDedectionFiles2) {
                                                                            if ($TypeOfDedectionFiles2['updated_on'] !== null && $TypeOfDedectionFiles2['updated_by'] !== null && $TypeOfDedectionFiles2['state'] == 1) {
                                                                                $count++;
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        } else {
                                                            foreach ($prnotdoc->PrNotDocFile as $file) {
                                                                if ($file['updated_on'] != null && $file['updated_by'] != null && $file['state'] == 1) {
                                                                    $count++;
                                                                }
                                                            }
                                                        }

                                                    @endphp

                                                    <span
                                                        class="badge {{ $count > 0 ? 'bg-success' : 'bg-danger' }} ">{{ $count }}</span>

                                                </td>
                                                <td class="hideInMobile">
                                                    {{ (isset($count) && $count > 0)  ? $prnotdoc->getLatestUpdate()->updated_on : '' }}
                                                </td>
                                                <td class="hideInMobile">
                                                    {{ (isset($count) && $count > 0)  ? $prnotdoc->getLatestUpdate()->updated_by : '' }}
                                                </td>
                                                <td class="space"></td>
                                                <td>
                                                    <div style="display: inline-flex; width: 100%;">
                                                        <button type="button"
                                                            class="btn btn-link btn-sm text-warning d-inline"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#bell{{ $prnotdoc->id }}Modal">
                                                            <i class="fa fa-bell"></i>
                                                        </button>

                                                        @if ((isset($count) && $count > 0) || $prnotdoc->file_name != null )
                                                            @if ($prnotdoc->file_name != null)
                                                                <button type="button"
                                                                    class="btn btn-link btn-sm text-dark d-inline"
                                                                    onclick="location.href='{{ route('download_prenotidoc', [$prnotdoc->id, $construct_id]) }}'">
                                                                    <i class="fa fa-download"></i>
                                                                </button>
                                                                <button type="button"
                                                                    class="btn btn-link btn-sm text-danger d-inline"
                                                                    onclick="location.href='{{ route('delete_prinotdoc', [$prnotdoc->id]) }}'">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            @else
                                                                <button type="button"
                                                                    class="btn btn-link btn-sm text-dark d-inline"
                                                                    onclick="location.href='{{ route('download_prenotidoc_folder', [$prnotdoc->folder_name, $construct_id]) }}'">
                                                                    <i class="fa fa-download"></i>
                                                                </button>
                                                                <button type="button"
                                                                    class="btn btn-link btn-sm text-danger d-inline"
                                                                    onclick="location.href='{{ route('DeleteAllPrinotdoc', [$prnotdoc->id]) }}'">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            @endif
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>


                                            <!--  Modal -->

                                            <x-reminder-email-model modelId="bell{{ $prnotdoc->id }}Modal"
                                                folderName="{{ 'Saldo' }}"
                                                conId="{{ $prnotdoc->construction_site_id }}" />
                                            <!-- End Modal -->

                                            <!-- File Preview Modal -->
                                            <x-file-preview-modal modelId="viewFileModal{{ $prnotdoc->id }}"
                                                filepath="{{ $prnotdoc->file_path }}" />
                                            <!-- End File Preview Modal -->


                                        @empty
                                        @endforelse

                                    @endif
                                    @if (isset($data110['TypeOfDedectionSub2']) && !empty($data110['TypeOfDedectionSub2']))
                                        @forelse ($data110['TypeOfDedectionSub2'] as $prnotdoc)
                                            @php
                                                $TypeOfDedectionSub1 = App\Models\TypeOfDedectionSub1::find($prnotdoc->type_of_dedection_sub1_id);
                                                $pr_not_doc_id = $TypeOfDedectionSub1->pr_not_doc_id;
                                                $parent_folder_name = $TypeOfDedectionSub1->folder_name;
                                                $docname = 'Documenti 110';
                                            @endphp

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
                                                                    if ($typeOfDedectionFiles['updated_on'] !== null && $typeOfDedectionFiles['updated_by'] !== null && $typeOfDedectionFiles['state'] == 1) {
                                                                        $count++;
                                                                    }
                                                                    foreach ($typeOfDedectionFiles->TypeOfDedectionFiles2 as $typeOfDedectionFiles2) {
                                                                        if ($typeOfDedectionFiles2['updated_on'] !== null && $typeOfDedectionFiles2['updated_by'] !== null && $typeOfDedectionFiles2['state'] == 1) {
                                                                            $count++;
                                                                        }
                                                                    }
                                                                }

                                                            @endphp
                                                            @if ($count > 0)
                                                                <span
                                                                    class="badge bg-success">{{ $count }}</span>
                                                            @else
                                                                <span
                                                                    class="badge bg-danger">{{ $count }}</span>
                                                            @endif
                                                        </td>
                                                    @else
                                                        <td>
                                                            <button type="button" class="viewFileBtn"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#viewFileModal{{ $prnotdoc->id }}"
                                                                style="outline: none; border: none; background-color: transparent;"
                                                                data-filepath="error404greengen.png"> <i
                                                                    class="fa fa-file-o"></i>
                                                                <strong
                                                                    class="me-4 ms-2">{{ $prnotdoc->file_name }}</strong>
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

                                                    @if ($prnotdoc->file_name != null && $prnotdoc->updated_by != null)
                                                        <td class="hideInMobile">{{ $prnotdoc->updated_on }}</td>
                                                        <td class="hideInMobile">{{ $prnotdoc->updated_by }}</td>
                                                    @elseif($prnotdoc->folder_name != null && isset($count) && $count > 0)
                                                        <td class="hideInMobile">
                                                            {{ $prnotdoc->DedectionSub2LatestUpdate()->updated_on }}</td>
                                                        <td class="hideInMobile">
                                                            {{ $prnotdoc->DedectionSub2LatestUpdate()->updated_by }}</td>
                                                    @endif


                                                    <td class="space">
                                                        <form action="{{ route('replace_sub2_file') }}" method="POST"
                                                            enctype="multipart/form-data">
                                                            @csrf

                                                            <input type="text" name="bydefault"
                                                                value="{{ $prnotdoc->bydefault }}" hidden>

                                                            <input type="text" name="pr_not_doc_id"
                                                                value="{{ $pr_not_doc_id }}" hidden>
                                                            <input type="text" name="file_id"
                                                                value="{{ $prnotdoc->id }}" hidden>
                                                            <input type="text" name="type_of_dedection_sub1_id"
                                                                value="{{ $prnotdoc->type_of_dedection_sub1_id }}"
                                                                hidden>

                                                            <input type="text" name="parent1_folder_name"
                                                                value="{{ $docname }}" hidden>

                                                            <input type="text" name="parent2_folder_name"
                                                                value="{{ $parent_folder_name }}" hidden>


                                                            <input type="text" name="orignal_name"
                                                                value="{{ $prnotdoc->file_name }}" hidden>


                                                            @if ($prnotdoc->updated_by == null && $prnotdoc->file_name != null)
                                                                <input type="file" autocomplete="off"
                                                                    class="form-control" id=""
                                                                    name="file" style="color:grey !important;"
                                                                    onchange="this.form.submit();">
                                                            @endif

                                                        </form>
                                                    </td>
                                                    <td>
                                                        <div style="display: inline-flex; width: 100%;">
                                                            <button type="button"
                                                                class="btn btn-link btn-sm text-warning d-inline"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#bell{{ $prnotdoc->id }}Modal">
                                                                <i class="fa fa-bell"></i>
                                                            </button>

                                                            @if ($prnotdoc->file_name != null && $prnotdoc->updated_by != null)
                                                                <button type="button"
                                                                    class="btn btn-link btn-sm text-dark d-inline"
                                                                    onclick="location.href='{{ route('download_sub2', $prnotdoc->id) }}'">
                                                                    <i class="fa fa-download"></i>
                                                                </button>
                                                                <a class="btn btn-link btn-sm text-danger d-inline"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#warningModal{{ $prnotdoc->id }}">
                                                                    <i class="fa fa-trash"></i>
                                                                </a>
                                                            @elseif($prnotdoc->folder_name != null && isset($count) && $count > 0)
                                                                <button type="button"
                                                                    class="btn btn-link btn-sm text-dark d-inline"
                                                                    onclick="location.href='{{ route('download_sub2_folder', [$docname, $parent_folder_name, $prnotdoc->folder_name]) }}'">
                                                                    <i class="fa fa-download"></i>
                                                                </button>
                                                                <button type="button"
                                                                    class="btn btn-link btn-sm text-danger d-inline"
                                                                    onclick="location.href='{{ route('delete_sub2_folder', $prnotdoc->id) }}'">
                                                                    <i class="fa fa-trash"></i>
                                                                </button>
                                                            @endif

                                                            @if ($prnotdoc->file_name != null && $prnotdoc->bydefault != 0)
                                                                <a class="btn btn-link btn-sm text-dark d-inline"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#replaceDocModal{{ $prnotdoc->id }}">
                                                                    <i class="fa fa-exchange"></i>
                                                                </a>
                                                            @endif


                                                            {{-- @if ($prnotdoc->file_name != null)
                                                                    <a class="btn btn-link btn-sm text-danger d-inline"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#warningModal{{ $prnotdoc->id }}">
                                                                        <i class="fa fa-trash"></i>
                                                                    </a>
                                                                @endif --}}
                                       
                    </div>
                    </td>
                    </tr>
                    <!-- File Preview Modal -->
                    <div class="modal fade" id="viewFileModal{{ $prnotdoc->id }}"
                        aria-labelledby="exampleModalLabel" aria-modal="true" role="dialog">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">
                                        Anteprima
                                        Documento</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <iframe id="view-file-frame"
                                        src="{{ asset('construction-assets/' . $prnotdoc->file_path) }}"
                                        width="100%" height="600px"></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End File Preview Modal -->

                    <!--  Modal -->
                    <x-reminder-email-model modelId="bell{{ $prnotdoc->id }}Modal"
                        folderName="{{ $prnotdoc->file_name == null ? $prnotdoc->folder_name : $prnotdoc->file_name }}"
                        conId="{{ $prnotdoc->ConstructionSite->id }}" />
                    <!-- End Modal -->

                    <!-- End Notification Modal -->
                    <!-- Replace Document Modal -->
                    <div class="modal fade" id="replaceDocModal{{ $prnotdoc->id }}"
                        aria-labelledby="exampleModalLabel" aria-modal="true" role="dialog">
                        <div class="modal-dialog modal-dialog">
                            <div class="modal-content send-email">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">
                                        <strong>Sostituisci un documento</strong>
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <form action="{{ route('replace_sub2_file') }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="row mb-3 mt-5">
                                            <img src="{{ asset('assets/images/swap-img.svg') }}"
                                                class="alert-img mx-auto">
                                        </div>
                                        <div class="mb-4">
                                            <h6 class="text-center">Trascina qui sotto il
                                                documento
                                                oppure selezionalo dal tuo PC</h6>
                                        </div>

                                        <input type="text" name="bydefault" value="{{ $prnotdoc->bydefault }}"
                                            hidden>

                                        <input type="text" name="pr_not_doc_id" value="{{ $pr_not_doc_id }}"
                                            hidden>
                                        <input type="text" name="file_id" value="{{ $prnotdoc->id }}" hidden>
                                        <input type="text" name="type_of_dedection_sub1_id"
                                            value="{{ $prnotdoc->type_of_dedection_sub1_id }}" hidden>

                                        <input type="text" name="parent1_folder_name" value="{{ $docname }}"
                                            hidden>

                                        <input type="text" name="parent2_folder_name"
                                            value="{{ $parent_folder_name }}" hidden>


                                        <input type="text" name="orignal_name" value="{{ $prnotdoc->file_name }}"
                                            hidden>

                                        <div class="mb-4">
                                            <input type="file" autocomplete="off"
                                                class="form-control file-uploader" name="file"
                                                style="color:grey !important;" onchange="this.form.submit();">
                                        </div>
                                    </div>
                                    <div class="modal-footer mb-3">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">Indietro</button>
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
                                    <h5 class="modal-title" id="exampleModalLabel">
                                        Attenzione</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                @if ($prnotdoc->bydefault == 1)
                                    <form action="{{ route('destroysub2') }}" method="Post">
                                    @else
                                        <form action="{{ route('deletefiles_sub2') }}" method="Post">
                                @endif
                                @csrf

                                <input type="text" name="id" value="{{ $prnotdoc->id }}" hidden>

                                <div class="modal-body">
                                    <p class="text-center m-0">Sei sicuro di voler
                                        procedere?</p>
                                </div>

                                <div class="modal-footer mb-3">
                                    <button type="button" class="btn btn-secondary"
                                        data-bs-dismiss="modal">Indietro</button>
                                    <button type="submit" name="reset-pass" class="btn btn-danger">Procedi</button>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- End Delete Modal -->
                    @endif
                @empty
                    @endforelse


                    @endif

                    @if (!empty($RelDocFile['RelDocFile']))
                        @foreach ($RelDocFile['RelDocFile'] as $prnotdoc)
                            @php

                                $allow = $prnotdoc->allow;
                                $allowedRoles = explode(',', $allow);
                                $relief_doc_file = App\Models\ReliefDoc::find($prnotdoc->relief_doc_id);
                            @endphp
                            <tr>
                                @if ($prnotdoc->folder_name != null)
                                    <td>
                                        @php
                                            $path = 'Releif document files/' . $prnotdoc->folder_name . '/' . $relief_doc_file->folder_name;
                                            session()->put($prnotdoc->folder_name, $path);
                                        @endphp

                                        <a class="fa fa-folder"
                                            href="{{ route('relif_sub_file', [$prnotdoc->id]) }}"></a>
                                        <a href="{{ route('relif_sub_file', [$prnotdoc->id]) }}" class="me-4 ms-2">
                                            <strong class="text-start">{{ $prnotdoc->folder_name }}</strong>
                                        </a><br>
                                        <small>{{ $prnotdoc->description }}</small>
                                    </td>
                                    <td>
                                        @php
                                            $count = 0;
                                            foreach ($prnotdoc->RelifDocFileSub1->unique('file_name') as $file) {
                                                if ($file['updated_on'] != null && $file['file_name'] && $file['state'] == 1) {
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
                                @else
                                    <td>
                                        <button type="button" class="viewFileBtn"
                                            style="outline: none; border: none; background-color: transparent;"
                                            data-filepath="error404greengen.png" data-bs-toggle="modal"
                                            data-bs-target="#viewFileModal{{ $prnotdoc->id }}"> <i
                                                class="fa fa-file-o"></i>
                                            <strong class="me-4 ms-2 text-start">{{ $prnotdoc->file_name }}</strong>
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
                                <td class="hideInMobile">{{ $prnotdoc->updated_on }}</td>
                                <td class="hideInMobile">{{ $prnotdoc->updated_by }}</td>
                                <td class="space">
                                    @if ($prnotdoc->file_name)
                                        <form action="{{ route('replace_rel_doc_files') }}" method="POST"
                                            enctype="multipart/form-data"
                                            id="replace_rel_doc_files_form_{{ $prnotdoc->id }}">
                                            @csrf

                                            <input type="text" name="relief_doc_id"
                                                value="{{ $relief_doc_file->id }}" hidden>

                                            <input type="text" name="bydefault"
                                                value="{{ $prnotdoc->bydefault }}" hidden>

                                            <input type="text" name="relief_doc_f_name"
                                                value="{{ $relief_doc_file->folder_name }}" hidden>

                                            <input type="text" name="orignal_name"
                                                value="{{ $prnotdoc->file_name }}" hidden>

                                            <input type="text" name="file_id" value="{{ $prnotdoc->id }}" hidden>

                                            <input type="text" name="orignal_name"
                                                value="{{ $prnotdoc->file_name }}" hidden>
                                            @if ($prnotdoc->updated_on == null)
                                                <input type="file" autocomplete="off" class="form-control"
                                                    name="file" style="color:grey !important;"
                                                    onchange="replace_rel_doc_files('{{ $prnotdoc->id }}')">
                                            @endif
                                        </form>
                                        <div class="spinner-border d-none" role="status"
                                            id="replace_rel_doc_files_spinner_{{ $prnotdoc->id }}">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div style="display: inline-flex; width: 100%;">
                                        <button type="button" class="btn btn-link btn-sm text-warning d-inline"
                                            data-bs-toggle="modal" data-bs-target="#bell{{ $prnotdoc->id }}Modal">
                                            <i class="fa fa-bell"></i>
                                        </button>

                                        @if ($prnotdoc->updated_by != null)
                                            @if ($prnotdoc->file_name != null)
                                                <button type="button" class="btn btn-link btn-sm text-dark d-inline"
                                                    onclick="location.href='{{ route('download_relief_file', $prnotdoc->id) }}'">
                                                    <i class="fa fa-download"></i>
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-link btn-sm text-dark d-inline"
                                                    onclick="location.href='{{ route('download_relief_folder', $prnotdoc->folder_name) }}'">
                                                    <i class="fa fa-download"></i>
                                                </button>
                                            @endif

                                            @if ($prnotdoc->file_name != null && $prnotdoc->bydefault != 0)
                                                <a class="btn btn-link btn-sm text-dark d-inline"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#replaceDocModal{{ $prnotdoc->id }}">
                                                    <i class="fa fa-exchange"></i>
                                                </a>
                                            @endif
                                            @if ($prnotdoc->file_name != null)
                                                <a class="btn btn-link btn-sm text-danger d-inline"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#warningModal{{ $prnotdoc->id }}">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>

                            <!-- File Preview Modal -->
                            <x-file-preview-modal modelId="viewFileModal{{ $prnotdoc->id }}"
                                filepath="{{ $prnotdoc->file_path }}" />
                            <!-- End File Preview Modal -->

                            <!--  Modal -->
                            <x-reminder-email-model modelId="bell{{ $prnotdoc->id }}Modal"
                                folderName="{{ $prnotdoc->file_name == null ? $prnotdoc->folder_name : $prnotdoc->file_name }}"
                                conId="{{ $prnotdoc->ConstructionSite->id }}" />
                            <!-- End Modal -->

                            <!-- Replace Document Modal -->
                            <div class="modal fade" id="replaceDocModal{{ $prnotdoc->id }}"
                                aria-labelledby="exampleModalLabel" aria-modal="true" role="dialog">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content send-email">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">
                                                <strong>Sostituisci un documento</strong>
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        @if ($prnotdoc->file_name)
                                            <form action="{{ route('replace_rel_doc_files') }}" method="POST"
                                                enctype="multipart/form-data">
                                                @csrf
                                                <div class="modal-body">
                                                    <div class="row mb-3 mt-5">
                                                        <img src="http://127.0.0.1:8000/assets/images/swap-img.svg"
                                                            class="alert-img mx-auto">
                                                    </div>
                                                    <div class="mb-4">
                                                        <h6 class="text-center">Trascina qui sotto
                                                            il
                                                            documento oppure selezionalo dal tuo PC
                                                        </h6>
                                                    </div>
                                                    <input type="text" name="relief_doc_id"
                                                        value="{{ $relief_doc_file->id }}" hidden>

                                                    <input type="text" name="bydefault"
                                                        value="{{ $prnotdoc->bydefault }}" hidden>

                                                    <input type="text" name="relief_doc_f_name"
                                                        value="{{ $relief_doc_file->folder_name }}" hidden>


                                                    <input type="text" name="orignal_name"
                                                        value="{{ $prnotdoc->file_name }}" hidden>

                                                    <input type="text" name="file_id"
                                                        value="{{ $prnotdoc->id }}" hidden>

                                                    <div class="mb-4">
                                                        <input type="file" autocomplete="off" class="form-control"
                                                            id="" name="file"
                                                            class="form-control file-uploader">
                                                    </div>
                                                </div>
                                                <div class="modal-footer mb-3">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Indietro</button>
                                                    <button type="submit" class="btn btn-green">Rimpiazza</button>
                                                </div>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <!-- End Replace Document Modal -->
                            <!-- Delete Modal -->
                            <div class="modal fade" id="warningModal{{ $prnotdoc->id }}"
                                aria-labelledby="exampleModalLabel" aria-modal="true" role="dialog">
                                <div class="modal-dialog modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">
                                                Attenzione
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        @if ($prnotdoc->bydefault == 1)
                                            <form action="{{ route('rec_file_destroy') }}" method="Post">
                                            @else
                                                <form action="{{ route('rec_file_delete') }}" method="Post">
                                        @endif
                                        @csrf
                                        <input type="hidden" name="id" value="{{ $prnotdoc->id }}">
                                        <div class="modal-body">
                                            <p class="text-center m-0">Sei sicuro di voler
                                                procedere?
                                            </p>
                                        </div>
                                        <div class="modal-footer mb-3">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Indietro</button>
                                            <button type="submit" name="reset-pass"
                                                class="btn btn-danger">Procedi</button>
                                        </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- End Delete Modal -->
                        @endforeach
                    @endif






                    @endif



                    </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
    </div>

    @section('scripts')
        {{-- <script>
            $(document).ready(function() {
                $('.save').attr("disabled", "disabled");
                $('.resume-box .input-group').addClass('d-none');
                $('.edit').click(function() {
                    $('.resume-box input').removeAttr('disabled');
                    $('.resume-box select').removeAttr('disabled');
                    $('.save').removeAttr('disabled');
                    $('.resume-box input').addClass('bg-light');
                    $('.resume-box .input-group').removeClass('d-none');
                    $('.resume-box .badge-div').addClass('d-none');
                    $('.resume-box select').addClass('bg-light');
                });
            });

            function deduction1_for110(id) {
                var spinner = $('#deduction1_for110_spinner_' + id)
                var form = $('#deduction1_for110_form_' + id)

                spinner.removeClass('d-none')
                form.addClass('d-none')
                form.submit();
            }

            function regprac_file_upload(id) {
                var spinner = $('#regprac_file_upload_spinner_' + id)
                var form = $('#regprac_file_upload_form_' + id)

                spinner.removeClass('d-none')
                form.addClass('d-none')
                form.submit();
            }

            $('input[name="fileTest"]').change(function() {
                var form = $(this).closest('form');
                form.submit();
            });

            

            function legge10_file_upload(id) {
                var spinner = $('#legge10_file_upload_spinner_' + id)
                var form = $('#legge10_file_upload_form_' + id)

                spinner.removeClass('d-none')
                form.addClass('d-none')
                form.submit();
            }

            function essentialnotification_file(id) {
                var spinner = $('#essentialnotification_file_spinner_' + id)
                var form = $('#essentialnotification_file_form_' + id)

                spinner.removeClass('d-none')
                form.addClass('d-none')
                form.submit();
            }
        </script> --}}



        <script>
            $(document).ready(function() {
                $('.save').attr("disabled", "disabled");
                $('.resume-box .input-group').addClass('d-none');
                $('.edit').click(function() {
                    $('.resume-box input').removeAttr('disabled');
                    $('.resume-box select').removeAttr('disabled');
                    $('.save').removeAttr('disabled');
                    $('.resume-box input').addClass('bg-light');
                    $('.resume-box .input-group').removeClass('d-none');
                    $('.resume-box .badge-div').addClass('d-none');
                    $('.resume-box select').addClass('bg-light');
                });
            });

            function submit_form() {
                $('#pri_upload_files_spinner').removeClass('d-none')
                $('#pri_upload_files').addClass('d-none')
                document.getElementById("pri_upload_files").submit();
            }

            function replace_rel_doc_files(id) {
                var spinner = $('#replace_rel_doc_files_spinner_' + id)
                var form = $('#replace_rel_doc_files_form_' + id)

                spinner.removeClass('d-none')
                form.addClass('d-none')
                form.submit();
            }
        </script>
    @endsection
</x-app-layout>
