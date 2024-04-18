<div class="userList-page-table table-responsive">
    <table class="table dt-responsive table-striped">
        <thead>
            <tr class="text-start">
                <th scope="col">NOME</th>
                <th scope="col">COMUNE</th>
                <th scope="col" class="hideInMobile"> Via </th>
                <th scope="col" class="hideInMobile hideInTablet">Tecnico</th>
                @if (auth()->user()->hasrole('admin') ||
                        auth()->user()->hasrole('user'))
                    {{-- <th scope="col" class="hideInMobile hideInTablet">Documenti</th> --}}
                    <th scope="col" class="hideInMobile hideInTablet">STATO</th>
                    <th scope="col" class="hideInMobile hideInTablet">SUB</th>
                @endif
            </tr>
        </thead>
        <tbody>

            @if ($data != null)

                @forelse ($data as $item)
                    {{-- @dd($item->countfiles($item->id)); --}}
                    @if ($item->name != null || $item->surename != null)
                        @php
                            // $total_updated_file = $item->countfiles($item->id);
                            $total_updated_file = $item->documentiCount($item->id);
                            // dd($total_updated_file);

                            if (
                                auth()
                                    ->user()
                                    ->hasrole('admin') ||
                                auth()
                                    ->user()
                                    ->hasrole('user')
                            ) {
                                $authRoute = route('construction_detail', ['id' => $item->id, 'pagename' => 'Cliente']);
                            } else {
                                $authRoute = route('show_preNoti_doc', $item->StatusPrNoti->id);
                            }
                        @endphp
                        <tr onclick="window.location='{{ $authRoute }}'" style="cursor: pointer" class="text-start">
                            <td class="con_name_mob">

                                {{-- @if ($item->GetConstructionCondominiOne != null)
                                @php
                                $ConstructionSiteSetting = \App\Models\ConstructionSiteSetting::where('construction_site_id',$item->GetConstructionCondominiOne->construction_site_id)->first (); 
                                @endphp
            
                              @if ($ConstructionSiteSetting->type_of_property == 'Condominio')
                               <i class="fa fa-users text-success"></i>
                               @elseif($ConstructionSiteSetting->ConstructionSite->GetConstructionSiteCondomini)
                               <i class="fa fa-users text-success"></i>
                               @endif

                               @elseif(ucwords(strtolower($item->ConstructionSiteSetting->type_of_property)) == 'Condominio')
                               <i class="fa fa-building text-success"></i>

                               @endif
  --}}

                                  
                                {{-- @if ($item->GetConstructionCondominiOne != null)
                                    @if (
                                        $item->GetConstructionCondominiOne->construction_assigned_id == $item->id &&
                                            ucwords(strtolower($item->ConstructionSiteSetting->type_of_property)) != 'Condominio')
                                        <i class="fa fa-users text-success"></i>
                                    @endif

                                @elseif(ucwords(strtolower($item->ConstructionSiteSetting->type_of_property)) == 'Condominio' || $item->ConstructionCondomini != null)
                                    <i class="fa fa-building text-success"></i>
                                @endif --}}
                                {{-- @dd($item->GetConstructionCondominiOne); --}}

                                    {{-- @dd($item->GetConstructionCondominiOne) --}}
                                    {{-- @dd($item->ConstructionCondomini ) --}}
                                    {{-- @if($item->GetConstructionCondominiOne)
                                    @dd($item->GetConstructionCondominiOne->ConstructionSiteSettingforChild)
                                    @endif --}}
                                {{-- @if (ucwords(strtolower($item->ConstructionSiteSetting->type_of_property)) == 'Condominio'  || ($item->ConstructionCondomini != null && $item->GetConstructionCondominiOne == null ||$item->GetConstructionCondominiOne != null && $item->GetConstructionCondominiOne->ConstructionSiteSettingforChild->type_of_property != 'Condominio'))
                                    <i class="fa fa-building text-success"></i>
                                @elseif ($item->GetConstructionCondominiOne != null)
                                    @if (
                                        $item->GetConstructionCondominiOne->construction_assigned_id == $item->id &&
                                            ucwords(strtolower($item->ConstructionSiteSetting->type_of_property)) != 'Condominio')
                                        <i class="fa fa-users text-success"></i>
                                    @endif
                                @endif --}}

                                        {{-- @dd($item->GetConstructionCondominiOne->ConstructionSiteSettingforParent) --}}
                                @if ($item->GetConstructionCondominiOne != null && ucwords(strtolower($item->ConstructionSiteSetting->type_of_property)) != 'Condominio' && (ucwords(strtolower($item->GetConstructionCondominiOne->ConstructionSiteSettingforParent->type_of_property)) == 'Condominio'))
                    
                                    <i class="fa fa-users text-success"></i>

                                    @elseif (ucwords(strtolower($item->ConstructionSiteSetting->type_of_property)) == 'Condominio' && ( $item->GetConstructionSiteCondomini != null && $item->GetConstructionSiteCondomini->ConstructionSiteSettingforChild != null && ucwords(strtolower($item->GetConstructionSiteCondomini->ConstructionSiteSettingforChild->type_of_property)) == 'Condominio' ) )
                                    <i class="fa fa-building text-success"></i>
                                @elseif ( ucwords(strtolower($item->ConstructionSiteSetting->type_of_property)) == 'Condominio')
                                    <i class="fa fa-building text-success"></i>
                                    @elseif(ucwords(strtolower($item->ConstructionSiteSetting->type_of_property)) != 'Condominio' && (
                                        $item->GetConstructionSiteCondomini != null &&
                                        $item->GetConstructionCondominiOne  != null && (ucwords(strtolower($item->GetConstructionCondominiOne->ConstructionSiteSettingforParent->type_of_property)) == 'Condominio') ))
                    
                                    <i class="fa fa-users text-success"></i>
                                  
                                @endif

{{--                             
                                @if ($item->GetConstructionCondominiOne != null && ucwords(strtolower($item->ConstructionSiteSetting->type_of_property)) != 'Condominio' && (ucwords(strtolower($item->GetConstructionCondominiOne->ConstructionSiteSettingforParent->type_of_property)) == 'Condominio'))
                                <i class="fa fa-users text-success"></i>
                                @elseif( $item->GetConstructionSiteCondomini != null &&
                                $item->GetConstructionSiteCondomini->ConstructionSiteSettingforChild != null &&
                                ucwords(strtolower($item->GetConstructionSiteCondomini->ConstructionSiteSettingforChild->type_of_property)) == 'Condominio' ||     ucwords(strtolower($item->ConstructionSiteSetting->type_of_property)) == 'Condominio')
                                <i class="fa fa-building text-success"></i>

                                @endif --}}

                                {{-- <a href="{{ $authRoute }}">{{ $item->surename }} {{ $item->name }} {{optional($item->PropertyData)->Piano ? ' - ' . $item->PropertyData->Piano   : ''}}</a> --}}
                                <a href="{{ $authRoute }}">{{ $item->surename }} {{ $item->name }}{{ optional($item->PropertyData)->Piano ? ' - ' . str_replace('"', '', $item->PropertyData->Piano) : '' }}</a>
                            </td>
                            <td>{{ $item->PropertyData != null ? $item->PropertyData->property_common : '' }}</td>
                            <td class="hideInMobile">
                                @if ($item->PropertyData != null)
                                    {{ $item->PropertyData->property_street . ' ' . $item->PropertyData->property_house_number . ' ' . $item->PropertyData->property_postal_code }}
                                @endif
                            </td>
                            <td class="hideInMobile hideInTablet">
                                @if ($item->StatusTechnician != null && $item->StatusTechnician->user != null)
                                    {{ $item->StatusTechnician->user->name }}
                                @endif
                            </td>

                            @if (auth()->user()->hasrole('admin') ||
                                    auth()->user()->hasrole('user'))
                                {{-- <td class="hideInMobile hideInTablet text-center">
                                  
                                    @if (isset($total_updated_file[0]) && isset($total_updated_file[1]))
                                    @if ($total_updated_file[0] == 0)
                                        <span class="badge rounded-pill px-2"
                                            style='background-color:#dc3545;'>0/13</span>

                                                @elseif($total_updated_file[0] < 0 || $total_updated_file[0] < $total_updated_file[1])
                                                <span class="badge rounded-pill  px-2"
                                                    style='background-color:#ffc107;'>{{ $total_updated_file[0] }}/{{ $total_updated_file[1] }}</span>
                                                    @elseif($total_updated_file[0] == $total_updated_file[1] || $total_updated_file[0] > $total_updated_file[1])
                                                    <span class="badge rounded-pill  px-2"
                                                            style='background-color:#198754;'>{{ $total_updated_file[0] }}/{{ $total_updated_file[1] }}</span>   
                                        @endif
                                    @else
                                        <span class="badge rounded-pill bg-red px-2">0/13</span>
                                    @endif

                                </td> --}}
                                <td class="hideInMobile hideInTablet">
                                    <span class="badge bg-primary">{{ $item->latest_status }}</span>
                                </td>
                                <td class="hideInMobile hideInTablet">
                                    <span>{{ $item->PropertyData == null ? '' : $item->PropertyData->sub_ordinate }}</span>
                                </td>
                            @endif
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="6">Nessun Risultato. Prova a digitare solo il cognome o nome senza spazi nè
                            punteggiatura</td>
                    </tr>
                @endforelse
            @else
                <tr>
                    <td colspan="6">Nessun materiale per questo</td>
                </tr>
            @endif
            @if ($data != null )
                <tr>
                    <td colspan="6" class="text-center">{{ $data->links('pagination::bootstrap-4') }}</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
