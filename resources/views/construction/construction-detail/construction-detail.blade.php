<x-app-layout :pageTitle="request()->route()->pagename">
    @section('styles')
    <link href="{{ asset('assets/css/lightgallery.css') }}" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://code.jquery.com/ui/1.10.4/themes/ui-lightness/jquery-ui.css">
    @endsection

    <x-construction-detail-head :consId="$construct_id"  />
    <x-construction-detail-nav  :constructionid="$construct_id"/>

    <div class="tab-content">
        <div class="card p-4 border-0 site-detail-card">
            @if (request()->route()->pagename == 'Cliente')
            <x-construction-detail.customer-data :cusdata="$data['data']" />
            @elseif (request()->route()->pagename == 'Cantiere')
            <x-construction-detail.building-site :builddata="$data" />
            @elseif (request()->route()->pagename == 'Materiali')
            <x-construction-detail.materials :matData="$data['data']" />
            @elseif (request()->route()->pagename == 'Assistenze')
            <x-construction-detail.assistances :materialAssist="$data['data']" />
            @elseif (request()->route()->pagename == 'Documenti')
            <x-construction-detail.papers :relief="$data" />
            @elseif (request()->route()->pagename == 'Stato')
            <x-construction-detail.state :conststatus="$data" />
            @elseif (request()->route()->pagename == 'Note')
            <x-construction-detail.note :cons="$construct_id" />
            @elseif (request()->route()->pagename == 'Immagini')
            <x-construction-detail.images :imagedata="$data" />
            @else
            Check URL again
            @endif
        </div>
    </div>

    @section('scripts')

    
    <script>
        $(document).ready(function() {
            $(document).ready(function() {
    var autocompleteUrl = "{{ route('users.autocomplete') }}";
    var selectedEmails = [];

    $('#email').on('keyup', function() {
        var query = $(this).val().trim(); // Get the current value of the input field
        if (query !== '') {
            fetchSuggestions(query); // Fetch suggestions based on the current query
        } else {
            hideSuggestions(); // Hide suggestions if the query is empty
        }
    });

    // Click event for selecting a suggestion
    $(document).on('click', '.custom-suggestion', function() {
        var suggestion = $(this).text();
        $('#email').val(suggestion); // Set the input field value to the clicked suggestion
        selectedEmails.push(suggestion); // Add the selected email to the array
        hideSuggestions(); // Hide suggestions after selecting a suggestion
    });

    // Function to fetch suggestions from the server
    function fetchSuggestions(query) {
        $.ajax({
            url: autocompleteUrl,
            method: 'GET',
            dataType: 'json',
            data: { q: query },
            success: function(data) {
                updateSuggestions(data);
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }

    // Function to update the suggestion dropdown with fetched suggestions
        function updateSuggestions(suggestions) {
            var dropdown = $('#custom-suggestion-dropdown');
            dropdown.empty(); // Clear existing suggestions
            if (suggestions.length > 0) { // Check if there are any suggestions
                suggestions.forEach(function(suggestion) {
                    dropdown.append('<li class="custom-suggestion">' + suggestion + '</li>');
                });
                dropdown.show(); // Show the suggestion dropdown if there are suggestions
            } else {
                dropdown.hide(); // Hide the suggestion dropdown if there are no suggestions
            }
        }


    // Function to hide the suggestion dropdown
    function hideSuggestions() {
        $('#custom-suggestion-dropdown').hide();
    }
});



            $('.save').attr("disabled", "disabled");
            $('.resume-box .input-group').addClass('d-none');
            $('.edit').click(function() {
                $(this).addClass('bg-orange');
                $('.resume-box input').removeAttr('disabled');
                $('.resume-box select').removeAttr('disabled');
                $('.save').removeAttr('disabled');
                $('.resume-box input').addClass('bg-light');
                $('.resume-box .input-group').removeClass('d-none');
                $('.resume-box .badge-div').addClass('d-none');
                $('.resume-box select').addClass('bg-light');
            });
        });
    </script>
    @endsection
</x-app-layout>