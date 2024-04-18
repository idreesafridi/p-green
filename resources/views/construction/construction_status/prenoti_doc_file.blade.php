<x-app-layout :pageTitle="$prenoti_doc_file->folder_name">
    @section('styles')
        <style>
            .input-group-addon {
                position: absolute !important;
                right: 0 !important;
                top: 0 !important;
                bottom: 0 !important;
                display: flex !important;
                align-items: center !important;
                padding: 0 10px !important;
                color: #495057 !important;
                border: 1px solid #ced4da;
                border-left: 0;
                border-radius: 0.25rem;
                background-color: #e9ecef;
            }

            .unit {
                margin-right: 5px;
            }
        </style>
    @endsection
    @php

        $backId = Session::get('backid');

    @endphp
    <x-construction-detail-head :consId="$prenoti_doc_file->construction_site_id" />
    <x-construction-detail-nav :constructionid="$prenoti_doc_file->construction_site_id" />

    <div class="tab-content">
        <div class="card p-4 border-0 site-detail-card">
            <div class="card-head document-page-header py-4">
                <div class="d-flex align-items-center">
                    @if ($backId !== null)
                        <a href="{{ Route('show_preNoti_doc', $backId) }}">
                        @else
                            <a href="{{ URL::previous() }}">
                    @endif

                    <i class="fa fa-arrow-left me-3 back"></i></a>
                    <h6 class="heading fw-bold mb-0">{{ $prenoti_doc_file->folder_name }}</h6>
                </div>
                <div class="row">
                    <div class="col-12 mt-4 col-lg-4 d-flex align-items-center">
                        <input type="text" class="form-control head-input" placeholder="Cerca tra i documenti"
                            id="searchfield">
                    </div>
                    <div class="col-12 col-lg-8 mt-4 d-flex  align-items-center">
                        <div>
                            <div style="float: right;">
                                <form action="{{ route('pri_upload_files') }}" method="POST"
                                    enctype="multipart/form-data" id="pri_upload_files">
                                    @csrf
                                    <input type="text" name="prenoti_doc_id" value="{{ $prenoti_doc_file->id }}"
                                        hidden>
                                    <input type="text" name="prenoti_doc_f_name"
                                        value="{{ $prenoti_doc_file->folder_name }}" hidden>
                                    <input type="file" autocomplete="off" name="files[]" accept=".pdf"
                                        id="input" class="form-control d-inline"
                                        style="color:grey !important; width:70%" multiple onchange="submit_form()">
                                    <span class='d-inline'>
                                        {{-- <button class="btn btn-outline-secondary" type="submit">Submit
                                        </button> --}}
                                    </span>
                                </form>

                                <!-- btn text : border disabled -->
                            </div>

                            <nav class="d-inline-block filterList me-2">
                                <div class="nav nav-tabs border-bottom-0 d-none" role="tablist"
                                    id="pri_upload_files_spinner">
                                    <div class="spinner-border" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    {{-- <a id="filter1" class="active btn btn-light text-black btn-sm me-2 mb-2"
                                            type="button" role="tab" data-bs-toggle="tab"
                                            href="#filterTab1">Tutti</a> --}}

                                    {{-- <a id="filter2" class="btn btn-light text-black btn-sm 50-filter me-2 mb-2"
                                            type="button" role="tab" data-bs-toggle="tab" href="#filterTab2">50</a>

                                        <a id="filter3" type="button" class="btn btn-danger me-2 mb-2" role="tab"
                                            data-bs-toggle="tab" href="#filterTab3">Essenziali</a>

                                        <a id="filter4" type="button" class="btn btn-info me-2 mb-2" role="tab"
                                            data-bs-toggle="tab" href="#filterTab4">Chiavetta</a> --}}
                                </div>
                            </nav>


                        </div>

                        <button
                            class="btn btn-green {{ $prenoti_doc_file->folder_name == 'Contratto Di Subappalto Impresa' ? '' : 'd-none' }}"
                            type="button" data-bs-toggle="modal" data-bs-target="#exampleModal"
                            onclick="getBusiness()">
                            Crea contratto
                        </button>

                        {{-- <div class="text-end">
                            <button type="" class="btn btn-green">
                                <i class="fa fa-download me-2"></i>
                                Scarica tutto
                            </button>
                        </div> --}}
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
                                    <th scope="col" class="text-white"></th>
                                </tr>
                            </thead>
                            <tbody>

                                @if (count($prenoti_doc_file->PrNotDocFile) > 0)

                                    @php

                                        $relief_doc_files = $prenoti_doc_file->PrNotDocFile;

                                        $updated_files = $relief_doc_files->filter(function ($prenoti_doc_file) {
                                            return $prenoti_doc_file->updated_on !== null;
                                        });

                                        $not_updated_files = $relief_doc_files->filter(function ($prenoti_doc_file) {
                                            return $prenoti_doc_file->updated_on === null;
                                        });

                                        $filtered_files = $updated_files->concat($not_updated_files);

                                        $unique_files = collect([]);

                                        $grouped_files = $filtered_files->groupBy(function ($item) {
                                            return strtolower($item->file_name);
                                        });

                                        foreach ($grouped_files as $group) {
                                            $unique_files->push($group->first());
                                        }

                                    @endphp

                                 
                                    @foreach ($unique_files->where('state', 1) as $prnotdocfile)
                                        @if ($prnotdocfile->folder_name != null || $prnotdocfile->file_name)
                                            @php
                                                $allow = $prnotdocfile->allow;
                                                $allowedRoles = explode(',', $allow);
                                            @endphp
                                            @hasrole('admin')
                                                @if (in_array('admin', $allowedRoles))
                                                    @include('construction.construction_status.prnotdocfile')
                                                @endif
                                            @endhasrole
                                            @hasrole('technician')
                                                @if (in_array('technician', $allowedRoles))
                                                    @include('construction.construction_status.prnotdocfile')
                                                @endif
                                            @endhasrole
                                            @hasrole('businessconsultant')
                                                @if (in_array('businessconsultant', $allowedRoles))
                                                    @include('construction.construction_status.prnotdocfile')
                                                @endif
                                            @endhasrole
                                            @hasrole('business')
                                                @include('construction.construction_status.prnotdocfile')
                                            @endhasrole
                                            @hasrole('photovoltaic')
                                                @if (in_array('photovoltaic', $allowedRoles))
                                                    @include('construction.construction_status.prnotdocfile')
                                                @endif
                                            @endhasrole
                                            @hasrole('user')
                                                @if (in_array('user', $allowedRoles))
                                                    @include('construction.construction_status.prnotdocfile')
                                                @endif
                                            @endhasrole
                                        @endif
                                    @endforeach
                                @endif

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Modal  for Create cont-->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true"
        data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Crea contratto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action ="{{ route('print_construction_stampa_material') }}" id="create_contratto_form"
                    method="POST">
                    @csrf
                    <div class="modal-body">

                        <!-- Modal body content goes here -->
                        <div class="create_contratto">
                            <div class="row">
                                <div class="col-12 col-md-4 ">
                                    <label for="materialDropdown" class="form-label">Impresa subappaltante</label>
                                    <select class="form-select form-control" id="materialDropdown">
                                        <!-- Options will be dynamically added here -->
                                    </select>
                                </div>
                            </div>
                            <input type="hidden" id="infissi" name="business_id" value="">
                            <input type="hidden" id="totali" name="totali" value="">
                            <input type="hidden" id="contractionId" name="contractionId" value="">
                            <input type="hidden" id="SumOfpricePerUnit" name="SumOfpricePerUnit[]" value="">
                            <input type="hidden" id="uploading" name="uploading" value="">
                        </div>
                        <div>
                            <button type="button" class="btn btn-green my-4" id="addQuestionButton"><i
                                    class="fa fa-plus"></i> Nuova lavorazione</button>
                        </div>
                        <div class="text-end fs-5 fw-bold mt-5">
                            Totale: <button type= "button" class="btn btn-green fw-bold" id="SumOfAllpricePerUnit"
                                style="height:50px"> &euro; 0.00</button>
                        </div>
                        <div id="error-message" class="text-danger text-end  mt-2">
                      
                        </div>
                    </div>


                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-green" onclick=createContract(this)><i
                            class="fa fa-upload me-2"></i>Carica</button>
                        <button type="button" class="btn btn-outline-green" data-bs-dismiss="modal"><i
                                class="fa fa-close me-2"></i>Chuidi</button>
                        <button type="button" class="btn btn-green" id="createContractButton" onclick = "createContract()"><i
                                class="fa fa-file me-2"></i> Crea contratto</button>

                    </div>
                </form>
            </div>
        </div>
    </div>

    @section('scripts')
        <script>




            $('#materialDropdown').change(function() {
                // Get the selected option value
                var selectedValue = $(this).val();

                // Check if there are any matching hidden input fields
                if ($('input[type="hidden"][id^="businessId_"]').length > 0) {
                    // Call a function to check the hidden input fields
                    checkHiddenInputs(selectedValue);
                }
            });

            function checkHiddenInputs(selectedValue) {

                var MaterialsId = $('select[id^="materialListId_"]').map(function() {
                    return $(this).val();
                }).get();

                $.ajax({
                    type: "POST",
                    url: "{{ route('business_check') }}",
                    data: {
                        'id': selectedValue,
                        'MaterialsId': MaterialsId
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        response.materialListId.forEach(function(materialId, index) {
                            $('select[name="material_list_id[]"][id^="materialListId_"]').each(function() {
                                if ($(this).val() == materialId) {
                                    var id = $(this).attr('id');
                                    var numericId = id.split('_')[1];
                                    var prezzoId = 'PrezzoPerUnita_' + numericId;
                                    var prizo = $('#' + prezzoId); // Corrected variable name
                                    var price = response.materialPrice[index];
                                    $('#' + prezzoId).val(price);


                                    calculateTotalPricebyField(prizo[0]);
                                }
                            });
                        });

                        if (response.materialListId.length === 0) {
                            $('select[name="material_list_id[]"][id^="materialListId_"]').each(function() {
                                var id = $(this).attr('id');
                                var numericId = id.split('_')[1];
                                var prezzoId = 'PrezzoPerUnita_' + numericId;
                                $('#' + prezzoId).val('');
                                var SumOfpricePerUnit = 'SumOfpricePerUnit_' + numericId;
                                $('#' + SumOfpricePerUnit).text('€ 0.00');
                                var quantity = 'quantity_' + numericId;
                                // $('#' + quantity).val('');
                            });

                            $('#SumOfAllpricePerUnit').text('€ 0.00');
                        }
                    }

                });






                // Get all the hidden input fields
                var hiddenInputs = $('input[type="hidden"][id^="businessId_"]');


                // Select only hidden input fields with IDs starting with 'businessId_'
                $('input[type="hidden"][id^="businessId_"]').each(function() {
                    // Compare the value with the selected option value
                    if ($(this).val() == selectedValue) {
                        // Do whatever you want here when a match is found
                        console.log('Match found for ' + selectedValue + ' in ' + $(this).attr('id'));
                    }
                });
            }

            function createContract(index = null) {
            
                if (index != null) {
                    $('#uploading').val('true');
                }
                var priceArr = [];

                $('[id^="SumOfpricePerUnit_"]').each(function() {
                    var value = parseFloat($(this).text().replace('€ ', ''));
                    if (!isNaN(value)) {
                        value = value.toFixed(2); // Example: Round to 2 decimal places
                        priceArr.push(value);
                    }
                });


                $('#SumOfpricePerUnit').val(priceArr);


                var Impresa = $('#materialDropdown').val();
                var business_id = Impresa;
                document.getElementById('infissi').value = business_id;
                var totalValue = parseFloat($('#SumOfAllpricePerUnit').text().replace('€ ', ''));

                document.getElementById('totali').value = totalValue;
                
                if (totalValue > 0) 
                {
                    var materialDropdown = document.getElementById('materialDropdown').value;
                    if (materialDropdown != "Seleziona Impresa"){
                           document.getElementById('create_contratto_form').submit();
                    }
                    else{ 
                        var businessEror =  "seleziona gentilmente un'attività";
                        $('#error-message').html(businessEror);
                    }
                 
                } else {
                    var message =  "Seleziona almeno un record."
                    $('#error-message').html(message);

                }
            }
           
            var constructionSiteId = "{{ $prenoti_doc_file->construction_site_id }}";



            let questionCounter = 0;


            function getBusiness() {
                $.ajax({
                    type: "GET",
                    url: "{{ route('business_users') }}",
                    success: function(response) {
                        // console.log(response);
                        var htmlContent =
                        '<option disabled selected>Seleziona Impresa</option>'; // Default disabled option
                        $.each(response.htmlContent, function(index, business) {
                            // console.log(business);
                            htmlContent += '<option value="' + business.id + '">' + business.user.name +
                                '</option>';
                        });
                        $('#materialDropdown').html(htmlContent);
                    }
                });
            }



            function getMatList(selectElement) {

                var CounterId = selectElement.id.split('_')[1];


                var material_type_id = $(selectElement).val();




                $.ajax({
                    type: "POST",
                    url: "{{ route('get_mat_list_ajax_for_report') }}",
                    data: {
                        'id': material_type_id,
                        'construction_site_id': constructionSiteId
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        $('#get_mat_list_ajax_' + CounterId).html(response);
                        var inputId = 'quantity_' + CounterId;
                        var inputElement = $('#get_mat_list_ajax_' + CounterId).find('input[name="quantity[]"]');
                        if (inputElement.length && !inputElement.attr('id')) {
                            inputElement.attr('id', inputId);
                        }

                        var selectId = 'materialListId_' + CounterId;
                        var selectElement = $('#get_mat_list_ajax_' + CounterId).find(
                            'select[name="material_list_id[]"]');
                        if (selectElement.length && !selectElement.attr('id')) {
                            selectElement.attr('id', selectId);
                        }
                    },
                    error: function(xhr, status, error) {
                        // console.error(error); // Log any errors to the console
                        // Optionally, display an error message to the user
                    }
                });
            }

            function calculateTotalPrice(selectElement) {
                var inputId = selectElement.id;
                var Counterprice = inputId.split('_')[1];

                var price = parseFloat($('#PrezzoPerUnita_' + Counterprice).val());
                var multiply = parseFloat(selectElement.value);

                if (!isNaN(price) && !isNaN(multiply)) {
                    var totalPrice = price * multiply;
                    $('#SumOfpricePerUnit_' + Counterprice).text('€ ' + totalPrice.toFixed(2));
                    // Do whatever you need with the total price
                    var totalSum = 0;
                    $('[id^="SumOfpricePerUnit_"]').each(function() {
                        var value = parseFloat($(this).text().replace('€ ', ''));
                        if (!isNaN(value)) {
                            totalSum += value;
                        }
                    });

                    // Update the button text with the total sum
                    $('#SumOfAllpricePerUnit').text('€ ' + totalSum.toFixed(2));
                }
            }


            $(document).ready(function() {


                $('#contractionId').val(constructionSiteId);
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

            function submit_form() {
                $('#pri_upload_files_spinner').removeClass('d-none')
                $('#pri_upload_files').addClass('d-none')
                document.getElementById("pri_upload_files").submit();
            }

            function replace_file_form_submit(id) {
                var form = $('#replace_file_' + id)
                var spinner = $('#replace_file_spinner_' + id)

                spinner.removeClass('d-none')
                form.addClass('d-none')
                form.submit()
            }

            $('#addQuestionButton').click(function() {
                addQuestion();
            });



            function addQuestion() {
                questionCounter++;

                let newQuestionGroup = `
        <div class="position-relative question-group"> 
            <hr>
            <i class="fa fa-close position-absolute fs-5 remove-question" style="top: 10px; right: 10px; cursor: pointer;" id="removeQuestion_${questionCounter}"></i>
            <div class="row py-2">
                <div class="col-12 col-lg-4">
                    <label for="Tipo" class="form-label">Tipo</label>
                  
                    <select class="form-select form-control tipoSelect" id="Tipo_${questionCounter}" onchange="getMatList(this)"></select>

                </div>
                <div class="col-12 col-lg-4">
                    <label for="Descrizionelavorazione" class="form-label">Descrizione lavorazione</label>
                    <input type="text" placeholder="Descrizione lavorazione" id="Descrizionelavorazione_${questionCounter}" name="Descrizionelavorazione[]" class="form-control">
                </div>
                <div class="col-6 col-lg-2">
                    <label for="Prezzo_per_unita" class="form-label">Prezzo per unita</label>
                    <input type="text" placeholder="Prezzo" id="PrezzoPerUnita_${questionCounter}" onkeyup="calculateTotalPricebyField(this)" name="Prezzo_per_unita[]" class="form-control">
                </div>
                <div class="col-6 col-lg-2 d-flex align-items-end justify-content-end">
                    <button type="button" class="btn btn-green fw-bold" style="height:50px" class="SumOfpricePerUnit" id="SumOfpricePerUnit_${questionCounter}"> 	&euro; 0.00</button>
                </div>
            </div>
            <div class="row">
                <div id="get_mat_list_ajax_${questionCounter}"></div>
                <input type="hidden"  id="businessId_${questionCounter}" >
            </div>
        </div>
    `;

                $('.create_contratto').append(newQuestionGroup);

                // Fetch and populate data for the new question group
                populateFields(questionCounter);
            }

            function populateFields(counter) {
                // Make AJAX call to fetch data for the fields

                $.ajax({
                    type: "GET",
                    url: "{{ route('fetch_data_for_create_contract', ['id' => '']) }}/" + constructionSiteId,
                    success: function(response) {
                        // Populate Tipo select options
                        var tipoSelect = $(`#Tipo_${counter}`);

                        // Clear existing options
                        tipoSelect.empty();


                        // Add default option "Seleziona tipo materiale"
                        tipoSelect.append($('<option>', {
                            value: '',
                            text: 'Seleziona tipo materiale',
                            disabled: true,
                            selected: true
                        }));

                        // Iterate through $option array
                        $.each(response.option, function(heading, values) {
                            // Append heading as an <optgroup>
                            var optgroup = $('<optgroup>', {
                                label: heading
                            });

                            // Iterate through values and append them as <option> inside the optgroup
                            $.each(values, function(index, value) {

                                // $.each(value, function(index, singelvalue) {
                                optgroup.append($('<option>', {
                                    value: value.id,
                                    text: value.name

                                }));
                                // });
                            });

                            // Append the optgroup to tipoSelect
                            tipoSelect.append(optgroup);
                        });
                    }
                });
            }

            function calculateTotalPricebyField(selectElement) {
                var MatListIdPrice = selectElement.id.split('_')[1];
                var price = parseFloat($('#quantity_' + MatListIdPrice).val());
                var multiply = parseFloat(selectElement.value);

                if (isNaN(multiply) || multiply === 0) {
                    multiply = 0; // default to 1 if multiply is null or 0
                }

                if (!isNaN(price) && !isNaN(multiply)) {
                    var totalPrice = price * multiply;
                    $('#SumOfpricePerUnit_' + MatListIdPrice).text('€ ' + totalPrice.toFixed(2));

                    // Update the total sum
                    updateTotalSum();
                }
            }

            function updateTotalSum() {
                var totalSum = 0;
                $('[id^="SumOfpricePerUnit_"]').each(function() {
                    var value = parseFloat($(this).text().replace('€ ', ''));
                    if (!isNaN(value)) {
                        totalSum += value;
                    }
                });

                // Update the button text with the total sum
                $('#SumOfAllpricePerUnit').text('€ ' + totalSum.toFixed(2));
            }

            function getMatListinfo(selectElement) {

                var MatListId = selectElement.id.split('_')[1];

                //    console.log(MatListId);

                var Impresa = $('#materialDropdown').val();

                var materialId = selectElement.value; // Get the selected material ID

                // console.log(materialId);
                // Make AJAX call to retrieve related data
                // alert(Impresa);
                $.ajax({
                    type: "POST",
                    url: "{{ route('get_mat_list_related_data') }}",
                    data: {
                        'material_list_id': materialId,
                        'construction_site_id': constructionSiteId,
                        'businessId': Impresa
                    },
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        // Set the quantity value to the input field
                        if (response.note !== null && response.note !== "") {
                            $('#Descrizionelavorazione_' + MatListId).val(response.note);
                        }


                        if (response.businessId !== null) {
                            $('#businessId_' + MatListId).val(response.businessId);
                        }


                        // Get the current text of #SumOfAllpricePerUnit
                        var currentText = $('#SumOfAllpricePerUnit').text();
                        // Extract numeric value from currentText
                        var currentPrice = parseFloat(currentText.replace('€ ', ''));

                        if (response.price !== null && response.price !== "") {
                            // Set the response price to Prezzo_per_unita_' + questionCounter
                            $('#PrezzoPerUnita_' + MatListId).val(response.price);
                            $('#SumOfpricePerUnit_' + MatListId).text('€ ' + response.price);
                            // Add response.price to currentPrice

                            $('#quantity_' + MatListId).val(1);
                            var newPrice = currentPrice + parseFloat(response.price);
                        } else {
                            var Prezzoperunita = $('#PrezzoPerUnita_' + MatListId).val();

                            if (Prezzoperunita.length === 0) {
                                $('#PrezzoPerUnita_' + MatListId).val('0');
                            }
                            var existingPrice = parseFloat($('#SumOfpricePerUnit_' + MatListId).text().replace('€ ',
                                ''));
                            // Subtract the existing value of SumOfpricePerUnit_' + questionCounter from currentPrice
                            var newPrice = currentPrice - existingPrice;
                            $('#SumOfpricePerUnit_' + MatListId).text('€ 0.00' + toFixed(2));
                        }

                        // Update #SumOfAllpricePerUnit with the combined value
                        $('#SumOfAllpricePerUnit').text('€ ' + newPrice.toFixed(2));
                    },

                    error: function(xhr, status, error) {
                        // Log any errors to the console
                        // console.error(error);
                        // Optionally, display an error message to the user
                    }
                });
            }


            $('.modal-body').on('click', '.remove-question', function() {
                var id = $(this).attr('id'); // Corrected to get the id attribute value
                var questionCounter = id.split('_')[1]; // Extract the question counter from the id
                var pricePerUnit = parseFloat($('#SumOfpricePerUnit_' + questionCounter).text().replace('€ ', ''));
                var totalSum = parseFloat($('#SumOfAllpricePerUnit').text().replace('€ ', ''));

                // Subtract the pricePerUnit from the totalSum
                totalSum -= pricePerUnit;

                // Update the totalSum button text
                $('#SumOfAllpricePerUnit').text('€ ' + totalSum.toFixed(2));

                // Optionally, you can remove the question group as well
                $(this).closest('.question-group').remove();
            });
        </script>
    @endsection
</x-app-layout>
