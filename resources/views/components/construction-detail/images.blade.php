<div class="card-head pt-4" id="image">
    <h6 class="heading fw-bold mb-0">Immagini Ante Opera</h6>
    <small>Premi sui tre puntini (
        <i class="fa fa-ellipsis-v"></i> ) per scaricare o eliminare un'immagine.
    </small>

    <form action="{{ route('construction_image_store', request()->route()->image) }}" method="post"
        enctype="multipart/form-data" id="upload_image">
        @csrf
        <div class="row mx-0 my-3 d-fex justify-content-end">
            <input type = "hidden" name="construction_id" value="{{ request()->route()->id }}">
            <input type="file" name="images[]" onchange="imgFileValidate(this.files.length)" id="inputfile"
                multiple="" accept="image/png, image/gif, image/jpeg, image/jpg" style="display: none;">
            <button type="button" style="cursor:pointer;" class="btn btn-outline-green col-12 col-md-3 col-lg-2"
                onclick="$('#inputfile').click()">CARICA IMMAGINE</button>
        </div>
    </form>

    <div class="spinner-border d-none" role="status" id="images_upload_spinner">
        <span class="visually-hidden">Loading...</span>
    </div>

    {{-- <input class="form-control me-2" placeholder="Cerca immagine"> --}}
</div>
<div class="card-body px-0 mt-3">
    <form action="{{ route('download_image_zip', request()->route()->image) }}" method="post">
        @csrf

        <input type = "hidden" name="construction_id" value="{{ request()->route()->id }}">
        <div class="demo-gallery">
            {{-- @dd($images_data->ConstructionImagesFolder); --}}
            <ul id="lightgallery" class="list-unstyled row justify-content-start">
                @foreach ($images_data->ConstructionImagesFolder as $image)
                    @if ($image->status == 1)
                        @php
                            // $url = asset('construction-assets/' . $image->construction_site_id . '/thumbnail/' . $image->path);
                            $url = asset('construction-assets/' . $image->construction_site_id . '/' . $image->path);
                            $thunmbnil = asset('construction-assets/' . $image->construction_site_id . '/thumbnail/' . $image->folder . '/' . $image->name);
                        @endphp
                        <li class="col-md-3 rounded" data-src="{{ $url }}"
                            data-sub-html="<h4>{{ $image->name }}</h4><p>{{ $image->user != null ? $image->user->name : '' }} - {{ $image->created_at }} </p>">
                            <input type="checkbox" value="{{ $image->id }}" name="image[]" class="img-check">
                            <a>
                                <img class="rounded shadow-sm gallery-img" src="{{ $thunmbnil }}">
                            </a>
                            <table>
                                <tbody>
                                    <tr>
                                        <td>
                                            <span
                                                class="mb-0 text-muted d-inline">{{ substr($image->name, 0, 8) . '...' }}</span>
                                        </td>
                                        <td>
                                            <div class="dropdown d-flex justify-content-end">
                                                <div class="dropdown-toggle img-dropdown-toggle p-1" type="button"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fa fa-ellipsis-v text-dark"></i>
                                                </div>
                                                <ul class="dropdown-menu">
                                                    <li class="dropdown-item">
                                                        <a href="{{ route('download_image', ['id' => $image->id]) }}">
                                                            <i class="fa fa-download me-3"></i>Scarica</a>
                                                    </li>
                                                    <li class="dropdown-item">
                                                        <a href="{{ route('destroy_image', ['id' => $image->id]) }}">
                                                            <i class="fa fa-trash me-3"></i>Elimina</a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <small class="d-flex justify-content-between">Caricato da:</small>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <small
                                                class="d-flex justify-content-between">{{ $image->user != null ? $image->user->name : '' }}</small>
                                        </td>
                                        <td class="text-end">
                                            <span
                                                class="text-muted">{{ date('y/m/d', strtotime($image->created_at)) }}</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
        <div class="btn-container d-flex justify-content-end d-none">
            <div class="pe-2" onclick="delete_files()">
                <div class="btn btn-outline-danger w-fit-content">
                    <i class="fa fa-trash me-2"></i>Elimina selezionati
                </div>
            </div>
            <div>
                <button type="submit" class="btn btn-green">
                    <i class="fa fa-download me-2"></i>Scarica selezionati
                </button>

            </div>
            <div class="ps-2" onclick="select_all_files()">
                <div class="btn btn-outline-success  w-fit-content" style="font-size: 14px; " id="selectAllButton">
                    <i class="fa fa-check me-2"></i>Seleziona tutto
                </div>
            </div>
        </div>
    </form>
</div>

@section('scripts')
    <!-- Gallery -->
    <script src="https://cdn.jsdelivr.net/picturefill/2.3.1/picturefill.min.js"></script>
    <script src="https://cdn.rawgit.com/sachinchoolur/lightgallery.js/master/dist/js/lightgallery.js"></script>
    <script src="https://cdn.rawgit.com/sachinchoolur/lg-pager.js/master/dist/lg-pager.js"></script>
    <script src="https://cdn.rawgit.com/sachinchoolur/lg-autoplay.js/master/dist/lg-autoplay.js"></script>
    <script src="https://cdn.rawgit.com/sachinchoolur/lg-fullscreen.js/master/dist/lg-fullscreen.js"></script>
    <script src="https://cdn.rawgit.com/sachinchoolur/lg-zoom.js/master/dist/lg-zoom.js"></script>
    <script src="https://cdn.rawgit.com/sachinchoolur/lg-hash.js/master/dist/lg-hash.js"></script>
    <script src="{{ asset('assets/gallery-assets/js/lg-rotate.js') }}"></script>
    <script src="{{ asset('assets/gallery-assets/js/lg-thumbnail.min.js') }}"></script>

    <script>
        lightGallery(document.getElementById('lightgallery'));

        $(document).ready(function() {
            $('.btn-container').addClass('d-none');
        })

        // show/hide download/delete buttons
        $('.img-check').click(function() {
            event.stopPropagation();

            var checked = $('.img-check:checkbox:checked');
            if (checked.length == 0) {
                $('.btn-container').addClass('d-none');
            } else if (checked.length >= 1) {
                $('.btn-container').removeClass('d-none');
            }
        });

        // disabling the jquery when click on anywhere esle except images so gallery shouldn't be open
        $('.demo-gallery table').click(function() {
            event.stopPropagation();
        });

        function imgFileValidate(files) {

            var spinner = $('#images_upload_spinner')
            var form = $('#upload_image')

            spinner.removeClass('d-none')
            form.addClass('d-none')

            var filesCount = files
            if (filesCount > 100) {
                alert("You can select only 100 images, you have selected: " + filesCount)
            }
            if (filesCount <= 100 && filesCount > 0) {
                form.submit()
            }
        }

        function delete_files() {
            var array_value = get_images()

            $.ajax({
                method: "post",
                url: "{{ route('destroy_image_ajax') }}",
                data: {
                    'data': array_value,
                    "_token": token
                },
                success: function(response) {

                    window.location.reload(true);
                }
            });
        }

        function get_images() {
            var array_value = [];

            // push checkbox value into type
            $('.img-check').each(function() {
                if ($(this).prop("checked") == true) {
                    array_value.push($(this).val());
                }
            })

            // make unique array for type
            return array_value.filter(onlyUnique);
        }

        // to make array unique
        function onlyUnique(value, index, self) {
            return self.indexOf(value) === index;
        }

        function select_all_files() {
            var checkboxes = document.querySelectorAll('.img-check');

            var allChecked = Array.from(checkboxes).every(function(checkbox) {
                return checkbox.checked;
            });

            checkboxes.forEach(function(checkbox) {
                checkbox.checked = !allChecked;
            });
        }
    </script>
@endsection
