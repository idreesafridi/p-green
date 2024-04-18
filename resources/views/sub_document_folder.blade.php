<x-app-layout :pageTitle="request()->route()->pagename">
    @section('styles')
    @endsection

    <x-construction-detail-head :consId="$construct_id"  />
    <x-construction-detail-nav :constructionid="$construct_id" />

    <div class="tab-content">
        <div class="card p-4 border-0 site-detail-card">
            <div class="card-head document-page-header py-4">
                <div class="d-flex align-items-center">
                    
                    @if(request()->headers->get('referer') && str_contains(request()->headers->get('referer'), 'construction-site/page'))
                    <a href="{{ request()->headers->get('referer') }}">
                        <i class="fa fa-arrow-left me-3 back"></i>
                    </a>
                @else
                    <a href="{{ route('show_prenoti_doc_file', $pr_not_doc_id) }}">
                        <i class="fa fa-arrow-left me-3 back"></i>
                    </a>
                @endif
                    {{-- <a href="{{Route('show_prenoti_doc_file', $pr_not_doc_id)}}"><i class="fa fa-arrow-left me-3 back"></i></a> --}}
                    <h6 class="heading fw-bold mb-0">{{ $parent_folder_name }}</h6>
                </div>
                <div class="row">
                    <div class="col-12 mt-4 col-lg-4 d-flex align-items-center">
                        <input type="text" id="searchfield" class="form-control head-input" placeholder="Cerca tra i documenti">
                    </div>
                    <div class="col-12 col-lg-8 mt-4 d-md-flex justify-content-between align-items-center">
                        <div>
                            <div style="float: right;">
                           
                                <form action="{{ route('upload_sub2_main_file') }}" method="POST"
                                    enctype="multipart/form-data" id="pri_upload_files">
                                    @csrf
                                    <input type="text" name="pr_not_doc_id" value="{{ $pr_not_doc_id }}" hidden>
                                    <input type="text" name="type_of_dedection_sub1_id"
                                        value="{{ $prenoti_doc_sub1->id }}" hidden>

                                    <input type="text" name="parent1_folder_name" value="{{ $docname }}" hidden>

                                    <input type="text" name="parent2_folder_name" value="{{ $parent_folder_name }}"
                                        hidden>

                                    <input type="file" autocomplete="off" name="files[]" accept=".pdf"
                                        id="input" class="form-control d-inline"
                                        style="color:grey !important; width:70%" multiple
                                        onchange="submit_form()">
                                    <span class='d-inline'>
                                        {{-- <button class="btn btn-outline-secondary" type="submit">Submit
                                            </button> --}}
                                    </span>
                                </form>
                                <div class="nav nav-tabs border-bottom-0 d-none" role="tablist" id="pri_upload_files_spinner">
                                    <div class="spinner-border" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-end">
                            @php
                                $folder_name = 'all';
                            @endphp
                            <a class="btn btn-green"
                                href="{{ route('download_sub2_folder', [$docname, $parent_folder_name, $folder_name]) }}">
                                <i class="fa fa-download me-2"> Scarica tutto</i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="tab-content">
                    <div class="tab-pane fade active show table-responsive" id="filterTab1">
                        <table class="table document-table table-hover" >
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
                                
                                @foreach ($prenoti_doc_sub1->TypeOfDedectionSub2 as $prnotdoc)
                                    @php
                                        $allow = $prnotdoc->allow;
                                        $allowedRoles = explode(',', $allow);
                                    @endphp
                                    @hasrole('admin')
                                        @if (in_array('admin', $allowedRoles))
                                            @include('typeofdeduc2')
                                        @endif
                                    @endhasrole
                                    @hasrole('technician')
                                        @if (in_array('technician', $allowedRoles))
                                            @include('typeofdeduc2')
                                        @endif
                                    @endhasrole
                                    @hasrole('businessconsultant')
                                        @if (in_array('businessconsultant', $allowedRoles))
                                            @include('typeofdeduc2')
                                        @endif
                                    @endhasrole
                                    @hasrole('photovoltaic')
                                        @if (in_array('photovoltaic', $allowedRoles))
                                            @include('typeofdeduc2')
                                        @endif
                                    @endhasrole
                                    @hasrole('user')
                                        @if (in_array('user', $allowedRoles))
                                            @include('typeofdeduc2')
                                        @endif
                                    @endhasrole
                                @endforeach
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
        </script>
    @endsection
</x-app-layout>
