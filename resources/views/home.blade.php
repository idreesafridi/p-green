<x-app-layout pageTitle="">
    @section('styles')
    @endsection

    <div class="row">
        @if (auth()->user()->hasrole('admin') ||
                auth()->user()->hasrole('user'))
            {{-- <div class="col-md-2 hideInMobile">
                <x-sidebar />
            </div> --}}
        @endif
        <div class="{{ auth()->user()->hasrole('admin') ||auth()->user()->hasrole('user')? 'col-md-12': 'col-md-12' }}">
            <x-print-doc-template />

            <x-construction-home-nav />

            <div class="card border-0">
                <div class="card-body p-2">
                    <div class="spinner-grow text-success" role="status" id="spinner">
                        <span class="visually-hidden">Loading...</span>
                    </div>

                    <div id="response_data"></div>
                </div>
            </div>
        </div>
    </div>

    @section('scripts')
        @if (auth()->user()->hasrole('admin') ||
                auth()->user()->hasrole('user') ||
                auth()->user()->hasrole('technician')) 
            <script>
                var searchUrl = "{{ route('construction_search') }}";
            </script>

            <script src="{{ asset('assets/js/custom.js') }}"></script>

        @else
        
            <script>
                var searchUrl = "{{ route('construction_search_role') }}";
            </script>

            <script src="{{ asset('assets/js/custom.js') }}"></script>
        @endif

        <script>
            $(document).ready(function(){
                $('#CreateShipping').on('shown.bs.modal', function () {
                    $('#create_shipping_centri').select2({ 
                        dropdownParent: $('#CreateShipping'),
                        language: {
                            inputTooShort: function(args) {
                                return "Digita qui il nome del tuo cantiere per cercare...";
                            }
                        },
                        ajax: {
                            url: "{{ route('centri_search') }}",
                            method: "POST",
                            dataType: 'json',
                            delay: 250,
                            data: function(params) {
                                return {
                                    search: params.term,
                                    page: params.page || 1,
                                    "_token": token
                                };
                            },
                            processResults: function(data, params) {
                                params.page = params.page || 1;
                                return {
                                    results: data.items,
                                    pagination: {
                                        more: (params.page * 10) < data.total_count
                                    }
                                };
                            },
                            cache: true
                        },
                        minimumInputLength: 1
                    });
                });
            });

            function shippingForm(event) {
                event.preventDefault()

                // Serialize the form data
                var formData = $('#shippingForm').serialize();

                // Send an AJAX request to the server
                $.ajax({
                    type: 'POST',
                    url: '{{ route('addConShippingList') }}',
                    data: formData,
                    headers: {
                        'X-CSRF-TOKEN': token
                    },
                    success: function(data) {
                        showCantieriAlertMessage("Cantiere creato...", 'success');
                        $('#centriMaterialList').html('')
                        showStep1()
                    }
                });
            }

            function addCentrie() {
                var centryval = $('#create_shipping_centri').val()

                $.ajax({
                    url: "{{ route('addCentrie') }}",
                    method: "post",
                    data: {
                        'centryval': centryval,
                        "_token": '{{ csrf_token() }}'
                    },
                    dataType: "json",
                    success: function(response) {
                        showCantieriAlertMessage("Cantiere creato...", 'success');
                        $('#create_shipping_centri').val(null)
                        $('#centryListShipping').html(response.result)
                        $('#centriMaterialList').html(response.centriMaterialList)
                        $('#centriHeaderBadg').html(response.centriHeaderBadg)
                    }
                });
            }

            function getShippingList(id = null) {
              
                $.ajax({
                    url: "{{ route('centriList') }}",
                    dataType: "json",
                    data: {
                        'id': id
                    },
                    success: function(response) {
                        $('#create_shipping_centri').val(null)
                        $('#centryListShipping').html(response.result)
                        $('#centriMaterialList').html(response.centriMaterialList)
                        $('#centriHeaderBadg').html(response.centriHeaderBadg)
                    }
                });
            }

            function removeShippingCentri(id) {
                $.ajax({
                    url: "{{ route('destroyCentrie') }}/" + id,
                    dataType: "json",
                    success: function(response) {
                        showCantieriAlertMessage("Shipping deleted...", 'error');
                        $('#create_shipping_centri').val(null)
                        $('#centryListShipping').html(response.result)
                        $('#centriMaterialList').html(response.centriMaterialList)
                        $('#centriHeaderBadg').html(response.centriHeaderBadg)
                    }
                });
            }

            function checkMatLimit(qty, value, id) {
                qty = parseInt(qty);
                value = parseInt(value);
                var shipId = "#shipchange" + id;

                $(shipId).val(1);

                if (value > qty) {
                    $('#matlist' + id).val(qty);
                    showCantieriAlertMessage(value + " è maggiore di " + qty + "..", 'error');
                }
                 else if (value <= 0) {
                    $('#matlist' + id).val(qty)
                    showCantieriAlertMessage("il valore deve essere 1 o maggiore.", 'error');
                }
            }

            function checkMatLimitUpdating(qty, value, id, sentItem) {
                qty = parseInt(qty);
                value = parseInt(value);
                var shipId = "#shipchange" + id;
                $(shipId).val(1);

                if (value > qty) {
                    $('#matlist' + id).val(qty)
                    showCantieriAlertMessage(value + " is greater then the " + qty + "..", 'error');
                }
                 else if (Math.abs(value) > sentItem ) {
                    $('#matlist' + id).val(qty)
                    showCantieriAlertMessage("value must be not greater then total.", 'error');
                }
            }

            function matlistId(id) {
                var matlistid = $('#matlistid' + id);
                var shipId = "#shipchange" + id;
                var construction_shipping_id = "#construction_shipping_id" + id;
                var rem_qty = "#rem_qty" + id;

                const checkbox = matlistid[0]; // get the DOM element from the jQuery object

                if (checkbox.checked) {
                    $('#matlist' + id).removeAttr('disabled');
                    $(shipId).removeAttr('disabled');
                    $(construction_shipping_id).removeAttr('disabled');
                    $(rem_qty).removeAttr('disabled');
                    $(shipId).val(1);
                } else {
                    $('#matlist' + id).attr('disabled', true);
                    $(shipId).attr('disabled', true);
                    $(construction_shipping_id).attr('disabled', true);
                    $(rem_qty).attr('disabled', true);
                    $(shipId).val(0);
                }
            }

            function showStep2(id = null) {
                $('#step1').removeClass('show active')
                $('#step2').addClass('show active')

                $('#stepnav1').removeClass('show active')
                $('#stepnav2').addClass('show active')

                getShippingList(id)
            }

            function showStep1() {
                $('#step2').removeClass('show active')
                $('#step1').addClass('show active')

                $('#stepnav2').removeClass('show active')
                $('#stepnav1').addClass('show active')
            }

            function editMatCantri(id) {
                showStep2(id)
            }
        </script>
    @endsection
</x-app-layout>
