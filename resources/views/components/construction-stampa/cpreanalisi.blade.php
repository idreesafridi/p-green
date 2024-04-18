<div class="cpreanalisi">
    <div style="position: relative; margin-top:12px;">
        <img src="{{ asset('assets/stampa/Preanalisi/1.png') }}"
            style="height: 1162px;width: 800px;position: relative; margin-top:-12px;">
        <div class="cognome">{{ $construction->name }} {{ $construction->surename }}</div>
        <div class="cf">
            {{ $construction->DocumentAndContact == null ? '' : $construction->DocumentAndContact->fiscal_document_number }}
        </div>
        <div class="comresid">{{ $construction->residence_common }}</div>
        <div class="viaresid">{{ $construction->residence_street }}</div>
        <div class="nresid">{{ $construction->residence_house_number }}</div>
        <div class="email">
            {{ $construction->DocumentAndContact == null ? '' : $construction->DocumentAndContact->contact_email }}
        </div>
        <div class="mobile">
            {{ $construction->DocumentAndContact == null ? '' : $construction->DocumentAndContact->contact_number }}
        </div>
        <div class="comuneImm">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_common }}</div>
        <div class="viaImm">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_street }}
            n°{{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_house_number }}</div>
    </div>

    <div style="position: relative;">
        <img src="{{ asset('assets/stampa/Preanalisi/2.png') }}"
            style="height: 1162px;width: 800px;position: relative;">

        @inject('constructionMissingColumn', 'App\Models\ConstructionMissingColumn')
        @php            
            $dataget = $constructionMissingColumn::where('construction_site_id', $construction->id)->first(); 
            //    $getdata = /App/Model/ConstructionMissingColumn::where(['id' => $construction->id])->first();
            //    dd( $dataget->documento );
        @endphp
        <div class="tecnico">
            @if ($dataget)
                @if ($dataget->user_id!=null)
                {{ $dataget->User->name == null ? '' : $dataget->User->name }}
                @endif
            @endif
        </div>
    </div>
</div>
</div>
