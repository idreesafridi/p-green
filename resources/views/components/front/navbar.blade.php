<!-- Start Header Area -->
<div id="top-header" class="top-header pt-4">
    <div class="container">
        <div class="row d-md-flex align-items-center">
            <div class="logo-container d-flex justify-content-center order-2 order-md-1">
                <img onclick="location.href='{{ route('home') }}'" src="{{ asset('assets/images/LOGO-GREEN.png') }}" alt="GREENGEN" class="img-fluid col-8 col-md-12 my-5 my-md-0">
            </div>
            <div class="searchbar-container order-3 order-md-2 mt-0 mb-5 my-md-0">
                @if (request()->route()->getName() == 'home' ||
                request()->route()->getName() == 'welcome' ||
                request()->route()->getName() == 'homeDashboard')
                <table>
                    <tbody>
                        <caption class="d-md-none">Trova il cantiere che stai cercando</caption>
                        <tr>
                            <td id="searchbar" class="searchbar flex-fill">
                                <input type="text" class="form-control searchbar-mobile flex-fill" placeholder="Ricerca veloce" id="search_keyword" oninput="search_keyword($(this).val())">
                            </td>
                            <td id="searchbar-button" class="searchbar-button flex-fill">
                                <button class="searchbar-button-icon px-1">
                                    <i class="fa fa-search"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                @endif
            </div>
            <div class="user-container order-1 order-md-3">
                <div class="dropdown text-end pe-2">
                    <a href="#" class="nav-link dropdown-toggle text-white" data-bs-toggle="dropdown">
                        <span class="fw-bold">{{ auth()->user()->name }}</span>&nbsp;
                        <i class="fa fa-user-circle "></i>
                    </a>
                    <div class="dropdown-menu navbar-dropdown-menu dropdown-menu-end border-0 shadow">
                        <div class="card border-0">
                            <div class="card-body border-bottom">
                                <div class="d-flex">
                                    <div class="flex-fill">
                                        <p class="mb-0"><span class="fw-bold">{{ auth()->user()->name }}</span></p>
                                        <small class="text-muted">{{ auth()->user()->email }}</small>
                                        <a href="{{ route('admin_reset_password_form') }}" class="d-block">{{ __('Resetta password') }}</a>
                                        <a href="{{ route('logout') }}" class="d-block">{{ __('Esci') }}</a>
                                    </div>
                                </div>
                            </div>
                            @if (auth()->user()->hasrole('admin') || auth()->user()->hasrole('user'))
                            <div class="list-group m-2">
                                <a href="{{ route('allReports') }}" class="list-group-item border-0">
                                    <i class="me-3 fa fa-file"></i>{{ __('Rapporti') }}
                                </a>
                                <a href="{{ route('allUsers') }}" class="list-group-item border-0">
                                    <i class="me-3 fa fa-user"></i>{{ __('Pagina Utenti') }}
                                </a>
                                <a href="{{ route('home') }}" class="list-group-item border-0">
                                    <i class="me-3 fa fa-building"></i>{{ __('Pagina Cantieri') }}
                                </a>
                                <a href="#" class="list-group-item border-0">
                                    <i class="me-3 fa fa-check"></i>{{ __('Progresso Lavoro') }}
                                </a>
                                <hr>
                               
                                <a href="https://drive.google.com/file/d/1G4HtoLXKnXlX3eKgn76RCX42gIsQzsi_/view?usp=drivesdk" style="background: #076d32 !important; color: white !important; text-decoration: none; display: inline-block;">
                                    <img src="path_to_your_logo_image" alt="Logo" style="width: 50px; height: 50px; border-radius: 50%; margin-right: 10px;">SCARICO FOTO APP
                                </a>
                             
                                
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- End Header Area -->