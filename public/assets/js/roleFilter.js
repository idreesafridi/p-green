sessionStorage.clear()

function setStorage(key, value) {
    return sessionStorage.setItem(key, value)
}

function getStorage(key) {
    return sessionStorage.getItem(key)
}

function removeStorage(key) {
    sessionStorage.removeItem(key)
}

$(document).ready(function () {
    $('#stepnav2').click(function () {
        $('.green-class').css('background-color', '#3cb371');
    });

    var page = getStorage('pagename')

    if (page == null || page == 'Active') {
        setStorage('pagename', 'Active')
        $('#nav_link_Active').addClass('active')
    } else {
        $('#nav_link_' + page).addClass('active')
    }

    sessionStorageData();
});

function store_nav(pagename) {
    var page = getStorage('pagename')

    if (page == pagename) {
        $('#nav_link_' + pagename).addClass('active')
    }
    else {
        $('#nav_link_' + page).removeClass('active')
        setStorage('pagename', pagename)
        $('#nav_link_' + pagename).addClass('active')
    }

    sessionStorageData();
}

function search_keyword(input) {
    if (input.length === 0) {
        removeStorage('search_keyword')
    } else {
        setStorage('search_keyword', input)
    }

    sessionStorageData();
}

function sessionStorageData(page = 1) {
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

    arr.forEach((element) => {
        var sessionKey = element['key'];

        setFilters(sessionKey)
    });

    search_query(arr, page)
}
function setFilters(sessionkey) {

    var key = getStorage(sessionkey)

    if (sessionkey == 'search_keyword') {
        $('#search_keyword').val(key)
    } else {
        var key_arr = key.split(',')
        var array_value = []

        $('.' + sessionkey).each(function () {
            array_value.push($(this).val());
        })

        const keyMatchValues = key_arr.filter(element => array_value.includes(element));

        $.each(keyMatchValues, function (i, val) {
            var new_val = val.replaceAll(' ', '_').toLowerCase();
            var key_id = sessionkey + '_' + new_val

            $("#" + key_id).prop('checked', true);

        });
    }

}
function consPage(id) {
    // Get the current URL
    const url = new URL(id);

    // Parse the query parameters
    const searchParams = new URLSearchParams(url.search);

    // Get the value of the 'page' parameter
    const pageNum = searchParams.get('page');

    var page = 'page=' + pageNum

    sessionStorageData(page)
}

// Search function for application list
// function search_query(page) {
//     $('#spinner').show()
//     $('#response_data').hide()
//
//     var page = getStorage('pagename')
//     var keyword = getStorage('search_keyword')
//
//     $.ajax({
//         method: "post",
//         url: searchUrl,
//         data: {
//             'pagename': page,
//             'keyword': keyword,
//             "_token": token
//         },
//         success: function (response) {
//
//             $('#spinner').hide()
//             $('#response_data').show()
//             $('#response_data').html(response.result)
//
//             $('#active_nav').html(response.count.active)
//             $('#internal_nav').html(response.count.internal)
//             $('#external_nav').html(response.count.external)
//             $('#condominio_nav').html(response.count.condominio)
//             $('#50_nav').html(response.count.c50)
//             $('#65_nav').html(response.count.c65)
//             $('#90_nav').html(response.count.c90)
//             $('#archived_nav').html(response.count.archived)
//             $('#close_nav').html(response.count.close)
//         }
//     });
// }
function search_query(arr = null, page) {
    $('#spinner').show()
    $('#response_data').hide()

    $.ajax({
        method: "post",
        url: searchUrl + '?' + page,
        data: {
            'data': arr,
            "_token": token
        },
        success: function (response) {

            $('#spinner').hide()
            $('#response_data').show()
            $('#response_data').html(response.result)

            $('#active_nav').html(response.count.active)
            $('#internal_nav').html(response.count.internal)
            $('#external_nav').html(response.count.external)
            $('#condominio_nav').html(response.count.condominio)
            $('#50_nav').html(response.count.c50)
            $('#65_nav').html(response.count.c65)
            $('#90_nav').html(response.count.c90)
            $('#archived_nav').html(response.count.archived)
            $('#close_nav').html(response.count.close)
        }
    });
}
