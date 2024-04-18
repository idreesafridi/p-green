<form action="{{ route('edit_materials_all', $matData->id) }}" id="materialForm" method="post">
    @csrf
    <div class="card-head" id="materials">
        <div class="row align-items-center justify-content-between gy-2">
            <div class="col-12 col-md-4">
                <h6 class="fw-bold mb-0">Lista Materiali</h6>
            </div>
            <div class="d-flex justify-content-end col-12 col-md-8">
                <div>
                    <button type="button" class="btn btn-outline-green m-1" data-bs-toggle="modal" data-bs-target="#addMaterial">
                        <strong><i class="fa fa-plus"></i> Aggiungi materiale</strong>
                    </button>
                    <button type="button" class="btn btn-green m-1 edit-m">
                        <strong><i class="fa fa-pencil"></i> Modifica</strong>
                    </button>
                    <button type="button" class="btn btn-outline-green m-1 save-m" onclick="changingMaterial()" disabled="disabled">
                        <strong><i class="fa fa-check"></i> Salva</strong>
                    </button>
                    <a href="{{ route('construction_material_print', $matData->id) }}" target="_blank" class="btn btn-outline-green m-1">
                        <strong><i class="fa fa-print"></i></strong>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body my-4 p-0">
        <table class="table material-list-table table-borderless dt-responsive " style="width: 100% !important;">
            <thead>
                <tr>
                    <th scope="col" class="">#</th>
                    <th scope="col" class="">Nome</th>
                    <th scope="col" class="">Quantità</th>
                    <th scope="col">Consegnato</th>
                    <th scope="col" class="">Stato</th>
                    <th scope="col" class="">Montato</th>
                    <th scope="col" class="">Avvio</th>
                    <th scope="col" class="">Note</th>
                    <th scope="col" class="">
                        <nobr>Ultimo agg</nobr>
                    </th>
                    <th scope="col" class="=">Aggiornato da</th>
                    <th scope="col" class="">Elimina Voce</th>
                    <th scope="col" class=""></th>
                    {{-- <th scope="col dropdown" class="hideInMobile"> Tipologia</th> --}}
                </tr>
            </thead>
            <tbody>
                <!--row-1-->
                @php
                $sr = 1;
                @endphp

                @php
                // dd($matData->ConstructionMaterial);
                $sortedData = $matData->ConstructionMaterial->sortBy(function ($item) {
                if ($item->MaterialList && $item->MaterialList->MaterialTypeBelongs && $item->MaterialList->MaterialTypeBelongs->MaterialOptionBelongs) {
                $name = $item->MaterialList->MaterialTypeBelongs->MaterialOptionBelongs->name;
                if ($name === 'Veicolo') {
                return 1;
                } elseif ($name === 'Infissi') {
                return 2;
                } elseif ($name === 'Termico') {
                return 3;
                } elseif ($name === 'Fotovoltaico' || $name === 'fotovoltaico') {
                return 4;
                } else {
                return 5;
                }
                }
                });

                $sortedData1 = $sortedData->sortBy(function ($item) {
                if ($item->MaterialList != null) {
                $name = $item->state;
                if ($name == 'Stato da selezionare') {
                return 1;
                } else {
                return 2;
                }
                }
                });

                @endphp
                {{-- @dd($sortedData); --}}
                @foreach ($sortedData->where('material_list_id', '!=', null)->where('delete_status', '!=', true) as $item)
                {{-- @if ($item->MaterialList != null && $item->MaterialList->name != null && $item->quantity != null) --}}
               
                @if ($item->MaterialList != null && $item->MaterialList->name != null)
                @php
                $color = '#ffffff'; // Default color (white)
                $name = $item->MaterialList->MaterialTypeBelongs->MaterialOptionBelongs->name;
                if ($name === 'Cappotto') {
                $color = '#ff5447'; // Red color for 'Cappotto'
                } elseif ($name === 'Termico') {
                $color = '#00b0f1'; // Blue color for 'Termico'
                } elseif ($name === 'Fotovoltaico') {
                $color = '#d9d9d9'; // Light gray color for 'Fotovoltaico'
                }

               $matChange = false;
               $created_at =  false;
               $created_at_not_updated = false;
              $minCreatedAt = $item->MatHistory->max('created_at');

              $minUpdatedAt = $item->MatHistory->max('updated_at');
              if(!empty($minCreatedAt)){
                $differenceInDays = now()->diffInDays($minCreatedAt);
                if($differenceInDays < 14){
                  $matChange = true;
                  if($differenceInDays == 0){
                    $created_at_not_updated = $differenceInDays  + 1;
                  }else{
                    $created_at_not_updated = $differenceInDays;
                  }
                
                }
              }
              if(!empty($minUpdatedAt)){
                $differenceInDaysforUpdate = now()->diffInDays($minUpdatedAt);
                if($differenceInDaysforUpdate < 7){
                    if($differenceInDaysforUpdate == 0){
                        $created_at = $differenceInDaysforUpdate  + 1;
                    }else{
                        $created_at = $differenceInDaysforUpdate;
                    }
                  
                }
              }

                
                @endphp
                <tr>
                    <td class="" style="background-color: {{ $color }};">
                        {{-- {{ $sr++ }} --}}
                        {{-- <i class='fa fa-history fa-3x'></i> --}}
                        <i class='fa fa-history fa-2x' onclick="fetchHistory({{ $item->id }})" style="cursor: pointer;"  data-toggle="tooltip" data-placement="top" title="{{$created_at ? 'Modificato  ' . $created_at . ' giorni fa, meno di una settimana' : ($created_at_not_updated ? 'Aggiornato ' . $created_at_not_updated . ' giorni fa meno di una settimana' : '')  }}"><sup><i class="text-warning fa fa-warning {{ $matChange == true  ? '' : 'd-none' }}"></i></sup></i>
                        <input type="hidden" name="change[]" value="0" id="matChange{{ $item->id }}">
                        <input type="hidden" name="construction_material_id[]" id="construction_material_id-{{ $item->id }}" value="{{ $item->id }}">
                        <input type="hidden" value="{{ count($matData->ConstructionMaterial) }}" name="loop_count">
                        <input type="hidden" id="tempNote">
                        <input type="hidden" id="tempAvivo">
                        <input type="hidden" id="tempQuantity">
                        <input type="hidden" id="tempStatus">
                    </td>
                    <td class="" style="white-space: normal;">
                        @if ($name == 'Veicolo' && $item->material_list_id != 295)
                        {{ $item->MaterialList->name }}
                        @else
                        <input type="hidden" name="old_material_list_id_for_all[]" value="{{ $item->material_list_id }}">
                        @if (in_array($item->MaterialList->MaterialTypeBelongs->name, ['Pannelli Fotovoltaici', 'Batterie', 'Inverter']))
                        <input type="hidden" name="old_material_list_id[]" value="{{ $item->material_list_id }}">
                        @endif
                        <select class="dark w-200 nome_td" disabled="disabled" style="appearance: none; -webkit-appearance: none; -moz-appearance: none;" id="material_list_id-{{ $item->id }}" name="material_list_id[]" onchange="matChangeValue(1, '{{ $item->id }}')">
                            @php
                            if ($item->MaterialList != null) {
                            $mat_list = App\Models\MaterialList::where('material_type_id', $item->MaterialList->material_type_id)->get();
                            } else {
                            $mat_list = [];
                            }

                            @endphp
                            @foreach ($mat_list as $mat_item)
                            <option value="{{ $mat_item->id }}" {{ $item->MaterialList->id == $mat_item->id ? 'selected' : '' }}>
                                {{ $mat_item->name }}
                            </option>
                            @endforeach
                        </select>
                        @endif
                    </td>
                    <td class="">
                        @if ($name != 'Veicolo' || $item->material_list_id == 295)
                        <input type="hidden" name="old_quantity_for_all[]" value="{{ $item->quantity }}">
                        @if (in_array($item->MaterialList->MaterialTypeBelongs->name, ['Pannelli Fotovoltaici', 'Batterie', 'Inverter']))
                        <input type="hidden" name="old_quantity[]" value="{{ $item->quantity }}">
                        @endif
                        <input type="text" class="quantity" style="text-align: right !Important;width: 2rem;background: transparent !Important;max-width: 100%;outline: none !important;border: 0;" name="quantity[]" disabled="disabled" value="{{ $item->quantity }}" onchange="quantityChangeValue(1, '{{ $item->id }}', 'quantity-{{ $item->id }}')" id="quantity-{{ $item->id }}">
                        {{ $item->MaterialList != null && $item->material_list_id != 295 ? $item->MaterialList->unit : '' }}
                        @endif
                    </td>
                    <td>
                        @if ($name != 'Veicolo')
                        <input type="hidden" id="consegnato-{{ $item->id }}" name="consegnato[]" value="{{ $item->consegnato }}">
                        @if ($item->ConstructionSite->ConstructionShippingSingle != null)
                        @if ($item->ConstructionSite->ConstructionShippingSingle->ConstructionShippingListGet($item->id) != null)
                        {{-- @if ($item->consegnato == 0 || $item->consegnato == null)
                                        <button class="toggleOff"
                                            style="background-color: rgb(236, 240, 0) !important; color: rgb(236, 240, 0)"
                                            onclick="toggle_model('1','{{ $item->id }}', 'consegnato')"
                        type="button" disabled>
                        <div class="toggle-icon">
                            <i class="fa fa-close"></i>
                        </div>
                        </button>
                        @else
                        <button class="toggleOn" style="background-color: rgb(236, 240, 0) !important; color: rgb(236, 240, 0)" onclick="toggle_model('0','{{ $item->id }}', 'consegnato')" type="button" disabled>
                            <div class="toggle-icon">
                                <i class="fa fa-check"></i>
                            </div>
                        </button>
                        @endif --}}


                        @if ($item->consegnato == 0 || $item->consegnato == null)
                        <button class="toggleOff" onclick="toggle_model('1','{{ $item->id }}', 'consegnato')" type="button" disabled>
                            <div class="toggle-icon">
                                <i class="fa fa-close"></i>
                            </div>
                        </button>
                        @else
                        <button class="toggleOn" onclick="toggle_model('0','{{ $item->id }}', 'consegnato')" type="button" disabled>
                            <div class="toggle-icon">
                                <i class="fa fa-check"></i>
                            </div>
                        </button>
                        @endif
                        @else
                        @if ($item->consegnato == 0 || $item->consegnato == null)
                        <button class="toggleOff" onclick="toggle_model('1','{{ $item->id }}', 'consegnato')" type="button" disabled>
                            <div class="toggle-icon">
                                <i class="fa fa-close"></i>
                            </div>
                        </button>
                        @else
                        <button class="toggleOn" onclick="toggle_model('0','{{ $item->id }}', 'consegnato')" type="button" disabled>
                            <div class="toggle-icon">
                                <i class="fa fa-check"></i>
                            </div>
                        </button>
                        @endif
                        @endif
                        @else
                        @if ($item->consegnato == 0 || $item->consegnato == null)
                        <button class="toggleOff" onclick="toggle_model('1','{{ $item->id }}', 'consegnato')" type="button" disabled>
                            <div class="toggle-icon">
                                <i class="fa fa-close"></i>
                            </div>
                        </button>
                        @else
                        <button class="toggleOn" onclick="toggle_model('0','{{ $item->id }}', 'consegnato')" type="button" disabled>
                            <div class="toggle-icon">
                                <i class="fa fa-check"></i>
                            </div>
                        </button>
                        @endif
                        @endif
                        @endif
                    </td>
                    <td class="">
                        @if ($name != 'Veicolo' || $item->material_list_id == 295)
                        @if ($item->MaterialList != null)
                        {{-- @if ($item->MaterialList->name == 'ZAVORRE' || $item->MaterialList->name == 'SBARRE' || $item->MaterialList->name == 'CORDOLI')
                                    @else --}}
                        <select class="text-dark   state  mycustemwidth {{ $item->material_list_id == 295 ? 'd-none' : '' }}" style="background-color: {{ $item->state == 'Da bollettare' ? '#00b0f1' : ($item->state == 'Consegna parziale' ? '#ffba33' : ($item->state == 'Consegna diretta' ? '#ff00ff' : ($item->state == 'Bollettato' ? '#198754' : 'bg-gray'))) }}" disabled="disabled" name="state[]" onchange="statusChangeValue(1, '{{ $item->id }}', 'status-{{ $item->id }}')" id="status-{{ $item->id }}">
                            <option value="Stato da selezionare" {{ $item->state == 'Stato da selezionare' ? 'selected' : '' }}>
                                Stato da selezionare
                            </option>
                            <option value="Da bollettare" {{ $item->state == 'Da bollettare' ? 'selected' : '' }}>
                                Da bollettare</option>
                            <option value="Consegna parziale" {{ $item->state == 'Consegna parziale' ? 'selected' : '' }}>Consegna
                                parziale
                            </option>
                            <option value="Consegna diretta" {{ $item->state == 'Consegna diretta' ? 'selected' : '' }}>Consegna diretta
                            </option>
                            <option value="Bollettato" {{ $item->state == 'Bollettato' ? 'selected' : '' }}>
                                Bollettato</option>

                        </select>
                        {{-- @endif --}}
                        @endif
                        @endif
                    </td>
                    <td class="">
                        @if ($name != 'Veicolo')
                        <div class="d-flex align-items-center">
                            <input type="hidden" name="montato" id="montato-{{ $item->id }}" value="{{ $item->montato }}">
                            @if ($item->montato == 0 || $item->montato == null)
                            <button class="toggleOff" onclick="toggle_model('1','{{ $item->id }}', 'montato')" type="button" disabled>
                                <div class="toggle-icon my-auto">
                                    <i class="fa fa-close"></i>
                                </div>
                            </button>
                            @else
                            <button class="toggleOn" onclick="toggle_model('0','{{ $item->id }}', 'montato')" type="button" disabled>
                                <div class="toggle-icon my-auto">
                                    <i class="fa fa-check"></i>
                                </div>
                            </button>
                            @endif
                        </div>
                        @endif
                    </td>
                    <td class="">
                        @if ($name != 'Veicolo' || $item->material_list_id == 295)
                        @if ($item->MaterialList != null)
                        @if (
                        $item->MaterialList->MaterialTypeBelongs->name == 'Pompa di Calore o Caldaia' ||
                        $item->MaterialList->MaterialTypeBelongs->name == 'Inverter')
                        <input type="date" name="avvio[]" class="form-control avvio w-125" disabled value="{{ $item->avvio != null ? $item->avvio : '' }}" onchange="avivoChangeValue(1, '{{ $item->id }}', 'avivo-{{ $item->id }}')" id="avivo-{{ $item->id }}">
                        @else
                        <input type="hidden" name="avvio[]" class="form-control avvio w-125" disabled>
                        @endif
                        @endif
                        @endif
                    </td>
                    <td class="">
                        <textarea class="mb-0 bg-gray note form-control border-1" style="width: 200px;" disabled="disabled" name="note[]" onchange="noteChangeValue(1, '{{ $item->id }}', 'note-{{ $item->id }}')" id="note-{{ $item->id }}">{{ $item->note }}</textarea>
                    </td>

                    <td class="">
                        @if ($name != 'Veicolo')
                        {{ date('d-m-Y', strtotime($item->updated_at)) }}
                        @else
                        {{ date('d-m-Y H:i:s', strtotime($item->updated_at)) }}
                        @endif
                    </td>
                    <td class="">{{ $item->user == null ? '' : $item->user->name }}</td>
                    <td class="">
                        <button type="button" class="p-0 btn btn-link btn-sm text-danger btn-delete" onclick="material_delete_id('{{ $item->id }}')">
                            <i class="fa fa-trash f-17 me-2"></i>
                        </button>
                    </td>
                    <td class="" data-bs-toggle="tooltip" data-bs-placement="top" title="VIEW MORE DETAILS"></td>
                    {{-- <td class="hideInMobile">
                            {{ $item->MaterialList != null ? $item->MaterialList->MaterialTypeBelongs->MaterialOptionBelongs->name : '' }}
                    </td> --}}
                </tr>
                @endif
                @endforeach


                {{-- <div class="" id="deletedRecords" > --}}
                    @if($sortedData->where('material_list_id', '!=', null)->where('delete_status', '!=', false)->count() > 0)
                    <tr>
                        <td colspan="2">
                            <span id="toggleRowsBtn" style="cursor: pointer"><i id="arrowIcon" class="fas fa-chevron-down"></i> Visualizza eliminati</span>
                        </td>
                        <td style= "visibility:hidden;">
                           
                        </td>
                        <td style="visibility:hidden;"></td>
                        <td style="visibility:hidden;"></td>
                        <td style="visibility:hidden;"></td>
                        <td style="visibility:hidden;"></td>
                        <td style="visibility:hidden;"></td>
                        <td style="visibility:hidden;"></td>
                        <td style="visibility:hidden;"></td>
                        <td style="visibility:hidden;"></td>
                        <td style="visibility:hidden;"></td>
                        <td style="visibility:hidden;"></td>
                        <td style="visibility:hidden;"></td>
                    </tr>
                    @endif
                    @php
                    $sr = 1;
                    @endphp
    
                    @php
                    // dd($matData->ConstructionMaterial);
                    $sortedData = $matData->ConstructionMaterial->sortBy(function ($item) {
                    if ($item->MaterialList && $item->MaterialList->MaterialTypeBelongs && $item->MaterialList->MaterialTypeBelongs->MaterialOptionBelongs) {
                    $name = $item->MaterialList->MaterialTypeBelongs->MaterialOptionBelongs->name;
                    if ($name === 'Veicolo') {
                    return 1;
                    } elseif ($name === 'Infissi') {
                    return 2;
                    } elseif ($name === 'Termico') {
                    return 3;
                    } elseif ($name === 'Fotovoltaico' || $name === 'fotovoltaico') {
                    return 4;
                    } else {
                    return 5;
                    }
                    }
                    });
    
                    $sortedData1 = $sortedData->sortBy(function ($item) {
                    if ($item->MaterialList != null) {
                    $name = $item->state;
                    if ($name == 'Stato da selezionare') {
                    return 1;
                    } else {
                    return 2;
                    }
                    }
                    });
                 
                    @endphp
                    {{-- @dd($sortedData); --}}
                    @foreach ($sortedData->where('material_list_id', '!=', null)->where('delete_status', '!=', false) as $item)
                    {{-- @if ($item->MaterialList != null && $item->MaterialList->name != null && $item->quantity != null) --}}
                   
                    @if ($item->MaterialList != null && $item->MaterialList->name != null)
                    @php
                    $color = '#ffffff'; // Default color (white)
                    $name = $item->MaterialList->MaterialTypeBelongs->MaterialOptionBelongs->name;
                    if ($name === 'Cappotto') {
                    $color = '#ff5447'; // Red color for 'Cappotto'
                    } elseif ($name === 'Termico') {
                    $color = '#00b0f1'; // Blue color for 'Termico'
                    } elseif ($name === 'Fotovoltaico') {
                    $color = '#d9d9d9'; // Light gray color for 'Fotovoltaico'
                    }
    
                   $matChange = false;
                  $minCreatedAt = $item->MatHistory->max('created_at');
               
                  if(!empty($minCreatedAt)){
                    $differenceInDays = now()->diffInDays($minCreatedAt);
                    if($differenceInDays < 14){
                      $matChange = true;
                    }
                  }
                    
                    @endphp
                    <tr class="deletedRecordsDiv  disabled-deleted-rows" style=" opacity: 0.4 !important;">
                        <td class="enabled-deleted-rows" style="background-color: {{ $color }};">
                            <i class='fa fa-history fa-2x'  onclick="fetchHistory({{ $item->id }})" style="cursor: pointer;"  data-toggle="tooltip" data-placement="top" title="Aggiungo meno di 30 giorni fa"><sup><i class="text-warning fa fa-warning {{ $matChange == true  ? '' : 'd-none' }}"></i></sup></i>
                        </td>
                        <td class="" style="white-space: normal;">
                            @if ($name == 'Veicolo' && $item->material_list_id != 295)
                            {{ $item->MaterialList->name }}
                            @else
                           
                            @if (in_array($item->MaterialList->MaterialTypeBelongs->name, ['Pannelli Fotovoltaici', 'Batterie', 'Inverter']))
                     
                            @endif
                            <select class="dark w-200 nome_td" disabled="disabled" style="appearance: none; -webkit-appearance: none; -moz-appearance: none;" >
                                @php
                                if ($item->MaterialList != null) {
                                $mat_list = App\Models\MaterialList::where('material_type_id', $item->MaterialList->material_type_id)->get();
                                } else {
                                $mat_list = [];
                                }
    
                                @endphp
                                @foreach ($mat_list as $mat_item)
                                <option value="{{ $mat_item->id }}" {{ $item->MaterialList->id == $mat_item->id ? 'selected' : '' }}>
                                    {{ $mat_item->name }}
                                </option>
                                @endforeach
                            </select>
                            @endif
                        </td>
                        <td class="">
                            @if ($name != 'Veicolo' || $item->material_list_id == 295)
                         
                            @if (in_array($item->MaterialList->MaterialTypeBelongs->name, ['Pannelli Fotovoltaici', 'Batterie', 'Inverter']))
                        
                            @endif
                            <input type="text" style="text-align: right !Important;width: 2rem;background: transparent !Important;max-width: 100%;outline: none !important;border: 0;"  disabled="disabled" >
                            {{ $item->MaterialList != null && $item->material_list_id != 295 ? $item->MaterialList->unit : '' }}
                            @endif
                        </td>
                        <td>
                            @if ($name != 'Veicolo')
                            <input type="hidden" id="consegnato-{{ $item->id }}" name="consegnato[]" value="{{ $item->consegnato }}">
                            @if ($item->ConstructionSite->ConstructionShippingSingle != null)
                            @if ($item->ConstructionSite->ConstructionShippingSingle->ConstructionShippingListGet($item->id) != null)
                            {{-- @if ($item->consegnato == 0 || $item->consegnato == null)
                                            <button class="toggleOff"
                                                style="background-color: rgb(236, 240, 0) !important; color: rgb(236, 240, 0)"
                                                onclick="toggle_model('1','{{ $item->id }}', 'consegnato')"
                            type="button" disabled>
                            <div class="toggle-icon">
                                <i class="fa fa-close"></i>
                            </div>
                            </button>
                            @else
                            <button class="toggleOn" style="background-color: rgb(236, 240, 0) !important; color: rgb(236, 240, 0)" onclick="toggle_model('0','{{ $item->id }}', 'consegnato')" type="button" disabled>
                                <div class="toggle-icon">
                                    <i class="fa fa-check"></i>
                                </div>
                            </button>
                            @endif --}}
    
    
                            @if ($item->consegnato == 0 || $item->consegnato == null)
                            <button class="toggleOff"    type="button" disabled>
                                <div class="toggle-icon">
                                    <i class="fa fa-close"></i>
                                </div>
                            </button>
                            @else
                            <button class="toggleOn"  type="button" disabled>
                                <div class="toggle-icon">
                                    <i class="fa fa-check"></i>
                                </div>
                            </button>
                            @endif
                            @else
                            @if ($item->consegnato == 0 || $item->consegnato == null)
                            <button class="toggleOff"    type="button" disabled>
                                <div class="toggle-icon">
                                    <i class="fa fa-close"></i>
                                </div>
                            </button>
                            @else
                            <button class="toggleOn"  type="button" disabled>
                                <div class="toggle-icon">
                                    <i class="fa fa-check"></i>
                                </div>
                            </button>
                            @endif
                            @endif
                            @else
                            @if ($item->consegnato == 0 || $item->consegnato == null)
                            <button class="toggleOff"  type="button" disabled>
                                <div class="toggle-icon">
                                    <i class="fa fa-close"></i>
                                </div>
                            </button>
                            @else
                            <button class="toggleOn"  type="button" disabled>
                                <div class="toggle-icon">
                                    <i class="fa fa-check"></i>
                                </div>
                            </button>
                            @endif
                            @endif
                            @endif
                        </td>
                        <td class="">
                            @if ($name != 'Veicolo' || $item->material_list_id == 295)
                            @if ($item->MaterialList != null)
                            {{-- @if ($item->MaterialList->name == 'ZAVORRE' || $item->MaterialList->name == 'SBARRE' || $item->MaterialList->name == 'CORDOLI')
                                        @else --}}
                            <select class="text-dark     mycustemwidth {{ $item->material_list_id == 295 ? 'd-none' : '' }}" style="background-color: {{ $item->state == 'Da bollettare' ? '#00b0f1' : ($item->state == 'Consegna parziale' ? '#ffba33' : ($item->state == 'Consegna diretta' ? '#ff00ff' : ($item->state == 'Bollettato' ? '#198754' : 'bg-gray'))) }}" disabled="disabled" >
                                <option value="Stato da selezionare" {{ $item->state == 'Stato da selezionare' ? 'selected' : '' }}>
                                    Stato da selezionare
                                </option>
                                <option value="Da bollettare" {{ $item->state == 'Da bollettare' ? 'selected' : '' }}>
                                    Da bollettare</option>
                                <option value="Consegna parziale" {{ $item->state == 'Consegna parziale' ? 'selected' : '' }}>Consegna
                                    parziale
                                </option>
                                <option value="Consegna diretta" {{ $item->state == 'Consegna diretta' ? 'selected' : '' }}>Consegna diretta
                                </option>
                                <option value="Bollettato" {{ $item->state == 'Bollettato' ? 'selected' : '' }}>
                                    Bollettato</option>
    
                            </select>
                            {{-- @endif --}}
                            @endif
                            @endif
                        </td>
                        <td class="">
                            @if ($name != 'Veicolo')
                            <div class="d-flex align-items-center">
        
                                @if ($item->montato == 0 || $item->montato == null)
                                <button class="toggleOff"  type="button" disabled>
                                    <div class="toggle-icon my-auto">
                                        <i class="fa fa-close"></i>
                                    </div>
                                </button>
                                @else
                                <button class="toggleOn"  type="button" disabled>
                                    <div class="toggle-icon my-auto">
                                        <i class="fa fa-check"></i>
                                    </div>
                                </button>
                                @endif
                            </div>
                            @endif
                        </td>
                        <td class="">
                            @if ($name != 'Veicolo' || $item->material_list_id == 295)
                            @if ($item->MaterialList != null)
                            @if (
                            $item->MaterialList->MaterialTypeBelongs->name == 'Pompa di Calore o Caldaia' ||
                            $item->MaterialList->MaterialTypeBelongs->name == 'Inverter')
                            <input type="date"  class="form-control avvio w-125" disabled  >
                            @else
                           
                            @endif
                            @endif
                            @endif
                        </td>
                        <td class="">
                            <textarea class="mb-0 bg-gray note form-control border-1" style="width: 200px;" disabled="disabled" >{{ $item->note }}</textarea>
                        </td>
    
                        <td class="">
                            @if ($name != 'Veicolo')
                            {{ date('d-m-Y', strtotime($item->updated_at)) }}
                            @else
                            {{ date('d-m-Y H:i:s', strtotime($item->updated_at)) }}
                            @endif
                        </td>
                        <td class="">{{ $item->user == null ? '' : $item->user->name }}</td>
                        <td class="enabled-deleted-rows">
                            <button type="button" class="p-0 btn btn-link btn-sm text-success btn-delete" onclick="material_delete_id('{{ $item->id }}')">
                                <i class="fa fa-refresh f-17 me-2"></i>
                            </button>
                        </td>
                        <td class="enabled-deleted-rows" data-bs-toggle="tooltip" data-bs-placement="top" title="VIEW MORE DETAILS"></td>
                        {{-- <td class="hideInMobile">
                                {{ $item->MaterialList != null ? $item->MaterialList->MaterialTypeBelongs->MaterialOptionBelongs->name : '' }}
                        </td> --}}
                    </tr>
                    @endif
                    @endforeach
    
                {{-- </div> --}}
                </tbody>
            </table>

            

           
          
        </div>




    <!-- model popup for material changing history -->

    <div class="modal fade" id="changingMaterial" aria-labelledby="changingMaterial" aria-modal="true" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="changingMaterialLabel">
                        <strong>Riepilogo modifiche</strong>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="changingMaterialBody">
                    <!-- your.view.name.blade.php -->

                </div>
                <div class="modal-footer mb-1">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Indietro</button>
                    <button type="button" class="btn change-color btn-green" id= "salvaModifiche" onclick="submitMaterialForm()">Applica modifiche</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End model popup for material changing history -->

 <!-- model popup for material changing history -->

 <div class="modal fade" id="changingMaterialPrint" aria-labelledby="changingMaterialPrint" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changingMaterialPrintLabel">
                    <strong>Riepilogo modifiche</strong>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="changingMaterialBodyPrint">
                <!-- your.view.name.blade.php -->

            </div>
            <div class="modal-footer mb-1">
                {{-- <p class="modal-footer-text" style="justify-content:flex-start !important;">
                    * Modifiche registrate dal {{ optional($item->history->max('updated_at'))->format('d-m-Y') }}
                </p> --}}
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
            </div>
        </div>
    </div>
</div>
<!-- End model popup for material changing history -->









</form>
<!-- Modal-1 -->
<div class="modal fade" id="addMaterial" aria-labelledby="addMaterial" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addMaterial">Aggiungi Materiale</h5>
                <button type="button" class="btn btn-outline-green ms-3" onclick="location.href='{{ route('material_create') }}'">
                    <strong>Crea materiale<i class="fa fa-arrow-right ms-2"></i></strong>
                </button>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('construction_material_store', $matData->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="modal1-content py-3">
                        <div class="row mx-0">
                            <div class="col-md-12">
                                <x-material-type-list />
                            </div>
                        </div>
                        <div id="get_mat_list_ajax"></div>
                    </div>
                </div>
                <div class="modal-footer mb-1">
                    <button type="button" class="btn btn-outline-green" data-bs-dismiss="modal">
                        <strong><i class="fa fa-times me-2"></i>Chiudi</strong>
                    </button>
                    <button type="submit" class="btn btn-green">
                        <strong><i class="fa fa-check me-2"></i>Salva e chiudi</strong>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- End Modal-1 -->
<!-- Toggle Modal -->
<div class="modal fade" id="ToggleModal" aria-labelledby="ToggleModal" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ToggleModalLabel">
                    <strong>Elimina conferma di consegna</strong>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('edit_materials_state', $matData->id) }}" id="toggleModalForm" method="POST">
                @csrf
                <div class="modal-body text-center">
                    <div class="row mb-3 mt-5">
                        <img src="{{ asset('assets/images/alertgreengen.png') }}" class="alert-img mx-auto">
                    </div>
                    <div class="row mb-4">
                        <input type="hidden" name="material_id" value="" id="toggleModalId">
                        <input type="hidden" name="toggleModalState" value="" id="toggleModalState">
                        <input type="hidden" name="toggleModalStatus" value="" id="toggleModalStatus">
                        <input type="hidden" name="reason" value="" id="toggleModalReason">
                        <div id="toggleModalHTML"></div>
                    </div>
                </div>
                <div class="modal-footer mb-1">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Indietro</button>
                    <button type="button" class="btn change-color" onclick = "changeState({{$matData->id}})">Aggiungi</button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="ToggleModalHistory" aria-labelledby="ToggleModalHistory" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ToggleModalHistoryLabel">
                    <strong>Elimina conferma di consegna</strong>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
          
                <div class="modal-body" id="ToggleModalHistoryBody">
                   
                </div>
                <div class="modal-footer mb-1">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Indietro</button>
                    {{-- <button type="button" class="btn change-color" onclick = "changeState()">Aggiungi</button> --}}
                    <button type="button" class="btn change-color btn-green" onclick = "montatoConfirm()">Aggiungi</button>
                </div>
        
        </div>
    </div>
</div>
<!-- End Toggle Modal -->





<!-- Warning Delete Modal -->
<div class="modal fade" id="deleteModal" aria-labelledby="deleteModal" aria-modal="true" role="dialog">
    <div class="modal-dialog   modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">
                    <strong>Elimina lavorazione</strong>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('delete_construction_material') }}" method="POST">
                @csrf
                <input type="hidden" name="material_delete_id" value="" id="material_delete_id">
                <div class="modal-body" id="DeletePopupBody">
                    {{-- <div class="row mb-3 mt-5">
                        <img src="{{ asset('assets/images/alertgreengen.png') }}" class="alert-img mx-auto">
                    </div>
                    <div class="row mb-4">
                        <h6>Sei sicuro di voler eliminare questa lavorazione?</h6>
                        <input type="hidden" name="material_delete_id" value="" id="material_delete_id">
                    </div> --}}
                </div>
                <div class="modal-footer mb-1">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Indietro</button>
                    <button type="submit" id= "ConfermoButton" class="btn btn-danger">Confermo</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- End Warning Delete Modal -->

<!-- Warning undo Delete Modal -->
{{-- <div class="modal fade" id="UndodeleteModal" aria-labelledby="UndodeleteModal" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="UndodeleteModalLabel">
                    <strong>annullare l'elaborazione</strong>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('delete_construction_material') }}" method="POST">
                @csrf
                <div class="modal-body text-center">
                    <div class="row mb-3 mt-5">
                        <img src="{{ asset('assets/images/alertgreengen.png') }}" class="alert-img mx-auto">
                    </div>
                    <div class="row mb-4">
                        <h6>Sei sicuro di voler annullare questo materiale?</h6>
                        <input type="hidden" name="material_delete_id" value="" id="material_undo_delete_id">
                    </div>
                </div>
                <div class="modal-footer mb-1">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Indietro</button>
                    <button type="submit" class="btn btn-success">Confermo</button>
                </div>
            </form>
        </div>
    </div>
</div> --}}
<!-- End undo Delete Modal -->







@section('scripts')
{{-- <script src="{{ asset('assets/js/jquery-3.6.1.min.js?t=<?= time() ?>') }}" type="text/javascript"></script> --}}
<script>

function montatoConfirm(){
    var reson = document.getElementById('motivationSelect-0').value;
    $('#toggleModalReason').val(reson);

    document.getElementById('toggleModalForm').submit();
}



function fetchHistory(id) {
        $.ajax({
            type: "GET",
            url: "{{ route('MaterialsHistories', '') }}/" + id,
            success: function (response) {
                $('#changingMaterialPrint').modal('show');

                $('#changingMaterialBodyPrint').html(response.htmlContent);
            }
        });
    }
   function submitMaterialForm() {
    // Assuming you have a form with an ID, update the selector accordingly
    var form = document.getElementById("materialForm");

    // Check all dropdowns for "Altro" option and corresponding textarea
    var errors = [];
    var selects = document.querySelectorAll('select[name^="reason"]');
    var firstErrorTextArea = null;

    selects.forEach(function (select, index) {
        var textArea = document.getElementById("popupTextArea-" + index);
        var selectedValue = select.options[select.selectedIndex].value;

        if (selectedValue === "Altro" && textArea.value.trim() === "") {
            errors.push("inserisci un altro motivo per la riga" + (index + 1));
            if (!firstErrorTextArea) {
                firstErrorTextArea = textArea;
            }
        }
    });

    // If there are errors, display them and focus on the first textarea with an error
    if (errors.length > 0) {
        var errorContainer = document.getElementById("errorContainer");
        errorContainer.innerHTML = errors.join('<br>');
        errorContainer.style.display = "block";

        if (firstErrorTextArea) {
            firstErrorTextArea.focus();
        }

        return false;
    }

    // If no errors, submit the form
    form.submit();
}

  


    var selectedValueBeforeChange = ""; // Variable to store the selected value before change

    function handleDropdownChange(index) {
    var select = document.getElementById("motivationSelect-" + index);
    var selectedValue = select.options[select.selectedIndex].value;
    var textAreaContainer = document.getElementById("textAreaContainer-" + index);
    var popupTextArea = document.getElementById("popupTextArea-" + index);
     var salvaModifiche = document.getElementById("salvaModifiche");
     var ConfermoButton = document.getElementById("ConfermoButton");

        // Show text area only if any option other than the default is selected
        var isAnyOptionSelected = select.selectedIndex !== 0;

        if (isAnyOptionSelected) {
            // Show text area if any option is selected
            textAreaContainer.style.display = "block";
            popupTextArea.value = ""; // Clear the text area when any option is selected
            salvaModifiche.disabled = true; 
            ConfermoButton.disabled = true; 
             
        } else {
            // Hide text area for the default option
            textAreaContainer.style.display = "none";
            salvaModifiche.disabled = false;
            ConfermoButton.disabled = false;
        }
    }

    // function updateDropdownAndClose() {
    //     var inputValue = document.getElementById("popupTextArea").value;
    //     var select = document.getElementById("motivationSelect");

    //     // Save the selected value before the change
    //     selectedValueBeforeChange = select.options[select.selectedIndex].value;

    //     // Update the text of the selected option
    //     select.options[select.selectedIndex].text = inputValue;

    //     // Update the value attribute of the <select> element
    //     select.value = inputValue;

    //     document.getElementById("textAreaContainer").style.display = "none";
    // }

    function toggleSalvaButton(index) {
        var textareaValue = document.getElementById("popupTextArea-" + index).value;
        var salvaButton = document.getElementById("salvaButton-" + index);
       
    
        // Enable or disable the "Salva" button based on textarea value
        salvaButton.disabled = textareaValue.trim() === "";
       

    }

    function updateDropdownAndClose(index) {
        var inputValue = document.getElementById("popupTextArea-" + index).value;
        var select = document.getElementById("motivationSelect-" + index);
        var salvaModifiche = document.getElementById("salvaModifiche");
        var ConfermoButton = document.getElementById("ConfermoButton");

         salvaModifiche.disabled = false;
         ConfermoButton.disabled = false;
        // Validate textarea
        if (inputValue.trim() === "") {
            alert("Il campo del motivo è obbligatorio.");
            return;
        }

        // Save the selected value before the change
        selectedValueBeforeChange = select.options[select.selectedIndex].value;

        // Update the text and value of the selected option
        var selectedOption = select.options[select.selectedIndex];
        selectedOption.text = inputValue;
        selectedOption.value = inputValue;

        document.getElementById("textAreaContainer-" + index).style.display = "none";
    }





    // function updateDropdownAndClose() {
    //     var inputValue = document.getElementById("popupTextArea").value;
    //     var select = document.getElementById("motivationSelect");

    //     // Save the selected value before the change
    //     selectedValueBeforeChange = select.options[select.selectedIndex].value;

    //     select.options[select.selectedIndex].text = inputValue;
    //     select.options[select.selectedIndex].value = inputValue;

    //     document.getElementById("textAreaContainer").style.display = "none";
    // }

    function closeTextAreaModal() {
        document.getElementById("textAreaContainer").style.display = "none";
    }
    $('#toggleRowsBtn').click(function() {
       
    var deletedRecordsTable = $('.deletedRecordsDiv');

    // Toggle table visibility
    deletedRecordsTable.toggle();

    // Toggle arrow icon class
    // $('#arrowIcon').toggleClass('fa-chevron-down fa-chevron-up');

    // Disable/enable rows and apply opacity
    // var rows = $('#deletedRecords tbody tr');
    // if (deletedRecordsTable.is(':visible')) {
    //     // Enable rows and remove opacity
    //     rows.removeClass('disabled-row').css('opacity', '1');
    // } else {
    //     // Disable rows and apply opacity
    //     rows.addClass('disabled-row').css('opacity', '0.5');
    // }
});


    $(document).ready(function() {
      
        function hardReload() {
            location.reload(true);
        }
    });
    $('#material_type_id').on('change', function() {
        var material_type_id = $(this).val()

        $.ajax({
            type: "POST"
            , url: "{{ route('get_mat_list_ajax') }}"
            , data: {
                'id': material_type_id
            }
            , headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
            , success: function(response) {
                $('#get_mat_list_ajax').html(response)
            }
        })
    })

    function matChangeValue(status, id) {
        $('#matChange' + id).val(status)
    }

    $(document).on('change', '#mat_list_ajax', function() {
        var mat_list_ajax = $(this).val()
        var list_array = $('#list_array').val()
        const obj = JSON.parse(list_array);

        if (mat_list_ajax == '') {
            $('#get_units').html('')
        } else {
            for (let index = 0; index < obj.length; index++) {
                const element = obj[index];
                if (element.id == mat_list_ajax) {
                    $('#get_units').html(element.unit)
                }
            }
        }
    })

    function changingMaterial() {
        // $('#changingMaterial').modal('show');
        var dataToSend = [];
        var construction_id = {{ $matData->id }}; // Include construction_id here

        // Iterate through each item
        @foreach($sortedData->where('material_list_id', '!=', null)->where('material_list_id', '!=', 295)->where('delete_status', '!=', true) as $item)
        var construction_material_id = $('#construction_material_id-{{ $item->id }}').val();
        var material_list_id = $('#material_list_id-{{ $item->id }}').val();
        var quantity = $('#quantity-{{ $item->id }}').val();
        var consegnato = $('#consegnato-{{ $item->id }}').val();
        var state = $('#status-{{ $item->id }}').val();
        var montato = $('#montato-{{ $item->id }}').val();
        var note = $('#note-{{ $item->id }}').val();
        var avvio = $('#avivo-{{ $item->id }}').val();

        // Push data for this item into the array
        dataToSend.push({
            construction_id: construction_id
            , construction_material_id: construction_material_id
            , material_list_id: material_list_id
            , quantity: quantity
            , consegnato: consegnato
            , montato: montato
            , state: state
            , note: note
            , avvio: avvio
        });
        @endforeach

        // Now 'dataToSend' is an array containing data for each item
        // You can send this array to the controller using AJAX
        $.ajax({
            type: "POST"
            , url: "{{ route('changingMaterial.store') }}"
            , data: {
                data: dataToSend
            }
            , headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
            , success: function(response) {
              

                // Show the modal
                $('#changingMaterial').modal('show');

                $('#changingMaterialBody').html(response.htmlContent);
            }
        });

    }
    //Material-list Page edit button
    $('.edit-m').click(function() {
        $(this).addClass('bg-orange');
        $('.material-list-table select').removeAttr('disabled');
        $('.toggleOn').removeAttr('disabled');
        $('.toggleOff').removeAttr('disabled');
        $('.btn-delete').removeAttr('disabled');
        $('.note').removeAttr('disabled');
        $('.quantity').removeAttr('disabled');
        $('.avvio').removeAttr('disabled');
        $('.note').removeClass('bg-gray');
        //$('.material-list-table input').addClass('bg-light');
        $('.save-m').removeAttr('disabled');
    });

    function toggle_model(status, id, state) {
        $('#ToggleModal').modal('show');

        $('#toggleModalId').val(id);
        $('#toggleModalStatus').val(status);
        $('#toggleModalState').val(state);

        var consegnatoStateOnVar =
            "<h6>Dichiari che il seguente materiale  è stato consegnato sul cantiere nella quantità indicata? <br> <br><br> <span class='f-13'> * In caso di incoerenza con la realtà dei fatti sarai richiamato direttamente dall 'ufficio.</span> </h6>"

        var consegnatoStateOffVar =
            "<h6>Dichiari che il seguente materiale è stato consegnato sul cantiere nella quantità indicata?<b> INFISSI(6.76) </b> è stato consegnato sul cantiere nella quantità indicata? <br> <b> INFISSI PVC(6.79) </b><br><br><span class='f-13'> * In caso di incoerenza con la realtà dei fatti sarai richiamato direttamente dall 'ufficio.</span> </h6>"

        var montatoStateOnVar =
            "<h6>Dichiari che il seguente materiale  è stato consegnato sul cantiere nella quantità indicata? <br> <br><br> <span class='f-13'> * In caso di incoerenza con la realtà dei fatti sarai richiamato direttamente dall 'ufficio.</span> </h6>"

        var montatoStateOffVar =
            "<h6>Dichiari che il seguente materiale è stato consegnato sul cantiere nella quantità indicata? è stato consegnato sul cantiere nella quantità indicata? <br> <br><br><span class='f-13'> * In caso di incoerenza con la realtà dei fatti sarai richiamato direttamente dall 'ufficio.</span> </h6>"

        if (status == '1') {
            $(".change-color").addClass("btn-danger");

            if (state == 'consegnato') {
                stateVar = consegnatoStateOnVar
            } else if (state == 'montato') {
                stateVar = montatoStateOnVar
            }

        } else if (status == '0') {
            $(".change-color").addClass("btn-green");

            if (state == 'consegnato') {
                stateVar = consegnatoStateOffVar
            } else if (state == 'montato') {
                stateVar = montatoStateOffVar
            }
        }

        $('#toggleModalHTML').html(stateVar)
    }

    function changeState(id){
            var toggleModalId = document.getElementById("toggleModalId").value;
            var toggleModalStatus = document.getElementById("toggleModalStatus").value;
            var toggleModalState = document.getElementById("toggleModalState").value;

            var data = {
                'id': toggleModalId
                , 'status': toggleModalStatus
                , 'state': toggleModalState
            }   
           
            $.ajax({
                type: "POST",
                url: "{{ route('toggleMaterialHistory', '') }}/" + id,
            
                data: {
                    data: data
                }
                , headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
                , success: function(response) {

                    $('#ToggleModal').modal('hide');
                    $('#ToggleModalHistory').modal('show');

                    $('#ToggleModalHistoryBody').html(response.htmlContent);
                    
                }
            });
            
        }   

    function material_delete_id(id) {
     

        $.ajax({
                type: "POST",
                url: "{{ route('deletePopupBodyGet', '') }}/" + id,
            
                data: {
                   
                }
                , headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
                , success: function(response) {
                      $('#deleteModal').modal('show');

                      $('#material_delete_id').val(id);
                    $('#DeletePopupBody').html(response.htmlContent);
                    
                }
            });



    }

    function material_undo_delete_id(id) {
        $('#UndodeleteModal').modal('show');

        $('#material_undo_delete_id').val(id);
    }

    $('.material-list-table').DataTable({
        "responsive": {
            details: {
                type: 'column'
                , target: -1
            }
        }
        , "language": {
            emptyTable: "Nessun dato disponibile nella tabella"
        }
        , "paging": false
        , "ordering": false
        , "info": false
        , "searching": false
        , "columnDefs": [{
            className: 'dtr-control arrow-right'
            , orderable: false
            , target: -1
        }]
    });

    $(function() {
        $('[data-bs-toggle="tooltip"]').tooltip()
    })

    // note update
    function noteChangeValue(status, id, noteId) {
        matChangeValue(status, id);

        var enteredValue = $('#tempNote').val();
        //alert($('#'+noteId).val());
        $('#' + noteId).val(enteredValue);
    }

    $('.material-list-table').on('keyup', 'tr textarea', function() {
        var enteredValue = $(this).val();
        $('#tempNote').val(enteredValue);
    });

    // avivo update
    function avivoChangeValue(status, id, avivoId) {
        setTimeout(function() {
            matChangeValue(status, id);

            var enteredValue = $('#tempAvivo').val();
            $('#' + avivoId).val(enteredValue);
        }, 1000);
    }

    $('.material-list-table').on('change', 'tr input[type="date"]', function() {
        var enteredValue = $(this).val();
        $('#tempAvivo').val(enteredValue);
    });


    // quantity update
    function quantityChangeValue(status, id, quantityId) {
        matChangeValue(status, id);

        var enteredValue = $('#tempQuantity').val();
        $('#' + quantityId).val(enteredValue);
    }

    $('.material-list-table').on('keyup', 'tr input[type="text"]', function() {
        var enteredValue = $(this).val();
        $('#tempQuantity').val(enteredValue);
    });

    // status update
    function statusChangeValue(status, id, statusId) {
        setTimeout(function() {
            //alert("here");
            matChangeValue(status, id);

            var enteredValue = $('#tempStatus').val();
            $('#' + statusId).val(enteredValue);
        }, 500);
    }

    $('.material-list-table').on('change', 'tr select', function() {
        //alert("hi");
        var enteredValue = $(this).val();
        $('#tempStatus').val(enteredValue);
    });

</script>
@endsection

