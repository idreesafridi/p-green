<x-app-layout pageTitle="Construction">
    @section('styles')
    @endsection
        <x-construction-site :constructionData="$data"/>
        <x-document-and-contact :constructionData="$data"/>
        <x-property-data />
        <x-construction-site-setting />
    @section('scripts')
    @endsection
</x-app-layout>
