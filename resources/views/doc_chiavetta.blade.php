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
                    <h6 class="heading fw-bold mb-0">Documenti Chiavetta</h6>
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
                            <a class="btn btn-green" href="{{ route('zip_doc_files', $slug) }}">
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
                                @foreach ($data['Folders'] as $folder)
                                    <tr>
                                        <td>
                                            <a class="fa fa-folder" href=""></a>
                                            <a href="{{ route('show_chiavetta_files', ['id' => $construct_id, 'folder_id' => $folder->id]) }}" class="me-4 ms-2">
                                                <strong>{{ $folder->folder_name }}</strong>
                                            </a><br>
                                            <small>{{ $folder->description }}</small>
                                        </td>
                                        <td>
                                            @php
                                                $count = 0;
                                                $count = \App\Models\ChiavettaFile::where('chiavetta_doc_id', $folder->id)->count();
                                            @endphp
                                            @if ($count > 0)
                                                <span class="badge bg-success">{{ $count }}</span>
                                            @else
                                                <span class="badge bg-danger">{{ $count }}</span>
                                            @endif
                                        </td>
                                        <td class="hideInMobile">
                                            @if ($count > 0)
                                                {{ $folder->updated_on }}
                                            @endif
                                        </td>
                                        <td class="hideInMobile">
                                            @if ($count > 0)
                                                {{ $folder->updated_by }}
                                            @endif
                                        </td>
                                        <td class="space"></td>
                                        <td>
                                            <div style="display: inline-flex; width: 100%;">
                                                <button type="button" class="btn btn-link btn-sm text-warning d-inline" data-bs-toggle="modal"
                                                    data-bs-target="#bell{{ $folder->id }}Modal">
                                                    <i class="fa fa-bell"></i>
                                                </button>
                                                @if ($count > 0)
                                                    <button type="button" class="btn btn-link btn-sm text-dark d-inline"
                                                        onclick="location.href='{{ route('download_chiavetta_files', [$folder->id, $construct_id]) }}'">
                                                        <i class="fa fa-download"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-link btn-sm text-danger d-inline"
                                                        onclick="location.href='{{ route('DeleteAllChiavettaFiles', [$folder->id, $construct_id]) }}'">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>

                                    <!--  Modal -->
                                    <x-reminder-email-model modelId="bell{{ $folder->id }}Modal"
                                        folderName="{{ $folder->folder_name }}"
                                        conId="{{ $construct_id }}" />
                                    <!-- End Modal -->
                                @endforeach

                                @if ($data['Files'] != null)
                                    @foreach ($data['Files'] as $legge10file_data)
                                        @include('construction.construction_status.legg10files')
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

            function legge10_file_upload_legg10files(id) {
                var spinner = $('#legge10_file_upload_legg10files_spinner_' + id)
                var form = $('#legge10_file_upload_legg10files_form_' + id)

                spinner.removeClass('d-none')
                form.addClass('d-none')
                form.submit();
            }
        </script>
    @endsection
</x-app-layout>
