<label class="col-form-label">Tipo</label>
{{-- @dd($materialTypeList); --}}
<select required name="material_type_id" id="material_type_id" class="form-control">
    <option selected disabled>Seleziona tipo materiale</option>
    <optgroup label="Cappotto">
        @foreach ($materialTypeList['Cappotto']->MaterialType as $cappotto)
            <option value="{{ $cappotto->id }}">{{ $cappotto->name }}</option>
        @endforeach
    </optgroup>

    <optgroup label="Termico">
        @foreach ($materialTypeList['Termico']->MaterialType as $termico)
            <option value="{{ $termico->id }}">{{ $termico->name }}</option>
        @endforeach
    </optgroup>

    <optgroup label="Infissi">
        @foreach ($materialTypeList['Infissi']->MaterialType as $Infissi)
            <option value="{{ $Infissi->id }}">{{ $Infissi->name }}</option>
        @endforeach
    </optgroup>
   
    <optgroup label="Fotovoltaico">
        @foreach ($materialTypeList['Fotovoltaico']->MaterialType as $fotovoltaico)
            @if ($fotovoltaico->id ==14 || $fotovoltaico->id ==15 || $fotovoltaico->id ==16 || $fotovoltaico->id ==17 || $fotovoltaico->id ==30)
                <option value="{{ $fotovoltaico->id }}">{{ $fotovoltaico->name }}</option>
            @endif
        @endforeach
    </optgroup>

    <optgroup label="Veicolo">
        @foreach ($materialTypeList['Veicolo']->MaterialType as $veicolo)
            <option value="{{ $veicolo->id }}">{{ $veicolo->name }}</option>
        @endforeach
    </optgroup>
</select>
