function setStorage(key, value) {
    return sessionStorage.setItem(key, value)
}

function getStorage(key) {
    return sessionStorage.getItem(key)
}

function removeStorage(key) {
    sessionStorage.removeItem(key)
}

function getMatReports(key, value) {
    if (value == '') {
        removeStorage(key)
    }
    else {
        setStorage(key, value)
    }

    removeStorage('search_filter')
    setStorage('model', 'ConstructionMaterial')
    reportSearchQuery()
}

function search_filter(value, model) {
    if (value == '') {
        removeStorage('search_filter')
    }
    else {
        setStorage('search_filter', value)
        setStorage('model', model)
    }

    reportSearchQuery()
}

function reportSearchQuery() {
    // Create an empty array
    var arr = [];

    if (window.sessionStorage.length != 0) {
        // Get all keys from session storage
        var keys = Object.keys(sessionStorage);

        // Loop through the keys and push their values into the array
        for (var i = 0; i < keys.length; i++) {
            var key = keys[i];
            var value = getStorage(key);
            arr.push({ key: key, value: value });
        }
    }

    $.ajax({
        method: "post",
        url: reportSearchUrl,
        data: {
            'data': arr,
            "_token": token
        },
        success: function (response) {
            $('#response_data_catieri').html(response.result)
        }
    });

}



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