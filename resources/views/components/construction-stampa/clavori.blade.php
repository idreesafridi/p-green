<div class="cdich30" style="position: relative;">
    <img src="{{ asset('assets/stampa/lavori/2ad8b1ba-f9c9-45cd-917c-ae6ed9efbc9b.png') }}" style="height: 1162px;width: 800px;position: relative; margin-top: 26px;">
    <div class="TECNICO">
        {{ $construction->StatusTechnician == null ? '' : ($construction->StatusTechnician->tecnician_id != null ? $construction->StatusTechnician->user->name : '') }}
    </div>
    <div class="usercomunen" style="margin-left: 5px;">
        {{ $construction->StatusTechnician == null ? '' : ($construction->StatusTechnician->tecnician_id != null ? $construction->StatusTechnician->user->residence_city : '') }}
    </div>
    <div class="usercomune">
        {{ $construction->StatusTechnician == null ? '' : ($construction->StatusTechnician->tecnician_id != null ? $construction->StatusTechnician->user->birthplace : '') }}
    </div>
    <div class="userprovn" style="margin-left: 9px;">
        {{ $construction->StatusTechnician == null ? '' : ($construction->StatusTechnician->tecnician_id != null ? $construction->StatusTechnician->user->residence_province : '') }}
    </div>
    <div class="userdatan">
        @if ($construction->StatusTechnician != null)
            @if ($construction->StatusTechnician->tecnician_id != null && $construction->StatusTechnician->user->dob!=null)
                {{ \Carbon\Carbon::parse($construction->StatusTechnician->user->dob)->format('d/m/Y') }}
            @endif
        @endif
    </div>
    <div class="usercf">
        {{ $construction->StatusTechnician == null ? '' : ($construction->StatusTechnician->tecnician_id != null ? $construction->StatusTechnician->user->fiscal_code : '') }}
    </div>
    <div class="usercoll">
        {{ $construction->StatusTechnician == null ? '' : ($construction->StatusTechnician->tecnician_id != null ? $construction->StatusTechnician->user->professional_college : '') }}
    </div>
    <div class="usercomcoll">
        {{ $construction->StatusTechnician == null ? '' : ($construction->StatusTechnician->tecnician_id != null ? $construction->StatusTechnician->user->common_college : '') }}
    </div>
    <div class="useriscr">
        {{ $construction->StatusTechnician == null ? '' : ($construction->StatusTechnician->tecnician_id != null ? $construction->StatusTechnician->user->registration_number : '') }}
    </div>

    
    <div class="comune" style="left: 33rem; top: 318px; position: absolute;">
        {{ $construction->PropertyData == null ? '' : $construction->PropertyData->property_common }}
    </div>
    {{-- <div class="t_u">U</div> --}}
    <div class="tratto" style="margin-left: 150px; margin-top: 100px;">--</div>
    <div class="dati_catasto" style="margin-left: 240px; margin-top: 100px;">
        {{ $construction->PropertyData == null ? '' : $construction->PropertyData->cadastral_dati }}
    </div>
    <div class="foglioImm" style="margin-left: 180px; margin-top: 100px;">
        {{ $construction->PropertyData == null ? '' : $construction->PropertyData->cadastral_section }}</div>
    <div class="partImm" style="margin-left: 190px; margin-top: 100px;">
        {{ $construction->PropertyData == null ? '' : $construction->PropertyData->cadastral_particle }}
    </div>
    {{-- <div class="subImm">{{ $construction->PropertyData == null ? '' : $construction->PropertyData->sub_ordinate }}</div>
    <div class="catc">
        {{ $construction->PropertyData == null ? '' : $construction->PropertyData->cadastral_category }}
    </div>
    <div class="castellana">Castellana Grotte</div>
    <div class="dataod">29-09-22</div>  --}}
</div>
