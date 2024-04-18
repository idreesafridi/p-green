<!-- Start Tab List -->
<nav class="mt-4">
    <div class="nav nav-tabs px-0 border-bottom-0" id="nav-tab" role="tablist">
        <a class="nav-link  {{ $userrole == null ? 'active' : '' }} border-0" href="{{ route('allUsers') }}"><i
                class="fa fa-building-o me-2"></i>{{ __('Tutti') }}</a>

        <a class="nav-link border-0 {{ $userrole == 'admin' ? 'active' : '' }}" href="{{ route('allUsers', 'admin') }}"><i
                class="fa fa-user me-2"></i>{{ __('Amministrativo') }}</a>

        <a class="nav-link border-0 {{ $userrole == 'technician' ? 'active' : '' }}"
            href="{{ route('allUsers', 'technician') }}"><i class="bi bi-rulers me-2"></i>{{ __('Tecnici') }}</a>

        <a class="nav-link border-0 {{ $userrole == 'business' ? 'active' : '' }}"
            href="{{ route('allUsers', 'business') }}"><i class="fa fa-briefcase me-2"></i>{{ __('Imprese') }}</a>

        <a class="nav-link border-0 {{ $userrole == 'businessconsultant' ? 'active' : '' }}"
            href="{{ route('allUsers', 'businessconsultant') }}"><i
                class="fa fa-calculator me-2"></i>{{ __('Commercialisti') }}</a>

        <a class="nav-link border-0 {{ $userrole == 'photovoltaic' ? 'active' : '' }}"
            href="{{ route('allUsers', 'photovoltaic') }}"><i class="bi bi-sun me-2"></i>{{ __('Ing. Fotovoltaici') }}</a>

        <a class="nav-link border-0 {{ $userrole == 'user' ? 'active' : '' }}"
            href="{{ route('allUsers', 'user') }}"><i class="fa fa-desktop me-2"></i>{{ __('Utenti') }}</a>

        <a class="nav-link border-0 {{ $userrole == 'worker' ? 'active' : '' }}"
            href="{{ route('allUsers', 'worker') }}"><i class="bi bi-hammer me-2"></i>{{ __('Operai') }}</a>
    </div>
</nav>
<!-- End Tab List -->
