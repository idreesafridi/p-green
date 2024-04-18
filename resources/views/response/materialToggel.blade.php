<div style="background-color: #fff; position: relative;">
    <div class="container mt-3">
       
        @if(!empty($updatedData))
        @php
        $index = 0;
        @endphp
        <table class="table table-striped table-responsive">
            <thead>
                <tr style="background-color: #020202;">
                    <th class="text-white">prima di modifica</th>
                    <th class="text-white">dopo modifica</th>
                    <th class="text-white">motivazione</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ strtoupper($updatedData['updated_field']) }} = {{ $updatedData['Original'] }}</td>
                    <td>{{ strtoupper($updatedData['updated_field']) }} =  {{ $updatedData['Updated_to'] }}</td>
                    <td>
                        <select class="form-select"  id="motivationSelect-{{ $index }}" onchange="handleDropdownChange('{{ $index }}')">
                            <option value="{{ $updatedData['reason'] }}" selected>
                                {{ $updatedData['reason'] }}
                            </option>
                            <option value="Altro">Altro</option>
                        </select>
                        <div id="textAreaContainer-{{ $index }}" style="display: none;">
                            <textarea id="popupTextArea-{{ $index }}" class="custom-textarea" rows="2"  col="5" oninput="toggleSalvaButton('{{ $index }}')" style="max-height: 100px; overflow-y: auto;" required></textarea>
                            <button type="button" class="btn btn-success" onclick="updateDropdownAndClose('{{ $index }}')" id="salvaButton-{{ $index }}" disabled>Salva</button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        @else
        <p>Nessun record è stato aggiornato ancora.</p>
    @endif
    </div>
</div>