<div class="row mb-3">
    @if($modelName == 'ConstructionMaterial')
        @if(in_array('material_list_id', $columnName))
        @php
            $matList = \App\Models\MaterialList::all();
        @endphp
            <div class="col-md-3">
                <select class="text-dark w-200 select2" name="material_list_id" onchange="getMatReports('material_list_id', this.value)">
                    <option value="">Seleziona materiale</option>
                    @foreach ($matList as $mat)
                        <option value="{{$mat->id}}">{{$mat->name}}</option>
                    @endforeach
                </select>
            </div>
        @endif
        @if(in_array('state', $columnName))
            <div class="col-md-3">
                <select class="text-dark w-200" name="state" onchange="getMatReports('state', this.value)">
                    <option value="">Stato da selezionare</option>
                    <option value="Da bollettare">Da bollettare</option>
                    <option value="Consegna parziale">Consegna parziale</option>
                    <option value="Consegna diretta">Consegna diretta</option>
                    <option value="Bollettato">Bollettato</option>
                </select>
            </div>
        @endif
        @if(in_array('consegnato', $columnName))
            <div class="col-md-3">
                <select class="text-dark w-200" name="consegnato" onchange="getMatReports('consegnato', this.value)">
                    <option value="">Consegnato</option>
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </div>
        @endif
        @if(in_array('montato', $columnName))
            <div class="col-md-3">
                <select class="text-dark w-200" name="montato" onchange="getMatReports('montato', this.value)">
                    <option value="">Montato</option>
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </div>
        @endif
        @if(in_array('quantity', $columnName))
            <div class="col-md-3">
                <input type="number" name="quantity" placeholder="Cerca Quantità" id="mat_search_filter" class="text-dark w-200" oninput="getMatReports('quantity', this.value)">
            </div>
        @endif
    @elseif ($modelName == 'ConstructionJobDetail')
        @if($columnName != null)
        <div style="display: flex; flex-wrap: wrap;">
            @if(in_array('fixtures', $columnName))
                <div class="me-3 mb-3">
                    <small class="text-muted">Impresa Infissi</small><br>
                    <div class="w-200">
                        <select class="bg-white w-100 select2" onchange="getJobReports('fixtures', this.value)">
                            <option value=""></option>
                            @foreach ($filter['fixtures'] as $item)
                                <option value="{{ $item->id }}">
                                    {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endif
            @if(in_array('plumbing', $columnName))
                <div class="me-3 mb-3">
                    <small class="text-muted">Impresa Impianti Idraulico</small><br>
                    <div class="w-200">
                        <select class="bg-white w-100 select2" onchange="getJobReports('plumbing', this.value)">
                            <option value=""></option>
                            @foreach ($filter['plumbing'] as $item)
                                <option value="{{ $item->id }}">
                                    {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endif
            @if(in_array('electrical', $columnName))
                <div class="me-3 mb-3">
                    <small class="text-muted">Impresa Impianti Elettrico</small><br>
                    <div class="w-200">
                        <select class="bg-white w-100 select2" onchange="getJobReports('electrical', this.value)">
                            <option value=""></option>
                            @foreach ($filter['electrician'] as $item)
                                <option value="{{ $item->id }}">
                                    {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endif
            @if(in_array('construction', $columnName))
                <div class="me-3 mb-3">
                    <small class="text-muted">Impresa Edile 1</small><br>
                    <div class="w-200">
                        <select class="bg-white w-100 select2" onchange="getJobReports('construction', this.value)">
                            <option value=""></option>
                            @foreach ($filter['construction'] as $item)
                                <option value="{{ $item->id }}">
                                    {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endif
            @if(in_array('construction2', $columnName))
                <div class="me-3 mb-3">
                    <small class="text-muted">Impresa Edile 2</small><br>
                    <div class="w-200">
                        <select class="bg-white w-100 select2" onchange="getJobReports('construction2', this.value)">
                            <option value=""></option>
                            @foreach ($filter['construction'] as $item)
                                <option value="{{ $item->id }}">
                                    {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endif
            @if(in_array('photovoltaic', $columnName))
                <div class="me-3 mb-3">
                    <small class="text-muted">Fotovoltaico</small><br>
                    <div class="w-200">
                        <select class="bg-white w-100 select2" onchange="getJobReports('photovoltaic', this.value)">
                            <option value=""></option>
                            @foreach ($filter['photovoltaic'] as $item)
                                <option value="{{ $item->id }}">
                                    {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endif
            @if(in_array('coordinator', $columnName))
                <div class="me-3 mb-3">
                    <small class="text-muted">Coordinatore</small><br>
                    <div class="w-200">
                        <select class="bg-white w-100 select2" onchange="getJobReports('coordinator', this.value)">
                            <option value=""></option>
                            @foreach ($filter['tech'] as $item)
                                <option value="{{ $item->id }}">
                                    {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endif
            @if(in_array('construction_manager', $columnName))
                <div class="me-3 mb-3">
                    <small class="text-muted">Direttore dei lavori</small><br>
                    <div class="w-200">
                        <select class="bg-white w-100 select2" onchange="getJobReports('construction_manager', this.value)">
                            <option value=""></option>
                            @foreach ($filter['tech'] as $item)
                                <option value="{{ $item->id }}">
                                    {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            @endif
        </div>
        @endif
    @else
        @if($columnName != null)
            <div class="col-md-10">
                <input type="text" name="search_filter" placeholder="Enter your keyword for filter result" id="search_filter" class="form-control-sm" oninput="search_filter(this.value, '{{$modelName}}')">
            </div>
        @endif
    @endif

    <div class="col-md-2">
        <a href="javascript:void(0);" onclick="printPageArea()" target="_blank" rel="noopener noreferrer" class="btn btn-success btn-sm"> <i class="fa fa-print"></i> Print</a>
    </div>
</div>