<div class="cmsr">
    <div style="position: relative;">
        <img src="{{ asset('assets/stampa/MAN/1.png') }}" style="height: 1162px;width: 800px;position: relative;">
        <div class="committente">{{ $construction->name }} {{ $construction->surename }}</div>
        <div class="dataNascita">
            @if ($construction->date_of_birth != null)
                {{ \Carbon\Carbon::parse($construction->date_of_birth)->format('d/m/Y') }}
            @endif
        </div>
        <div class="comuneNascita">{{ $construction->town_of_birth }}</div>
        <div class="provinciaNascita">{{ $construction->province }}</div>
        <div class="comuneResid">{{ $construction->residence_common }} ({{ $construction->province }})</div>
        <div class="viaResid">{{ $construction->residence_street }}</div>
        <div class="numResid">{{ $construction->residence_house_number }}</div>
        <div class="cf">
            {{ $construction->DocumentAndContact == null ? '' : $construction->DocumentAndContact->fiscal_document_number }}
        </div>
    </div>

    <img src="{{ asset('assets/stampa/MAN/2.png') }}" style="height: 1162px;width: 800px;position: relative;">
    <img src="{{ asset('assets/stampa/MAN/3.png') }}" style="height: 1162px;width: 800px;position: relative;">
    <img src="{{ asset('assets/stampa/MAN/4.png') }}" style="height: 1162px;width: 800px;position: relative;">
</div>
