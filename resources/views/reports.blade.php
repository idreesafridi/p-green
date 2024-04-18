<x-app-layout pageTitle="All reports">
    @section('styles')
    @endsection

    <div class="container"> 
        <div class="row">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form action="{{route('generateReport')}}" method="post" id="reportForm" onsubmit="reportForm(event)">
                            @csrf
                            <div class="row">
                                <div class="col-md-5">
                                    <div class="input-group" id="model_list_span">
                                        @php
                                            $conArr = ['Property Data', 'Documents And Contact', 'Construction Materials', 'Construction Site Setting', 'Computo', 'Enea Balance', 'Legge 10', 'Pre Analysis', 'PrNoti', 'RegPrac', 'Relief', 'SAL', 'Technician', 'Work Close', 'Work Started'];
                                        @endphp
                                        <select name="model_list" id="model_list" class="form-control" onchange="GetModelFields(this.value)">
                                            <option selected disabled value="">Seleziona tabella</option>
                                            
                                                <option value="Property Data">Dati proprietà</option>
                                                <option value="Documents And Contact">Documenti e contatti</option>
                                                <option value="Construction Materials">Materiali di costruzione</option>
                                                <option value="Construction Site Setting">Impostazione del cantiere</option>
                                                <option value="Computo">Computo</option>
                                                <option value="Enea Balance">Enea Balance</option>
                                                <option value="Legge 10">Legge 10</option>
                                                <option value="Pre Analysis">Pre Analysis</option>
                                                <option value="PrNoti">PrNoti</option>
                                                <option value="RegPrac">RegPrac</option>
                                                <option value="Relief">Relief</option>
                                                <option value="SAL">SAL</option>
                                                <option value="Technician">Tecnico</option>
                                                <option value="Work Close">Lavora vicino</option>
                                                <option value="Work Started">Lavoro iniziato</option>
                                                <option value="Construction Job Detail">Dettagli Lavori</option>
                                                <option value="Contratto 110">Contratto 110</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-md-12">
                                    <div id="response_model_column"></div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="card shadow-sm">
                    <div class="card-body userList-page-table p-4 table-responsive">
                        <div id="report_filters"></div>
                        <div id="response_data_catieri">Seleziona prima i filtri</div>
                        <div class="spinner-grow text-success d-none mt-2" role="status" id="spinner">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @section('scripts')
    <script>
        sessionStorage.clear()
        
        function GetModelFields(value) {
            $.ajax({
                'method':'post',
                'url':"{{route('get_model_column')}}",
                dataType: 'json',
                data: {
                    'data': value,
                    "_token": token
                },
                success: function (response) {
                    $('#response_model_column').html(response.result)
                    $('#report_filters').html('')
                    $('#response_data_catieri').html('Seleziona prima i filtri')
                }
            })
        }

        function getJobReports(columnName, value) {
            $('#response_data_catieri').html('');
            $('#spinner').removeClass('d-none');
            $.ajax({
                'method':'post',
                'url':"{{route('get_job_reports')}}",
                dataType: 'json',
                data: {
                    'columnName': columnName,
                    'id': value,
                    "_token": token
                },
                success: function (response) {
                    $('#spinner').addClass('d-none');
                    $('#response_data_catieri').html(response.result);
                }
            })
        }

        function reportForm(e) {
            e.preventDefault()

            // Serialize the form data
            var formData = $('#reportForm').serialize();

            console.log(formData);
            $('#response_data_catieri').html('');
            $('#spinner').removeClass('d-none');
            $.ajax({
                'method':'post',
                'url':"{{route('generateReport')}}",
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': token
                },
                success: function (response) {
                    $('#spinner').addClass('d-none');
                    $('#response_data_catieri').html(response.result)
                    $('#report_filters').html(response.filters)
                }
            })
        }

        function printPageArea() {
            // var printContent = document.getElementById('response_data_catieri').innerHTML;
            // var originalContent = document.body.innerHTML;
            // document.body.innerHTML = printContent;
            // window.print();
            // document.body.innerHTML = originalContent;

            var prtContent = document.getElementById("response_data_catieri");
            var WinPrint = window.open('', '', 'left=0,top=0,width=800,height=900,toolbar=0,scrollbars=0,status=0');
            WinPrint.document.write(prtContent.innerHTML);
            WinPrint.document.close();
            WinPrint.focus();
            WinPrint.print();
        }

        var reportSearchUrl = "{{ route('reportsSearch') }}";
    </script>

    <script src="{{ asset('assets/js/reports.js') }}"></script>
    @endsection
</x-app-layout>
