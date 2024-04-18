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
                            <th scope="col">Materiale</th>
                            <th scope="col">Tipologia</th>
                            <th scope="col">Quantita</th>
                            <th scope="col">Stato</th>
                            <th scope="col">Note</th>
                            <th scope="col">Consegnato</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $sr = 1;
                            $sortedData = $data->ConstructionMaterial->sortBy(function ($item) {
                            if ($item->MaterialList && $item->MaterialList->MaterialTypeBelongs && $item->MaterialList->MaterialTypeBelongs->MaterialOptionBelongs) {
                            $name = $item->MaterialList->MaterialTypeBelongs->MaterialOptionBelongs->name;
                            if ($name === 'Infissi') {
                            return 1;
                            } elseif ($name === 'Termico') {
                            return 2;
                            } elseif ($name === 'Fotovoltaico' || $name === 'fotovoltaico') {
                            return 3;
                            }
                            else{
                            return 4;
                            }
                            }
                            });


                            $sortedData1 = $sortedData->sortBy(function ($item) {
                            if ($item->MaterialList != null) {
                            $name = $item->state;
                            if ($name == 'Stato da selezionare') {
                            return 1;
                            }
                            else
                            return 2;
                            }
                            });

                        @endphp
                        @foreach ($sortedData as $item)
                            <tr>
                                <td>{{ $sr++ }}</td>
                                <td>{{ $item->MaterialList != null ? $item->MaterialList->name : '' }}</td>
                                <td>{{ $item->MaterialList != null ? ($item->MaterialList->MaterialTypeBelongs != null ? $item->MaterialList->MaterialTypeBelongs->name : '') : '' }}
                                </td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ $item->state }}</td>
                                <td>{{ $item->note }}</td>
                                <td>{{ $item->consegnato }}</td>
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
