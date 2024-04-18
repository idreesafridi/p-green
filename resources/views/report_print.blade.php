<x-doc-app-layout>
    @section('styles')
    @endsection

    @section('scripts')
    <script>
        var reportSearchUrl = "{{ route('reportsSearch') }}";
        $(document).ready(function(){
            reportSearchQuery();
        })
    </script>

    <script src="{{ asset('assets/js/reports.js') }}"></script>
    @endsection
</x-doc-app-layout>