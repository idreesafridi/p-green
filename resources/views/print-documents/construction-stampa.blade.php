<x-doc-app-layout>

    @section('styles')
        <style>
            .normal_body {
                background-color: white !important;
            }
        </style>
    @endsection

    @php
        $comp = 'construction-stampa.c' . $page;
    @endphp
    <div >
        <x-dynamic-component :component="$comp" :construction="$data" />
    </div>

</x-doc-app-layout>
