<div class="c50">
    <div class="container0" style="position: relative;">
        <img src="{{ asset('assets/stampa/50/1.png') }}" style="height: 1162px;width: 800px;position: relative;">
        <div class="committente">{{ $construction->name }} {{ $construction->surename }}</div>
        <div class="cf">
            {{ $construction->DocumentAndContact == null ? '' : $construction->DocumentAndContact->fiscal_document_number }}
        </div>
        <div class="comuneNascita">{{ $construction->town_of_birth }}</div>
        <div class="provinciaNascita">{{ $construction->province }}</div>
        <div class="dataNascita">
            @if ($construction->date_of_birth != null)
                {{ \Carbon\Carbon::parse($construction->date_of_birth)->format('d/m/Y') }}
            @endif
        </div>
        <div class="comuneResid">{{ $construction->residence_common }}</div>
        <div class="provinciaResid">{{ $construction->residence_province }}</div>
        <div class="viaResid">{{ $construction->residence_street }}</div>
        <div class="numResid">{{ $construction->residence_house_number }}</div>
        <div class="comuneImm">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_common }}</div>
        <div class="provinciaImm">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_province }}</div>
        <div class="viaImm">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_street }}</div>
        <div class="numImm">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_house_number }}</div>
        <div class="comuneImm1">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_common }}</div>
        <div class="foglioImm">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->cadastral_section }}</div>
        <div class="partImm">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->cadastral_particle }}</div>
        <div class="subImm">{{ $construction->PropertyData == null ? '' : $construction->PropertyData->sub_ordinate }}
        </div>
    </div>

    <div class="container1" style="position: relative;">
        <img src="{{ asset('assets/stampa/50/2.png') }}" style="height: 1162px;width: 800px;position: relative;">
        <div class="comuneImm">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_common }}</div>
        <div class="provinciaImm">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_province }}</div>
        <div class="viaImm">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_street }}</div>
        <div class="numImm">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_house_number }}</div>
        <div class="foglioImm">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->cadastral_section }}</div>
        <div class="partImm">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->cadastral_particle }}</div>
        <div class="subImm">{{ $construction->PropertyData == null ? '' : $construction->PropertyData->sub_ordinate }}
        </div>
    </div>

    <div class="container2" style="position: relative;">
        <img src="{{ asset('assets/stampa/50/3.png') }}" style="height: 1162px;width: 800px;position: relative;">
    </div>

    <div class="container3" style="position: relative;">
        <img src="{{ asset('assets/stampa/50/4.png') }}" style="height: 1162px;width: 800px;position: relative;">
    </div>

    <div class="container4" style="position: relative;">
        <img src="{{ asset('assets/stampa/50/5.png') }}" style="height: 1162px;width: 800px;position: relative;">
    </div>

    <div class="container5" style="position: relative;">
        <img src="{{ asset('assets/stampa/50/6.png') }}" style="height: 1162px;width: 800px;position: relative;">
    </div>

    <div class="container6" style="position: relative;">
        <img src="{{ asset('assets/stampa/50/7.png') }}" style="height: 1162px;width: 800px;position: relative;">
    </div>

    <div class="container7" style="position: relative;">
        <img src="{{ asset('assets/stampa/50/8.png') }}" style="height: 1162px;width: 800px;position: relative;">
    </div>

    <div class="container8" style="position: relative;">
        <img src="{{ asset('assets/stampa/50/9.png') }}" style="height: 1162px;width: 800px;position: relative;">
        <div class="committente">{{ $construction->name }} {{ $construction->surename }}</div>
        <div class="dataNascita">
            @if ($construction->date_of_birth != null)
                {{ \Carbon\Carbon::parse($construction->date_of_birth)->format('d/m/Y') }}
            @endif
        </div>
        <div class="comuneNascita">{{ $construction->town_of_birth }}</div>
        <div class="provinciaNascita">{{ $construction->province }}</div>
        <div class="comuneResid">{{ $construction->residence_common }}</div>
        <div class="provinciaResid">{{ $construction->residence_province }}</div>
        <div class="viaResid">{{ $construction->residence_street }} - n° {{ $construction->PropertyData->property_house_number }}  </div>
        <div class="capResid">{{ $construction->residence_postal_code }}</div>
        <div class="cf">
            {{ $construction->DocumentAndContact == null ? '' : $construction->DocumentAndContact->fiscal_document_number }}
        </div>
        <div class="comuneImm">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_common }}</div>
        <div class="provinciaImm">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_province }}</div>
        <div class="viaImm">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_street }} - n° {{ $construction->PropertyData->property_house_number }}  
        </div>
        <div class="capImm">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_postal_code }}</div>
        <div class="foglioImm">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->cadastral_section }}</div>
        <div class="partImm">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->cadastral_particle }}</div>
        <div class="subImm">{{ $construction->PropertyData == null ? '' : $construction->PropertyData->sub_ordinate }}
        </div>
        <div class="committente1">{{ $construction->name }} {{ $construction->surename }}</div>
        <div class="comuneNascita1">{{ $construction->town_of_birth }}</div>
        <div class="dataNascita1">
            @if ($construction->date_of_birth != null)
                {{ \Carbon\Carbon::parse($construction->date_of_birth)->format('d/m/Y') }}
            @endif
        </div>
        <div class="comuneResid1">{{ $construction->residence_common }}</div>
        <div class="viaResid1">{{ $construction->residence_street }}</div>
        <div class="comuneResid2">{{ $construction->residence_common }}</div>
        <div class="cf1">
            {{ $construction->DocumentAndContact == null ? '' : $construction->DocumentAndContact->fiscal_document_number }}
        </div>
    </div>

    <div style="height: 1162px!important; width: 800px!important;position: relative;" ></div>

    <div class="container9" style="position: relative;">
        <img src="{{ asset('assets/stampa/50/11.png') }}" style="height: 1162px;width: 800px;position: relative;">
        <div class="committente">{{ $construction->name }} {{ $construction->surename }}</div>
        <div class="cf">
            {{ $construction->DocumentAndContact == null ? '' : $construction->DocumentAndContact->fiscal_document_number }}
        </div>
        <div class="cf1">
            {{ $construction->DocumentAndContact == null ? '' : $construction->DocumentAndContact->fiscal_document_number }}
        </div>
        <div class="capResid">{{ $construction->residence_postal_code }}</div>
        <div class="comuneNascita">{{ $construction->town_of_birth }}</div>
        <div class="provinciaNascita">{{ $construction->province }}</div>
        <div class="dataNascita">
            @if ($construction->date_of_birth != null)
                {{ \Carbon\Carbon::parse($construction->date_of_birth)->format('d/m/Y') }}
            @endif
        </div>
        <div class="comuneNascita1">{{ $construction->town_of_birth }}</div>
        <div class="provinciaNascita1">{{ $construction->province }}</div>
        <div class="dataNascita1">
            @if ($construction->date_of_birth != null)
                {{ \Carbon\Carbon::parse($construction->date_of_birth)->format('d/m/Y') }}
            @endif
        </div>
        <div class="comuneResid">{{ $construction->residence_common }}</div>
        <div class="provinciaResid">{{ $construction->residence_province }}</div>
        <div class="viaResid">{{ $construction->residence_street }}</div>
        <div class="numResid">{{ $construction->residence_house_number }}</div>
        <div class="comuneImm">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_common }}</div>
        <div class="provinciaImm">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_province }}</div>
        <div class="provinciaImm1">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_province }}</div>
        <div class="viaImm">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_street }}</div>
        <div class="viaImm1">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_street }}</div>
        <div class="numImm">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_house_number }}</div>
        <div class="numImm1">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_house_number }}</div>
        <div class="comuneImm1">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_common }}</div>
        <div class="comuneImm2">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_common }}</div>
        <div class="foglioImm">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->cadastral_section }}</div>
        <div class="partImm">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->cadastral_particle }}</div>
        <div class="subImm">{{ $construction->PropertyData == null ? '' : $construction->PropertyData->sub_ordinate }}
        </div>
        <div class="capImm">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_postal_code }}</div>
        <div class="sezcat">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->cadastral_dati }}
        </div>
        <div class="committente1">{{ $construction->name }} {{ $construction->surename }} </div>
    </div>
</div>
