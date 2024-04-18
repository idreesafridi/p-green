<div class="crcee_le">
    <div class="container3">
        <img src="{{ asset('assets/stampa/RCEE/le_5.png') }}" id="ci4"
            style="height: 1200px;width: 800px;position: absolute;top: -3rem;">
        <img src="{{ asset('assets/stampa/RCEE/le_6.png') }}" id="ci5"
            style="height: 1200px;width: 800px;position: absolute;top: 73rem;">
        <img src="{{ asset('assets/stampa/RCEE/le_7.png') }}" id="ci6"
            style="height: 1200px;width: 800px;position: absolute;top: 147rem;">
        <img src="{{ asset('assets/stampa/RCEE/le_8.png') }}" id="ci7"
            style="height: 1200px;width: 800px;position: absolute;top: 219rem;">
        <img src="{{ asset('assets/stampa/RCEE/le_9.png') }}" id="ci8"
            style="height: 1200px;width: 800px;position: absolute;top: 292rem;">
        <img src="{{ asset('assets/stampa/RCEE/le_10.png') }}" id="ci9"
            style="height: 1200px;width: 800px;position: absolute;top: 365rem;">

        <div id=nome4>{{ $construction->name }}</div>
        <div id=cognome4>{{ $construction->surename }}</div>
        <div id=cna4>{{ $construction->town_of_birth }}</div>
        <div id=pna4>{{ $construction->province }}</div>
        <div id=dna4>
            @if ($construction->date_of_birth != null)
                {{ \Carbon\Carbon::parse($construction->date_of_birth)->format('d/m/Y') }}
            @endif
        </div>
        <div id=comResid4>{{ $construction->residence_common }}</div>
        <div id=proResid4>{{ $construction->residence_province }}</div>
        <div id=viaResid4>{{ $construction->residence_street }}</div>
        <div id=nResid4>{{ $construction->residence_house_number }}</div>
        <div id=capResid4>{{ $construction->residence_postal_code }}</div>
        <div id=cf4>{{ $construction->DocumentAndContact == null ? '' : $construction->DocumentAndContact->fiscal_document_number }}</div>
        <div id=nome5>{{ $construction->name }}</div>
        <div id=cognome5>{{ $construction->surename }}</div>
        <div id=cna5>{{ $construction->town_of_birth }}</div>
        <div id=pna5>{{ $construction->province }}</div>
        <div id=dna5>
            @if ($construction->date_of_birth != null)
                {{ \Carbon\Carbon::parse($construction->date_of_birth)->format('d/m/Y') }}
            @endif
        </div>
        <div id=comResid5>{{ $construction->residence_common }}</div>
        <div id=proResid5 style="margin-top: 18px;">{{ $construction->residence_province }}</div>
        <div id=viaResid5>{{ $construction->residence_street }}</div>
        <div id=nResid5>{{ $construction->residence_house_number }}</div>
        <div id=capResid5 style="margin-top: 18px;">{{ $construction->residence_postal_code }}</div>
        <div id=cf5>{{ $construction->DocumentAndContact == null ? '' : $construction->DocumentAndContact->fiscal_document_number }}</div>
        <div id=comImm6>{{ $construction->residence_common }}</div>
        <div id=capImm6>{{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_postal_code }}</div>
        <div id=viaImm6>{{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_street }}</div>
        <div id=pod6>{{ $construction->PropertyData == null ? '' : $construction->PropertyData->pod_code }}</div>   
        <div id=nImm6>{{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_house_number }}</div>
        <div id=nome6>{{ $construction->name }}</div>
        <div id=cognome6>{{ $construction->surename }}</div>
        <div id=cna6>{{ $construction->town_of_birth }}</div>
        <div id=pna6>{{ $construction->province }}</div>
        <div id=dna6>
            @if ($construction->date_of_birth != null)
                {{ \Carbon\Carbon::parse($construction->date_of_birth)->format('d/m/Y') }}
            @endif
        </div>
        <div id=tel6>{{ $construction->DocumentAndContact == null ? '' : $construction->DocumentAndContact->contact_number  }}</div>
        <div id=nome7>{{ $construction->name }}</div>
        <div id=cognome7>{{ $construction->surename }}</div>
        <div id=cna7>{{ $construction->town_of_birth }}</div>
        <div id=pna7>{{ $construction->province }}</div>
        <div id=dna7>
            @if ($construction->date_of_birth != null)
                {{ \Carbon\Carbon::parse($construction->date_of_birth)->format('d/m/Y') }}
            @endif
        </div>
        <div id=comResid7>{{ $construction->residence_common }}</div>
        <div id=proResid7>{{ $construction->residence_province }}</div>
        <div id=viaResid7>{{ $construction->residence_street }}</div>
        <div id=nResid7>{{ $construction->residence_house_number }}</div>
        <div id=capResid7>{{ $construction->residence_postal_code }}</div>
        <div id=cf7>{{ $construction->DocumentAndContact == null ? '' : $construction->DocumentAndContact->fiscal_document_number }}</div>
        <div id=comImm4>{{ $construction->residence_common }}</div>
        <div id=provImm4>{{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_province }}</div>
        <div id=capImm4>{{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_postal_code }}</div>
        <div id=viaImm4>{{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_street }}</div>
        <div id=nImm4>{{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_house_number }}</div>
        <div id=cf8>{{ $construction->DocumentAndContact == null ? '' : $construction->DocumentAndContact->fiscal_document_number }}</div>
    </div>
    <div class="correzione" style="position: absolute; top: 439rem;">
        <div class="container">
            <img src="{{ asset('assets/stampa/RCEE/le_1.png') }}" id="ci"
                style="height: 1200px;width: 800px;position: absolute;top: -3rem; left: 0px;">
            <div id=committente style="width:max-content">{{ $construction->name }} {{ $construction->surename }}</div>
            <div id=cna style="width:max-content">{{ $construction->town_of_birth }}</div>
            <div id=pna style="width:max-content">{{ $construction->province }}</div>
            <div id=dna style="width:max-content">
                @if ($construction->date_of_birth != null)
                    {{ \Carbon\Carbon::parse($construction->date_of_birth)->format('d/m/Y') }}
                @endif
            </div>
            <div id=comResid style="width:max-content">{{ $construction->residence_common }}</div>
            <div id=proResid style="width:max-content">{{ $construction->residence_province }}</div>
            <div id=viaResid style="width:max-content">{{ $construction->residence_street }}</div>
            <div id=nResid style="width:max-content">{{ $construction->residence_house_number }}</div>
            <div id=capResid style="width:max-content">{{ $construction->residence_postal_code }}</div>
            <div id=cf style="width:max-content">{{ $construction->DocumentAndContact == null ? '' : $construction->DocumentAndContact->fiscal_document_number }}</div>
            @inject('constructionMissingColumn', 'App\Models\ConstructionMissingColumn')
            @php  
                $dataget = $constructionMissingColumn::where('construction_site_id', $construction->id)->first();  
             //    $getdata = /App/Model/ConstructionMissingColumn::where(['id' => $construction->id])->first();
             //    dd( $dataget->documento );
            @endphp
            <div id=doc style="width:max-content">
                @if ($dataget)
                    {{ $dataget->documento == null ? '' : str_replace(['&apos;', '&#39;'], "'", $dataget->documento) }}
                @endif
            </div>
            <div id=ndoc style="width:max-content">{{ $construction->DocumentAndContact == null ? '' : $construction->DocumentAndContact->document_number }}</div>
            <div id=ril style="width:max-content">{{ $construction->DocumentAndContact == null ? '' : $construction->DocumentAndContact->issued_by }}</div>
            <div id=dril style="width:max-content">
                @if ($construction->DocumentAndContact != null && $construction->DocumentAndContact->release_date!=null)
                    @if ($construction->DocumentAndContact->release_date=='0000-00-00')
                        {{ $construction->DocumentAndContact->release_date }}
                    @else
                        {{ \Carbon\Carbon::parse($construction->DocumentAndContact->release_date)->format('d/m/Y') }}
                    @endif
                @endif
            </div>
            <div id=comImm style="width:max-content">{{ $construction->residence_common }}</div>
            <div id=viaImm style="width:max-content">{{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_street }}</div>
            <div id=nImm style="width:max-content">{{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_house_number }}</div>
        </div>
        <div class="container2" style="position: relative;top: 70rem;">
            <img src="{{ asset('assets/stampa/RCEE/le_2.png') }}" id="ci2"
                style="height: 1200px;width: 800px;position: absolute;top: 72rem;">
            <div id=comImm2 style="width:max-content">{{ $construction->residence_common }}</div>
            <div id=provImm2 style="width:max-content">{{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_province }}</div>
            <div id=viaImm2 style="width:max-content">{{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_street }}</div>
            <div id=nImm2 style="width:max-content">{{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_house_number }}</div>
            <div id=cognome2 style="width:max-content">{{ $construction->surename }}</div>
            <div id=nome2 style="width:max-content">{{ $construction->name }}</div>
            <div id=cf2 style="width:max-content">{{ $construction->DocumentAndContact == null ? '' : $construction->DocumentAndContact->fiscal_document_number }}</div>

        </div>
        <div class="container3">
            <img src="{{ asset('assets/stampa/RCEE/le_3.png') }}" id="ci3"
                style="height: 1200px;width: 800px;position: absolute;top: 288rem;">
            <div id=comImm3 style="margin-top: -82px; width:max-content">{{ $construction->residence_common }}</div>
            <div id=provImm3 style="margin-top: -82px; width:max-content">{{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_province }}</div>
            <div id=viaImm3 style="margin-top: -82px; width:max-content">{{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_street }}</div>
            <div id=nImm3 style="margin-top: -82px; width:max-content">{{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_house_number }}</div>
            <div id=cognome3 style="margin-top: -82px; width:max-content">{{ $construction->surename }}</div>
            <div id=nome3 style="margin-top: -82px; width:max-content">{{ $construction->name }}</div>
            <div id=cf3 style="margin-top: -82px; width:max-content">{{ $construction->DocumentAndContact == null ? '' : $construction->DocumentAndContact->fiscal_document_number }}</div>

        </div>
        <div class="container3">
            <img src="{{ asset('assets/stampa/RCEE/le_4.png') }}" id="ci4"
                style="height: 1200px;width: 800px;position: absolute; top: 363rem;">
            <div id=comImm44 style="width:max-content">{{ $construction->residence_common }}</div>
            <div id=provImm44 style="width:max-content">{{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_province }}</div>
            <div id=viaImm44 style="width:max-content">{{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_street }}</div>
            <div id=nImm44 style="width:max-content">{{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_house_number }}</div>
            <div id=cognome44 style="width:max-content">{{ $construction->surename }}</div>
            <div id=nome44 style="width:max-content">{{ $construction->name }}</div>
            <div id=cf44 style="width:max-content">{{ $construction->DocumentAndContact == null ? '' : $construction->DocumentAndContact->fiscal_document_number }}</div>
        </div>
    </div>
</div>
