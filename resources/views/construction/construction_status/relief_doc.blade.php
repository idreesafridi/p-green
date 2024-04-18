<x-app-layout :pageTitle="request()->route()->pagename">
    @section('styles')
    @endsection

    <x-construction-detail-head :consId="$construct_id" />
    <x-construction-detail-nav :constructionid="$construct_id"  />

    <div class="tab-content">
        <div class="card p-4 border-0 site-detail-card">
            <div class="card-head document-page-header py-4">
                <div class="d-flex align-items-center">
                    <h6 class="heading fw-bold mb-0">Rilievo</h6>
                </div>

                <form>
                    <div class="row">
                        <div class="col-12 mt-4 col-lg-4 d-flex align-items-center">
                            <input type="text" class="form-control head-input" placeholder="Cerca tra i documenti">
                        </div>
                        <div class="col-12 col-lg-8 mt-4 d-md-flex justify-content-between align-items-center">
                            <div>
                            </div>
                            <div class="text-end">
                                {{-- <button type="" class="btn btn-green">
                                    <i class="fa fa-download me-2"></i>
                                    Scarica tutto
                                </button> --}}
                                @php
                                    $folder_name = 'all';
                                @endphp
                                <a class="btn btn-green" href="{{ route('download_reliefdoc_folder', $folder_name) }}">
                                    <i class="fa fa-download me-2"> Scarica tutto</i>
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
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
                                @foreach ($relief_doc->ReliefDocument as $reliefdocument)
                                    @php
                                        $allow = $reliefdocument->allow;
                                        $allowedRoles = explode(',', $allow);
                                    @endphp
                                    @hasrole('admin')
                                        @if (in_array('admin', $allowedRoles))
                                            @include('construction.construction_status.reliefdoc')
                                        @endif
                                    @endhasrole
                                    @hasrole('technician')
                                        @if (in_array('technician', $allowedRoles))
                                            @include('construction.construction_status.reliefdoc')
                                        @endif
                                    @endhasrole
                                    @hasrole('businessconsultant')
                                        @if (in_array('businessconsultant', $allowedRoles))
                                            @include('construction.construction_status.reliefdoc')
                                        @endif
                                    @endhasrole
                                    @hasrole('photovoltaic')
                                        @if (in_array('photovoltaic', $allowedRoles))
                                            @include('construction.construction_status.reliefdoc')
                                        @endif
                                    @endhasrole
                                    @hasrole('user')
                                        @if (in_array('user', $allowedRoles))
                                            @include('construction.construction_status.reliefdoc')
                                        @endif
                                    @endhasrole
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
