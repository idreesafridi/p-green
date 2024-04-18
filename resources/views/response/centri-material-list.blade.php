@if ($var != null)
    @if ($var->ConstructionSite != null)
        @if ($var->ConstructionSite->ConstructionMaterial != null)
            <form action="{{ route('addConShippingList') }}" method="post" onsubmit="shippingForm(event)"
                id="shippingForm">
                @csrf
                <div class="card border-0">
                    <div class="card-body">
                        @php
                            $shipMatArr = [0 => null];
                            $shipQtyArr = [0 => null];
                            $shipTruckArr = [0 => null];
                            $shipRemQtyArr = [0 => null];
                            $shipChangeArr = [0 => null];
                        @endphp
                        @forelse ($var->ConstructionSite->ConstructionMaterial as $item)
                            @if ($item->ConstructionSite->ConstructionShippingListThrough != null)
                                @foreach ($item->ConstructionSite->ConstructionShippingListThrough as $itemShipping)
                                    @if ($item->material_list_id == $itemShipping->ConstructionShippingMaterials->material_list_id)
                                        @php
                                            array_push($shipMatArr, $item->material_list_id);
                                            array_push($shipQtyArr, $itemShipping->qty);
                                            array_push($shipRemQtyArr, $itemShipping->rem_qty);
                                            array_push($shipChangeArr, $itemShipping->ship_change);
                                            if ($itemShipping->shipping_truck != null) {
                                                array_push($shipTruckArr, $itemShipping->shipping_truck);
                                            }
                                        @endphp
                                    @endif
                                @endforeach
                            @endif
                            @php
                                $index = array_search($item->material_list_id, $shipMatArr);
                            @endphp
                            <div class="row mb-2 border border-1 p-2">
                                <div class="col-md-12">
                                    <label class="list-group-item d-flex align-items-center">
                                        <input type="hidden"
                                            {{ in_array($item->material_list_id, $shipMatArr) && $item->MaterialList != null && $shipChangeArr[$index] == 1 ? '' : 'disabled' }}
                                            value="{{ $var->id }}" name="construction_shipping_id"
                                            id="construction_shipping_id{{ $item->id }}">
                                        <input type="hidden" {{ $shipChangeArr[$index] == 0 ? 'disabled' : '' }}
                                            {{ in_array($item->material_list_id, $shipMatArr) && $item->MaterialList != null && $shipChangeArr[$index] == 1 ? '' : 'disabled' }}
                                            value="0" id="shipchange{{ $item->id }}" name="shipchange[]">
                                        <input class="form-check-input me-2"
                                            {{ in_array($item->material_list_id, $shipMatArr) && $item->MaterialList != null && $shipChangeArr[$index] == 1 ? 'checked' : '' }}
                                            type="checkbox" id="matlistid{{ $item->id }}"
                                            onchange="matlistId('{{ $item->id }}')" value="{{ $item->id }}"
                                            name="centri_material_id[]">
                                        {{ $item->MaterialList == null ? '' : $item->MaterialList->name }} -
                                        <span class="text-muted">
                                            {{ $item->MaterialList == null ? '' : $item->MaterialList->MaterialTypeBelongs->name }}
                                        </span>
                                        @if ($index)
                                        {{-- updating --}}
                                            <div class="ms-auto d-flex align-items-center">
                                                <span class="me-1 text-muted"> Consegnati </span> <strong
                                                    class="me-2 text-muted">
                                                    {{ $shipQtyArr[$index] }}</strong>
                                                <input
                                                    oninput="checkMatLimitUpdating('{{ in_array($item->material_list_id, $shipMatArr) ? $shipRemQtyArr[$index] : $item->quantity }}', this.value, '{{ $item->id }}', {{$shipQtyArr[$index]}})"
                                                    class="form-control text-center" id="matlist{{ $item->id }}"
                                                    type="number" min=""
                                                    max="{{ in_array($item->material_list_id, $shipMatArr) ? $shipRemQtyArr[$index] : $item->quantity }}"
                                                    value="{{ in_array($item->material_list_id, $shipMatArr) ? $shipRemQtyArr[$index] : $item->quantity }}"
                                                    name="qty[]" {{ $shipChangeArr[$index] == 0 ? 'disabled' : '' }}
                                                    {{ in_array($item->material_list_id, $shipMatArr) && $item->MaterialList != null && $shipChangeArr[$index] == 1 ? '' : 'disabled' }}>
                                            </div>
                                        @else
                                       {{-- new --}}
                                            <span class="ms-auto">
                                                <input
                                                    oninput="checkMatLimit('{{ $item->quantity }}', this.value, '{{ $item->id }}')"
                                                    class="form-control text-center" id="matlist{{ $item->id }}"
                                                    type="number" min="" max="{{ $item->quantity }}"
                                                    value="{{ $item->quantity }}" name="qty[]"
                                                    {{ $shipChangeArr[$index] == 0 ? 'disabled' : '' }}
                                                    {{ in_array($item->material_list_id, $shipMatArr) && $item->MaterialList != null && $shipChangeArr[$index] == 1 ? '' : 'disabled' }}>
                                            </span>
                                        @endif

                                    </label>
                                </div>
                            </div>
                        @empty
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="list-group">
                                        <span class="text-danger">Non hai materiale per
                                            <strong>{{ $var->ConstructionSite->name }}
                                                {{ $var->ConstructionSite->surename }}</strong></span>
                                    </div>
                                </div>
                            </div>
                        @endforelse
                    </div>
                    <div class="card-footer">
                        <div class="tab-footer text-start mb-2">
                            <input class="form-control" type="text" placeholder="Mezzo di trasporto. Es. Vito, Caputo, Campanella, Camion Grotto"
                                name="shipping_truck"
                                value="{{ $shipTruckArr != null ? (isset($shipTruckArr[1]) ? $shipTruckArr[1] : '') : '' }}"
                                required>
                        </div>

                        <div class="tab-footer text-end">
                            <button type="button" class="step-btn previous" onclick="showStep1()">Indietro</button>
                            <button type="submit" name="" class="step-btn save">Aggiungi</button>

                            <button type="button" data-bs-target="_blank" class="step-btn save"
                                onclick="location.href='{{ route('print_shipping') }}'">VEDI ANTEPRIMA</button>
                        </div>
                    </div>
                </div>
            </form>
        @else
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="list-group">
                                <span class="text-danger">Nessun materiale per questo 
                                    <strong>{{ $var->ConstructionSite->name }}</strong>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @else
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="list-group">
                            <span class="text-danger">Nessun cantiere selezionato</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@else
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="list-group">
                        <span class="text-danger">Nessuna spedizione creata</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
