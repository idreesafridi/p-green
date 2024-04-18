
$('#searchfield').keyup(function() {
    var searchText = $(this).val().toLowerCase();
    $('#dataTable tbody tr').each(function() {
    var rowData = $(this).find('td');
    var foundMatch = false;
    rowData.each(function() {
        if ($(this).text().toLowerCase().indexOf(searchText) !== -1) {
        foundMatch = true;
        return false;
        }
    });
    $(this).toggle(foundMatch);
    });
});

function goBack() {
    window.history.back();
  }


  $(document).ready(function() {
    $('#dataTable').DataTable({
        "order": [[0, "asc"]],  // Sort the first column in ascending order by default
        "paging": false,
        "searching": false,
        "info": false,
        "columnDefs": [
            { "orderable": false, "targets": [0, 1, 2, 3, 4, 5] },
                    ],
                    "language": {
                        "emptyTable": "Nessun dato disponibile"  // This will hide the "No data available in table" message
                    }
    }); 
});