<div class="cupsa" style="position: relative;">
    <img src="{{ asset('assets/stampa/UPSA/1.png') }}" style="height: 1162px;width: 800px;position: relative;">
    <div class="committente">{{ $construction->name }} {{ $construction->surename }}</div>
    <div class="cf">
        {{ $construction->DocumentAndContact == null ? '' : $construction->DocumentAndContact->fiscal_document_number }}</div>
    <div class="comuneResid">{{ $construction->residence_common }} </div>
    <div class="viaResid">{{ $construction->residence_street }} n° {{ $construction->residence_house_number }}</div>
    <div class="provinciaResid">{{ $construction->residence_province }}</div>
    <div class="comuneImm">{{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_common }}
    </div>
    <div class="viaImm">{{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_street }} n° {{ $construction->residence_house_number }}
    </div>
    <div class="provinciaImm">
        {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_province }}</div>
    <div class="sezc">{{ $construction->PropertyData == null ? '' : $construction->PropertyData->cadastral_dati }}</div>
    <div class="foglioImm">
        {{ $construction->PropertyData == null ? '' : $construction->PropertyData->cadastral_section }}</div>
    <div class="partImm">
        {{ $construction->PropertyData == null ? '' : $construction->PropertyData->cadastral_particle }}</div>
    <div class="subImm">{{ $construction->PropertyData == null ? '' : $construction->PropertyData->sub_ordinate }}
    </div>
</div>
