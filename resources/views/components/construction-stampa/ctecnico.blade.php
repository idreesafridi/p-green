<div class="ctecnico" style="position: relative;">
    <img src="{{ asset('assets/stampa/Tecnico/1.png') }}" style="height: 1162px;width: 800px;position: relative;">
    <div class="committente">{{ $construction->name }} {{ $construction->surename }}</div>
    <div class="cf">
        {{ $construction->DocumentAndContact == null ? '' : $construction->DocumentAndContact->fiscal_document_number }}</div>
    <div class="committente2">{{ $construction->name }} {{ $construction->surename }}</div>
    <div class="comuneImm">{{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_common }}
    </div>
    <div class="foglioImm">
        {{ $construction->PropertyData == null ? '' : $construction->PropertyData->cadastral_section }}</div>
    <div class="partImm">{{ $construction->PropertyData == null ? '' : $construction->PropertyData->cadastral_particle }}
    </div>
    <div class="subImm">{{ $construction->PropertyData == null ? '' : $construction->PropertyData->sub_ordinate }}</div>
</div>

<img src="{{ asset('assets/stampa/Tecnico/2.png') }}" style="height: 1162px; width: 800px; position: relative;">
<img src="{{ asset('assets/stampa/Tecnico/3.png') }}" style="height: 1162px; width: 800px; position: relative;">
<img src="{{ asset('assets/stampa/Tecnico/4.png') }}" style="height: 1162px; width: 800px; position: relative;">
