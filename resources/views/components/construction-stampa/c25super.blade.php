<div class="super" style="position: relative;">
    <img src="{{ asset('assets/stampa/25SUPER/1.png') }}" id="ci" style="height: 1162px;width: 800px;position: relative;">

    <div id=comuneImm>{{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_common }}</div>
    <div id=viaImm>{{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_street }}</div>
    <div id=foglioImm>{{ $construction->PropertyData == null ? '' : $construction->PropertyData->cadastral_section }}</div>
    <div id=partImm>
        {{ $construction->PropertyData == null ? '' : $construction->PropertyData->cadastral_particle }}</div>
    <div id=subImm>{{ $construction->PropertyData == null ? '' : $construction->PropertyData->sub_ordinate }}
    </div>
    <img src="{{ asset('assets/stampa/25SUPER/2.png') }}" id="ci" style="height: 1162px;width: 800px;position: relative;">
</div>
