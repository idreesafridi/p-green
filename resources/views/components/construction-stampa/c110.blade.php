<div class="c110">
    <div class="container0" style="position: relative;">
        <img src="{{ asset('assets/stampa/110/1.png') }}"
            style="height: 1162px;width: 800px;position: relative; margin-top: 3px;">
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
        <img src="{{ asset('assets/stampa/110/2.png') }}"
            style="height: 1162px;width: 800px;position: relative; margin-top: 3px;">
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
        <div class="subImm">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->sub_ordinate }}
        </div>
    </div>

    <div class="container2" style="position: relative;">
        <img src="{{ asset('assets/stampa/110/3.png') }}" style="height: 1162px;width: 800px;position: relative;">
    </div>

    <div class="container3" style="position: relative;">
        <img src="{{ asset('assets/stampa/110/4.png') }}" style="height: 1162px;width: 800px;position: relative;">
    </div>

    <div class="container4" style="position: relative;">
        <img src="{{ asset('assets/stampa/110/5.png') }}" style="height: 1162px;width: 800px;position: relative;">
    </div>

    <div class="container5" style="position: relative;">
        <img src="{{ asset('assets/stampa/110/6.png') }}" style="height: 1162px;width: 800px;position: relative;">
    </div>

    <div class="container6" style="position: relative;">
        <img src="{{ asset('assets/stampa/110/7.png') }}" style="height: 1162px;width: 800px;position: relative;">
    </div>

    <div class="container7" style="position: relative;">
        <img src="{{ asset('assets/stampa/110/8.png') }}" style="height: 1162px;width: 800px;position: relative;">
    </div>

    <div class="container8" style="position: relative;">
        <img src="{{ asset('assets/stampa/110/9.png') }}" style="height: 1162px;width: 810px;position: relative;">
        <div class="committente">{{ $construction->name }} {{ $construction->surename }} </div>
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
        <div class="capResid">{{ $construction->residence_postal_code }}</div>
        <div class="cf">
            {{ $construction->DocumentAndContact == null ? '' : $construction->DocumentAndContact->fiscal_document_number }}
        </div>

        @inject('constructionMissingColumn', 'App\Models\ConstructionMissingColumn')
        @php            
            $dataget = $constructionMissingColumn::where('construction_site_id', $construction->id)->first(); 
            //    $getdata = /App/Model/ConstructionMissingColumn::where(['id' => $construction->id])->first();
            //    dd( $dataget->documento );
        @endphp
        <div class="documento">
            @if ($dataget)
                {{ $dataget->documento == null ? '' : str_replace(['&apos;', '&#39;'], "'", $dataget->documento) }}
            @endif
        </div>
        <div class="numDoc">
            {{ $construction->DocumentAndContact == null ? '' : $construction->DocumentAndContact->document_number }}
        </div>
        <div class="rilascioDoc">
            {{ $construction->DocumentAndContact == null ? '' : $construction->DocumentAndContact->issued_by }}</div>
        <div class="dataDoc">
            @if ($construction->DocumentAndContact != null && $construction->DocumentAndContact->release_date!=null)
                @if ($construction->DocumentAndContact->release_date=='0000-00-00')
                    {{ $construction->DocumentAndContact->release_date }}
                @else
                    {{ \Carbon\Carbon::parse($construction->DocumentAndContact->release_date)->format('d/m/Y') }}
                @endif
            @endif
        </div>
        <div class="comuneImm">{{ $construction->residence_common }} ({{ $construction->residence_province }})</div>
        <div class="viaImm">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_street }}</div>
        <div class="numImm">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_house_number }}</div>
    </div>

    <div style="height: 1162px!important; width: 800px!important;position: relative;" ></div>

    <div class="container9" style="position: relative;">
        <img src="{{ asset('assets/stampa/110/10.png') }}" style="height: 1162px;width: 800px;position: relative;">
        <div class="committente">{{ $construction->name }} {{ $construction->surename }} </div>
        <div class="comuneNascita">{{ $construction->town_of_birth }}</div>
        <div class="dataNascita">
            @if ($construction->date_of_birth != null)
                {{ \Carbon\Carbon::parse($construction->date_of_birth)->format('d/m/Y') }}
            @endif
        </div>
        <div class="comuneResid">{{ $construction->residence_common }} ({{ $construction->residence_province }})</div>
        <div class="viaResid">{{ $construction->residence_street }} n° {{ $construction->residence_house_number }}
        </div>
        <div class="cf">
            {{ $construction->DocumentAndContact == null ? '' : $construction->DocumentAndContact->fiscal_document_number }}
        </div>
        <div class="comuneImm">{{ $construction->residence_common }} ({{ $construction->residence_province }})</div>
        <div class="viaImm">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_street }}</div>
        <div class="numImm">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_house_number }}</div>
        <div class="foglioImm">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->cadastral_section }}</div>
        <div class="partImm">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->cadastral_particle }}</div>
        <div class="subImm">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->sub_ordinate }}
        </div>
    </div>

    <div style="height: 1162px!important; width: 800px!important;position: relative;" ></div>

    <div class="container10" style="position: relative;">
        <img src="{{ asset('assets/stampa/110/11.png') }}" style="height: 1162px;width: 800px;position: relative;">
        <div class="committente"> {{ $construction->name }} {{ $construction->surename }}</div>
        <div class="comuneNascita">{{ $construction->town_of_birth }}</div>
        <div class="provinciaNascita">{{ $construction->province }}</div>
        <div class="dataNascita">
            @if ($construction->date_of_birth != null)
                {{ \Carbon\Carbon::parse($construction->date_of_birth)->format('d/m/Y') }}
            @endif
        </div>
        <div class="cf">
            {{ $construction->DocumentAndContact == null ? '' : $construction->DocumentAndContact->fiscal_document_number }}
        </div>
        <div class="comuneResid">{{ $construction->residence_common }}</div>
        <div class="capResid">{{ $construction->residence_postal_code }}</div>
        <div class="provinciaResid">{{ $construction->residence_province }}</div>
        <div class="viaResid">{{ $construction->residence_street }}</div>
        <div class="numResid">{{ $construction->residence_house_number }}</div>
        <div class="comuneImm">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_common }}</div>
        <div class="capImm">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_postal_code }}</div>
        <div class="provinciaImm">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_province }}</div>
        <div class="viaImm">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_street }}</div>
        <div class="numImm">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_house_number }}</div>
        <div class="comuneImm1">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_common }}</div>
        <div class="provinciaImm1">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_province }}</div>
        <div class="viaImm1">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_street }}</div>
        <div class="numImm1">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_house_number }}</div>
        <div class="comuneImm2">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_common }}</div>
        <div class="foglioImm">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->cadastral_section }}</div>
        <div class="partImm">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->cadastral_particle }}</div>
        <div class="subImm">{{ $construction->PropertyData == null ? '' : $construction->PropertyData->sub_ordinate }}
        </div>
        <div class="catCatasto">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->cadastral_category }}</div>
        {{-- <div class="committente1">{{ $construction->name }} {{ $construction->surename }}</div>
        <div class="comuneNascita1">{{ $construction->town_of_birth }}</div>
        <div class="provinciaNascita1">{{ $construction->province }}</div>
        <div class="dataNascita1" style="margin-bottom: 5px">
            @if ($construction->date_of_birth != null)
                {{ \Carbon\Carbon::parse($construction->date_of_birth)->format('d/m/Y') }}
            @endif
        </div>
        <div class="cf1">
            {{ $construction->DocumentAndContact == null ? '' : $construction->DocumentAndContact->fiscal_document_number }}
        </div> --}}
    </div>

    <div style="height: 1162px!important; width: 800px!important;position: relative;" ></div>

    <div class="container11" style="position: relative;">
        <img src="{{ asset('assets/stampa/110/12.png') }}" style="height: 1162px;width: 800px;position: relative;">
        <div class="committente"> {{ $construction->name }} {{ $construction->surename }}</div>
        <div class="comuneNascita">{{ $construction->town_of_birth }} ({{ $construction->province }})</div>
        <div class="dataNascita">
            @if ($construction->date_of_birth != null)
                {{ \Carbon\Carbon::parse($construction->date_of_birth)->format('d/m/Y') }}
            @endif
        </div>
        <div class="comuneResid" style="margin-top: -8px"><br>{{ $construction->residence_common }}
            ({{ $construction->province }}) {{ $construction->residence_street }} n°
            {{ $construction->residence_house_number }}</div>
        <div class="cf">
            {{ $construction->DocumentAndContact == null ? '' : $construction->DocumentAndContact->fiscal_document_number }}
        </div>
    </div>

    <div class="container12" style="position: relative;">
        <img src="{{ asset('assets/stampa/110/13.png') }}" style="height: 1162px;width: 800px;position: relative;">
    </div>

    <div class="container13" style="position: relative;">
        <img src="{{ asset('assets/stampa/110/14.png') }}" style="height: 1162px;width: 800px;position: relative;">
    </div>

    <div class="container14" style="position: relative;">
        <img src="{{ asset('assets/stampa/110/15.png') }}" style="height: 1162px;width: 800px;position: relative;">
    </div>

    <div class="container15" style="position: relative;">
        <img src="{{ asset('assets/stampa/110/16.png') }}" style="height: 1162px;width: 800px;position: relative;">
    </div>

    <div style="height: 1162px!important; width: 800px!important;position: relative;" ></div>
    {{-- @dd($construction->PropertyData) --}}
    <div class="container16" style="position: relative;">
        <img src="{{ asset('assets/stampa/110/17.png') }}"
            style="height: 1162px;width: 800px;position: relative;margin-top:3px">
        <div class="committente"> {{ $construction->name }} {{ $construction->surename }}</div>
        <div class="comuneNascita">{{ $construction->town_of_birth }} ({{ $construction->province }})</div>
        <div class="dataNascita">
            @if ($construction->date_of_birth != null)
                {{ \Carbon\Carbon::parse($construction->date_of_birth)->format('d/m/Y') }}
            @endif
        </div>
        <div class="comuneResid">{{ $construction->residence_common }} ({{ $construction->province }})</div>
        <div class="viaResid">{{ $construction->residence_street }}</div>
        <div class="numResid">{{ $construction->residence_house_number }}</div>
        <div class="comuneImm">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_common }}
            ({{ $construction->province }})
        </div>
        <div class="viaImm">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_street }}</div>
        <div class="numImm">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_house_number }}</div>
        <div class="comuneImm1">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_common }}</div>
        <div class="provinciaImm">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_province }}</div>
        <div class="viaImm1">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_street }}</div>
        <div class="numImm1">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_house_number }}</div>
    </div>

    <div style="height: 1162px!important; width: 800px!important;position: relative;" ></div>

    <div class="container17" style="position: relative;">
        <img src="{{ asset('assets/stampa/110/18.png') }}" style="height: 1162px;width: 800px;position: relative;">
        <div class="committente">{{ $construction->name }} {{ $construction->surename }} </div>
        <div class="comuneNascita">{{ $construction->town_of_birth }}</div>
        <div class="provinciaNascita">{{ $construction->province }}</div>
        <div class="dataNascita">
            @if ($construction->date_of_birth != null)
                {{ \Carbon\Carbon::parse($construction->date_of_birth)->format('d/m/Y') }}
            @endif
        </div>
        <div class="cf">
            {{ $construction->DocumentAndContact == null ? '' : $construction->DocumentAndContact->fiscal_document_number }}
        </div>
        <div class="viaResid">{{ $construction->residence_street }}</div>
        <div class="numResid">{{ $construction->residence_house_number }}</div>
        <div class="comuneResid">{{ $construction->residence_common }}</div>
        <div class="provinciaResid">{{ $construction->residence_province }}</div>
        <div class="telefono_mobile">
            {{ $construction->DocumentAndContact == null ? '' : $construction->DocumentAndContact->contact_number }}
        </div>
        <div class="email">
            {{ $construction->DocumentAndContact == null ? '' : $construction->DocumentAndContact->contact_email }}
        </div>
        <div class="comuneImm">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_common }}</div>
        <div class="provinciaImm">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_province }}</div>
        <div class="foglioImm">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->cadastral_section }}</div>
        <div class="partImm">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->cadastral_particle }}</div>
        <div class="subImm">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->sub_ordinate }}</div>
    </div>

    <div class="container18" style="position: relative;">
        <img src="{{ asset('assets/stampa/110/19.png') }}" style="height: 1162px;width: 800px;position: relative;">
    </div>

    <div class="container19" style="position: relative;">
        <img src="{{ asset('assets/stampa/110/20.png') }}" style="height: 1162px;width: 800px;position: relative;">
        <div class="committente"> {{ $construction->name }} {{ $construction->surename }}</div>
        <div class="telefono_mobile">
            {{ $construction->DocumentAndContact == null ? '' : $construction->DocumentAndContact->contact_number }}
        </div>
        <div class="email">
            {{ $construction->DocumentAndContact == null ? '' : $construction->DocumentAndContact->contact_email }}
        </div>
    </div>

    <div class="container20" style="position: relative;">
        <img src="{{ asset('assets/stampa/110/21.png') }}" style="height: 1162px;width: 800px;position: relative;">
    </div>

    <div class="container21" style="position: relative;">
        <img src="{{ asset('assets/stampa/110/22.png') }}" style="height: 1162px;width: 800px;position: relative;">
        <div class="nome">{{ $construction->name }}</div>
        <div class="cognome">{{ $construction->surename }}</div>
        <div class="comuneNascita">{{ $construction->town_of_birth }}</div>
        <div class="dataNascita">
            @if ($construction->date_of_birth != null)
                {{ \Carbon\Carbon::parse($construction->date_of_birth)->format('d/m/Y') }}
            @endif
        </div>
        <div class="cf">
            {{ $construction->DocumentAndContact == null ? '' : $construction->DocumentAndContact->fiscal_document_number }}
        </div>
        <div class="viaResid">{{ $construction->residence_street }}</div>
        <div class="comuneResid">{{ $construction->residence_common }}</div>
        <div class="provinciaResid">{{ $construction->residence_province }}</div>
    </div>

    <div class="container22" style="position: relative;">
        <img src="{{ asset('assets/stampa/110/23.png') }}" style="height: 1162px;width: 800px;position: relative;">
    </div>

    <div class="container23" style="position: relative;">
        <img src="{{ asset('assets/stampa/110/24.png') }}" style="height: 1162px;width: 800px;position: relative;">
    </div>

    <div class="container24" style="position: relative;">
        <img src="{{ asset('assets/stampa/110/25.png') }}" style="height: 1162px;width: 800px;position: relative;">
    </div>

    <div class="container25" style="position: relative;">
        <img src="{{ asset('assets/stampa/110/26.png') }}" style="height: 1162px;width: 800px;position: relative;">
        <div class="committente"> {{ $construction->name }} {{ $construction->surename }}</div>
        <div class="comuneNascita">{{ $construction->town_of_birth }}</div>
        <div class="provinciaNascita">{{ $construction->province }}</div>
        <div class="dataNascita">
            @if ($construction->date_of_birth != null)
                {{ \Carbon\Carbon::parse($construction->date_of_birth)->format('d/m/Y') }}
            @endif
        </div>
        <div class="comuneResid">{{ $construction->residence_common }}</div>
        <div class="viaResid">{{ $construction->residence_street }}</div>
        <div class="numResid">{{ $construction->residence_house_number }}</div>
    </div>

    <div style="height: 1162px!important; width: 800px!important;position: relative;" ></div>

    <div class="container26" style="position: relative;">
        <img src="{{ asset('assets/stampa/110/27.png') }}"
            style="height: 1162px;width: 800px;position: relative; margin-top:5px;">
        <div class="committente11">{{ $construction->name }} {{ $construction->surename }}</div>
        <div class="comuneNascita11">{{ $construction->town_of_birth }} - ({{ $construction->province }})</div>
        <div class="dataNascita11">
            @if ($construction->date_of_birth != null)
                {{ \Carbon\Carbon::parse($construction->date_of_birth)->format('d/m/Y') }}
            @endif
        </div>
        <div class="comuneResid10">{{ $construction->residence_common }}</div>
        <div class="viaResid9">{{ $construction->residence_street }}</div>
        <div class="numResid7">{{ $construction->residence_house_number }}</div>
        <div class="telefono_mobile3">
            {{ $construction->DocumentAndContact == null ? '' : $construction->DocumentAndContact->contact_number }}
        </div>
        <div class="cf9">
            {{ $construction->DocumentAndContact == null ? '' : $construction->DocumentAndContact->fiscal_document_number }}
        </div>
    </div>

    <div class="container27" style="position: relative;">
        <img src="{{ asset('assets/stampa/110/28.png') }}" style="height: 1162px;width: 800px;position: relative;">
    </div>

    <div class="container28" style="position: relative;">
        <img src="{{ asset('assets/stampa/110/29.png') }}"
            style="height: 1162px;width: 800px;position: relative; margin-top:2px">
        <div class="committente12">{{ $construction->name }} {{ $construction->surename }}</div>
        <div class="comuneNascita12">{{ $construction->town_of_birth }}</div>
        <div class="provinciaNascita7">{{ $construction->province }}</div>
        <div class="dataNascita12">
            @if ($construction->date_of_birth != null)
                {{ \Carbon\Carbon::parse($construction->date_of_birth)->format('d/m/Y') }}
            @endif
        </div>
        <div class="cf10">
            {{ $construction->DocumentAndContact == null ? '' : $construction->DocumentAndContact->fiscal_document_number }}
        </div>
        <div class="comuneResid11">{{ $construction->residence_common }} ({{ $construction->residence_province }})
        </div>
        <div class="viaResid10">{{ $construction->residence_street }} n°
            {{ $construction->residence_house_number }}</div>
        <div class="capResid4">{{ $construction->residence_postal_code }}</div>
        <div class="viaImm9">
            {{-- @dd( $construction) --}}
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_street }} -
            n° {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_house_number }} 
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_common }}
            ({{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_province }}) FG:
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->cadastral_section }} PART:
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->cadastral_particle }}
            SUB: {{ $construction->PropertyData == null ? '' : $construction->PropertyData->sub_ordinate }}
        </div>
    </div>

    <div style="height: 1162px!important; width: 800px!important;position: relative;" ></div>

    <div class="container29" style="position: relative;">
        <img src="{{ asset('assets/stampa/110/30.png') }}"
            style="height: 1162px;width: 800px;position: relative; margin-top:3px">
        <div class="committente14">{{ $construction->name }} {{ $construction->surename }}</div>
        <div class="dataNascita13">
            @if ($construction->date_of_birth != null)
                {{ \Carbon\Carbon::parse($construction->date_of_birth)->format('d/m/Y') }}
            @endif
        </div>
        <div class="comuneNascita13">{{ $construction->town_of_birth }}</div>
        <div class="provinciaNascita9">{{ $construction->province }}</div>
        <div class="comuneResid13">{{ $construction->residence_common }}</div>
        <div class="provinciaResid7">{{ $construction->residence_province }}</div>
        <div class="viaResid12">{{ $construction->residence_street }} n°
            {{ $construction->residence_house_number }}</div>
        <div class="capResid6">{{ $construction->residence_postal_code }}</div>
        <div class="cf11">
            {{ $construction->DocumentAndContact == null ? '' : $construction->DocumentAndContact->fiscal_document_number }}
        </div>
        <div class="numDoc3" style="position: absolute; top: 13.4rem; left: 32rem;">{{ $construction->DocumentAndContact == null ? '' : $construction->DocumentAndContact->document_number }}</div>
        <div class="rilascioDoc3">
            {{ $construction->DocumentAndContact == null ? '' : $construction->DocumentAndContact->issued_by }}</div>
        <div class="dataDoc4">
            @if ($construction->DocumentAndContact != null && $construction->DocumentAndContact->release_date!=null)
                @if ($construction->DocumentAndContact->release_date=='0000-00-00')
                    {{ $construction->DocumentAndContact->release_date }}
                @else
                    {{ \Carbon\Carbon::parse($construction->DocumentAndContact->release_date)->format('d/m/Y') }}
                @endif
            @endif
        </div>
    </div>

    <div style="height: 1162px!important; width: 800px!important;position: relative;" ></div>

    <div class="container30" style="position: relative;">
        <img src="{{ asset('assets/stampa/110/31.png') }}"
            style="height: 1162px;width: 800px;position: relative; margin-top:3px">
        <div class="commune" style="position: absolute; top: 22rem; left: 27rem;">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_common }}
        </div>
        <div class="provnc" style="position: absolute; top: 23.9rem; left: 8rem;">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_province }}
        </div>
        <div class="via" style="position: absolute; top: 23.9rem; left: 19rem; font-size: 13px;">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_street }}
        </div>
        <div class="n" style="position: absolute; top: 23.9rem; left: 33rem;">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_house_number }}
        </div>
        <div class="cap" style="position: absolute; top: 25.6rem; left: 8rem;">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_postal_code }}
        </div>
        <div class="fg" style="position: absolute; top: 25.6rem; left: 25rem;">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->cadastral_section }}
        </div>
        <div class="part" style="position: absolute; top: 25.6rem; left: 32rem;">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->cadastral_particle }}
        </div>
        <div class="sub" style="position: absolute; top: 25.6rem; left: 40rem;">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->sub_ordinate }}
        </div>
        
        <div class="committente16">{{ $construction->name }} {{ $construction->surename }}</div>
        <div class="comuneNascita14">{{ $construction->town_of_birth }}</div>
        <div class="dataNascita14">
            @if ($construction->date_of_birth != null)
                {{ \Carbon\Carbon::parse($construction->date_of_birth)->format('d/m/Y') }}
            @endif
        </div>
        <div class="comuneResid14">{{ $construction->residence_common }}</div>
        <div class="comuneResid17">{{ $construction->residence_common }}</div>
        <div class="viaResid13">{{ $construction->residence_street }}</div>
        <div class="cf12">
            {{ $construction->DocumentAndContact == null ? '' : $construction->DocumentAndContact->fiscal_document_number }}
        </div>
    </div>

    <div class="container31" style="position: relative;">
        <img src="{{ asset('assets/stampa/110/32.png') }}" style="height: 1162px;width: 800px;position: relative;">
    </div>

    <div class="container32" style="position: relative;">
        <img src="{{ asset('assets/stampa/110/33.png') }}"
            style="height: 1162px;width: 800px;position: relative; margin-top:3px">
        <div class="committente18">{{ $construction->name }} {{ $construction->surename }}</div>
        <div class="dataNascita16">
            @if ($construction->date_of_birth != null)
                {{ \Carbon\Carbon::parse($construction->date_of_birth)->format('d/m/Y') }}
            @endif
        </div>
        <div class="comuneNascita16">{{ $construction->town_of_birth }}</div>
        <div class="provinciaNascita11">{{ $construction->province }}</div>
        <div class="comuneResid16">{{ $construction->residence_common }}</div>
        <div class="provinciaResid9">{{ $construction->residence_province }}</div>
        <div class="viaResid15">{{ $construction->residence_street }} n°
            {{ $construction->residence_house_number }}</div>
        <div class="capResid8">{{ $construction->residence_postal_code }}</div>
        <div class="cf14">
            {{ $construction->DocumentAndContact == null ? '' : $construction->DocumentAndContact->fiscal_document_number }}
        </div>
        <div class="comuneImm13">{{ $construction->residence_common }}</div>
        <div class="provinciaImm88">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_province }}</div>
        <div class="viaImm11">{{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_street }} n° {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_house_number }}
        </div>
        <div class="capImm3">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_postal_code }}</div>
        <div class="foglioImm7">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->cadastral_section }}
        </div>
        <div class="partImm7">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->cadastral_particle }}
        </div>
        <div class="subImm7">{{ $construction->PropertyData == null ? '' : $construction->PropertyData->sub_ordinate }}</div>
        <div class="committente19">{{ $construction->name }} {{ $construction->surename }}</div>
        <div class="comuneNascita17">{{ $construction->town_of_birth }}</div>
        <div class="dataNascita17">
            @if ($construction->date_of_birth != null)
                {{ \Carbon\Carbon::parse($construction->date_of_birth)->format('d/m/Y') }}
            @endif
        </div>
        <div class="comuneResid88">{{ $construction->residence_common }}</div>
        <div class="viaResid16">{{ $construction->residence_street }}</div>
        <div class="comuneResid19">{{ $construction->residence_common }}</div>
        <div class="cf15">
            {{ $construction->DocumentAndContact == null ? '' : $construction->DocumentAndContact->fiscal_document_number }}
        </div>
    </div>

    <div style="height: 1162px!important; width: 800px!important;position: relative;" ></div>

    {{-- <div class="container33" style="position: relative;">
        <img src="{{ asset('assets/stampa/110/34.png') }}" style="height: 1162px;width: 800px;position: relative;">
    </div>

    <div class="container34" style="position: relative;">
        <img src="{{ asset('assets/stampa/110/35.png') }}" style="height: 1162px;width: 800px;position: relative;">
    </div>

    <div class="container35" style="position: relative;">
        <img src="{{ asset('assets/stampa/110/36.png') }}" style="height: 1162px;width: 800px;position: relative;">
    </div>

    <div class="container36" style="position: relative;">
        <img src="{{ asset('assets/stampa/110/37.png') }}" style="height: 1162px;width: 800px;position: relative;">
    </div> --}}

    <div class="container37" style="position: relative;">
        <img src="{{ asset('assets/stampa/110/38.png') }}"
            style="height: 1162px;width: 800px;position: relative; margin-top:3px">
        <div class="committente17">{{ $construction->name }} {{ $construction->surename }}</div>
        <div class="comuneNascita15">{{ $construction->town_of_birth }}</div>
        <div class="provinciaNascita10">{{ $construction->province }}</div>
        <div class="dataNascita15">
            @if ($construction->date_of_birth != null)
                {{ \Carbon\Carbon::parse($construction->date_of_birth)->format('d/m/Y') }}
            @endif
        </div>
        <div class="cf13">
            {{ $construction->DocumentAndContact == null ? '' : $construction->DocumentAndContact->fiscal_document_number }}
        </div>
        <div class="comuneResid15">{{ $construction->residence_common }}</div>
        <div class="capResid7">{{ $construction->residence_postal_code }}</div>
        <div class="provinciaResid8">{{ $construction->residence_province }}</div>
        <div class="viaResid14">{{ $construction->residence_street }}</div>
        <div class="nresid">{{ $construction->residence_house_number }}</div>
        <div class="comuneImm12">{{ $construction->residence_common }}</div>
        <div class="capImm12">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_postal_code }}</div>
        <div class="provinciaImm7">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_province }}
        </div>
        <div class="viaImm10">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_street }}</div>
        <div class="nimm15">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_house_number }}
        </div>
        <div class="viaImm111">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_street }}</div>
        <div class="nimm16">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_house_number }}
        </div>
        <div class="capImm13">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_postal_code }}</div>
        <div class="comuneImm14">{{ $construction->residence_common }}</div>
        <div class="provinciaImm8">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_province }}
        </div>
        <div class="sezc1">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->cadastral_dati }}
        </div>
        <div class="foglioImm6">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->cadastral_section }}
        </div>
        <div class="partImm6">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->cadastral_particle }}
        </div>
        <div class="subImm6">
            {{ $construction->PropertyData == null ? '' : $construction->PropertyData->sub_ordinate }}
        </div>
    </div>

    <div class="container38" style="position: relative;">
        <img src="{{ asset('assets/stampa/110/39.png') }}" style="height: 1162px;width: 800px;position: relative;">
    </div>

    <div class="container39" style="position: relative;">
        <img src="{{ asset('assets/stampa/110/40.png') }}" style="height: 1162px;width: 800px;position: relative;">
    </div>

    <div class="container40" style="position: relative;">
        <img src="{{ asset('assets/stampa/110/41.png') }}" style="height: 1162px;width: 800px;position: relative;">
    </div>

    <div class="container41" style="position: relative;">
        <img src="{{ asset('assets/stampa/110/42.png') }}"
            style="height: 1162px;width: 800px;position: relative; margin-top:3px">
        <div class="committente">{{ $construction->name }} {{ $construction->surename }}</div>
        <div class="committente1">{{ $construction->name }} {{ $construction->surename }}</div>
        <div class="comuneNascita">{{ $construction->town_of_birth }}</div>
        <div class="dataNascita">
            @if ($construction->date_of_birth != null)
                {{ \Carbon\Carbon::parse($construction->date_of_birth)->format('d/m/Y') }}
            @endif
        </div>
        <div class="comuneResid">{{ $construction->residence_common }}</div>
        <div class="comuneResid1">{{ $construction->residence_common }}</div>
        <div class="viaResid">{{ $construction->residence_street }}</div>
        <div class="cf">
            {{ $construction->DocumentAndContact == null ? '' : $construction->DocumentAndContact->fiscal_document_number }}
        </div>
    </div>

    <div class="container42" style="position: relative;">
        <img src="{{ asset('assets/stampa/110/43.png') }}" style="height: 1162px;width: 800px;position: relative;">
    </div>
</div>
