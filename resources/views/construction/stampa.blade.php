<x-doc-app-layout>

    @section('styles')
        <style>
            .normal_body {
                background-color: white !important;
            }
        </style>
    @endsection

    <div>
        @for ($i = 1; $i <= count($data); $i++)
            @php
                $filePath = public_path('assets/stampa/' . $id . '/' . $i . '.png');
            @endphp
    
            @if (file_exists($filePath))
                <div class="" style="position: relative;">
                    <img src="{{ asset('assets/stampa/' . $id . '/' . $i . '.png') }}" id="ci"
                        style="height: 1162px; width: 800px; position: relative;">
                </div>
            @endif
        @endfor
    </div>
    

</x-doc-app-layout>
