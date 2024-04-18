<div class="sidebar shadow-sm sticky-top">
    <div class="container">
        <ul class="menu-list">
            @if (auth()->user()->hasrole('admin') ||
                    auth()->user()->hasrole('user'))
                <li class="flex-fill">
                    <a class="m-link" href="{{ route('home') }}">
                        <i class="fa fa-home"></i>&nbsp;
                        <span class="hideInMobile">&nbsp;Pagina &nbsp;</span>Cantieri
                    </a>
                </li>
                <li class="flex-fill">
                    <a class="m-link" href="{{ route('view_assistanse') }}"> 
                        <i class="fa fa-calendar-check-o"></i>&nbsp;
                        <span class="hideInMobile">&nbsp;Assistenze</span>
                    </a>
                </li>
                <li class="dropdown flex-fill">
                    <a class="m-link">
                        <i class="fa fa-plus"></i>&nbsp;
                        <span class="hideInMobile">&nbsp;Crea&nbsp;</span>Nuovo
                    </a>

                    <div class="dropdown-content w-100">
                        <a class="dropdown-item" href="{{ route('shipyard_store') }}">{{ __('Cantiere') }}</a>
                        <a class="dropdown-item" href="{{ route('createUser', 'technician') }}">{{ __('Tecnico') }}</a>
                        <a class="dropdown-item" href="{{ route('createUser', 'business') }}">{{ __('Impresa') }}</a>
                        <a class="dropdown-item" href="{{ route('createUser', 'user') }}">{{ __('Utente') }}</a>
                        <a class="dropdown-item" href="{{ route('createUser', 'worker') }}">{{ __('Operaio') }}</a>
                        <a class="dropdown-item"
                            href="{{ route('createUser', 'businessconsultant') }}">{{ __('Commercialista') }}</a>
                        <a class="dropdown-item"
                            href="{{ route('createUser', 'photovoltaic') }}">{{ __('Ingegnere Fotovoltaico') }}</a>
                    </div>
                </li>
            @else
                <li class="flex-fill">
                    <a class="m-link" href="{{ route('home') }}">
                        <i class="fa fa-home"></i>&nbsp;
                        <span class="hideInMobile">&nbsp;Pagina </span>&nbsp;<span>Cantieri</span>
                    </a>
                </li>
            @endif
        </ul>
    </div>
</div>
