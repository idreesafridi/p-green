<x-app-layout :pageTitle="request()->route()->pagename">
    @section('styles')
    @endsection

    <x-construction-detail-head :consId="$construct_id"  />
    <x-construction-detail-nav :constructionid="$construct_id" />
    @php
    $conststatus = App\Models\ConstructionSite::find($construct_id);
    @endphp
    <div class="tab-content">
        <div class="card p-4 border-0 site-detail-card">
            <div class="card-head document-page-header py-4">
                <div class="d-flex align-items-center">
                 
                        @if($conststatus->StatusPrNoti->id)
                    <a href="{{ route('show_preNoti_doc', $conststatus->StatusPrNoti->id) }}#paper">
                        @else
                     <a href="{{ url()->previous() }}">
                        @endif
                        <i class="fa fa-arrow-left me-3 back"></i>
                    </a>
                    <h6 class="heading fw-bold mb-0">ESSENZIALI</h6>
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
                            <a class="btn btn-green" href="{{ route('zip_chiavetta_or_essential', $slug) }}">
                                <i class="fa fa-download me-2"> Scarica tutto</i></a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="tab-content">
                    <div class="tab-pane fade active show table-responsive" id="filterTab1">
                        <table class="table document-table table-hover ">
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
                                {{-- @dd($data110['deduction1_for110']); --}}
                                @if (isset($data110['deduction1_for110']) && $data110['deduction1_for110'] != null)
                                    {{-- Type of Deducrionsub 1 --}}
                                    @php
                                        $folder_name = 'Documenti 110';
                                    @endphp
                                    <tr>
                                        @if ($data110['deduction1_for110'] != null)
                                        {{-- @dd($data110['deduction1_for110']); --}}
                                            <td>
                                                <a class="fa fa-folder" href="{{ route('essentialSaldo', $data110['deduction1_for110']->construction_site_id) }}">
                                                </a>
                                                <a href="{{ route('essentialSaldo', $data110['deduction1_for110']->construction_site_id) }}" class="me-4 ms-2">
                                                    <strong>saldo</strong>
                                                </a><br>
                                                <small>Legge 10 SALDO, notifica SALDO, Integrazione CILAS...</small>
                                            </td>
                                            <td>

                                                @if ($count > 0)
                                                    <span class="badge bg-success">{{ $count }}</span>
                                                @else
                                                    <span class="badge bg-danger">{{ $count }}</span>
                                                @endif
                                            </td>
                                            {{-- @dd($data110); --}}
                                        @else
                                            <td>
                                                <button type="button" class="viewFileBtn" data-bs-toggle="modal"
                                                    data-bs-target="#viewFileModal{{ $data110['deduction1_for110']->id }}"
                                                    style="outline: none; border: none; background-color: transparent;"
                                                    data-filepath="error404greengen.png"> <i class="fa fa-file-o"></i>
                                                    <strong
                                                        class="me-4 ms-2">{{ $data110['deduction1_for110']->file_name }}</strong>
                                                </button>

                                                <!-- File Preview Modal -->
                                                <x-file-preview-modal
                                                    modelId="viewFileModal{{ $data110['deduction1_for110']->id }}"
                                                    filepath="{{ $data110['deduction1_for110']->file_path }}" />
                                                <!-- End File Preview Modal -->
                                            </td>
                                            <td>
                                                @if ($data110['deduction1_for110']->updated_by == null)
                                                    <span class="badge bg-danger">MANCANTE</span>
                                                @else
                                                    <span class="badge bg-success">CARICATO</span>
                                                @endif
                                            </td>
                                        @endif
                                        <td class="hideInMobile">{{ $data110['deduction1_for110']->updated_on }}</td>
                                        <td class="hideInMobile">{{ $data110['deduction1_for110']->updated_by }}</td>
                                        <td class="space">
                                            <form action="{{ route('replace_sub1_file') }}" method="POST"
                                                enctype="multipart/form-data"
                                                id="deduction1_for110_{{ $data110['deduction1_for110']->id }}">
                                                @csrf
                                                <input type="text" name="pr_not_doc_id"
                                                    value="{{ $data110['deduction1_for110']->pr_not_doc_id }}" hidden>
                                                <input type="text" name="file_id"
                                                    value="{{ $data110['deduction1_for110']->id }}" hidden>
                                                <input type="text" name="parent1_folder_name"
                                                    value="{{ $folder_name }}" hidden>

                                                <input type="text" name="orignal_name"
                                                    value="{{ $data110['deduction1_for110']->file_name }}" hidden>

                                                <input type="text" name="type_of_dedection_sub1_id"
                                                    value="{{ $data110['deduction1_for110']->id }}" hidden>
                                                @if ($data110['deduction1_for110']->updated_by == null && $data110['deduction1_for110']->file_name != null)
                                                    <input type="file" autocomplete="off" class="form-control"
                                                        id="" name="file" style="color:grey !important;"
                                                        onchange="deduction1_for110('{{ $data110['deduction1_for110']->id }}')">
                                                @endif
                                            </form>
                                            <div class="spinner-border d-none" role="status"
                                                id="deduction1_for110_spinner_{{ $data110['deduction1_for110']->id }}">
                                                <span class="visually-hidden">Loading...</span>
                                            </div>
                                        </td>
                                        {{-- start --}}
                                        <td>
                                            <div style="display: inline-flex; width: 100%;">
                                                <button type="button" class="btn btn-link btn-sm text-warning d-inline"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#bell{{ $data110['deduction1_for110']->id }}Modal">
                                                    <i class="fa fa-bell"></i>
                                                </button>
                                                @if ($data110['deduction1_for110']->updated_by != null)
                                                    @if ($data110['deduction1_for110']->file_name != null)
                                                        <button type="button"
                                                            class="btn btn-link btn-sm text-dark d-inline"
                                                            onclick="location.href='{{ route('download_sub1', $data110['deduction1_for110']->id) }}'">
                                                            <i class="fa fa-download"></i>
                                                        </button>
                                                    @else
                                                        <button type="button"
                                                            class="btn btn-link btn-sm text-dark d-inline"
                                                            onclick="location.href='{{ route('download_sub1_folder', [$folder_name, $data110['deduction1_for110']->folder_name]) }}'">
                                                            <i class="fa fa-download"></i>
                                                        </button>
                                                    @endif

                                                    {{-- file exchange --}}
                                                    @if ($data110['deduction1_for110']->file_name != null && $data110['deduction1_for110']->bydefault != 0)
                                                        <a class="btn btn-link btn-sm text-dark d-inline"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#replaceDocModal{{ $data110['deduction1_for110']->id }}">
                                                            <i class="fa fa-exchange"></i>
                                                        </a>
                                                    @endif

                                                    @if ($data110['deduction1_for110']->file_name != null)
                                                        <a class="btn btn-link btn-sm text-danger d-inline"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#warningModal{{ $data110['deduction1_for110']->id }}">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    @endif
                                                @endif
                                            </div>
                                        </td>
                                        {{-- end --}}
                                    </tr>

                                    <!--  Modal -->
                                    <x-reminder-email-model modelId="bell{{ $data110['deduction1_for110']->id }}Modal"
                                        folderName="{{ 'Saldo' }}" conId="{{ $construct_id }}" />
                                    <!-- End Modal -->

                                    <!-- File Preview Modal -->
                                    <x-file-preview-modal
                                        modelId="viewFileModal{{ $data110['deduction1_for110']->id }}"
                                        filepath="{{ $data110['deduction1_for110']->file_path }}" />
                                    <!-- End File Preview Modal -->

                                    {{-- end  --}}
                                    {{-- if 110 not empty --}}
                                    {{-- reg prac doc files --}}
                                    {{--@if ($data110['RegPracDoc_for110'] != null)
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
                                            <!-- End Delete Modal -->
                                        @endforeach
                                    @endif --}}
                                @endif

                                {{-- common for 50 65 and 90 
                                @if ($common_for_50_65_90 != null)
                                    @if ($common_for_50_65_90['Common_RegPracDoc'] != null)
                                        @foreach ($common_for_50_65_90['Common_RegPracDoc'] as $regpracdoc)
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
                                @endif --}}

                                {{-- common files --}}
                                @if ($data != null)
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
    `                                     

                                    @foreach ( $unique_files as $prnotdoc)
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
                                                @include('essentialreldocfile')
                                            @endif
                                        @endhasrole
                                        @hasrole('technician')
                                            @if (in_array('technician', $allowedRoles))
                                                @include('essentialreldocfile')
                                            @endif
                                        @endhasrole
                                        @hasrole('businessconsultant')
                                            @if (in_array('businessconsultant', $allowedRoles))
                                                @include('essentialreldocfile')
                                            @endif
                                        @endhasrole
                                        @hasrole('photovoltaic')
                                            @if (in_array('photovoltaic', $allowedRoles))
                                                @include('essentialreldocfile')
                                            @endif
                                        @endhasrole
                                        @hasrole('user')
                                            @if (in_array('user', $allowedRoles))
                                                @include('essentialreldocfile')
                                            @endif
                                        @endhasrole
                                    @endforeach
                                    {{-- legg 10 --}}
                                    @if ($data['Legge10'])
                                        @php
                                            $allow = $data['Legge10']->allow;
                                            $allowedRoles = explode(',', $allow);
                                        @endphp
                                        @hasrole('admin')
                                            @if (in_array('admin', $allowedRoles))
                                                @include('essentiallegg10')
                                            @endif
                                        @endhasrole
                                        @hasrole('technician')
                                            @if (in_array('technician', $allowedRoles))
                                                @include('essentiallegg10')
                                            @endif
                                        @endhasrole
                                        @hasrole('businessconsultant')
                                            @if (in_array('businessconsultant', $allowedRoles))
                                                @include('essentiallegg10')
                                            @endif
                                        @endhasrole
                                        @hasrole('photovoltaic')
                                            @if (in_array('photovoltaic', $allowedRoles))
                                                @include('essentiallegg10')
                                            @endif
                                        @endhasrole
                                        @hasrole('user')
                                            @if (in_array('user', $allowedRoles))
                                                @include('essentiallegg10')
                                            @endif
                                        @endhasrole
                                    @endif
                                    {{-- reg prac doc --}}
                                    {{-- @if ($data['Notifica'])
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
                                    @endif  --}}
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
                // sortTable();
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


            function sortTable() {
                var tbody = document.querySelector('#myTable tbody');
                var rows = Array.from(tbody.querySelectorAll('tr'));

                rows.sort(function(a, b) {
                    var textA = a.cells[0].textContent.toLowerCase();
                    var textB = b.cells[0].textContent.toLowerCase();
                    if (textA < textB) return -1;
                    if (textA > textB) return 1;
                    return 0;
                });

                rows.forEach(function(row) {
                    tbody.appendChild(row);
                });
            }

            // Call the sort function when the page loads
            window.addEventListener('load', sortTable);

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

            function replace_rel_doc_files(id) {
                var spinner = $('#replace_rel_doc_files_spinner_' + id)
                var form = $('#replace_rel_doc_files_form_' + id)

                spinner.removeClass('d-none')
                form.addClass('d-none')
                form.submit();
            }

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
        </script>
    @endsection
</x-app-layout>
