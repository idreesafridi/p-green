<script src="{{ asset('assets/js/jquery-3.6.1.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/js/share.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

<script src="https://cdn.datatables.net/1.13.2/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.2/js/dataTables.bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.4.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.4.0/js/responsive.bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/3.3.4/jquery.inputmask.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/intlTelInput.min.js"></script>

<script>
    var token = "{{ csrf_token() }}"; 

    $(document).ready(function(){
        $('#example').DataTable({
            paging: false,
            "language": {
                emptyTable: "Nessun dato disponibile nella tabella"
            },
        });
    });
 
    $('.select2').select2();
    $('#tecnician_id').select2();
    $('#model_list').select2();
    // $('#pianoSelect2').select2();

    $('.document-table').DataTable({
        "responsive": true,
        "order": [[0, "asc"]],
        "language": {
            emptyTable: "Nessun dato disponibile nella tabella"
        },
        "paging": false,
      
        "info": false,
        "searching": false,

                  
    });

    function showCantieriAlertMessage(msg, status) {
        toastr.options = {
            "closeButton": true,
            "positionClass": "toast-bottom-right",
            "progressBar": true,
            "debug": false,
            "newestOnTop": false,
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }

        if (status == 'success') {
            toastr.success(msg)
        }
        if (status == 'error') {
            toastr.error(msg)
        }
    }

    function showAlertMessage(data, message = null) {
        toastr.options = {
            "closeButton": true,
            "positionClass": "toast-bottom-right",
            "progressBar": true,
            "debug": false,
            "newestOnTop": false,
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }

        if (data == 'state') {
            toastr.success("Stato aggiornato...")
        }
        if (data == 'centeri') {
            toastr.success(message)
        }
        if (data == 'success') {
            var msg = "{{ Session::get('success') }}"

            toastr.success(msg)
        }
        if (data == 'error') {
            var msg = "{{ Session::get('error') }}"

            toastr.error(msg)
        }
        if (data == 'js') {
            toastr.error(message)
        }

        //alert("Status Update Successfully...");
        $("#send").prop("disabled", true)

    }

    @if (Session::has('success'))
        showAlertMessage('success')
    @endif

    @if (Session::has('error'))
        showAlertMessage('error')
    @endif

    const phoneInputField = document.querySelector(".phone");
    const phoneInput = window.intlTelInput(phoneInputField, {
        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
        separateDialCode: true,
        initialCountry: "IT",
    });

    $('.phone').inputmask('999 999 99 99', {
        placeholder: ""
    });

</script>

{{-- dynamic scripts for other pages --}}
@yield('scripts')
</body>

</html>