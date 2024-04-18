<div class="cguarino">
    <div style="position: relative; margin-top:25px;">
        <img src="{{ asset('assets/stampa/Guarino/1.png') }}"
            style="height: 1162px;width: 800px;position: relative; margin-top:-25px;">
        <div class="committente">{{ $construction->name }} {{ $construction->surename }}</div>
        <div class="na">{{ $construction->town_of_birth }} ({{ $construction->province }})</div>
        <div class="dna">
            @if ($construction->date_of_birth != null)
                {{ \Carbon\Carbon::parse($construction->date_of_birth)->format('d/m/Y') }}
            @endif
        </div>
        <div class="cf">
            {{ $construction->DocumentAndContact == null ? '' : $construction->DocumentAndContact->fiscal_document_number }}
        </div>
        <div class="resid">{{ $construction->residence_common }}</div>
        <div class="via">{{ $construction->residence_street }} n°{{ $construction->residence_house_number }} -
            ({{ $construction->residence_province }})
        </div>
        <div class="capresid">{{ $construction->residence_postal_code }}</div>
        <div class="immo">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_street }} -
            n° {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_house_number }} 
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_common }}
            ({{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_province }}) FG:
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->cadastral_section }} PART:
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->cadastral_particle }}
            SUB: {{ $construction->PropertyData == null ? '' : $construction->PropertyData->sub_ordinate }}
        </div>
    </div>
    <div>

        <img src="{{ asset('assets/stampa/Guarino/2.png') }}"
            style="height: 1162px;width: 800px;position: relative;">
        <div class="committente2">{{ $construction->name }} {{ $construction->surename }}</div>
        <div class="dna2">
            @if ($construction->date_of_birth != null)
                {{ \Carbon\Carbon::parse($construction->date_of_birth)->format('d/m/Y') }}
            @endif
        </div>
        <div class="na2">{{ $construction->town_of_birth }}</div>
        <div class="pna2">{{ $construction->province }}</div>
        <div class="nresid2">{{ $construction->residence_house_number }}</div>
        <div class="capresid2">{{ $construction->residence_postal_code }}</div>
        <div class="comresid2">{{ $construction->residence_common }}</div>
        <div class="proresid2">{{ $construction->residence_postal_code }}</div>
        <div class="cf2">
            {{ $construction->DocumentAndContact == null ? '' : $construction->DocumentAndContact->fiscal_document_number }}
        </div>
        <div class="ndoc2">
            {{ $construction->DocumentAndContact == null ? '' : $construction->DocumentAndContact->document_number }}
        </div>
        <div class="rildoc2">
            {{ $construction->DocumentAndContact == null ? '' : $construction->DocumentAndContact->issued_by }}</div>
        <div class="dataRildoc2">
            @if ($construction->DocumentAndContact != null && $construction->DocumentAndContact->release_date!=null)
                @if ($construction->DocumentAndContact->release_date=='0000-00-00')
                    {{ $construction->DocumentAndContact->release_date }}
                @else
                    {{ \Carbon\Carbon::parse($construction->DocumentAndContact->release_date)->format('d/m/Y') }}
                @endif
            @endif
        </div>
        <div class="proprietario">Proprietario</div>
    </div>

    <div style="position: relative;">
        <img src="{{ asset('assets/stampa/Guarino/3.png') }}" style="height: 1162px;width: 800px;position: relative;">
        <div class="committente3">{{ $construction->name }} {{ $construction->surename }}</div>
        <div class="committente4">{{ $construction->name }} {{ $construction->surename }}</div>
        <div class="na3">{{ $construction->town_of_birth }} ({{ $construction->province }})</div>
        <div class="dna3">
            @if ($construction->date_of_birth != null)
                {{ \Carbon\Carbon::parse($construction->date_of_birth)->format('d/m/Y') }}
            @endif
        </div>
        <div class="resid2">{{ $construction->residence_common }}</div>
        <div class="via2">{{ $construction->residence_street }} n°{{ $construction->residence_house_number }} -
            ({{ $construction->residence_province }})</div>
        <div class="cf3">
            {{ $construction->DocumentAndContact == null ? '' : $construction->DocumentAndContact->fiscal_document_number }}
        </div>
    </div>

    <div style="position: relative;">
        <img src="{{ asset('assets/stampa/Guarino/4.png') }}" style="height: 1162px;width: 800px;position: relative;">
    </div>
</div>
