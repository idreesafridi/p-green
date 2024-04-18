<x-app-layout :pageTitle="request()->route()->pagename">
    @section('styles')
    @endsection
    
    @php
        $url = url()->previous();
   
        $chiveta = Str::contains($url, 'page/chiavetta');
       
        if($chiveta)
        {
            $backRoute =  Route('chiavetta', $construct_id);
        }
        else
        {
            $backRoute =  Route('type_of_deduc_sub1', [$prnotdocid , $sub1id , $docname]);
        } 
    @endphp

    <x-construction-detail-head :consId="$construct_id"  />
    <x-construction-detail-nav  :constructionid="$construct_id"/>

    <div class="tab-content">
        <div class="card p-4 border-0 site-detail-card">
            <div class="card-head document-page-header py-4">
                <div class="d-flex align-items-center">

                
                    <a href="{{ $backRoute }}"><i class="fa fa-arrow-left me-3 back"></i></a>


                    {{-- <h6 class="heading fw-bold mb-0">Tutti i Documenti</h6> --}}
                    <h6 class="heading fw-bold mb-0">{{$sub_chiled_f_name}}</h6>
                    {{-- @dd($sub_chiled_f_name) --}}

                </div>
                <div class="row">
                    <div class="col-12 mt-4 col-lg-4 d-flex align-items-center">
                        <input type="text" id="searchfield" class="form-control head-input" placeholder="Cerca tra i documenti" >
                    </div>
                    <div class="col-12 col-lg-8 mt-4 d-md-flex justify-content-between align-items-center">
                        <div>
                            <div style="float: right;">
                                <form action="{{ route('upload_chiled_files') }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf
                                    <input type="text" name="pr_not_doc_id" value="{{ $prnotdocid }}" hidden>
                                    <input type="text" name="type_of_dedection_sub1_id" value="{{ $sub1id }}"
                                        hidden>
                                    <input type="text" name="type_of_dedection_sub2_id"
                                        value="{{ $prenoti_doc_sub2->id }}" hidden>

                                    <input type="text" name="parent1_folder_name" value="{{ $docname }}" hidden>

                                    <input type="text" name="parent2_folder_name" value="{{ $prntfname }}" hidden>

                                    <input type="text" name="parent3_folder_name" value="{{ $sub_chiled_f_name }}"
                                        hidden>

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
                        </div>
                        <div class="text-end">
                            {{-- <button type="" class="btn btn-green">
                                    <i class="fa fa-download me-2"></i>
                                    Scarica tutto
                                </button> --}}
                            {{-- <a class="btn btn-green" href="{{ route('download_prenoti_folder') }}">
                                    <i class="fa fa-download me-2"> Scarica tutto</i>
                                </a> --}}
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
                               
                                @foreach ($prenoti_doc_sub2->TypeOfDedectionFiles as $prnotdoc)
                                {{-- @dd($prenoti_doc_sub2->TypeOfDedectionFiles ) --}}
                                    @php
                                        $allow = $prnotdoc->allow;
                                        $allowedRoles = explode(',', $allow);
                                    @endphp
                                    @hasrole('admin')
                                        @if (in_array('admin', $allowedRoles))
                                            @include('typeofdeduc3')
                                        @endif
                                    @endhasrole
                                    @hasrole('technician')
                                        @if (in_array('technician', $allowedRoles))
                                            @include('typeofdeduc3')
                                        @endif
                                    @endhasrole
                                    @hasrole('businessconsultant')
                                        @if (in_array('businessconsultant', $allowedRoles))
                                            @include('typeofdeduc3')
                                        @endif
                                    @endhasrole
                                    @hasrole('photovoltaic')
                                        @if (in_array('photovoltaic', $allowedRoles))
                                            @include('typeofdeduc3')
                                        @endif
                                    @endhasrole
                                    @hasrole('user')
                                        @if (in_array('user', $allowedRoles))
                                            @include('typeofdeduc3')
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
        </script>
    @endsection
</x-app-layout>
