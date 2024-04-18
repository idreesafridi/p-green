<div class="card-head d-flex align-items-center justify-content-between" id="building-site">
    <h3 class="mb-0 d-inline-block">Dati Immobile</h3>

    @php

        $condo_id = $builddata->ConstructionCondominiMain($builddata->id);
        $condo_id_parent = $builddata->ConstructionCondominiParent($builddata->id);
    @endphp


    {{-- @if(ucwords(strtolower($builddata->ConstructionSiteSetting->type_of_property)) == 'Condominio')
    <button onclick="getCondomini('{{ $builddata->id }}')" type="button" class="btn btn-outline-green"
        data-bs-toggle="modal" data-bs-target="#condomini-popup">
        <i class="fa fa-building me-2" aria-hidden="true"></i>Vedi Condomini
    </button>
    @elseif ($condo_id &&  ucwords(strtolower($builddata->ConstructionSiteSetting->type_of_property)) != 'Condominio')
        <a href="{{ route('construction_detail', ['id' => $condo_id->construction_site_id, 'pagename' => 'Cantiere']) }}"
            class="btn btn-outline-green"><i class="fa fa-users me-2" aria-hidden="true"></i>
            VAI A CONDOMINIO PRINCIPALE</a>
    @endif --}}

        {{-- @if($condo_id)
        @dd($condo_id->ConstructionSiteSettingforParent)
        @endif   --}}
            
   @if ($condo_id &&  ucwords(strtolower($builddata->ConstructionSiteSetting->type_of_property)) != 'Condominio' && ucwords(strtolower($condo_id->ConstructionSiteSettingforParent->type_of_property))== 'Condominio')
    <a href="{{ route('construction_detail', ['id' => $condo_id->construction_site_id, 'pagename' => 'Cantiere']) }}"
        class="btn btn-outline-green"><i class="fa fa-users me-2" aria-hidden="true"></i>
        VAI A CONDOMINIO PRINCIPALE</a>
     @elseif(ucwords(strtolower($builddata->ConstructionSiteSetting->type_of_property)) == 'Condominio')
    <button onclick="getCondomini('{{ $builddata->id }}')" type="button" class="btn btn-outline-green"
        data-bs-toggle="modal" data-bs-target="#condomini-popup">
        <i class="fa fa-building me-2" aria-hidden="true"></i>Vedi Condomini
    </button>
  @endif

</div>
<div class="card-body p-0">
    <form action="{{ route('construction_update_building', $builddata->id) }}" method="post">
        @csrf
        @method('put')
        <ul class="resume-box">
            <li>
                <div class="icon text-center">
                    <i class="fa fa-user icon-fixed"></i>
                </div>
                <div class="d-flex flex-row flex-wrap align-items-center mb-3 mt-2">
                    <div class="me-3 me-md-5">
                        <small class="text-muted">Codice POD</small>
                        <div>
                            <input class="mb-0 bg-white" type="text"
                                value="{{ $builddata->PropertyData == null ? '' : $builddata->PropertyData->pod_code }}"
                                name="pod_code" disabled="disabled">
                        </div>
                    </div>

                    <div class="me-3 me-md-5">
                        <div class="col-12">
                            <div class="badge-div">
                                @php
                                    $typeofdeduction = explode(',', $builddata->ConstructionSiteSetting == null ? '' : $builddata->ConstructionSiteSetting->type_of_deduction);
                                @endphp

                                @foreach ($typeofdeduction as $item)
                                    @if (strtolower($item) != 'fotovoltaico')
                                        <span
                                            class="badge bg-dark {{ $item == null ? 'd-none' : '' }}">{{ $item }}%</span>
                                    @else
                                        <br /><span class="badge bg-blue w-100">Fotovoltaico</span>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="me-3 me-md-5">
                        <div class="col-12">
                            <div class="input-group mt-2">
                                <div class="input-group-text my-1">
                                    <input name="type_of_deduction[]" value="110"
                                        {{ in_array('110', $typeofdeduction) ? 'checked' : '' }}
                                        class="form-check-input mt-0 bg-white" type="checkbox">&nbsp;110%
                                </div>
                                &nbsp;
                                <div class="input-group-text my-1">
                                    <input name="type_of_deduction[]" value="50"
                                        {{ in_array('50', $typeofdeduction) ? 'checked' : '' }}
                                        class="form-check-input mt-0 bg-white" type="checkbox">&nbsp;50%
                                </div>
                                &nbsp;
                                <div class="input-group-text my-1">
                                    <input name="type_of_deduction[]" value="65"
                                        {{ in_array('65', $typeofdeduction) ? 'checked' : '' }}
                                        class="form-check-input mt-0 bg-white" type="checkbox">&nbsp;65%
                                </div>
                                &nbsp;
                                <div class="input-group-text my-1">
                                    <input name="type_of_deduction[]" value="90"
                                        {{ in_array('90', $typeofdeduction) ? 'checked' : '' }}
                                        class="form-check-input mt-0 bg-white" type="checkbox">&nbsp;90%
                                </div>
                                &nbsp;
                                <div class="input-group-text my-1">
                                    <input name="type_of_deduction[]" value="Fotovoltaico"
                                        {{ in_array('Fotovoltaico', $typeofdeduction) ? 'checked' : '' }}
                                        class="form-check-input mt-0 bg-white" type="checkbox">&nbsp;Fotovoltaico
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            <li>
                <div class="icon text-center">
                    <i class="fa fa-map-marker icon-fixed"></i>
                </div>
                <div class="fw-medium mb-0">Dettagli immobile</div>
                <div class="d-flex flex-row flex-wrap align-items-center mb-3 mt-2">
                    <div class="me-3 me-md-5">
                        <small class="text-muted">Via</small>
                        <div class="w-250 immobile_Via">
                            <input class="mb-0 bg-white w-100" type="text"
                                value="{{ $builddata->PropertyData == null ? '' : $builddata->PropertyData->property_street }}"
                                name="property_street" disabled="disabled">
                        </div>
                    </div>
                    <div class="me-3 me-md-5">
                        <small class="text-muted">Numero civico</small>
                        <div>
                            <input class="mb-0 bg-white" type="text"
                                value="{{ $builddata->PropertyData == null ? '' : $builddata->PropertyData->property_house_number }}"
                                name="property_house_number" disabled="disabled">
                        </div>
                    </div>
                    <div class="me-3 me-md-5">
                        <small class="text-muted">Comune</small>
                        <div>
                            <input class="mb-0 bg-white" type="text"
                                value="{{ $builddata->PropertyData == null ? '' : $builddata->PropertyData->property_common }}"
                                name="property_common" disabled="disabled">
                        </div>
                    </div>
                    <div class="me-3 me-md-5">
                        <small class="text-muted">CAP</small>
                        <div>
                            <input class="bg-white" type="text"
                                value="{{ $builddata->PropertyData == null ? '' : $builddata->PropertyData->property_postal_code }}"
                                name="property_postal_code" disabled="disabled">
                        </div>
                    </div>
                    <div>
                        <small class="text-muted">Provincia</small>
                        <div>
                            <input class="bg-white" type="text"
                                value="{{ $builddata->PropertyData == null ? '' : $builddata->PropertyData->property_province }}"
                                name="property_province" disabled="disabled">
                        </div>
                    </div>
                </div>
            </li>
            <li>
                <div class="d-flex flex-row flex-wrap align-items-center mb-3 mt-2">
                    <div class="me-3 me-md-5">
                        <small class="text-muted">Sezione catastale</small>
                        <div>
                            <input class="mb-0 bg-white" type="text"
                                value="{{ $builddata->PropertyData == null ? '' : $builddata->PropertyData->cadastral_dati }}"
                                name="cadastral_dati" disabled="disabled">
                        </div>
                    </div>
                    <div class="me-3 me-md-5">
                        <small class="text-muted">Categoria catastale</small>
                        <div class="w-50">
                            <option value=""></option>
                            <select name="cadastral_category" class="bg-white w-100" disabled="disabled">
                                <option value=""></option>
                                <option value="A/1"
                                    {{ ($builddata->PropertyData == null ? '' : $builddata->PropertyData->cadastral_category == 'A/1') ? 'selected' : '' }}>
                                    A/1
                                </option>
                                <option value="A/2"
                                    {{ ($builddata->PropertyData == null ? '' : $builddata->PropertyData->cadastral_category == 'A/2') ? 'selected' : '' }}>
                                    A/2
                                </option>
                                <option value="A/3"
                                    {{ ($builddata->PropertyData == null ? '' : $builddata->PropertyData->cadastral_category == 'A/3') ? 'selected' : '' }}>
                                    A/3
                                </option>
                                <option value="A/4"
                                    {{ ($builddata->PropertyData == null ? '' : $builddata->PropertyData->cadastral_category == 'A/4') ? 'selected' : '' }}>
                                    A/4
                                </option>
                                <option value="A/5"
                                    {{ ($builddata->PropertyData == null ? '' : $builddata->PropertyData->cadastral_category == 'A/5') ? 'selected' : '' }}>
                                    A/5
                                </option>
                                <option value="A/6"
                                    {{ ($builddata->PropertyData == null ? '' : $builddata->PropertyData->cadastral_category == 'A/6') ? 'selected' : '' }}>
                                    A/6
                                </option>
                                <option value="A/7"
                                    {{ ($builddata->PropertyData == null ? '' : $builddata->PropertyData->cadastral_category == 'A/7') ? 'selected' : '' }}>
                                    A/7
                                </option>
                                <option value="A/8"
                                    {{ ($builddata->PropertyData == null ? '' : $builddata->PropertyData->cadastral_category == 'A/8') ? 'selected' : '' }}>
                                    A/8
                                </option>
                                <option value="A/9"
                                    {{ ($builddata->PropertyData == null ? '' : $builddata->PropertyData->cadastral_category == 'A/9') ? 'selected' : '' }}>
                                    A/9
                                </option>
                                <option value="A/10"
                                    {{ ($builddata->PropertyData == null ? '' : $builddata->PropertyData->cadastral_category == 'A/10') ? 'selected' : '' }}>
                                    A/10
                                </option>
                                <option value="A/11"
                                    {{ ($builddata->PropertyData == null ? '' : $builddata->PropertyData->cadastral_category == 'A/11') ? 'selected' : '' }}>
                                    A/11
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="me-3 me-md-5">
                        <small class="text-muted">Foglio</small>
                        <div>
                            <input class="mb-0 bg-white" type="text"
                                value="{{ $builddata->PropertyData == null ? '' : $builddata->PropertyData->cadastral_section }}"
                                name="cadastral_section" disabled="disabled">
                        </div>
                    </div>
                    <div class="me-3 me-md-5">
                        <small class="text-muted">Particella</small>
                        <div>
                            <input class="bg-white" type="text"
                                value="{{ $builddata->PropertyData == null ? '' : $builddata->PropertyData->cadastral_particle }}"
                                name="cadastral_particle" disabled="disabled">
                        </div>
                    </div>
                    <div>
                        <small class="text-muted">Subalterni</small>
                        <div>
                            <input class="bg-white" type="text"
                                value="{{ $builddata->PropertyData == null ? '' : $builddata->PropertyData->sub_ordinate }}"
                                name="sub_ordinate" disabled="disabled">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <small class="text-muted">Piano</small>
                      
                        <div class="Piano">
                            <select class="form-control bg-white w-100 select2" multiple disabled="disabled" id="Piano" name="Piano[]">
                                <option value="S1" {{ in_array('S1', $builddata->PropertyData->Piano ? explode(',', $builddata->PropertyData->Piano) : []) ? 'selected' : '' }}>S1</option>
                                <option value="T" {{ in_array('T', $builddata->PropertyData->Piano ? explode(',', $builddata->PropertyData->Piano) : []) ? 'selected' : '' }}>T</option>
                                <option value="R" {{ in_array('R', $builddata->PropertyData->Piano ? explode(',', $builddata->PropertyData->Piano) : []) ? 'selected' : '' }}>R</option>
                                <option value="P1" {{ in_array('P1', $builddata->PropertyData->Piano ? explode(',', $builddata->PropertyData->Piano) : []) ? 'selected' : '' }}>P1</option>
                                <option value="P2" {{ in_array('P2', $builddata->PropertyData->Piano ? explode(',', $builddata->PropertyData->Piano) : []) ? 'selected' : '' }}>P2</option>
                                <option value="P3" {{ in_array('P3', $builddata->PropertyData->Piano ? explode(',', $builddata->PropertyData->Piano) : []) ? 'selected' : '' }}>P3</option>
                                <option value="P4" {{ in_array('P4', $builddata->PropertyData->Piano ? explode(',', $builddata->PropertyData->Piano) : []) ? 'selected' : '' }}>P4</option>
                            </select>
                        </div>
                        
                        
                    </div>
                </div>
            </li>
            <li>
                <div class="icon text-center">
                    <i class="fa fa-info icon-fixed"></i>
                </div>
                <div class="d-flex flex-row flex-wrap align-items-center mb-3 mt-2">
                    <div class="me-3 me-md-5">
                        <small class="text-muted">Tipologia Immobile</small><br>
                        <div>
                            <select name="type_of_property" class="form-control bg-white">
                                <option selected="">Seleziona</option>
                                <option value="Condominio"
                                    {{ ($builddata->ConstructionSiteSetting == null ? '' : ucwords(strtolower($builddata->ConstructionSiteSetting->type_of_property)) == 'Condominio') ? 'selected' : '' }}>
                                    Condominio</option>
                                <option value="Unifamiliare"
                                    {{ ($builddata->ConstructionSiteSetting == null ? '' : ucwords(strtolower($builddata->ConstructionSiteSetting->type_of_property)) == 'Unifamiliare') ? 'selected' : '' }}>
                                    Unifamiliare</option>
                                <option value="Plurifamiliare"
                                    {{ ($builddata->ConstructionSiteSetting == null ? '' : ucwords(strtolower($builddata->ConstructionSiteSetting->type_of_property)) == 'Plurifamiliare') ? 'selected' : '' }}>
                                    Plurifamiliare</option>
                            </select>
                        </div>
                    </div>
                    <div class="me-3 me-md-5">
                        <small class="text-muted">Tipologia cantiere</small><br>
                        <div>
                            <select class="form-control bg-white" name="type_of_construction" disabled="disabled">
                                <option selected=""></option>
                                <option value="0"
                                    {{ ($builddata->ConstructionSiteSetting == null ? '' : $builddata->ConstructionSiteSetting->type_of_construction == '0') ? 'selected' : '' }}>
                                    Interno
                                </option>
                                <option value="1"
                                    {{ ($builddata->ConstructionSiteSetting == null ? '' : $builddata->ConstructionSiteSetting->type_of_construction == '1') ? 'selected' : '' }}>
                                    Esterno</option>
                            </select>
                        </div>
                    </div>
                </div>
            </li>
            <li>
                <div class="icon text-center">
                    <i class="fa fa-truck icon-fixed"></i>
                </div>
                <div class="fw-medium mb-0">Dettagli Lavori</div>
                <div class="d-flex flex-row flex-wrap align-items-center mb-3 mt-2">
                    <div class="me-3 me-md-5">
                        <small class="text-muted">Impresa Infissi</small><br>
                        <div class="w-300 Lavori_select">
                            <select class="bg-white w-100 select2" disabled="disabled" name="fixtures">
                                <option value=""></option>

                                @foreach ($fixtures as $fixtures_item)
                                    <option value="{{ $fixtures_item->id }}"
                                        {{ $builddata->ConstructionJobDetail != null ? ($builddata->ConstructionJobDetail->fixtures == $fixtures_item->id ? 'selected' : '') : '' }}>
                                        {{ $fixtures_item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-0 fw-medium">
                            <span>€ </span>
                            <input type="number" name="fixtures_company_price"
                                value="{{ $builddata->ConstructionJobDetail != null ? $builddata->ConstructionJobDetail->fixtures_company_price : '' }}"
                                class="w-50 bg-white">
                        </div>
                    </div>
                    <div class="me-3 me-md-5">
                        <small class="text-muted">Impresa Impianti Idraulico</small><br>
                        <div class="w-250 Lavori_select">
                            <select class="bg-white w-100 select2"  disabled="disabled" name="plumbing">
                                <option value=""></option>
                                @foreach ($plumbing as $plum_item)
                                    <option value="{{ $plum_item->id }}"
                                        {{ $builddata->ConstructionJobDetail != null ? ($builddata->ConstructionJobDetail->plumbing == $plum_item->id ? 'selected' : '') : '' }}>
                                        {{ $plum_item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-0 fw-medium">
                            <span>€ </span>
                            <input type="number" name="plumbing_company_price"
                                value="{{ $builddata->ConstructionJobDetail != null ? $builddata->ConstructionJobDetail->plumbing_company_price : '' }}"
                                class="w-50 bg-white">
                        </div>
                    </div>
                    <div class="me-3 me-md-5">
                        <small class="text-muted">Impresa Impianti Elettrico</small><br>
                        <div class="w-200 Lavori_select">
                            <select class="bg-white w-100 select2" disabled="disabled" name="electrical">
                                <option value=""></option>
                                @foreach ($electrician as $electrician_item)
                                    <option value="{{ $electrician_item->id }}"
                                        {{ $builddata->ConstructionJobDetail != null ? ($builddata->ConstructionJobDetail->electrical == $electrician_item->id ? 'selected' : '') : '' }}>
                                        {{ $electrician_item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-0 fw-medium">
                            <span>€ </span>
                            <input type="number" name="electrical_installations_company_price"
                                value="{{ $builddata->ConstructionJobDetail != null ? $builddata->ConstructionJobDetail->electrical_installations_company_price : '' }}"
                                class="w-50 bg-white" disabled="disabled">
                        </div>
                    </div>
                    <div class="me-3 me-md-5">
                        <small class="text-muted">Impresa Edile 1</small><br>
                        <div class="w-230 Lavori_select">
                            <select class="bg-white w-100 select2" disabled="disabled" name="construction">
                                <option value=""></option>
                                @foreach ($construction as $construction_item)
                                    <option value="{{ $construction_item->id }}"
                                        {{ $builddata->ConstructionJobDetail != null ? ($builddata->ConstructionJobDetail->construction == $construction_item->id ? 'selected' : '') : '' }}>
                                        {{ $construction_item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-0 fw-medium">
                            <span>€ </span>
                            <input type="number" name="construction_company1_price"
                                value="{{ $builddata->ConstructionJobDetail != null ? $builddata->ConstructionJobDetail->construction_company1_price : '' }}"
                                class="w-50 bg-white" disabled="disabled">
                        </div>
                    </div>
                    <div class="me-3 me-md-5">
                        <small class="text-muted">Impresa Edile 2</small><br>
                        <div class="w-230 Lavori_select">
                            <select class="bg-white w-100 select2" disabled="disabled" name="construction2">
                                <option value=""></option>
                                @foreach ($construction as $construction2_item)
                                    <option value="{{ $construction2_item->id }}"
                                        {{ $builddata->ConstructionJobDetail != null ? ($builddata->ConstructionJobDetail->construction2 == $construction2_item->id ? 'selected' : '') : '' }}>
                                        {{ $construction2_item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-0 fw-medium">
                            <span>€ </span>
                            <input type="number" name="construction_company2_price"
                                value="{{ $builddata->ConstructionJobDetail != null ? $builddata->ConstructionJobDetail->construction_company2_price : '' }}"
                                class="w-50 bg-white" disabled="disabled">
                        </div>
                    </div>
                    <div class="me-3 me-md-5">
                        <small class="text-muted">Fotovoltaico</small><br>
                        <div class="w-100">
                            <select class="bg-white w-75 select2" name="photovoltaic" disabled="disabled">
                                <option value=""></option>
                                @foreach ($photovoltaic as $photovoltaic_item)
                                    <option value="{{ $photovoltaic_item->id }}"
                                        {{ $builddata->ConstructionJobDetail != null ? ($builddata->ConstructionJobDetail->photovoltaic == $photovoltaic_item->id ? 'selected' : '') : '' }}>
                                        {{ $photovoltaic_item->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-0 fw-medium">
                            <span>€ </span>
                            <input type="number" name="photovoltaic_price"
                                value="{{ $builddata->ConstructionJobDetail != null ? $builddata->ConstructionJobDetail->photovoltaic_price : '' }}"
                                class="w-50 bg-white" disabled="disabled">
                        </div>
                    </div>
                 
                    <div class="me-3 me-md-5">
                       
                        <small class="text-muted">Coordinatore</small><br>
                        <div class="w-230 Lavori_select">
                            <select class="bg-white w-100 select2" disabled="disabled" name="coordinator">
                                <option value=""></option>
                              
                                @foreach ($tech as $tech_item)
                                    <option value="{{ $tech_item->id }}"
                                        {{ $builddata->ConstructionJobDetail != null ? ($builddata->ConstructionJobDetail->coordinator == $tech_item->id ? 'selected' : '') : '' }}>
                                        {{ $tech_item->name }}
                        
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="me-3 me-md-5">
                        <small class="text-muted">Direttore dei lavori</small>
                        <i class="fa fa-star p-1 text-muted"></i>
                        <div class="w-230 Lavori_select">
                            <select class="bg-white w-100 select2" disabled="disabled" name="construction_manager">
                                <option value=""></option>
                                @foreach ($tech as $tech_item2)
                                    <option value="{{ $tech_item2->id }}"
                                        {{ $builddata->ConstructionJobDetail != null ? ($builddata->ConstructionJobDetail->construction_manager == $tech_item2->id ? 'selected' : '') : '' }}>
                                        {{ $tech_item2->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
        <div class="d-flex justify-content-end">
            @if ($builddata->archive == 0 || $builddata->archive == null)
                <button type="button" class="btn btn-outline-green m-1"
                    onclick="location.href='{{ route('set_archive', ['id' => $builddata->id, 'archive' => 1]) }}'">
                    <strong><i class="fa fa-inbox me-2"></i>Archivia</strong>
                </button>
            @else
                <button type="button" class="btn btn-outline-warning m-1"
                    onclick="location.href='{{ route('set_archive', ['id' => $builddata->id, 'archive' => 0]) }}'">
                    <strong><i class="fa fa-inbox me-2"></i>Extract</strong>
                </button>
            @endif

            <button type="button" class="btn btn-green m-1 edit">
                <strong><i class="fa fa-pencil me-2"></i>Modifica</strong>
            </button>
            <button type="submit" class="btn btn-outline-green m-1 save" disabled="disabled">
                <strong><i class="fa fa-check me-2"></i>Salva</strong>
            </button>
        </div>
    </form>
</div>
{{-- @dd($builddata) --}}
<!-- Condomini-popup Modal -->
<div class="modal fade" id="condomini-popup" aria-labelledby="exampleModalLabel" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-dialog-scrollable modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Lista condomini</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 w-100">
                        <thead>
                            <tr>
                                <th>NOME</th>
                                <th>COMUNE</th>
                                <th>Via</th>
                                <th>Tecnico</th>
                                <th>Documenti</th>
                                <th>STATO</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="condomini_response_data"></tbody>
                    </table>
                </div>
                <div class="d-flex mt-3">
                    <a class="btn btn-link border-0 me-3 w-50"
                        href="{{ route('shipyard_store', ['id' => $builddata->id]) }}" target="_blank"><i
                            class="fa fa-plus fa-1"></i> {{ __('Aggiungi condomino') }}</a>
                    oppure
                    <form class=" w-50" method="post" action="{{ route('condo_store') }}" id="condo_store_form">
                        @csrf
                       
                        <input type="hidden" name="fk_id" value="{{ $builddata->id }}">

                        <select class="btn btn-link1" name="con_condo_id" id="con_condo_id"
                            onchange="$('#condo_store_form').submit()">
                            <option selected disabled>Seleziona cantiere</option>
                         
                            @foreach ($condoList as $condoItem)
                          
                                <option value="{{ $condoItem->id }}">{{ $condoItem->name }}      {{ $condoItem->surename }} &nbsp;  &nbsp;  {{ $condoItem->PropertyData->property_street ?  "(" . optional($condoItem->PropertyData)->property_street  . ' ' . optional($condoItem->PropertyData)->property_house_number  . ' ' . optional($condoItem->PropertyData)->property_postal_code   .  ")" .'  '. optional($condoItem->PropertyData)->sub_ordinate   :  ''}} 
                                  
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
            </div>
        </div>
    </div>
</div>

<!-- End Condomini-popup Modal -->

@section('scripts')
    <script>    
    document.addEventListener('DOMContentLoaded', function() {
    var selectElement = document.querySelector('select[name="Piano[]"]');
    
    // Get the stored values from the database (assuming stored as JSON array)
    var storedValues = {!! json_encode($builddata->PropertyData->Piano ?? []) !!};
    
    // Loop through each option and mark as selected if it exists in the stored values
    Array.from(selectElement.options).forEach(function(option) {
        if (storedValues.includes(option.value)) {
            option.selected = true;
        }
    });
});



        $(document).ready(function() {
            $('#con_condo_id').select2({
                dropdownParent: $('#condomini-popup')
            });

            $('.save').attr("disabled", "disabled");
            $('.resume-box .input-group').addClass('d-none');
            $('.edit').click(function() {
                $(this).addClass('bg-orange');
                $('.resume-box input').removeAttr('disabled');
                $('.resume-box select').removeAttr('disabled');
                $('.save').removeAttr('disabled');
                $('.resume-box input').addClass('bg-light');
                $('.resume-box .input-group').removeClass('d-none');
                $('.resume-box .badge-div').addClass('d-none');
                $('.resume-box select').addClass('bg-light');
            });
        });

        function getCondomini(id) {
            $.ajax({
                method: "post",
                url: "{{ route('getCondomini') }}",
                data: {
                    'id': id,
                    "_token": '{{ csrf_token() }}'
                },
                success: function(response) {
                    $('#condomini_response_data').html(response)
                }
            });
        }
    </script>
@endsection
