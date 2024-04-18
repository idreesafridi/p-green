<div style="background-color: #fff; position: relative;">
    <div class="container mt-3">
        @if(count($updatedData) > 0)
        <div class= "mb-2" id="errorContainer" style="color: red; display: none;"></div>
        <table class="table table-striped table-responsive">
            <thead>
                <tr style="background-color: #020202;">
                    <th class="text-white">prima di modifica</th>
                    <th class="text-white">dopo modifica</th>
                    <th class="text-white">motivazione</th>
                </tr>
            </thead>
            <tbody>
                   
                @foreach($updatedData as $index => $row)
                <tr>
                    <input type="hidden"  name="construction_site_id_history[]"  value="{{ $row['construction_site_id'] }}">
                    <input type="hidden"  name="material_id_history[]"  value="{{ $row['material_id'] }}">
                    <input type="hidden"  name="changeBy_history[]"  value="{{ $row['changeBy'] }}">
                    <input type="hidden"  name="updatedField_history[]"  value="{{ $row['updated_field'] }}">
                    <input type="hidden"  name="Original_history[]"  value="{{ $row['Original'] }}">
                    <input type="hidden"  name="Updated_to_history[]"  value="{{ $row['Updated_to'] }}">
                

                    <td>
                        @if($row['Original'] != null)
                        {{ strtoupper($row['updated_field']) }} = {{ $row['Original'] }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if($row['Updated_to'] != null)
                        {{ strtoupper($row['updated_field']) }} =  {{ $row['Updated_to'] }}
                        @else
                        -
                       @endif
                    </td>
                        <td>
                        <select class="form-select" name="reason[]" id="motivationSelect-{{ $index }}" onchange="handleDropdownChange('{{ $index }}')" oninput="updateDropdown('{{ $index }}')">
                            <option value="{{ $row['reason'] }}" selected>
                                {{ $row['reason'] }}
                            </option>
                            <option value="Altro">Altro</option>
                        </select>
                        <div id="textAreaContainer-{{ $index }}" style="display: none;">
                            <textarea id="popupTextArea-{{ $index }}" class="custom-textarea" rows="2" col="5" oninput="toggleSalvaButton('{{ $index }}')" style="max-height: 100px; overflow-y: auto;" required></textarea>
                            <button type="button" class="btn btn-success" onclick="updateDropdownAndClose('{{ $index }}')" id="salvaButton-{{ $index }}" disabled>Salva</button>
                        </div>
                        
                    </td>
              
                </tr>
                @endforeach
                
            </tbody>
        </table>
        @else
        <p>Nessun record è stato aggiornato ancora.</p>
    @endif
    </div>
</div>