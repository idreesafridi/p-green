<ul class="nav nav-tabs" role="tablist">
    <li class="nav-link {{ request()->route()->getName() == 'shipyard_store'? 'active': '' }} border-0 list-item-1">
        <a href="{{ route('shipyard_store') }}"><span>1</span> Dati Cliente</a>
    </li>
    <li class="nav-link {{ request()->route()->getName() == 'document_create'? 'active': '' }} border-0 list-item-2">
        <a href="{{ route('document_create') }}"><span>2</span> Documenti e Contatti</a>
    </li>
    <li
        class="nav-link {{ request()->route()->getName() == 'property_data_create'? 'active': '' }} border-0 list-item-3">
        <a href="{{ route('property_data_create') }}"><span>3</span> Dati Immobile</a>
    </li>
    <li
        class="nav-link {{ request()->route()->getName() == 'construction_setting_data_create'? 'active': '' }} border-0 list-item-4">
        <a href="{{ route('construction_setting_data_create') }}"><span>4</span> Impostazioni Cantiere</a>
    </li>
</ul>
