<div class="cpal" style="position: relative; margin-top: -20px">
    <img src="{{ asset('assets/stampa/PAL/1.png') }}"
        style="height: 1162px;width: 800px;position: relative; margin-top:20px">
    <div class="committente">{{ $construction->name }} {{ $construction->surename }}</div>
    <div class="committente1">{{ $construction->name }} {{ $construction->surename }}</div>
    <div class="na">{{ $construction->town_of_birth }} ({{ $construction->province }})</div>
    <div class="dna">
        @if ($construction->date_of_birth != null)
            {{ \Carbon\Carbon::parse($construction->date_of_birth)->format('d/m/Y') }}
        @endif
    </div>
    <div class="resid">{{ $construction->residence_common }}</div>
    <div class="via">{{ $construction->residence_street }} n°{{ $construction->residence_house_number }}</div>
    <div class="cf">
        {{ $construction->DocumentAndContact == null ? '' : $construction->DocumentAndContact->fiscal_document_number }}</div>

    <img src="{{ asset('assets/stampa/PAL/2.png') }}" style="height: 1162px;width: 800px;position: relative;">
</div>
