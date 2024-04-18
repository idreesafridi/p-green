<x-app-layout :pageTitle="request()->route()->pagename">
    @section('styles')
    @endsection
    @php

        $backId = Session::get('backid');

    @endphp
    <x-construction-detail-head :consId="$prenoti_doc_file->construction_site_id" />

    <x-construction-detail-nav :constructionid="$prenoti_doc_file->construction_site_id" />

    <div class="tab-content">
        <div class="card p-4 border-0 site-detail-card">
            <div class="card-head document-page-header py-4">
                <div class="d-flex align-items-center">
                    <a href="{{ Route('show_preNoti_doc', $backId) }}"><i class="fa fa-arrow-left me-3 back"></i></a>
                    <h6 class="heading fw-bold mb-0">{{ $folder_name }}</h6>
                </div>
                <div class="row">
                    <div class="col-12 mt-4 col-lg-4 d-flex align-items-center">
                        <input type="text" class="form-control head-input" placeholder="Cerca tra i documenti"
                            id="searchfield">
                    </div>
                    <div class="col-12 col-lg-8 mt-4 d-md-flex justify-content-between align-items-center">
                        <div>
                            <div style="float: right;">
                                <form action="{{ route('upload_sub1_main_file') }}" method="POST"
                                    enctype="multipart/form-data" id="pri_upload_files">
                                    @csrf
                                    <input type="text" name="pr_not_doc_id" value="{{ $prenoti_doc_file->id }}"
                                        hidden>

                                    <input type="text" name="parent1_folder_name" value="{{ $folder_name }}" hidden>

                                    <input type="text" name="orignal_name" value="" hidden>

                                    <input type="text" name="type_of_dedection_sub1_id" value="" hidden>

                                    <input type="hidden" name = "construction_id"
                                        value =  "{{ $prenoti_doc_file->construction_site_id }}">

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
                            <div class="nav nav-tabs border-bottom-0 d-none" role="tablist"
                                id="pri_upload_files_spinner">
                                <div class="spinner-border" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                        <div class="text-end">
                            @php
                                $fol2 = 'all';
                            @endphp
                            <a class="btn btn-green" href="{{ route('download_sub1_folder', [$folder_name, $fol2]) }}">
                                <i class="fa fa-download me-2"> Scarica tutto</i>
                            </a>
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
                                
                                {{-- @dd($prenoti_doc_file->TypeOfDedectionSub1); --}}
                                @if (count($prenoti_doc_file->TypeOfDedectionSub1) != 0)
                                    @foreach ($prenoti_doc_file->TypeOfDedectionSub1->where('state', 1) as $prnotdoc)
                                        @if ($prnotdoc->folder_name != null || $prnotdoc->file_name)
                                            @php
                                                $allow = $prnotdoc->allow;
                                                $allowedRoles = explode(',', $allow);
                                            @endphp
                                            @hasrole('admin')
                                           
                                                @if (in_array('admin', $allowedRoles))
                                                    @include('typeofdeduc1')
                                                @endif
                                            @endhasrole
                                            @hasrole('technician')
                                                @if (in_array('technician', $allowedRoles))
                                                    @include('typeofdeduc1')
                                                @endif
                                            @endhasrole
                                            @hasrole('businessconsultant')
                                                @if (in_array('businessconsultant', $allowedRoles))
                                                    @include('typeofdeduc1')
                                                @endif
                                            @endhasrole
                                            @hasrole('photovoltaic')
                                                @if (in_array('photovoltaic', $allowedRoles))
                                                    @include('typeofdeduc1')
                                                @endif
                                            @endhasrole
                                            @hasrole('user')
                                                @if (in_array('user', $allowedRoles))
                                                    @include('typeofdeduc1')
                                                @endif
                                            @endhasrole
                                        @endif
                                    @endforeach
                                @endif
                                @if (isset($relief_doc_file))
                                    @if ($prenoti_doc_file->folder_name == 'Documenti Sicurezza')
                                        @php
                                            $allow = $relief_doc_file->allow;
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
                                @endif
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

            <!-- File Preview Modal -->
            <div class="modal fade" id="viewFileModal" aria-labelledby="exampleModalLabel" aria-modal="true"
                role="dialog">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Anteprima Documento</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <iframe id="view-file-frame" src="{{ asset('assets/sample.pdf') }}" width="100%"
                                height="600px"></iframe>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End File Preview Modal -->

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
