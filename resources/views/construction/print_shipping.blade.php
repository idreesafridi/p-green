<x-doc-app-layout>

    @section('styles')
        <style>
            .normal_body {
                background-color: white !important;
            }

            .header {
                margin-top: 70px;
                background-color: #0f9d58;
                color: white;
                padding: 10px;
            }

            table {
                margin-top: 20px;
                border-collapse: collapse;
            }

            tr,
            td,
            th {
                border: 1px solid black !important;
            }

            .headingsize {
                font-size: 18px !important;
            }

            .tdbg {
                background-color: #d9d9d9;
            }
        </style>
    @endsection

    <div class="header">
        <h3>PROGRAMMA CONSEGNA</h3>
        <p>Data</p>
    </div>
    <div class="table">
        <table>
            <tr>
                <th class="headingsize">VETTORE</th>
                <th class="headingsize">CANTIERE</th>
                <th class="headingsize" colspan="2">MATERIALE</th>
                <th>bollettato</th>
                <th>carico</th>
                <th>scarico</th>
                <th class="headingsize" colspan="2">STRUTTURA</th>
                <th>carico</th>
                <th>scarico</th>
                <th class="headingsize">NOTE</th>
            </tr>
            <tr>
                <th></th>
                <th class="tdbg"></th>
                <th class="tdbg">Qtà</th>
                <th class="tdbg">Materiale</th>
                <th class="tdbg"></th>
                <th class="tdbg"></th>
                <th class="tdbg"></th>
                <th class="tdbg">Qtà</th>
                <th class="tdbg">Materiale</th>
                <th class="tdbg"></th>
                <th class="tdbg"></th>
                <th class="tdbg"></th>
            </tr>
            @foreach ($all as $item)
                @php
                    $shippingList = $item->ConstructionShippingList;
                @endphp
                @if (count($shippingList) > 0)
                    <tr>
                        <th rowspan="{{ count($shippingList) }}">
                            {{ $shippingList[0]->shipping_truck }}</th>
                        <th rowspan="{{ count($shippingList) }}">{{ $item->ConstructionSite->name }}
                            {{ $item->ConstructionSite->surename }}</th>
                        @if ($shippingList[0]->ship_change == 1)
                            @if (
                                $shippingList[0]->ConstructionShippingMaterials->MaterialList->name == 'ZAVORRE' ||
                                    $shippingList[0]->ConstructionShippingMaterials->MaterialList->name == 'SBARRE' ||
                                    $shippingList[0]->ConstructionShippingMaterials->MaterialList->name == 'CORDOLI')
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>{{ $shippingList[0]->qty }}</td>
                                <td>{{ $shippingList[0]->ConstructionShippingMaterials->MaterialList->name }}
                                </td>
                                <td></td>
                                <td></td>
                                <td></td>
                            @else
                                <td>{{ $shippingList[0]->qty }}</td>
                                <td>{{ $shippingList[0]->ConstructionShippingMaterials->MaterialList->name }}
                                </td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            @endif
                        @endif
                    </tr>

                    @for ($i = 1; $i < count($shippingList); $i++)
                        <tr>
                            @if ($shippingList[$i]->ship_change == 1)
                                @if ($shippingList[$i]->ConstructionShippingMaterials->MaterialList != null)
                                    @if (
                                        $shippingList[$i]->ConstructionShippingMaterials->MaterialList->name == 'ZAVORRE' ||
                                            $shippingList[$i]->ConstructionShippingMaterials->MaterialList->name == 'SBARRE' ||
                                            $shippingList[$i]->ConstructionShippingMaterials->MaterialList->name == 'CORDOLI')
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>{{ $shippingList[$i]->qty }}</td>
                                        <td>{{ $shippingList[$i]->ConstructionShippingMaterials->MaterialList->name }}
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    @else
                                        <td>{{ $shippingList[$i]->qty }}</td>
                                        <td>{{ $shippingList[$i]->ConstructionShippingMaterials->MaterialList->name }}
                                        </td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    @endif
                                @endif
                            @endif
                        </tr>
                    @endfor
                @endif
            @endforeach
        </table>
    </div>

</x-doc-app-layout>
