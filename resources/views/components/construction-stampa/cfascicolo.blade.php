<div class="cfascicolo" style="position: relative;">
    <img src="{{ asset('assets/stampa/FASCICOLO/FASCICOLO DI CANTIERE-1.png') }}"
        style="height: 1162px;width: 800px;position: relative;">
    <img src="{{ asset('assets/stampa/FASCICOLO/FASCICOLO DI CANTIERE-2.png') }}"
        style="height: 1162px;width: 800px;position: relative;">
    <img src="{{ asset('assets/stampa/FASCICOLO/FASCICOLO DI CANTIERE-3.png') }}"
        style="height: 1162px;width: 800px;position: relative;">
    <img src="{{ asset('assets/stampa/FASCICOLO/FASCICOLO DI CANTIERE-4.png') }}"
        style="height: 1162px;width: 800px;position: relative;">

    <div class="committente">{{ $construction->name }} {{ $construction->surename }}</div>
    <div class="comuneResid">{{ $construction->residence_common }}</div>
    <div class="viaResid">{{ $construction->residence_street }} n°{{ $construction->residence_house_number }}</div>
</div>
