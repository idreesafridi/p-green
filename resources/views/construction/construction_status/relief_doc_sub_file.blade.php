<x-app-layout :pageTitle="$relief_doc_file->folder_name">
    @section('styles')
    @endsection

    @php
    $reldocid = Session::get('reldocid');

    @endphp

    <x-construction-detail-head :consId="$construct_id" />
    <x-construction-detail-nav :constructionid="$construct_id"  />

    <div class="tab-content">
        <div class="card p-4 border-0 site-detail-card">
            <div class="card-head document-page-header py-4">
                <div class="d-flex align-items-center">
                    @if($reldocid !== null)
                   
                    <a href="{{ Route('show_relief_doc_file', $reldocid)}}">

                      @else
                      <a href="{{ URL::previous() }}">
                      @endif

                        <i class="fa fa-arrow-left me-3 back"></i></a>
                    <h6 class="heading fw-bold mb-0">{{$sub_chiled_f_name}}</h6>

                </div>

                <div class="row">
                    <div class="col-12 mt-4 col-lg-4 d-flex align-items-center">
                        <input type="text" class="form-control head-input" placeholder="Cerca tra i documenti">
                    </div>
                    <div class="col-12 col-lg-8 mt-4 d-md-flex justify-content-between align-items-center">
                        <div>
                            <div style="float: right;">

                                <form action="{{ route('upload_sub1_files') }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <input type="text" name="rel_doc_file_id" value="{{ $relief_doc_file->id }}"
                                        hidden>

                                    <input type="text" name="rel_doc_file_folder_name"
                                        value="{{ $relief_doc_file->folder_name }}" hidden>

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
                            <nav class="d-inline-block filterList">
                                <div class="nav nav-tabs border-bottom-0" role="tablist">
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

                                @if (count($relief_doc_file->RelifDocFileSub1) != 0)


                                    @foreach ($relief_doc_file->RelifDocFileSub1->unique('file_name') as $prnotdoc)
                                        @php
                                            $allow = $prnotdoc->allow;
                                            $allowedRoles = explode(',', $allow);
                                        @endphp
                                        @hasrole('admin')
                                            @if (in_array('admin', $allowedRoles))
                                                @include('construction.construction_status.reliefdocsubfile')
                                            @endif
                                        @endhasrole
                                        @hasrole('technician')
                                            @if (in_array('technician', $allowedRoles))
                                                @include('construction.construction_status.reliefdocsubfile')
                                            @endif
                                        @endhasrole
                                        @hasrole('businessconsultant')
                                            @if (in_array('businessconsultant', $allowedRoles))
                                                @include('construction.construction_status.reliefdocsubfile')
                                            @endif
                                        @endhasrole
                                        @hasrole('photovoltaic')
                                            @if (in_array('photovoltaic', $allowedRoles))
                                                @include('construction.construction_status.reliefdocsubfile')
                                            @endif
                                        @endhasrole
                                        @hasrole('user')
                                            @if (in_array('user', $allowedRoles))
                                                @include('construction.construction_status.reliefdocsubfile')
                                            @endif
                                        @endhasrole
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
