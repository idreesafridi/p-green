<x-app-layout :pageTitle="$relief_doc_file->folder_name">
    @section('styles')
    @endsection
    @php

        $backId = Session::get('backid');

        $url = url()->previous();
        $chiveta = Str::contains($url, 'page/chiavetta');
        if (!$chiveta) {
            $backRoute = Route('show_preNoti_doc', $backId);
        } else {
            $backRoute = Route('chiavetta', $construct_id);
        }
    @endphp

    <x-construction-detail-head :consId="$construct_id" />
    <x-construction-detail-nav :constructionid="$construct_id" />

    <div class="tab-content">
        <div class="card p-4 border-0 site-detail-card">
            <div class="card-head document-page-header py-4">
                <div class="d-flex align-items-center">

                    @if ($backId !== null)
                        <a href="{{ $backRoute }}">
                    @endif
                    <i class="fa fa-arrow-left me-3 back"></i></a>

                    <h6 class="heading fw-bold mb-0">{{ $relief_doc_file->folder_name }}</h6>
                </div>
                @if ($relief_doc_file->folder_name == 'Documenti Rilievo')
                    <div class="text-end">
                        <a href="{{ asset('assets/TabellaLavorazioniPdf/TABELLA_LAVORAZIONI.pdf') }}"
                            download="TABELLA_LAVORAZIONI">Scarica il modello Tabella Lavorazioni <i
                                class="fa fa-download"></i></a><br>
                        <a href="{{ asset('assets/TabellaLavorazioniPdf/RACCOLTA_DATI_ANTE_RILIEVO.pdf') }}"
                            download="RACCOLTA_DATI_ANTE_RILIEVO">Scarica il modello Raccolta Dati Ante Rilievo <i
                                class="fa fa-download"></i></a>
                    </div>
                @endif
                <div class="row">
                    <div class="col-12 mt-4 col-lg-4 d-flex align-items-center">
                        <input type="text" class="form-control head-input" placeholder="Cerca tra i documenti">
                    </div>
                    <div class="col-12 col-lg-8 mt-4 d-md-flex justify-content-between align-items-center">
                        <div>
                            <div style="float: right;">

                                <form action="{{ route('upload_files') }}" method="POST" enctype="multipart/form-data"
                                    id="upload_files_form">
                                    @csrf
                                    <input type="text" name="relief_doc_id" value="{{ $relief_doc_file->id }}"
                                        hidden>
                                    <input type="text" name="relief_doc_f_name"
                                        value="{{ $relief_doc_file->folder_name }}" hidden>
                                    <input type="file" autocomplete="off" name="files[]" accept=".pdf"
                                        id="input" class="form-control d-inline"
                                        style="color:grey !important; width:70%" multiple onchange="submit_form()">
                                    <span class='d-inline'>
                                        {{-- <button class="btn btn-outline-secondary" type="submit">Submit
                                        </button> --}}
                                    </span>
                                </form>
                                <!-- btn text : border disabled -->

                            </div>

                            <nav class="d-inline-block filterList">
                                <div class="nav nav-tabs border-bottom-0 d-none" role="tablist"
                                    id="upload_files_form_spinner">
                                    <div class="spinner-border" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            </nav>

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
                                {{-- @dd($relief_doc_file->ReliefDocumentFile); --}}
                                @if (count($relief_doc_file->ReliefDocumentFile) != 0)
                                    @php

                                        $relief_doc_files = $relief_doc_file->ReliefDocumentFile;

                                        $updated_files = $relief_doc_files->filter(function ($relief_doc_file) {
                                            return $relief_doc_file->updated_on !== null;
                                        });

                                        $not_updated_files = $relief_doc_files->filter(function ($relief_doc_file) {
                                            return $relief_doc_file->updated_on === null;
                                        });

                                        $filtered_files = $updated_files->concat($not_updated_files);
                                        // dd($filtered_files);
                                        $unique_files = collect([]);
                                        $grouped_files = $filtered_files->groupBy(function ($item) {
                                            return strtolower($item->file_name);
                                        });

                                        if ($relief_doc_file->folder_name == 'Documenti Rilievo') {
                                            foreach ($grouped_files as $group) {
                                                foreach ($group as $item) {
                                                    $unique_files->push($item);
                                                }
                                            }
                                        } else {
                                            foreach ($grouped_files as $group) {
                                                $unique_files->push($group->first());
                                            }
                                        }

                                    @endphp


                                    @foreach ($unique_files->where('state', 1) as $prnotdoc)
                                        {{-- @dd($prnotdoc->ConstructionSite->ConstructionSiteSetting->type_of_deduction == '110') --}}
                                        @if ($prnotdoc->ConstructionSite->ConstructionSiteSetting != null)
                                            @php
                                                $type_of_deduction = $prnotdoc->ConstructionSite->ConstructionSiteSetting->type_of_deduction;
                                            @endphp
                                            @if (strpos($type_of_deduction, '65') !== false ||
                                                    strpos($type_of_deduction, '50') !== false ||
                                                    strpos($type_of_deduction, '90') !== false)
                                            @else
                                                @php
                                                    $prnotdoc['state'] = str_replace(['Cila protocollata 50-65-90', 'Protocollo Cila 50-65-90'], 0, $prnotdoc['file_name']);
                                                @endphp
                                            @endif
                                        @endif



                                        @php
                                            $allow = $prnotdoc->allow;
                                            $allowedRoles = explode(',', $allow);

                                        @endphp
                                        @hasrole('admin')
                                            @if (in_array('admin', $allowedRoles))
                                                @include('construction.construction_status.reliefdocfile')
                                            @endif
                                        @endhasrole
                                        @hasrole('technician')
                                            @if (in_array('technician', $allowedRoles))
                                                @include('construction.construction_status.reliefdocfile')
                                            @endif
                                        @endhasrole
                                        @hasrole('businessconsultant')
                                            @if (in_array('businessconsultant', $allowedRoles))
                                                @include('construction.construction_status.reliefdocfile')
                                            @endif
                                        @endhasrole
                                        @hasrole('business')
                                            @include('construction.construction_status.reliefdocfile')
                                        @endhasrole
                                        @hasrole('photovoltaic')
                                            @if (in_array('photovoltaic', $allowedRoles))
                                                @include('construction.construction_status.reliefdocfile')
                                            @endif
                                        @endhasrole
                                        @hasrole('user')
                                            @if (in_array('user', $allowedRoles))
                                                @include('construction.construction_status.reliefdocfile')
                                            @endif
                                        @endhasrole
                                        @hasrole('worker')
                                            @if (in_array('user', $allowedRoles))
                                                @include('construction.construction_status.reliefdocfile')
                                            @endif
                                        @endhasrole
                                    @endforeach


                                    {{-- @if (isset($data['Notifica']))
                                     @if ($data['Notifica']->state == 'saldo')
                                        @php
                                            $allow = $data['Notifica']->allow;
                                            $allowedRoles = explode(',', $allow);
                                        @endphp
                                        @hasrole('admin')
                                            @if (in_array('admin', $allowedRoles))
                                                @include('essentialnotification')
                                            @endif
                                        @endhasrole
                                        @hasrole('technician')
                                            @if (in_array('technician', $allowedRoles))
                                                @include('essentialnotification')
                                            @endif
                                        @endhasrole
                                        @hasrole('businessconsultant')
                                            @if (in_array('businessconsultant', $allowedRoles))
                                                @include('essentialnotification')
                                            @endif
                                        @endhasrole
                                        @hasrole('photovoltaic')
                                            @if (in_array('photovoltaic', $allowedRoles))
                                                @include('essentialnotification')
                                            @endif
                                        @endhasrole
                                        @hasrole('user')
                                            @if (in_array('user', $allowedRoles))
                                                @include('essentialnotification')
                                            @endif
                                        @endhasrole
                                        @endif
                                    @endif --}}
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
                $('#upload_files_form_spinner').removeClass('d-none')
                $('#upload_files_form').addClass('d-none')
                document.getElementById("upload_files_form").submit();
            }

            function replace_rel_doc_files(id) {
                var spinner = $('#replace_rel_doc_files_spinner_' + id)
                var form = $('#replace_rel_doc_files_form_' + id)

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
        </script>
    @endsection
</x-app-layout>
