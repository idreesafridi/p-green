<x-app-layout :pageTitle="$data['user_role'] ?? 'users'">
    @section('styles')
        <link href="https://cdn.datatables.net/1.13.2/css/jquery.dataTables.min.css" rel="stylesheet">
        <link href="https://cdn.datatables.net/colreorder/1.6.1/css/colReorder.dataTables.min.css" rel="stylesheet">
        <link href="https://cdn.datatables.net/responsive/2.4.0/css/responsive.dataTables.min.css" rel="stylesheet">
    @endsection

    <h1 class="h4 m-0">{{ __('Utenti') }}</h1>

    @if ($errors->has('email'))
        <span class="text-danger">{{ $errors->first('email') }}</span>
    @endif

    <x-all-user-nav :userrole="$data['user_role']" />

    <div class="tab-content">

        <div class="card border-0">
            <div class="card-body userList-page-table p-4 table-responsive">
                <table class="table table-striped dt-responsive w-100" id="users_table">
                    <thead>
                        <tr>
                            <th scope="col">{{ __('Nome') }}</th>
                            <th scope="col">{{ __('Email') }}</th>
                            <th scope="col">{{ __('TELEFONO') }}</th>
                            <th scope="col">{{ __('Comune') }}</th>
                            <th scope="col">{{ __('TIPOLOGIA') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($data['users'] as $user)
                        @php
                        $role = $user->roles[0]->name;
                        $Residenza = $role == 'business' ? 'Sede legale' : 'Residenza';
                        $Comune =  $role == 'business' ? 'Sede legale' : 'Comune di residenza';
                        $Provincia =  $role == 'business' ? 'Provincia sede' : 'Provincia di residenza';
                        $Via =  $role == 'business' ? 'Via sede' : 'Residenza';
                        $Ordine =  $role == 'business' ? 'CCNL' : 'Ordine/ Collegio professionale';
                        $Partita =  $role == 'business' ? 'Partita IVA' : 'Numero iscrizione';
                        @endphp
                            <tr>
                                <td>{{ $user['name'] }}</td>
                                <td>{{ $user['email'] }}</td>
                                <td>{{ $user['phone'] }}</td>
                                <td>{{ $user['residence_city'] }}</td>
                                <td>
                                    {{ $user->business != null ? $user->business->company_type : '' }}
                                </td>
                                <td>
                                    @if ($data['user_role'] == 'technician' || 'technician' == $user->roles[0]->name)
                                        @if ($user->techincian == null)
                                            <!-- <a href="{{ route('addTechDetails', $user['id']) }}"
                                                title="Add {{ $user['name'] }} details"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="fa fa-plus"></i>
                                            </a> -->
                                        @else
                                            {{ __('Added') }}
                                        @endif
                                    @endif

                                    <button type="button" class="btn btn-link btn-sm imprese_btn"
                                        data-bs-toggle="modal" data-bs-target="#editUser{{ $user['id'] }}" Update
                                        {{ $user['name'] }}>
                                        <i class="fa fa-pencil  me-lg-2"></i>
                                    </button>

                                    @hasrole('admin')
                                        <button type="button" class="btn btn-link btn-sm text-warning imprese_btn"
                                            data-bs-toggle="modal" data-bs-target="#changePassword{{ $user['id'] }}">
                                            <i class="fa fa-key"></i>
                                        </button>
                                    @endhasrole

                                    <button type="button" class="btn btn-link btn-sm text-danger imprese_btn"
                                        data-bs-toggle="modal" data-bs-target="#deleteModal{{ $user['id'] }}">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr>

                            <!-- Modal -->
                            <div class="modal fade" id="editUser{{ $user['id'] }}" data-bs-backdrop="static"
                                data-bs-keyboard="false" aria-labelledby="editUser{{ $user['id'] }}Label"
                                aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h1 class="modal-title fs-5" id="editUser{{ $user['id'] }}Label">
                                                {{-- Update {{ $user['name'] }} --}}
                                                MODIFICA ANAGRAFICA IMPRESA
                                            </h1>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <form
                                            action="{{ route('updateUser', ['id' => $user['id'], 'role' => $user->roles[0]->name]) }}"
                                            class="" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body">
                                                <div class="row userData">
                                                    <div class="col-lg-4 col-12">
                                                        <label class="col-form-label" for="name">Nome</label>
                                                        <input id="name" type="text" name="name"
                                                            value="{{ $user['name'] }}" class="form-control"
                                                            placeholder="{{ $user['name'] }}" autocomplete="off">
                                                    </div>
                                                    <div class="col-lg-4 col-12">
                                                        <label class="col-form-label" for="email">Email</label>
                                                        <input id="email" type="email" name="email"
                                                            value="{{ $user['email'] }}" class="form-control"
                                                            autocomplete="off" placeholder="{{ $user['email'] }}">
                                                    </div>
                                                    <div class="col-lg-4 col-12">
                                                        <label class="col-form-label" for="phone">Numero di
                                                            telefono</label>
                                                        <input id="phone" type="tel" name="phone"
                                                            value="{{ $user['phone'] }}" class="form-control"
                                                            placeholder="{{ $user['phone'] }}" autocomplete="off">
                                                    </div>
                                                    <br>

                                                
                                                    @if ($data['user_role'] == 'business' || $user->roles[0]->name == 'business')
                                                        <div class="col-lg-4 col-12">
                                                            <label class="col-form-label" for="company_name">Nome della
                                                                ditta</label>
                                                            <input id="company_name" type="company_name"
                                                                name="company_name"
                                                                value="{{ $user->business != null ? $user->business->company_name : '' }}"
                                                                class="form-control"
                                                                placeholder="{{ $user->business != null ? $user->business->company_name : '' }}"
                                                                autocomplete="off">
                                                        </div>
                                                        <div class="col-lg-4 col-12">
                                                            <label class="col-form-label" for="company_type">Tipologia
                                                                di impresa</label>
                                                            <select id="company_type" name="company_type"
                                                                id="company_type" class="form-control mb-3"
                                                                placeholder="company_type" autocomplete="off">
                                                                <option value="">seleziona il tipo di impresa
                                                                </option>
                                                                <option value="Idraulico"
                                                                    {{ $user->business != null ? ($user->business->company_type == 'Idraulico' ? 'selected' : '') : '' }}>
                                                                    Idraulico
                                                                </option>
                                                                <option value="Elettricista"
                                                                    {{ $user->business != null ? ($user->business->company_type == 'Elettricista' ? 'selected' : '') : '' }}>
                                                                    Elettricista</option>
                                                                <option value="Edile"
                                                                    {{ $user->business != null ? ($user->business->company_type == 'Edile' ? 'selected' : '') : '' }}>
                                                                    Edile</option>
                                                                <option value="Infissi"
                                                                    {{ $user->business != null ? ($user->business->company_type == 'Infissi' ? 'selected' : '') : '' }}>
                                                                    Infissi</option>
                                                            </select>
                                                        </div>
                                                        <br>
                                                    @endif

                                                        {{-- @dd($user); --}}
                                                    <p class="mb-0 mt-3"><strong>Nascita</strong></p>
                                                    <div class="col-lg-4 col-12">
                                                        <label class="col-form-label" for="birthplace">Comune di
                                                            nascita</label>
                                                        <input id="birthplace" type="text" name="birthplace"
                                                            value="{{ $user['birthplace'] }}" class="form-control"
                                                            placeholder="Castellana Grotte" autocomplete="off">

                                                    </div>
                                                    <div class="col-lg-4 col-12">
                                                        <label class="col-form-label" for="birth_country">Provincia di
                                                            nascita</label>
                                                        <input id="birth_country" type="text" name="birth_country"
                                                            value="{{ $user['birth_country'] }}" class="form-control"
                                                            placeholder="Bari" autocomplete="off">

                                                    </div>

                                                    <div class="col-lg-4 col-12">
                                                        <label class="col-form-label" for="dob">Data di
                                                            nascita</label>
                                                        @if ($user['dob'] == '0000-00-00')
                                                            <input id="dob" type="date" name="dob"
                                                                value="" class="form-control"
                                                                autocomplete="off">
                                                        @else
                                                            <input id="dob" type="date" name="dob"
                                                                value="{{ \Carbon\Carbon::parse($user['dob'])->format('Y-m-d') }}"
                                                                class="form-control" autocomplete="off">
                                                        @endif
                                                    </div>
                                                    <br>

                                                    <p class="mb-0 mt-3"><strong>{{$Residenza}}</strong></p>
                                                    <div class="col-lg-4 col-12">
                                                        <label class="col-form-label" for="residence_city">{{$Comune}}</label>
                                                        <input id="residence_city" type="text"
                                                            name="residence_city"
                                                            value="{{ $user['residence_city'] }}"
                                                            class="form-control" placeholder="Castellana Grotte"
                                                            autocomplete="off">

                                                    </div>
                                                    <div class="col-lg-4 col-12">
                                                        <label class="col-form-label"
                                                            for="residence_province">{{$Provincia}}</label>
                                                        <input id="residence_province" type="text"
                                                            name="residence_province"
                                                            value="{{ $user['residence_province'] }}"
                                                            class="form-control" placeholder="Bari"
                                                            autocomplete="off">

                                                    </div>
                                                    <div class="col-lg-4 col-12">
                                                        <label class="col-form-label"
                                                            for="residence">{{$Via}}</label>
                                                        <input id="residence" type="text" name="residence"
                                                            value="{{ $user['residence'] }}" class="form-control"
                                                            placeholder="Polignano" autocomplete="off">

                                                    </div>
                                                    <div class="col-lg-4 col-12">
                                                        <label class="col-form-label" for="fiscal_code">Codice
                                                            fiscale</label>
                                                        <input id="fiscal_code" type="text" name="fiscal_code"
                                                            value="{{ $user['fiscal_code'] }}" class="form-control"
                                                            placeholder="PNGPLN61A01A662C" autocomplete="off">

                                                    </div>
                                                    <div class="col-lg-4 col-12">
                                                        <label class="col-form-label"
                                                            for="professional_college">{{$Ordine}}</label>
                                                        <input id="professional_college" type="text"
                                                            name="professional_college"
                                                            value="{{ $user['professional_college'] }}"
                                                            class="form-control" placeholder="Dei Geometri di Taranto"
                                                            autocomplete="off">

                                                    </div>
                                                    <div class="col-lg-4 col-12 {{$role == 'business' ? 'd-none' : ''}}" >
                                                        <label class="col-form-label" for="common_college">Comune
                                                            collegio</label>
                                                        <input id="common_college" type="text"
                                                            name="common_college"
                                                            value="{{ $user['common_college'] }}"
                                                            class="form-control" placeholder="Taranto"
                                                            autocomplete="off">

                                                    </div>
                                                    <div class="col-lg-4 col-12">
                                                        <label class="col-form-label" for="registration_number">{{$Partita}}</label>
                                                        <input id="registration_number" type="text"
                                                            name="registration_number"
                                                            value="{{ $user['registration_number'] }}"
                                                            class="form-control" placeholder="2030"
                                                            autocomplete="off">

                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer mb-3">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Indietro</button>
                                                <button type="submit" name="update"
                                                    class="btn btn-success">Salva</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Warning Modal -->
                            <div class="modal fade" id="deleteModal{{ $user['id'] }}"
                                aria-labelledby="exampleModalLabel" aria-modal="true" role="dialog">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">Attenzione</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('deleteUser', $user['id']) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <div class="modal-body">
                                                <p class="text-center m-0">Sei sicuro di voler procedere?</p>
                                            </div>
                                            <div class="modal-footer mb-3">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Indietro</button>
                                                <button type="submit" name="reset-pass"
                                                    class="btn btn-danger">Procedi</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- End Warning Modal -->

                            <!-- Warning Modal -->
                            <div class="modal fade" id="changePassword{{ $user['id'] }}"
                                aria-labelledby="changePassword{{ $user['id'] }}Label" aria-modal="true"
                                role="dialog">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="changePassword{{ $user['id'] }}Label">
                                                Attenzione</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('passwordSendRequest') }}" method="POST">
                                            @csrf
                                            <div class="modal-body">
                                                <p class="text-center m-0">Sei sicuro di voler procedere?</p>
                                            </div>
                                            <div class="modal-footer mb-3">
                                                <input type="hidden" name="useremail" id="useremail"
                                                    value="{{ $user['email'] }}">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Indietro</button>
                                                <button type="submit" class="btn btn-danger">Procedi</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- End Warning Modal -->
                        @empty
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @section('scripts')
        <script src="https://cdn.datatables.net/1.13.2/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/colreorder/1.6.1/js/dataTables.colReorder.min.js"></script>
        <script src="https://cdn.datatables.net/responsive/2.4.0/js/dataTables.responsive.min.js"></script>
        <script>
            $('#users_table').DataTable({
                //paging: false
                "language": {
                    emptyTable: "Nessun dato disponibile nella tabella"
                },
            });
        </script>
    @endsection
</x-app-layout>
