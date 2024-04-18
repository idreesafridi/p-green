<x-app-layout :pageTitle="$legge10_folder_name">
    @section('styles')
    @endsection

    <x-construction-detail-head :consId="$construct_id" />
    <x-construction-detail-nav :constructionid="$construct_id"  />

    <div class="tab-content">
        <div class="card p-4 border-0 site-detail-card">
            <div class="card-head document-page-header py-4">
                <div class="d-flex align-items-center">
                    {{-- <i class="fa fa-arrow-left me-3 back"></i> --}}
                    <h6 class="heading fw-bold mb-0">Legge 10</h6>
                </div>

                <div class="row">
                    <div class="col-12 mt-4 col-lg-4 d-flex align-items-center">
                        <input type="text" class="form-control head-input" placeholder="Cerca tra i documenti">
                    </div>
                    <div class="col-12 col-lg-8 mt-4 d-md-flex justify-content-between align-items-center">
                        <div>
                            <div style="float: right;">

                                <form action="{{ route('legge10_multifile_upload') }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <input type="text" name="status_leg10_id" value="{{ $status_leg10_id }}" hidden>
                                    <input type="file" autocomplete="off" name="files[]" accept=".pdf"
                                        id="input" class="form-control d-inline"
                                        style="color:grey !important; width:70%" multiple
                                        onchange="this.form.submit();">
                                    <span class='d-inline'>
                                        {{-- <button class="btn btn-outline-secondary" type="submit">Submit
                                        </button> --}}
                                    </span>
                                </form>
                                <!-- btn text : border disabled -->

                            </div>
                            <nav class="d-inline-block
                                        filterList">
                                <div class="nav nav-tabs border-bottom-0" role="tablist">
                                    {{-- <a id="filter1" class="active btn btn-light text-black btn-sm me-2 mb-2"
                                            type="button" role="tab" data-bs-toggle="tab"
                                            href="#filterTab1">Tutti</a> --}}

                                    {{-- <a id="filter2" class="btn btn-light text-black btn-sm 50-filter me-2 mb-2"
                                            type="button" role="tab" data-bs-toggle="tab" href="#filterTab2">50</a>

                                        <a id="filter3" type="button" class="btn btn-danger me-2 mb-2" role="tab"
                                            data-bs-toggle="tab" href="#filterTab3">Essenziali</a>

                                        <a id="filter4" type="button" class="btn btn-info me-2 mb-2" role="tab"
                                            data-bs-toggle="tab" href="#filterTab4">Chiavetta</a> --}}
                                </div>
                            </nav>

                        </div>


                        <a class="btn btn-green" href="{{ route('download_legg10_all_file') }}">
                            <i class="fa fa-download me-2"> Scarica tutto</i>
                        </a>
                    </div>
                </div>

            </div>
            <div class="card-body p-0">
                <div class="tab-content">
                    <div class="tab-pane fade active show" id="filterTab1">
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
                                @if ($legge10file != null)
                                    @foreach ($legge10file as $legge10file_data)
                                        @php
                                            $allow = $legge10file_data->allow;
                                            $allowedRoles = explode(',', $allow);
                                        @endphp
                                        @hasrole('admin')
                                            @if (in_array('admin', $allowedRoles))
                                                @include('construction.construction_status.legg10files')
                                            @endif
                                        @endhasrole
                                        @hasrole('technician')
                                            @if (in_array('technician', $allowedRoles))
                                                @include('construction.construction_status.legg10files')
                                            @endif
                                        @endhasrole
                                        @hasrole('businessconsultant')
                                            @if (in_array('businessconsultant', $allowedRoles))
                                                @include('construction.construction_status.legg10files')
                                            @endif
                                        @endhasrole
                                        @hasrole('photovoltaic')
                                            @if (in_array('photovoltaic', $allowedRoles))
                                                @include('construction.construction_status.legg10files')
                                            @endif
                                        @endhasrole
                                        @hasrole('user')
                                            @if (in_array('user', $allowedRoles))
                                                @include('construction.construction_status.legg10files')
                                            @endif
                                        @endhasrole
                                    @endforeach
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
        </script>
    @endsection
</x-app-layout>
