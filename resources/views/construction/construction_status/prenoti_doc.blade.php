<x-app-layout :pageTitle="request()->route()->pagename">
    @section('styles')
    @endsection


    <x-construction-detail-head :consId="$var->id" />
    <x-construction-detail-nav :constructionid="$var->id" />

    <div class="tab-content">
        <div class="card p-4 border-0 site-detail-card">
            <div class="card-head document-page-header py-4">
                <div class="d-flex align-items-center">
                    {{-- <i class="fa fa-arrow-left me-3 back"></i> --}}
                    @if (auth()->user()->hasrole('admin') ||
                            auth()->user()->hasrole('user'))
                        <h6 class="heading fw-bold mb-0">Tutti i documenti</h6>
                    @elseif(auth()->user()->hasrole('technician'))
                        <h6 class="heading fw-bold mb-0">Documenti Tecnico</h6>
                    @endif
                </div>
                <form>
                    <div class="row">
                        <div class="col-12 mt-4 col-lg-4 d-flex align-items-center">
                            <input type="text" class="form-control head-input" id="searchfield1"
                                placeholder="Cerca tra i documenti">
                        </div>
                        <div class="col-12 col-lg-8 mt-4 d-md-flex justify-content-between align-items-center">
                            <div>
                                @if (auth()->user()->hasrole('admin') ||
                                        auth()->user()->hasrole('user'))
                                    <nav class="d-inline-block filterList  ">
                                        <div class="nav nav-tabs border-bottom-0" role="tablist">
                                            <a id="filter1" class="active btn btn-light text-black btn-sm me-2 mb-2"
                                                type="button" role="tab" data-bs-toggle="tab"
                                                href="#filterTab1" onclick="filter_documents({{ $var->id }}, 'all')">Tutti</a>
                                            @if ($var != null)
                                                @php
                                                    if ($var->ConstructionSiteSetting != null) {
                                                        $typeofconstruct = explode(',', $var->ConstructionSiteSetting->type_of_deduction);
                                                    } else {
                                                        $typeofconstruct = [];
                                                    }

                                                    $slug = 'Documenti Sicurezza';

                                                @endphp

                                                @foreach ($typeofconstruct as $typeofconstruct)

                                                    @if ($typeofconstruct == '110')
                                                        @php
                                                            $slug = 'Documenti 110';
                                                            $route = route('check_50_65_90_110', [$slug, $var->id]);
                                                        @endphp
                                                    @elseif ($typeofconstruct == '90')
                                                        @php
                                                            $slug = 'Documenti 90';
                                                            $route = route('check_50_65_90_110', [$slug, $var->id]);
                                                        @endphp
                                                    @elseif ($typeofconstruct == '65')
                                                        @php
                                                            $slug = 'Documenti 65';
                                                            $route = route('check_50_65_90_110', [$slug, $var->id]);
                                                        @endphp
                                                    @elseif ($typeofconstruct == '50')
                                                        @php
                                                            $slug = 'Documenti 50';
                                                            $route = route('check_50_65_90_110', [$slug, $var->id]);
                                                        @endphp
                                                    @elseif (strtolower($typeofconstruct) == 'fotovoltaico')
                                                        @php
                                                            $slug = 'Documenti Fotovoltaico';
                                                            $route = route('check_fotovoltac', [$slug, $var->id]);
                                                        @endphp
                                                    @else
                                                        @php
                                                            $slug = '';
                                                            $route = '';
                                                        @endphp
                                                    @endif

                                                    <a class="btn btn-light text-black btn-sm 50-filter me-2 mb-2 {{ $typeofconstruct == null || $typeofconstruct == '' ? 'd-none' : '' }}"
                                                        onclick="filter_documents({{ $var->id }}, '{{ $slug }}')">{{ $typeofconstruct }}</a>
                                                @endforeach
                                            @endif

                                            {{-- <a  type="button" class="btn btn-danger me-2 mb-2" role="tab"
                                                data-bs-toggle="tab" href=""></a> --}}


                                            <a href="{{ route('essential', $var->id) }}"
                                                class="btn btn-danger me-2 mb-2">Essenziali</a>
                                            <a href="{{ route('chiavetta', $var->id) }}"
                                                class="btn btn-info me-2 mb-2">Chiavetta</a>

                                            {{-- <a id="filter4" type="button" class="btn btn-info me-2 mb-2" role="tab"
                                                data-bs-toggle="tab" href="#filterTab4"></a> --}}
                                        </div>
                                    </nav>
                                @elseif(auth()->user()->hasrole('technician'))
                                @endif

                            </div>
                            <div class="text-end">
                                {{-- <button type="" class="btn btn-green">
                                    <i class="fa fa-download me-2"></i>
                                    Scarica tutto
                                </button> --}}
                                @php
                                    $foldername = 'all';
                                @endphp

                                <a class="btn btn-green" href="{{ route('download_prenotidoc_folder', ['foldername'=>'all', $var->id]) }}">
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
                        <table class="table document-table table-hover" id="documentTable" >
                            <thead>
                                <tr>
                                    <th scope="col">Nome Documento</th>
                                    <th scope="col">Stato</th>
                                    <th scope="col" class="hideInMobile">Aggiornato il</th>
                                    <th scope="col" class="hideInMobile">Aggiornato Da</th>
                                    <th scope="col" class=""></th>
                                    <th scope="col" class="text-white"></th>
                                </tr>
                            </thead>
                            <tbody id="response">

                                @foreach ($prenoti_doc['prenoti_doc']->PrNotDoc->where('state',1)->unique('folder_name') as $prnotdoc)

                                   @php
                                        $allow = $prnotdoc->allow;
                                        $allowedRoles = explode(',', $allow);
                                    @endphp

                                    @hasrole('admin')
                                        @if (in_array('admin', $allowedRoles))
                                            @include('construction.construction_status.prenotidoc')
                                        @endif
                                    @endhasrole
                                    @hasrole('technician')
                                        @if (in_array('technician', $allowedRoles))
                                            @include('construction.construction_status.prenotidoc')
                                            @include('construction.construction_status.technician_regprac_doc')
                                        @endif
                                    @endhasrole
                                    @hasrole('businessconsultant')
                                        @if (in_array('businessconsultant', $allowedRoles))
                                            @include('construction.construction_status.prenotidoc')
                                        @endif
                                    @endhasrole
                                    @hasrole('business')
                                        @if (in_array('business', $allowedRoles))
                                            @include('construction.construction_status.prenotidoc')
                                        @endif
                                    @endhasrole
                                    @hasrole('photovoltaic')
                                        @if (in_array('photovoltaic', $allowedRoles))
                                            @include('construction.construction_status.prenotidoc')
                                        @endif
                                    @endhasrole
                                    @hasrole('user')
                                        @if (in_array('user', $allowedRoles))
                                            @include('construction.construction_status.prenotidoc')
                                        @endif
                                    @endhasrole

                                    <!--  Modal -->
                                    <x-reminder-email-model modelId="bell{{ $prnotdoc->id }}Modal"
                                        folderName="{{ $prnotdoc->folder_name }}" conId="{{ $var->id }}">
                                    </x-reminder-email-model>
                                    <!-- End Modal -->
                                @endforeach
                                {{-- relief_doc --}}
                                {{-- @dd($prenoti_doc['prenoti_and_relif']->unique('folder_name')->where('state',1)); --}}


                                {{-- @php

                                $relief_doc_files = $prenoti_doc['prenoti_and_relif'];

                                $updated_files = $relief_doc_files->filter(function ($relief_doc_file) {
                                    return $relief_doc_file->updated_on !== null;
                                });

                                // dd($updated_files);
                                $not_updated_files = $relief_doc_files->filter(function ($relief_doc_file) {
                                    return $relief_doc_file->updated_on === null;
                                });



                                $filtered_files = $updated_files->concat($not_updated_files);
                                $unique_files = collect([]);
                                $grouped_files = $filtered_files->groupBy(function ($item) {
                                    return strtolower($item->file_name);
                                });

                                foreach ($grouped_files as $group) {
                                    $unique_files->push($group->first());
                                }

                            @endphp --}}

                                @foreach ($prenoti_doc['prenoti_and_relif']->where('state',1)->unique('folder_name') as $reliefdocument)
                                    @php
                                        $allow = $reliefdocument->allow;
                                        $allowedRoles = explode(',', $allow);
                                    @endphp
                                    @hasrole('admin')
                                        @if (in_array('admin', $allowedRoles))
                                            @include('construction.construction_status.prenotiandrelif')
                                        @endif
                                    @endhasrole
                                    @hasrole('technician')
                                        @if (in_array('technician', $allowedRoles))
                                            @include('construction.construction_status.prenotiandrelif')
                                        @endif
                                    @endhasrole
                                    @hasrole('businessconsultant')
                                        @if (in_array('businessconsultant', $allowedRoles))
                                            @include('construction.construction_status.prenotiandrelif')
                                        @endif
                                    @endhasrole
                                    @hasrole('business')
                                        @if (in_array('business', $allowedRoles))
                                            @include('construction.construction_status.prenotiandrelif')
                                        @endif
                                    @endhasrole
                                    @hasrole('photovoltaic')
                                        @if (in_array('photovoltaic', $allowedRoles))
                                            @include('construction.construction_status.prenotiandrelif')
                                        @endif
                                    @endhasrole
                                    @hasrole('user')
                                        @if (in_array('user', $allowedRoles))
                                            @include('construction.construction_status.prenotiandrelif')
                                        @endif
                                    @endhasrole


                                    <!--  Modal -->
                                    <x-reminder-email-model modelId="bell{{ $prnotdoc->id }}Modal"
                                        folderName="{{ $prnotdoc->folder_name }}" conId="{{ $var->id }}">
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
            function handleSearch() {

                var searchText = $("#searchfield1").val().toLowerCase();

                // Search in table 1
                $("#documentTable tbody tr").each(function() {
                    var rowData = $(this).text().toLowerCase();

                    // console.log(rowData);
                    $(this).toggle(rowData.indexOf(searchText) > -1);
                });



                // If you have a second table, search in it similarly
                $("#dataTable2 tbody tr").each(function() {

                    var rowData = $(this).text().toLowerCase();
                    $(this).toggle(rowData.indexOf(searchText) > -1);
                });
            }

            function filter_documents(id, slug) {
                $('#response').html('');
                //alert(slug);

                $.ajax({
                    method: "post",
                    url: "{{ route('show_filtered_documents') }}",
                    data: {
                        'slug': slug,
                        'id': id,
                        "_token": token
                    },
                    success: function (response) {
                        $('#response').html(response.data);
                    }
                });
            }

            // Attach the search event
            $("#searchfield1").on("keyup", handleSearch);




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
