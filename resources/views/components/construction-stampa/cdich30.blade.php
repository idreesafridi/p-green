<div class="cdich30" style="position: relative;">
    <img src="{{ asset('assets/stampa/DICH30/1.png') }}" style="height: 1162px;width: 800px;position: relative; margin-top: 2px;">
    <div class="TECNICO">
        {{ $construction->StatusTechnician == null ? '' : ($construction->StatusTechnician->tecnician_id != null ? $construction->StatusTechnician->user->name : '') }}
    </div>
    <div class="usercomunen">
        {{ $construction->StatusTechnician == null ? '' : ($construction->StatusTechnician->tecnician_id != null ? $construction->StatusTechnician->user->residence_city : '') }}
    </div>
    <div class="usercomune">
        {{ $construction->StatusTechnician == null ? '' : ($construction->StatusTechnician->tecnician_id != null ? $construction->StatusTechnician->user->birthplace : '') }}
    </div>
    <div class="userprovn">
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

    <div class="t_u">U</div>
    <div class="tratto">--</div>
    <div class="dati_catasto">
        {{ $construction->PropertyData == null ? '' : $construction->PropertyData->cadastral_dati }}
    </div>
    <div class="foglioImm">
        {{ $construction->PropertyData == null ? '' : $construction->PropertyData->cadastral_section }}</div>
    <div class="partImm">
        {{ $construction->PropertyData == null ? '' : $construction->PropertyData->cadastral_particle }}
    </div>
    <div class="subImm">{{ $construction->PropertyData == null ? '' : $construction->PropertyData->sub_ordinate }}</div>
    <div class="catc">
        {{ $construction->PropertyData == null ? '' : $construction->PropertyData->cadastral_category }}
    </div>
    <div class="castellana">Castellana Grotte</div>
    <div class="dataod">29-09-22</div> 
</div>
