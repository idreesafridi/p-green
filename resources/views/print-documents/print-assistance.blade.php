<x-doc-app-layout>

    @section('styles')
        <style>
            .normal_body {
                background-color: white !important;
            }
        </style>
    @endsection

    <div class="container">
        <div class="row my-5 mx-0">
            <div class="col-md-9 order-2 order-md-1">
                <h2 class="d-inline">{{ $data->name }} {{ $data->surename }} </h2>
                <span>{{ $data->DocumentAndContact != null ? $data->DocumentAndContact->contact_number : '' }}</span>
                <h6>{{ $data->residence_street . ' ' . $data->residence_house_number . ' ' . $data->residence_postal_code }}
                </h6>
            </div>
            <div class="col-md-3 d-flex justify-content-end order-1 order-md-2">
                <img src="{{ asset('assets/images/img.jpg') }}" class="img-fluid">
            </div>
        </div>
        <div>
            <div class="table-responsive">
                <table class="table .table-sm">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">MODELLO MACCHINA</th>
                            <th scope="col">MATRICOLA</th>
                            <th scope="col">DATA ASSISTENZA</th>
                            <th scope="col">FATTURA</th>
                            <th scope="col">RAPPORTINO</th>
                            <th scope="col">NOTE</th>
                            <th scope="col">STATO</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $sr = 1;
                        @endphp
                        @foreach ($data->MaterialsAsisstance as $item)
                            <tr>
                                <td>{{ $sr++ }}</td>
                                <td>{{ $item->machine_model }}</td>
                                <td>{{ $item->freshman }}</td>
                                <td>{{ $item->start_date }}</td>
                                <td></td>
                                <td></td>
                                <td>{{ $item->notes }}</td>
                                <td>{{ $item->state }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="d-flex justify-content-end">
                    <span>Stampato il: {{ date('d/m/y') }}</span>
                </div>
            </div>
        </div>
        <div class="mt-3">
            <span>Cliente chiamato: SI/No</span><br>
            <span>Bolle stampate: SI/No</span><br>
            <span>Portale aggiornato: SI/No</span><br>
        </div>
    </div>

    <!-- End Main Body Area -->
    @section('scripts')
        <script>
            window.print();
        </script>
    @endsection
</x-doc-app-layout>
