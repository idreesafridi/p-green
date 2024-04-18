<x-app-layout pageTitle="Chiavetta">
    @section('styles')
    @endsection
      
    <x-construction-detail-head :consId="$construct_id" /> 
    <x-construction-detail-nav  :constructionid="$construct_id" />

    <div class="tab-content">
        <div class="card p-4 border-0 site-detail-card">
            <div class="card-head document-page-header py-4">
                <div class="d-flex align-items-center">

                    <a href="javascript:history.back();"> <i class="fa fa-arrow-left me-3 back "></i></a>

                    <h6 class="heading fw-bold mb-0">Chiavetta</h6>
                </div>
                <div class="row">
                    <div class="col-12 mt-4 col-lg-4 d-flex align-items-center">
                        <input type="text" class="form-control head-input" placeholder="Cerca tra i documenti">
                    </div>
                    <div class="col-12 col-lg-8 mt-4 d-md-flex justify-content-between align-items-center">
                        <div>
                            <div style="float: right;">
                            </div>

                        </div>
                        <div class="text-end">
                            @php
                                $slug = 'chaivetta';
                            @endphp
                            <a class="btn btn-green" href="{{ route('zip_chiavetta_or_essential', $slug) }}">
                                <i class="fa fa-download me-2"> Scarica tutto</i></a>
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
                                {{-- 110 --}}

                                @if ($data110 != null)

                                    @if ($data110['deduction1_for110'] != null || $data110['TypeOfDedectionFiles'] != null)


                                        {{-- document typeofdeduction2 --}}
                                      
                                        @foreach ($data110['deduction2_for110']->where('state', 1) as $prnotdoc)
                                            @php
                                                $allow = $prnotdoc->allow;
                                                $allowedRoles = explode(',', $allow);
                                            @endphp
                                            @hasrole('admin')
                                                @if (in_array('admin', $allowedRoles))
                                                    @include('chiavetta1_for110')
                                                @endif
                                            @endhasrole
                                            @hasrole('technician')
                                                @if (in_array('technician', $allowedRoles))
                                                    @include('chiavetta1_for110')
                                                @endif
                                            @endhasrole
                                            @hasrole('businessconsultant')
                                                @if (in_array('businessconsultant', $allowedRoles))
                                                    @include('chiavetta1_for110')
                                                @endif
                                            @endhasrole
                                            @hasrole('photovoltaic')
                                                @if (in_array('photovoltaic', $allowedRoles))
                                                    @include('chiavetta1_for110')
                                                @endif
                                            @endhasrole
                                            @hasrole('user')
                                                @if (in_array('user', $allowedRoles))
                                                    @include('chiavetta1_for110')
                                                @endif
                                            @endhasrole
                                        @endforeach

                                        @foreach ($data110['TypeOfDedectionFiles']->where('state', 1) as $prnotdoc)
                                            @php
                                                $allow = $prnotdoc->allow;
                                                $allowedRoles = explode(',', $allow);
                                            @endphp
                                            @hasrole('admin')
                                                @if (in_array('admin', $allowedRoles))
                                                    @include('chiavettatypeofdec3')
                                                @endif
                                            @endhasrole
                                            @hasrole('technician')
                                                @if (in_array('technician', $allowedRoles))
                                                    @include('chiavettatypeofdec3')
                                                @endif
                                            @endhasrole
                                            @hasrole('businessconsultant')
                                                @if (in_array('businessconsultant', $allowedRoles))
                                                    @include('chiavettatypeofdec3')
                                                @endif
                                            @endhasrole
                                            @hasrole('photovoltaic')
                                                @if (in_array('photovoltaic', $allowedRoles))
                                                    @include('chiavettatypeofdec3')
                                                @endif
                                            @endhasrole
                                            @hasrole('user')
                                                @if (in_array('user', $allowedRoles))
                                                    @include('chiavettatypeofdec3')
                                                @endif
                                            @endhasrole
                                        @endforeach
                                        {{-- Type of Deducrionsub 1 --}}
                                        @php
                                            $folder_name = 'Documenti 110';
                                        @endphp
                                        @foreach ($data110['deduction1_for110']->where('state', 1) as $prnotdoc)
                                            @php
                                                $allow = $prnotdoc->allow;
                                                $allowedRoles = explode(',', $allow);
                                            @endphp
                                            @hasrole('admin')
                                                @if (in_array('admin', $allowedRoles))
                                                    @include('chiavetta1_documenti110')
                                                @endif
                                            @endhasrole
                                            @hasrole('technician')
                                                @if (in_array('technician', $allowedRoles))
                                                    @include('chiavetta1_documenti110')
                                                @endif
                                            @endhasrole
                                            @hasrole('businessconsultant')
                                                @if (in_array('businessconsultant', $allowedRoles))
                                                    @include('chiavetta1_documenti110')
                                                @endif
                                            @endhasrole
                                            @hasrole('photovoltaic')
                                                @if (in_array('photovoltaic', $allowedRoles))
                                                    @include('chiavetta1_documenti110')
                                                @endif
                                            @endhasrole
                                            @hasrole('user')
                                                @if (in_array('user', $allowedRoles))
                                                    @include('chiavetta1_documenti110')
                                                @endif
                                            @endhasrole
                                        @endforeach
                                        {{-- end  --}}
                                    @endif
                                @endif
                                {{-- end 110 --}}

                                {{-- ----------------------------------------------------- --}}
                                {{-- start document 90 --}}
                                {{-- @dd($data90); --}}
                                @if ($data90 != null)

                                    {{-- @if ($data90['deduction1_for90'] != null) --}}

                                    {{-- document typeofdeduction2 --}}
                         
                                    @foreach ($data90['deduction2_for90']->where('state', 1) as $prnotdoc)
                                        @php
                                            $allow = $prnotdoc->allow;
                                            $allowedRoles = explode(',', $allow);
                                        @endphp
                                        @hasrole('admin')
                                            @if (in_array('admin', $allowedRoles))
                                                @include('chiavetta_deduction2_for90')
                                            @endif
                                        @endhasrole
                                        @hasrole('technician')
                                            @if (in_array('technician', $allowedRoles))
                                                @include('chiavetta_deduction2_for90')
                                            @endif
                                        @endhasrole
                                        @hasrole('businessconsultant')
                                            @if (in_array('businessconsultant', $allowedRoles))
                                                @include('chiavetta_deduction2_for90')
                                            @endif
                                        @endhasrole
                                        @hasrole('photovoltaic')
                                            @if (in_array('photovoltaic', $allowedRoles))
                                                @include('chiavetta_deduction2_for90')
                                            @endif
                                        @endhasrole
                                        @hasrole('user')
                                            @if (in_array('user', $allowedRoles))
                                                @include('chiavetta_deduction2_for90')
                                            @endif
                                        @endhasrole
                                        <!-- End Delete Modal -->
                                    @endforeach
                                    {{-- deduction1 --}}
                                    @php
                                        $folder_name = 'Documenti 90';
                                    @endphp
                                    @if ($data90['deduction1_for90'])
                                        @php
                                            $allow = $prnotdoc->allow;
                                            $allowedRoles = explode(',', $allow);
                                        @endphp
                                        @hasrole('admin')
                                            @if (in_array('admin', $allowedRoles))
                                                @include('chiavetta_deduction1_for90')
                                            @endif
                                        @endhasrole
                                        @hasrole('technician')
                                            @if (in_array('technician', $allowedRoles))
                                                @include('chiavetta_deduction1_for90')
                                            @endif
                                        @endhasrole
                                        @hasrole('businessconsultant')
                                            @if (in_array('businessconsultant', $allowedRoles))
                                                @include('chiavetta_deduction1_for90')
                                            @endif
                                        @endhasrole
                                        @hasrole('photovoltaic')
                                            @if (in_array('photovoltaic', $allowedRoles))
                                                @include('chiavetta_deduction1_for90')
                                            @endif
                                        @endhasrole
                                        @hasrole('user')
                                            @if (in_array('user', $allowedRoles))
                                                @include('chiavetta_deduction1_for90')
                                            @endif
                                        @endhasrole
                                        <!-- End Delete Modal -->
                                    @endif
                                    {{-- end type of deduction 1 --}}
                                    {{-- @endif --}}
                                @endif
                                {{-- end 90 --}}

                                {{-- ----------------------------------------------------- --}}
                                {{-- start 65 --}}
                                @if ($data65 != null)
                                    @if ($data65['deduction2_for65'] != null)
                                        {{-- document typeofdeduction2 --}}
                                        @foreach ($data65['deduction2_for65']->where('state', 1) as $prnotdoc)
                                            @php
                                                $allow = $prnotdoc->allow;
                                                $allowedRoles = explode(',', $allow);
                                            @endphp
                                            @hasrole('admin')
                                                @if (in_array('admin', $allowedRoles))
                                                    @include('chiavetta_typeofdeduc2')
                                                @endif
                                            @endhasrole
                                            @hasrole('technician')
                                                @if (in_array('technician', $allowedRoles))
                                                    @include('chiavetta_typeofdeduc2')
                                                @endif
                                            @endhasrole
                                            @hasrole('businessconsultant')
                                                @if (in_array('businessconsultant', $allowedRoles))
                                                    @include('chiavetta_typeofdeduc2')
                                                @endif
                                            @endhasrole
                                            @hasrole('photovoltaic')
                                                @if (in_array('photovoltaic', $allowedRoles))
                                                    @include('chiavetta_typeofdeduc2')
                                                @endif
                                            @endhasrole
                                            @hasrole('user')
                                                @if (in_array('user', $allowedRoles))
                                                    @include('chiavetta_typeofdeduc2')
                                                @endif
                                            @endhasrole
                                        @endforeach
                                    @endif
                                @endif
                                {{-- reg prac doc --}}

                                {{-- ------------------------------------ --}}
                                {{-- document 50 --}}
                                @if ($data50 != null)
                                    @if ($data50['deduction1_for50'] != null)

                                        {{-- deductiontype2 --}}
                                        @foreach ($data50['deduction2_for50']->where('state', 1) as $prnotdoc)
                                            @php
                                                $allow = $prnotdoc->allow;
                                                $allowedRoles = explode(',', $allow);
                                            @endphp
                                            @hasrole('admin')
                                                @if (in_array('admin', $allowedRoles))
                                                    @include('chiavetta_typeofdeduc2_for50')
                                                @endif
                                            @endhasrole
                                            @hasrole('technician')
                                                @if (in_array('technician', $allowedRoles))
                                                    @include('chiavetta_typeofdeduc2_for50')
                                                @endif
                                            @endhasrole
                                            @hasrole('businessconsultant')
                                                @if (in_array('businessconsultant', $allowedRoles))
                                                    @include('chiavetta_typeofdeduc2_for50')
                                                @endif
                                            @endhasrole
                                            @hasrole('photovoltaic')
                                                @if (in_array('photovoltaic', $allowedRoles))
                                                    @include('chiavetta_typeofdeduc2_for50')
                                                @endif
                                            @endhasrole
                                            @hasrole('user')
                                                @if (in_array('user', $allowedRoles))
                                                    @include('chiavetta_typeofdeduc2_for50')
                                                @endif
                                            @endhasrole
                                        @endforeach

                                        {{-- End Rel Doc File --}}

                                    @endif
                                @endif

                                {{-- end document 50 --}}
                                {{-- ------------------------------Common Files chaiavetta ---------------------------------------- --}}
                                {{-- file structure --}}
                                @if ($data50 != null || $data65 || $data110 || $common_schemi_fotovoltaic != null)
                                    {{-- common folder for 110 65 and 50 --}}
                                    {{-- scheme --}}
                                 
                                    @if ($common_schemi['Schemi'] != null)
                                        @php
                                            $allow = $common_schemi['Schemi']->allow;
                                            $allowedRoles = explode(',', $allow);
                                        @endphp
                                        @hasrole('admin')
                                            @if (in_array('admin', $allowedRoles))
                                                @include('chiavetta_forschemi')
                                            @endif
                                        @endhasrole
                                        @hasrole('technician')
                                            @if (in_array('technician', $allowedRoles))
                                                @include('chiavetta_forschemi')
                                            @endif
                                        @endhasrole
                                        @hasrole('businessconsultant')
                                            @if (in_array('businessconsultant', $allowedRoles))
                                                @include('chiavetta_forschemi')
                                            @endif
                                        @endhasrole
                                        @hasrole('photovoltaic')
                                            @if (in_array('photovoltaic', $allowedRoles))
                                                @include('chiavetta_forschemi')
                                            @endif
                                        @endhasrole
                                        @hasrole('user')
                                            @if (in_array('user', $allowedRoles))
                                                @include('chiavetta_forschemi')
                                            @endif
                                        @endhasrole
                                    @endif
                                    {{-- then for 50 first --}}
                                    @if ($data50 != null)
                                        @if ($data50['deduction1_for50'] != null)
                                            {{--  document 1 --}}
                                            @php
                                                $folder_name = 'Documenti 50';
                                            @endphp
                                            @foreach ($data50['deduction1_for50'] as $prnotdoc)
                                                @php
                                                    $allow = $prnotdoc->allow;
                                                    $allowedRoles = explode(',', $allow);
                                                @endphp
                                                @hasrole('admin')
                                                    @if (in_array('admin', $allowedRoles))
                                                        @include('chiavetta_typeofdeduc1_for50')
                                                    @endif
                                                @endhasrole
                                                @hasrole('technician')
                                                    @if (in_array('technician', $allowedRoles))
                                                        @include('chiavetta_typeofdeduc1_for50')
                                                    @endif
                                                @endhasrole
                                                @hasrole('businessconsultant')
                                                    @if (in_array('businessconsultant', $allowedRoles))
                                                        @include('chiavetta_typeofdeduc1_for50')
                                                    @endif
                                                @endhasrole
                                                @hasrole('photovoltaic')
                                                    @if (in_array('photovoltaic', $allowedRoles))
                                                        @include('chiavetta_typeofdeduc1_for50')
                                                    @endif
                                                @endhasrole
                                                @hasrole('user')
                                                    @if (in_array('user', $allowedRoles))
                                                        @include('chiavetta_typeofdeduc1_for50')
                                                    @endif
                                                @endhasrole
                                            @endforeach
                                        @endif
                                    @endif
                                    {{-- if 110 not empty --}}
                                    {{-- reg prac doc files --}}
                                    {{-- @if ($data110 != null)
                                        @if ($data110['RegPracDoc_for110'] != null)
                                            @foreach ($data110['RegPracDoc_for110'] as $regpracdoc)
                                                @php
                                                    $allow = $regpracdoc->allow;
                                                    $allowedRoles = explode(',', $allow);
                                                @endphp
                                                @hasrole('admin')
                                                    @if (in_array('admin', $allowedRoles))
                                                        @include('construction.construction_status.regpracdoc_authenticate')
                                                    @endif
                                                @endhasrole
                                                @hasrole('technician')
                                                    @if (in_array('technician', $allowedRoles))
                                                        @include('construction.construction_status.regpracdoc_authenticate')
                                                    @endif
                                                @endhasrole
                                                @hasrole('businessconsultant')
                                                    @if (in_array('businessconsultant', $allowedRoles))
                                                        @include('construction.construction_status.regpracdoc_authenticate')
                                                    @endif
                                                @endhasrole
                                                @hasrole('photovoltaic')
                                                    @if (in_array('photovoltaic', $allowedRoles))
                                                        @include('construction.construction_status.regpracdoc_authenticate')
                                                    @endif
                                                @endhasrole
                                                @hasrole('user')
                                                    @if (in_array('user', $allowedRoles))
                                                        @include('construction.construction_status.regpracdoc_authenticate')
                                                    @endif
                                                @endhasrole
                                            @endforeach
                                        @endif
                                    @endif--}}
                                @endif 

                                {{-- common for 50 65 and 90 --}}
                                {{-- @if ($common_for_50_65_90 != null)
                                    @if ($common_for_50_65_90['Common_RegPracDoc'] != null)
                                        @foreach ($common_for_50_65_90['Common_RegPracDoc'] as $regpracdoc)
                                            @php
                                                $allow = $regpracdoc->allow;
                                                $allowedRoles = explode(',', $allow);
                                            @endphp
                                            @hasrole('admin')
                                                @if (in_array('admin', $allowedRoles))
                                                    @include('chiavetta_common_50_65_90')
                                                @endif
                                            @endhasrole
                                            @hasrole('technician')
                                                @if (in_array('technician', $allowedRoles))
                                                    @include('chiavetta_common_50_65_90')
                                                @endif
                                            @endhasrole
                                            @hasrole('businessconsultant')
                                                @if (in_array('businessconsultant', $allowedRoles))
                                                    @include('chiavetta_common_50_65_90')
                                                @endif
                                            @endhasrole
                                            @hasrole('photovoltaic')
                                                @if (in_array('photovoltaic', $allowedRoles))
                                                    @include('chiavetta_common_50_65_90')
                                                @endif
                                            @endhasrole
                                            @hasrole('user')
                                                @if (in_array('user', $allowedRoles))
                                                    @include('chiavetta_common_50_65_90')
                                                @endif
                                            @endhasrole
                                        @endforeach
                                    @endif
                                @endif --}}

                                {{-- commonchiavetta start hereeee --}}

                                @php 

                                $relief_doc_files =   $data['RelDocFile'];
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

                                 
                                      foreach ($grouped_files as $group) {
                                          $unique_files->push($group->first());
                                      }
                                
                              @endphp
                                @foreach ($unique_files as $prnotdoc)
                                    @php
                                        $allow = $prnotdoc->allow;
                                        $allowedRoles = explode(',', $allow);
                                    @endphp
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
                                    @hasrole('admin')
                                        @if (in_array('admin', $allowedRoles))
                                            @include('chiavetta_common_rel_doc_file')
                                        @endif
                                    @endhasrole
                                    @hasrole('technician')
                                        @if (in_array('technician', $allowedRoles))
                                            @include('chiavetta_common_rel_doc_file')
                                        @endif
                                    @endhasrole
                                    @hasrole('businessconsultant')
                                        @if (in_array('businessconsultant', $allowedRoles))
                                            @include('chiavetta_common_rel_doc_file')
                                        @endif
                                    @endhasrole
                                    @hasrole('photovoltaic')
                                        @if (in_array('photovoltaic', $allowedRoles))
                                            @include('chiavetta_common_rel_doc_file')
                                        @endif
                                    @endhasrole
                                    @hasrole('user')
                                        @if (in_array('user', $allowedRoles))
                                            @include('chiavetta_common_rel_doc_file')
                                        @endif
                                    @endhasrole
                                @endforeach

                                {{-- legg 10 --}}
                                @if ($data['Legge10'] != null)
                                    @foreach ($data['Legge10'] as $legge10file_data)
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

                                {{-- PrNotDocFile --}}
                                @foreach ($data['PrNotDocFile'] as $prnotdocfile)
                                    @php
                                        $allow = $legge10file_data->allow;
                                        $allowedRoles = explode(',', $allow);
                                    @endphp
                                    @hasrole('admin')
                                        @if (in_array('admin', $allowedRoles))
                                            @include('chiavetta_prnodoc_file')
                                        @endif
                                    @endhasrole
                                    @hasrole('technician')
                                        @if (in_array('technician', $allowedRoles))
                                            @include('chiavetta_prnodoc_file')
                                        @endif
                                    @endhasrole
                                    @hasrole('businessconsultant')
                                        @if (in_array('businessconsultant', $allowedRoles))
                                            @include('chiavetta_prnodoc_file')
                                        @endif
                                    @endhasrole
                                    @hasrole('photovoltaic')
                                        @if (in_array('photovoltaic', $allowedRoles))
                                            @include('chiavetta_prnodoc_file')
                                        @endif
                                    @endhasrole
                                    @hasrole('user')
                                        @if (in_array('user', $allowedRoles))
                                            @include('chiavetta_prnodoc_file')
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

            function replace_sub2_file(id) {
                var spinner = $('#replace_sub2_file_spinner_' + id)
                var form = $('#replace_sub2_file_form_' + id)

                spinner.removeClass('d-none')
                form.addClass('d-none')
                form.submit();
            }

            function replace_sub1_file(id) {
                var spinner = $('#replace_sub1_file_spinner_' + id)
                var form = $('#replace_sub1_file_form_' + id)

                spinner.removeClass('d-none')
                form.addClass('d-none')
                form.submit();
            }

            function replace_sub2_file_deduction2(id) {
                var spinner = $('#replace_sub2_file_deduction2_spinner_' + id)
                var form = $('#replace_sub2_file_deduction2_form_' + id)

                spinner.removeClass('d-none')
                form.addClass('d-none')
                form.submit();
            }

            function replace_sub2_file_typeofdeduc2(id) {
                var spinner = $('#replace_sub2_file_typeofdeduc2_spinner_' + id)
                var form = $('#replace_sub2_file_typeofdeduc2_form_' + id)

                spinner.removeClass('d-none')
                form.addClass('d-none')
                form.submit();
            }

            function replace_sub1_file_deduction1_for90(id) {
                var spinner = $('#replace_sub1_file_deduction1_for90_spinner_' + id)
                var form = $('#replace_sub1_file_deduction1_for90_form_' + id)

                spinner.removeClass('d-none')
                form.addClass('d-none')
                form.submit();
            }

            function replace_sub2_file_typeofdeduc2_for50(id) {
                var spinner = $('#replace_sub2_file_typeofdeduc2_for50_spinner_' + id)
                var form = $('#replace_sub2_file_typeofdeduc2_for50_form_' + id)

                spinner.removeClass('d-none')
                form.addClass('d-none')
                form.submit();
            }

            function replace_rel_doc_files_common_rel_doc_file(id) {
                var spinner = $('#replace_rel_doc_files_common_rel_doc_file_spinner_' + id)
                var form = $('#replace_rel_doc_files_common_rel_doc_file_form_' + id)

                spinner.removeClass('d-none')
                form.addClass('d-none')
                form.submit();
            }

            function legge10_file_upload_legg10files(id) {
                var spinner = $('#legge10_file_upload_legg10files_spinner_' + id)
                var form = $('#legge10_file_upload_legg10files_form_' + id)

                spinner.removeClass('d-none')
                form.addClass('d-none')
                form.submit();
            }

            function replace_file_prnodoc_file(id) {
                var spinner = $('#replace_file_prnodoc_file_spinner_' + id)
                var form = $('#replace_file_prnodoc_file_form_' + id)

                spinner.removeClass('d-none')
                form.addClass('d-none')
                form.submit();
            }

            function regprac_file_upload_common506590(id) {
                var spinner = $('#regprac_file_upload_common506590_spinner_' + id)
                var form = $('#regprac_file_upload_common506590_form_' + id)

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
        </script>
    @endsection
</x-app-layout>
