<x-app-layout :pageTitle="request()->route()->pagename">
    @section('styles')
    @endsection

    <x-construction-detail-head :consId="$construct_id"  />
    <x-construction-detail-nav :constructionid="$construct_id" />
  
    <div class="tab-content">
        <div class="card p-4 border-0 site-detail-card">
            <div class="card-head document-page-header py-4">
                <div class="d-flex align-items-center">
                    <a href="{{ url()->previous() }}">
                        <i class="fa fa-arrow-left me-3 back"></i>
                    </a>
                    <h6 class="heading fw-bold mb-0">{{ $folder->folder_name }}</h6>
                </div>
                <div class="row">
                    <div class="col-12 mt-4 col-lg-4 d-flex align-items-center">
                        <input type="text" id="searchfield" class="form-control head-input"
                            placeholder="Cerca tra i documenti">
                    </div>
                    <div class="col-12 col-lg-8 mt-4 d-md-flex justify-content-between align-items-center">
                        <div>
                            <div style="float: right;">
                                <form action="{{ route('upload_chiavetta_file') }}" method="POST"
                                    enctype="multipart/form-data" id="chiavetta_upload_files">
                                    @csrf
                                    <input type="text" name="parent_folder_id" value="{{ $folder->id }}" hidden>
                                    <input type="text" name="parent_folder_name" value="{{ $folder->folder_name }}" hidden>
                                    <input type="text" name="orignal_name" value="" hidden>
                                    <input type="hidden" name = "construction_id" value="{{ $construct_id }}">

                                    <input type="file" autocomplete="off" name="files[]" accept=".pdf"
                                        id="input" class="form-control d-inline"
                                        style="color:grey !important; width:70%" multiple onchange="submit_form()">
                                    <span class='d-inline'>
                                    </span>
                                </form>
                                <!-- btn text : border disabled -->
                            </div>
                            <div class="nav nav-tabs border-bottom-0 d-none" role="tablist"
                                id="chiavetta_upload_files_spinner">
                                <div class="spinner-border" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
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
                                @foreach ($data['Files'] as $file)
                                    @if ($file->state != 0)
                                        <tr>
                                            <td>
                                                <button type="button" class="viewFileBtn" data-bs-toggle="modal"
                                                    data-bs-target="#viewFileModal{{ $file->id }}"
                                                    style="outline: none; border: none; background-color: transparent;"
                                                    data-filepath="error404greengen.png"> <i class="fa fa-file-o"></i>
                                                    <strong class="me-4 ms-2">{{ $file->file_name }}</strong>
                                                </button>
                                                <small>{{ $file->description }}</small>
                                            </td>
                                            <td>
                                                @if ($file->updated_by == null)
                                                    <span class="badge bg-danger">MANCANTE</span>
                                                @else
                                                    <span class="badge bg-success">CARICATO</span>
                                                @endif
                                            </td>
                                            <td class="hideInMobile">{{ $file->updated_on }}</td>
                                            <td class="hideInMobile">{{ $file->updated_by }}</td>
                                            <td class="space"></td>
                                            <td>
                                            <div style="display: inline-flex; width: 100%;">
                                                <a href="{{ asset('construction-assets/' . $file->file_path) }}" class="btn btn-link btn-sm text-dark d-inline" download>
                                                    <i class="fa fa-download"></i>
                                                </a>                                                
                                                <a class="btn btn-link btn-sm text-dark d-inline" data-bs-toggle="modal"
                                                    data-bs-target="#replaceDocModal{{ $file->id }}">
                                                    <i class="fa fa-exchange"></i>
                                                </a>
                                                <a class="btn btn-link btn-sm text-danger d-inline" data-bs-toggle="modal"
                                                    data-bs-target="#warningModal{{ $file->id }}">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </div>
                                            </td>
                                        </tr>


                                        <!-- File Preview Modal -->
                                        <x-file-preview-modal modelId="viewFileModal{{$file->id}}" filepath="{{ $file->file_path }}"/>
                                        <!-- End File Preview Modal -->

                                        <!-- Replace Document Modal -->
                                        <div class="modal fade" id="replaceDocModal{{$file->id}}" aria-labelledby="exampleModalLabel"
                                            aria-modal="true" role="dialog">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content send-email">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLabel">
                                                            <strong>Sostituisci un documento</strong>
                                                        </h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    @if ($file->file_name)
                                                        <form action="{{ route('replace_chiavetta_file') }}" method="POST"
                                                            enctype="multipart/form-data">
                                                            @csrf
                                                            <div class="modal-body">
                                                                <div class="row mb-3 mt-5">
                                                                    <img src="https://greengen.crm-labloid.com/assets/images/swap-img.svg" class="alert-img mx-auto">
                                                                </div>
                                                                <div class="mb-4">
                                                                    <h6 class="text-center">Trascina qui sotto
                                                                        il
                                                                        documento oppure selezionalo dal tuo PC
                                                                    </h6>
                                                                </div>
                                                                <input type="text" name="parent_folder_id" value="{{ $folder->id }}" hidden>
                                                                <input type="text" name="parent_folder_name" value="{{ $folder->folder_name }}" hidden>
                                                                <input type="text" name="orignal_name" value="{{ $file->file_name }}" hidden>
                                                                <input type="hidden" name="construction_id" value="{{ $construct_id }}">
                                                                <input type="text" name="file_id" value="{{ $file->id }}" hidden>

                                                                <div class="mb-4">
                                                                    <input type="file" autocomplete="off" class="form-control" id=""
                                                                        name="file" class="form-control file-uploader">
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer mb-3">
                                                                <button type="button" class="btn btn-secondary"
                                                                    data-bs-dismiss="modal">Indietro</button>
                                                                <button type="submit" class="btn btn-green">Rimpiazza</button>
                                                            </div>
                                                        </form>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <!-- End Replace Document Modal -->

                                        <!-- Delete Modal -->
                                        <div class="modal fade" id="warningModal{{$file->id}}" aria-labelledby="exampleModalLabel" aria-modal="true"
                                            role="dialog">
                                            <div class="modal-dialog modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLabel">Attenzione</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <form action="{{ route('delete_chiavetta_file') }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')

                                                        <input type="hidden" name="id" value="{{ $file->id }}">
                                                        <div class="modal-body">
                                                            <p class="text-center m-0">Sei sicuro di voler procedere?</p>
                                                        </div>
                                                        <div class="modal-footer mb-3">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Indietro</button>
                                                            <button type="submit" name="reset-pass" class="btn btn-danger">Procedi</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- End Delete Modal -->
                                    @endif
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

            function submit_form() {
                $('#chiavetta_upload_files_spinner').removeClass('d-none')
                $('#chiavetta_upload_files').addClass('d-none')
                document.getElementById("chiavetta_upload_files").submit();
            }
        </script>
    @endsection
</x-app-layout>
