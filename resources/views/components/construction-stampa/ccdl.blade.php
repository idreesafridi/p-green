<div class="ccdl" style="position: relative;">
    <img src="{{ asset('assets/stampa/CDL/1.jpg') }}" style="height: 1162px;width: 800px;position: relative;">
    <div class="committente">{{ $construction->name }} {{ $construction->surename }}</div>
    <div class="cf">
        {{ $construction->DocumentAndContact == null ? '' : $construction->DocumentAndContact->fiscal_document_number }}</div>
    <div class="comuneNascita">{{ $construction->town_of_birth }} ({{ $construction->province }})</div>
    <div class="dataNascita">
        @if ($construction->date_of_birth != null)
            {{ \Carbon\Carbon::parse($construction->date_of_birth)->format('d/m/Y') }}
        @endif
    </div>
    <div class="comuneResid">{{ $construction->residence_common }} ({{ $construction->province }}) </div>
    <div class="viaResid">{{ $construction->residence_street }}</div>
    <div class="numResid">{{ $construction->residence_house_number }}</div>
    <div class="comuneImm">
        {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_common }}
        ({{ $construction->province }})</div>
    <div class="viaImm">{{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_street }} -
        n°{{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_house_number }}</div>
    <div class="foglioImm">
        {{ $construction->PropertyData == null ? '' : $construction->PropertyData->cadastral_section }}</div>
    <div class="partImm">
        {{ $construction->PropertyData == null ? '' : $construction->PropertyData->cadastral_particle }}</div>
    <div class="subImm">{{ $construction->PropertyData == null ? '' : $construction->PropertyData->sub_ordinate }}
    </div>
</div>
