<div class="card-body table-responsive p-0" id="state">
    <table class="table stato-page-table table-hover">
        <thead>
            <tr>
                <th scope="col" class="hideInMobile">NOME</th>
                <th scope="col">Stato</th>
                <th scope="col" class="hideInMobile">Aggiornato in data</th>
                <th scope="col" class="hideInMobile">Aggiornato da</th>
                <th scope="col">Solleciti</th>
            </tr>
        </thead>
        <tbody>
            <tr>

                <td class="hideInMobile">Preanalisi</td>
                <td>
                    <form method="post" action="{{ route('pre_analysis', $conststatus->StatusPreAnalysis->id) }}">
                        @csrf
                        {{-- <input id="pre_analysis_id" value="{{$conststatus->StatusPreAnalysis->id }}"
                            type="hidden" /> --}}
                        <div class="mb-3 hideInDesktop">Preanalisi</div>
                        <div class="align-items-center d-flex">

                            <select
                                class="btn {{ $conststatus->StatusPreAnalysis->state == null ? 'btn-light text-dark' : 'btn-primary text-white' }}"
                                onchange="this.form.submit()" type="button" name="state">
                                <option value=""></option>
                                <option value="To be invoiced"
                                    {{ $conststatus->StatusPreAnalysis->state == 'To be invoiced' ? 'selected' : '' }}>
                                    Preanalisi fatturato</option>
                                <option value="Revenue"
                                    {{ $conststatus->StatusPreAnalysis->state == 'Revenue' ? 'selected' : '' }}>
                                    Preanalisi da fatturato</option>
                                <option value="Cashed out"
                                    {{ $conststatus->StatusPreAnalysis->state == 'Cashed out' ? 'selected' : '' }}>
                                    Preanalisi incassato
                                </option>
                                <option value="Not due"
                                    {{ $conststatus->StatusPreAnalysis->state == 'Not due' ? 'selected' : '' }}>
                                    Preanalisi Non Dovuta</option>
                            </select>
                            <div class="mx-2 row hideInMobile hideInTablet">

                                <div class="col-6">
                                    <span class="badge bg-light text-dark">Fatturato</span><br>
                                    <input type="date" name="turnover" onblur="this.form.submit()"
                                        value="{{ optional($conststatus->StatusPreAnalysis)->turnover }}"
                                        class="bg-white mt-2 border-0">
                                </div>
                                <div class="col-6">
                                    <span class="badge bg-light text-dark">Incassato</span><br>
                                    <input type="date" name="embedded" onblur="this.form.submit()"
                                        value="{{ optional($conststatus->StatusPreAnalysis)->embedded }}"
                                        class="bg-white mt-2 border-0">
                                </div>
                            </div>
                        </div>
                        <button type="submit" style="display: none;"></button>
                    </form>
                </td>
                <td class="hideInMobile">
                    {{ $conststatus->StatusPreAnalysis == null ? '' : $conststatus->StatusPreAnalysis->updated_on }}
                </td>
                <td class="hideInMobile">
                    {{-- @dd($conststatus); --}}
                    {{ $conststatus->StatusPreAnalysis == null ? '' : ($conststatus->StatusPreAnalysis->user != null ? $conststatus->StatusPreAnalysis->user->name : '') }}
                </td>
                <td>
                    <div class="w-140">
                        <div>
                            <form method="post" action="{{ route('pre_analysis', $conststatus->StatusPreAnalysis->id) }}">
                           @csrf
                                <input onblur="this.form.submit()"  type="text"
                                class="w-42 bg-white border-0 mt-1" name ="reminders_emails"
                                value="{{ $conststatus->StatusPreAnalysis == null ? null : $conststatus->StatusPreAnalysis->reminders_emails }}">
                            <br>
                            <input onblur="this.form.submit()" name= "reminders_days" type="text"
                                class="w-42 me-2 bg-white border-0 mt-1"
                                value="{{ $conststatus->StatusPreAnalysis == null ? null : $conststatus->StatusPreAnalysis->reminders_days }}">
                            <button type="button" class="btn btn-green btn-sm mt-1">Sollecita</button>
                            </form>
                        </div>
                    </div>
                </td>
            </tr>

            <tr>
                <td class="hideInMobile">Tecnico</td>
                <td>
                    <input type="hidden" id="status_technisan_id"
                        value="{{ $conststatus->StatusTechnician == null ? '' : $conststatus->StatusTechnician->id }}">
                    <div class="mb-3 hideInDesktop">Tecnico</div>
                    {{-- @dd($conststatus->StatusTechnician->state) --}}
                    @if ($conststatus->StatusTechnician == null)

                        <select class="btn btn-light text-dark btn-sm mb-1" onchange="status_technisan()"
                            id="status_technisan_state" type="button">
                            <option value=""></option>
                            <option value="Not Assigned">Da assegnare </option>
                            <option value="Assigned">Assegnato</option>
                        </select>

                        <select class="btn btn-light text-dark btn-sm text-start mb-1" onchange="status_technisan()"
                            id="tecnician_id" type="button">
                            <option value=""></option>
                            @foreach ($alltechnisan as $alltechnisan)
                                <option value="{{ $alltechnisan->id }}"> {{ $alltechnisan->name }}</option>
                            @endforeach
                        </select>
                    @else
                        <select
                            class="btn {{ $conststatus->StatusTechnician->state == null ? 'btn-light text-dark' : ($conststatus->StatusTechnician->state == 'Not Assigned' || 'not assigned' ? 'btn-warning text-dark' : ($conststatus->StatusTechnician->state == 'Asigned' || 'assigned' ? 'btn-primary text-white' : '')) }} btn-sm mb-1"
                            onchange="status_technisan()" id="status_technisan_state" type="button">

                            <option value=""></option>
                            <option value="Not Assigned"
                                {{ $conststatus->StatusTechnician->state == 'Not Assigned' || 'not assigned' ? 'selected' : '' }}>
                                Da
                                assegnare
                            </option>
                            <option value="Assigned"
                                {{ $conststatus->StatusTechnician->state == 'Assigned' || 'assigned' ? 'selected' : '' }}>
                                Assegnato
                            </option>
                        </select>
                        <select class="btn btn-link1 btn-light text-dark btn-sm text-start mb-1 select2"
                            name="tecnician_id" id="tecnician_id" onchange="status_technisan()">
                            <option selected value=""></option>
                            @foreach ($alltechnisan as $alltechnisan)
                                <option value="{{ $alltechnisan->id }}"
                                    {{ $conststatus->StatusTechnician->tecnician_id == $alltechnisan->id ? 'selected' : '' }}>
                                    {{ $alltechnisan->name }}</option>
                            @endforeach
                        </select>

                        {{-- <select class="btn btn-light text-dark btn-sm text-start mb-1" onchange="status_technisan()"
                            id="tecnician_id" type="button">
                            <option value=""></option>
                            @foreach ($alltechnisan as $alltechnisan)
                                <option value="{{ $alltechnisan->id }}"
                                    {{ $conststatus->StatusTechnician->tecnician_id == $alltechnisan->id ? 'selected' : '' }}>
                                    {{ $alltechnisan->name }}</option>
                            @endforeach
                        </select> --}}
                    @endif
                </td>

                <td class="hideInMobile">
                    {{ $conststatus->StatusTechnician == null ? '' : $conststatus->StatusTechnician->updated_on }}</td>
                <td class="hideInMobile">
                    {{ $conststatus->StatusTechnician == null ? '' : ($conststatus->StatusTechnician->updatedBy != null ? $conststatus->StatusTechnician->updatedBy->name : '') }}
                </td>
                <td>
                    <form class="w-140">
                        <div>
                            <input type="text" onchange="status_technisan()" id="technisan_reminders_emails"
                                class="w-42 bg-white border-0 mt-1"
                                value="{{ $conststatus->StatusTechnician == null ? '' : $conststatus->StatusTechnician->reminders_emails }}">
                            <br>
                            <input type="text" onchange="status_technisan()"id="technisan_reminders_days"
                                class="w-42 me-2 bg-white border-0 mt-1"
                                value="{{ $conststatus->StatusTechnician == null ? '' : $conststatus->StatusTechnician->reminders_days }}">
                            <button type="button" class="btn btn-green btn-sm mt-1">Sollecita</button>
                        </div>
                    </form>
                </td>
            </tr>

            <tr>
                <td class="hideInMobile">
                    @if ($conststatus->StatusRelief == null)
                        <a href="#">Rilievo</a>
                    @else
                        <a href="{{ route('show_relief_doc', $conststatus->StatusRelief->id) }}">Rilievo</a>
                    @endif
                </td>
                <td>
                    <input type="hidden" id="status_relief_id"
                        value="{{ $conststatus->StatusRelief == null ? '' : $conststatus->StatusRelief->id }}">
                    <div class="mb-3 hideInDesktop">
                        @if ($conststatus->StatusRelief == null)
                            <a href="#">Rilievo</a>
                        @else
                            <a href="{{ route('show_relief_doc', $conststatus->StatusRelief->id) }}">Rilievo</a>
                        @endif
                    </div>
                    @if ($conststatus->StatusRelief == null)
                        <select class="btn btn-light text-dark btn-sm" onchange="status_relief()" id="relief_state"
                            type="button">
                            <option value=" "> </option>
                            <option value="Received">Ricevuto</option>
                            <option value="To assign">In Attesa</option>
                        </select>
                    @else
                        <select
                            class="btn {{ $conststatus->StatusRelief->state == null ? 'btn-light text-dark' : ($conststatus->StatusRelief->state == 'To assign' ? 'btn-warning text-dark' : ($conststatus->StatusRelief->state == 'Received' ? 'btn-primary text-white' : '')) }} btn-sm"
                            onchange="status_relief()" id="relief_state" type="button">
                            <option value=" "> </option>
                            <option value="Received"
                                {{ $conststatus->StatusRelief->state == 'Received' ? 'selected' : '' }}>
                                Ricevuto</option>
                            <option value="To assign"
                                {{ $conststatus->StatusRelief->state == 'To assign' ? 'selected' : '' }}>In Attesa
                            </option>

                        </select>
                    @endif
                </td>

                <td class="hideInMobile">
                    {{ $conststatus->StatusRelief == null ? '' : $conststatus->StatusRelief->updated_on }}</td>
                <td class="hideInMobile">
                    {{ $conststatus->StatusRelief == null ? '' : ($conststatus->StatusRelief->updatedBy != null ? $conststatus->StatusRelief->updatedBy->name : '') }}
                </td>
                <td>
                    <form class="w-140">
                        <div>
                            <input type="text" onchange="status_relief()" id="relief_reminders_emails"
                                class="w-42 bg-white border-0 mt-1"
                                value="{{ $conststatus->StatusRelief == null ? '' : $conststatus->StatusRelief->reminders_emails }}">
                            <br>
                            <input type="text" onchange="status_relief()" id="relief_reminders_days"
                                class="w-42 me-2 bg-white border-0 mt-1"
                                value="{{ $conststatus->StatusRelief == null ? '' : $conststatus->StatusRelief->reminders_days }}">
                            <button type="button" class="btn btn-green btn-sm mt-1">Sollecita</button>
                        </div>
                    </form>
                </td>
            </tr>

            <tr>
                <td class="hideInMobile">
                    @if ($conststatus->StatusLegge10 == null)
                        <a href="#">Legge 10</a>
                    @else
                        <a href="{{ route('legge10_doc', $conststatus->StatusLegge10->id) }}">Legge 10</a>
                    @endif
                </td>
                <td>
                    <input type="hidden" id="status_legge10_id"
                        value="{{ $conststatus->StatusLegge10 == null ? '' : $conststatus->StatusLegge10->id }}">
                    <div class="mb-3 hideInDesktop">
                        @if ($conststatus->StatusLegge10 == null)
                            <a href="#">Legge 10</a>
                        @else
                            <a href="{{ route('legge10_doc', $conststatus->StatusLegge10->id) }}">Legge 10</a>
                        @endif
                    </div>
                    @if ($conststatus->StatusLegge10 == null)
                        <select class="btn btn-light text-dark btn-sm" id="legge_state" onchange="status_leg10()"
                            type="button">
                            <option value=""> </option>
                            <option value="Completed">Completato</option>
                            <option value="Waiting">In Attesa</option>
                        </select>
                    @else
                        <select
                            class="btn {{ $conststatus->StatusLegge10->state == null ? 'btn-light text-dark' : ($conststatus->StatusLegge10->state == 'Waiting' ? 'btn-warning text-dark' : ($conststatus->StatusLegge10->state == 'Completed' ? 'btn-primary text-white' : '')) }} btn-sm"
                            id="legge_state" onchange="status_leg10()" type="button">
                            <option value=""> </option>
                            <option value="Completed"
                                {{ $conststatus->StatusLegge10->state == 'Completed' ? 'selected' : '' }}>Completato
                            </option>
                            <option value="Waiting"
                                {{ $conststatus->StatusLegge10->state == 'Waiting' ? 'selected' : '' }}>In Attesa
                            </option>
                        </select>
                    @endif
                </td>

                <td class="hideInMobile">
                    {{ $conststatus->StatusLegge10 == null ? '' : $conststatus->StatusLegge10->updated_on }}</td>
                <td class="hideInMobile">
                    {{ $conststatus->StatusLegge10 == null ? '' : ($conststatus->StatusLegge10->user != null ? $conststatus->StatusLegge10->user->name : '') }}
                </td>
                <td>
                    <form class="w-140">
                        <div>
                            <input type="text" onchange="status_leg10()" id="legge_reminders_emails"
                                class="w-42 bg-white border-0 mt-1"
                                value="{{ $conststatus->StatusLegge10 == null ? '' : $conststatus->StatusLegge10->reminders_emails }}">
                            <br>
                            <input type="text" onchange="status_leg10()" id="legge_reminders_days"
                                class="w-42 me-2 bg-white border-0 mt-1"
                                value="{{ $conststatus->StatusLegge10 == null ? '' : $conststatus->StatusLegge10->reminders_days }}">
                            <button type="button" class="btn btn-green btn-sm mt-1">Sollecita</button>
                        </div>
                    </form>
                </td>
            </tr>

            <tr>
                <td class="hideInMobile">Computo</td>
                <td>
                    <input type="hidden" id="status_computation_id"
                        value="{{ $conststatus->StatusComputation == null ? '' : $conststatus->StatusComputation->id }}">
                    <div class="mb-3 hideInDesktop">Computo</div>
                    @if ($conststatus->StatusComputation == null)
                        <select class="btn btn-light text-dark btn-sm" id="computation_state"
                            onchange="status_computation()" type="button">
                            <option value=""> </option>
                            <option value="Completed">Completato</option>
                            <option value="Waiting">In Attesa</option>
                        </select>
                    @else
                        <select
                            class="btn {{ $conststatus->StatusComputation->state == null ? 'btn-light text-dark' : ($conststatus->StatusComputation->state == 'Waiting' ? 'btn-warning text-dark' : ($conststatus->StatusComputation->state == 'Completed' ? 'btn-primary text-white' : '')) }} btn-sm"
                            id="computation_state" onchange="status_computation()" type="button">
                            <option value=""> </option>
                            <option value="Completed"
                                {{ $conststatus->StatusComputation->state == 'Completed' ? 'selected' : '' }}>
                                Completato
                            </option>
                            <option value="Waiting"
                                {{ $conststatus->StatusComputation->state == 'Waiting' ? 'selected' : '' }}>In Attesa
                            </option>
                        </select>
                    @endif
                </td>


                <td class="hideInMobile">
                    {{ $conststatus->StatusComputation == null ? '' : $conststatus->StatusComputation->updated_on }}
                </td>
                <td class="hideInMobile">
                    {{ $conststatus->StatusComputation == null ? '' : ($conststatus->StatusComputation->user != null ? $conststatus->StatusComputation->user->name : '') }}
                </td>
                <td></td>
            </tr>

            <tr>
                <td class="hideInMobile">
                    @if ($conststatus->StatusPrNoti == null)
                        <a href="#">Notifica Preliminare</a>
                    @else
                        <a href="{{ route('show_preNoti_doc', $conststatus->StatusPrNoti->id) }}">Notifica
                            Preliminare</a>
                    @endif
                </td>
                <td>
                    <input type="hidden" id="status_prenoti_id"
                        value="{{ $conststatus->StatusPrNoti == null ? '' : $conststatus->StatusPrNoti->id }}">
                    <div class="mb-3 hideInDesktop">
                        @if ($conststatus->StatusPrNoti == null)
                            <a href="#">Notifica Preliminare</a>
                        @else
                            <a href="{{ route('show_preNoti_doc', $conststatus->StatusPrNoti->id) }}">Notifica
                                Preliminare</a>
                        @endif
                    </div>
                    @if ($conststatus->StatusPrNoti == null)
                        <select class="btn btn-light text-dark btn-sm" id="prenoti_state" onchange="status_prenoti()"
                            type="button">
                            <option value=""> </option>
                            <option value="Completed">Completato</option>
                            <option value="Waiting">In Attesa</option>
                        </select>
                    @else
                        <select
                            class="btn {{ $conststatus->StatusPrNoti->state == null ? 'btn-light text-dark' : ($conststatus->StatusPrNoti->state == 'Waiting' ? 'btn-warning text-dark' : ($conststatus->StatusPrNoti->state == 'Completed' ? 'btn-primary text-white' : '')) }} btn-sm"
                            id="prenoti_state" onchange="status_prenoti()" type="button">
                            <option value=""> </option>
                            <option value="Completed"
                                {{ $conststatus->StatusPrNoti->state == 'Completed' ? 'selected' : '' }}>
                                Completato
                            </option>
                            <option value="Waiting"
                                {{ $conststatus->StatusPrNoti->state == 'Waiting' ? 'selected' : '' }}>In Attesa
                            </option>
                        </select>
                    @endif
                </td>

                <td class="hideInMobile">
                    {{ $conststatus->StatusPrNoti == null ? '' : $conststatus->StatusPrNoti->updated_on }}</td>
                <td class="hideInMobile">
                    {{ $conststatus->StatusPrNoti == null ? '' : ($conststatus->StatusPrNoti->user != null ? $conststatus->StatusPrNoti->user->name : '') }}
                </td>
                <td>
                    <form class="w-140">
                        <div>
                            <input type="text" class="w-42 bg-white border-0 mt-1" id="PreNoti_reminders_emails"
                                onchange="status_prenoti()"
                                value="{{ $conststatus->StatusPrNoti == null ? '' : $conststatus->StatusPrNoti->reminders_emails }}">
                            <br>
                            <input type="text" class="w-42 me-2 bg-white border-0 mt-1"
                                id="PreNoti_reminders_days" onchange="status_prenoti()"
                                value="{{ $conststatus->StatusPrNoti == null ? '' : $conststatus->StatusPrNoti->reminders_days }}">
                            <button type="button" class="btn btn-green btn-sm mt-1">Sollecita</button>
                        </div>
                    </form>
                </td>
            </tr>

            <tr>
                <td class="hideInMobile">
                    @if ($conststatus->statusRegPrac == null)
                        <a href="#">Pratica Protocollata</a>
                    @else
                        <a href="{{ route('regprac_prac', $conststatus->statusRegPrac->id) }}">Pratica
                            Protocollata</a>
                    @endif
                </td>
                <td>
                    <input type="hidden" id="status_regprac_id"
                        value="{{ $conststatus->statusRegPrac == null ? '' : $conststatus->statusRegPrac->id }}">
                    <div class="mb-3 hideInDesktop">
                        @if ($conststatus->statusRegPrac == null)
                            <a href="#">Pratica Protocollata</a>
                        @else
                            <a href="{{ route('regprac_prac', $conststatus->statusRegPrac->id) }}">Pratica
                                Protocollata</a>
                        @endif
                    </div>
                    @if ($conststatus->statusRegPrac == null)
                        <select class="btn btn-light text-dark btn-sm" id="regprac_state" onchange="status_regprac()"
                            type="button">
                            <option value=""> </option>
                            <option value="Completed">Completato</option>
                            <option value="Waiting">In Attesa</option>
                        </select>
                    @else
                        <select
                            class="btn {{ $conststatus->statusRegPrac->state == null ? 'btn-light text-dark' : ($conststatus->statusRegPrac->state == 'Waiting' ? 'btn-warning text-dark' : ($conststatus->statusRegPrac->state == 'Completed' ? 'btn-primary text-white' : '')) }} btn-sm"
                            id="regprac_state" onchange="status_regprac()" type="button">
                            <option value=""> </option>
                            <option value="Completed"
                                {{ $conststatus->statusRegPrac->state == 'Completed' ? 'selected' : '' }}>
                                Completato
                            </option>
                            <option value="Waiting"
                                {{ $conststatus->statusRegPrac->state == 'Waiting' ? 'selected' : '' }}>In Attesa
                            </option>
                        </select>
                    @endif
                </td>

                <td class="hideInMobile">
                    {{ $conststatus->statusRegPrac == null ? '' : $conststatus->statusRegPrac->updated_on }}</td>
                <td class="hideInMobile">
                    {{ $conststatus->statusRegPrac == null ? '' : ($conststatus->statusRegPrac->user != null ? $conststatus->statusRegPrac->user->name : '') }}
                </td>
                <td>
                    <form class="w-140">
                        <div>
                            <input type="text" class="w-42 bg-white border-0 mt-1" id="regprac_reminders_emails"
                                onchange="status_regprac()"
                                value="{{ $conststatus->statusRegPrac == null ? '' : $conststatus->statusRegPrac->reminders_emails }}">
                            <br>
                            <input type="text" class="w-42 me-2 bg-white border-0 mt-1"
                                id="regprac_reminders_days" onchange="status_regprac()"
                                value="{{ $conststatus->statusRegPrac == null ? '' : $conststatus->statusRegPrac->reminders_days }}">
                            <button type="button" class="btn btn-green btn-sm mt-1">Sollecita</button>
                        </div>
                    </form>
                </td>
            </tr>

            <tr>
                <td class="hideInMobile">Lavori Iniziati</td>
                <td>
                    <input type="hidden" id="status_workst_id"
                        value="{{ $conststatus->StatusWorkStarted == null ? '' : $conststatus->StatusWorkStarted->id }}">
                    <div class="mb-3 hideInDesktop">Lavori Iniziati</div>
                    <div class="align-items-center d-flex">
                        @if ($conststatus->StatusWorkStarted == null)
                            <select class="btn btn-light text-dark btn-sm" id="workst_state"
                                onchange="status_workstarted()" type="button">
                                <option value=""> </option>
                                <option value="Completed">Completata</option>
                                <option value="Deliever">CONSEGNARE</option>
                                <option value="Waiting">In Attesa</option>
                            </select>
                        @else
                            <select
                                class="btn {{ $conststatus->StatusWorkStarted->state == null ? 'btn-light text-dark' : ($conststatus->StatusWorkStarted->state == 'Waiting' ? 'btn-warning text-dark' : ($conststatus->StatusWorkStarted->state == 'Completed' || $conststatus->StatusWorkStarted->state == 'Deliever' ? 'btn-primary text-white' : '')) }} btn-sm"
                                id="workst_state" onchange="status_workstarted()" type="button">
                                <option value=""> </option>
                                <option value="Completed"
                                    {{ $conststatus->StatusWorkStarted->state == 'Completed' ? 'selected' : '' }}>
                                    Completati
                                </option>
                                <option value="Deliever"
                                    {{ $conststatus->StatusWorkStarted->state == 'Deliever' ? 'selected' : '' }}>
                                    CONSEGNARE
                                </option>
                                <option value="Waiting"
                                    {{ $conststatus->StatusWorkStarted->state == 'Waiting' ? 'selected' : '' }}>
                                    In Attesa
                                </option>
                            </select>
                        @endif

                        <div class="ms-4 d-inline-block hideInMobile hideInTablet">
                            <span class="badge bg-light text-dark">Data Prevista</span><br>
                            <input type="date" id="work_started_date" onblur="status_workstarted()"
                                value="{{ $conststatus->StatusWorkStarted == null ? '' : $conststatus->StatusWorkStarted->work_started_date }}"
                                class="bg-white mt-2 border-0">
                        </div>
                    </div>
                </td>
                {{-- //StatusWorkStarted --}}
                <td class="hideInMobile">
                    {{ $conststatus->StatusWorkStarted == null ? '' : $conststatus->StatusWorkStarted->updated_on }}
                </td>
                <td class="hideInMobile">
                    {{ $conststatus->StatusWorkStarted == null ? '' : ($conststatus->StatusWorkStarted->user != null ? $conststatus->StatusWorkStarted->user->name : '') }}
                </td>
                <td></td>
            </tr>

            <tr>
                <td class="hideInMobile">SAL</td>
                <td>
                    <input type="hidden" id="sal_id"
                        value="{{ $conststatus->StatusSAL == null ? '' : $conststatus->StatusSAL->id }}">
                    <div class="mb-3 hideInDesktop">SAL</div>

                    @if ($conststatus->StatusSAL == null)
                        <select class="btn btn-light text-dark btn-sm mb-1" id="sal_state" onchange="status_sal()"
                            type="button">
                            <option value=""> </option>
                            <option value="Completed">Completato</option>
                            <option value="Waiting">In Attesa</option>
                        </select>

                        <select class="btn btn-light text-dark btn-sm mb-1 select2" type="button"
                            id="select_accountant" onchange="status_sal()">
                            <option selected value=""></option>

                            @foreach ($account as $acountant)
                                <option value="{{ $acountant->id }}">
                                    {{ $acountant->name }}</option>
                            @endforeach
                        </select>
                    @else
                        <select
                            class="btn {{ $conststatus->StatusSAL->state == null ? 'btn-light text-dark' : ($conststatus->StatusSAL->state == 'Waiting' ? 'btn-warning text-dark' : ($conststatus->StatusSAL->state == 'Completed' ? 'btn-primary text-white' : '')) }} btn-sm mb-1"
                            id="sal_state" onchange="status_sal()" type="button">
                            <option value=""> </option>
                            <option value="Completed"
                                {{ $conststatus->StatusSAL->state == 'Completed' ? 'selected' : '' }}>
                                Completata
                            </option>
                            <option value="Waiting"
                                {{ $conststatus->StatusSAL->state == 'Waiting' ? 'selected' : '' }}>
                                In Attesa
                            </option>
                        </select>

                        <select class="btn btn-light text-dark btn-sm mb-1 select2" type="button"
                            id="select_accountant" onchange="status_sal()">
                            @foreach ($account as $acountant)
                                <option value="{{ $acountant->id }}"
                                    {{ $conststatus->StatusSAL->select_accountant == $acountant->id ? 'selected' : '' }}>
                                    {{ $acountant->name }}</option>
                            @endforeach
                        </select>
                    @endif
                </td>

                <td class="hideInMobile">
                    {{ $conststatus->StatusSAL == null ? '' : $conststatus->StatusSAL->updated_on }}</td>
                <td class="hideInMobile">
                    {{ $conststatus->StatusSAL == null ? '' : ($conststatus->StatusSAL->user != null ? $conststatus->StatusSAL->user->name : '') }}
                </td>
                <td>
                    <form>
                        <div class="ms-52">
                            <button type="button" class="btn btn-green btn-sm w-105">Invia Notifica</button>
                        </div>
                    </form>
                </td>
            </tr>

            <tr>
                <td class="hideInMobile">Saldo Enea</td>
                <td>
                    <input type="hidden" id="eneablnce_id"
                        value="{{ $conststatus->StatusEneaBalance == null ? '' : $conststatus->StatusEneaBalance->id }}">
                    <div class="mb-3 hideInDesktop">Saldo Enea</div>
                    @if ($conststatus->StatusEneaBalance == null)
                        <select class="btn btn-light text-dark btn-sm mb-1" id="eneablnce_state"
                            onchange="status_eneablnc()" type="button">
                            <option value=""> </option>
                            <option value="Completed">Completata</option>
                            <option value="Waiting">In Attesa</option>
                        </select>
                        <select class="btn btn-light text-dark btn-sm mb-1 select2" type="button"
                            id="eneablnce_select_accountant" onchange="status_eneablnc()">
                            @foreach ($account as $acountant)
                                <option value="{{ $acountant->id }}">
                                    {{ $acountant->name }}</option>
                            @endforeach
                        </select>
                    @else
                        <select
                            class="btn {{ $conststatus->StatusEneaBalance->state == null ? 'btn-light text-dark' : ($conststatus->StatusEneaBalance->state == 'Waiting' ? 'btn-warning text-dark' : ($conststatus->StatusEneaBalance->state == 'Completed' ? 'btn-primary text-white' : '')) }} btn-sm mb-1"
                            id="eneablnce_state" onchange="status_eneablnc()" type="button">
                            <option value=""> </option>
                            <option value="Completed"
                                {{ $conststatus->StatusEneaBalance->state == 'Completed' ? 'selected' : '' }}>
                                Completata
                            </option>
                            <option value="Waiting"
                                {{ $conststatus->StatusEneaBalance->state == 'Waiting' ? 'selected' : '' }}>
                                In Attesa
                            </option>
                        </select>
                        <select class="btn btn-light text-dark btn-sm mb-1 select2" type="button"
                            id="eneablnce_select_accountant" onchange="status_eneablnc()">
                            @foreach ($account as $acountant)
                                <option value="{{ $acountant->id }}"
                                    {{ $conststatus->StatusEneaBalance->select_accountant == $acountant->id ? 'selected' : '' }}>
                                    {{ $acountant->name }}</option>
                            @endforeach
                        </select>
                    @endif
                </td>

                <td class="hideInMobile">
                    {{ $conststatus->StatusEneaBalance == null ? '' : $conststatus->StatusEneaBalance->updated_on }}
                </td>
                <td class="hideInMobile">

                    {{ $conststatus->StatusEneaBalance == null ? '' : ($conststatus->StatusEneaBalance->user != null ? $conststatus->StatusEneaBalance->user->name : '') }}
                </td>
                <td>
                    <form>
                        <div class="ms-52">
                            {{-- <input type="text" class="w-42 bg-white border-0"
                                value="{{ $conststatus->StatusEneaBalance->reminders_emails }}">
                            <br>
                            <input type="text" class="w-42 me-2 bg-white border-0"
                                value="{{ $conststatus->StatusEneaBalance->reminders_days }}"> --}}
                            <button type="button" class="btn btn-green btn-sm w-105">Invia Notifica</button>
                        </div>
                    </form>
                </td>
            </tr>

            <tr>
                <td class="hideInMobile">Chiuso</td>
                <td>
                    <input type="hidden" id="workclose_id"
                        value="{{ $conststatus->StatusWorkClose == null ? '' : $conststatus->StatusWorkClose->id }}">
                    <div class="mb-3 hideInDesktop">Chiuso</div>
                    @if ($conststatus->StatusWorkClose == null)
                        <select class="btn btn-light text-dark btn-sm" id="workclose_state"
                            onchange="status_workclose()" type="button">
                            <option value=""> </option>
                            <option value="Completed">Chiuso</option>
                            <option value="Waiting">In Attesa</option>
                        </select>
                    @else
                        <select
                            class="btn {{ $conststatus->StatusWorkClose->state == 'Completed' ? 'btn-primary text-white' : ($conststatus->StatusWorkClose->state == 'Waiting' ? 'btn-warning text-dark' : 'btn-light text-dark') }}   btn-sm"
                            id="workclose_state" onchange="status_workclose()" type="button">
                            <option value=""> </option>
                            <option value="Completed"
                                {{ $conststatus->StatusWorkClose->state == 'Completed' ? 'selected' : '' }}>
                                Chiuso
                            </option>
                            <option value="Waiting"
                                {{ $conststatus->StatusWorkClose->state == 'Waiting' ? 'selected' : '' }}>
                                In Attesa
                            </option>
                        </select>
                    @endif
                </td>

                <td class="hideInMobile">
                    {{ $conststatus->StatusWorkClose == null ? '' : $conststatus->StatusWorkClose->updated_on }}</td>
                <td class="hideInMobile">
                    {{ $conststatus->StatusWorkClose == null ? '' : ($conststatus->StatusWorkClose->user != null ? $conststatus->StatusWorkClose->user->name : '') }}
                </td>
                <td></td>
            </tr>
        </tbody>
    </table>
</div>
@section('scripts')
    <script>
        // status_technisan
        function status_technisan() {
            var id = $("#status_technisan_id").val();
            var tecnician_id = $("#tecnician_id").val();

            var state = $("#status_technisan_state").val();
            var reminders_emails = $("#technisan_reminders_emails").val();
            var reminders_days = $("#technisan_reminders_days").val();
            var data = {
                'state': state,
                'tecnician_id': tecnician_id,
                'reminders_emails': reminders_emails,
                'reminders_days': reminders_days,
                '_token': '{{ csrf_token() }}',
            }

            $.ajax({
                type: "post",
                url: "{{ route('status_technisan') }}" + "/" + id,
                data: data,
                dataType: "json",
                success: function(response) {
                    showAlertMessage('state');
                    window.location.reload();
                    // console.log(response.status);
                }
            });

        };
        // status_relief
        function status_relief() {
            var id = $("#status_relief_id").val();
            var state = $("#relief_state").val();
            var reminders_emails = $("#relief_reminders_emails").val();
            var reminders_days = $("#relief_reminders_days").val();
            var data = {
                'state': state,
                'reminders_emails': reminders_emails,
                'reminders_days': reminders_days,
                '_token': '{{ csrf_token() }}',
            }

            $.ajax({
                type: "post",
                url: "{{ route('status_relief') }}" + "/" + id,
                data: data,
                dataType: "json",
                success: function(response) {
                    showAlertMessage('state');
                    window.location.reload();
                    // console.log(response.status);
                }
            });
        };

        // status_leg10
        function status_leg10() {
            var id = $("#status_legge10_id").val();
            var state = $("#legge_state").val();
            var reminders_emails = $("#legge_reminders_emails").val();
            var reminders_days = $("#legge_reminders_days").val();
            var data = {
                'state': state,
                'reminders_emails': reminders_emails,
                'reminders_days': reminders_days,
                '_token': '{{ csrf_token() }}',
            }

            $.ajax({
                type: "post",
                url: "{{ route('status_leg10') }}" + "/" + id,
                data: data,
                dataType: "json",
                success: function(response) {
                    showAlertMessage('state');
                    window.location.reload();
                    // console.log(response.status);
                }
            });

        };
        // status_computation
        function status_computation() {

            var id = $("#status_computation_id").val();
            var state = $("#computation_state").val();
            var data = {
                'state': state,
                '_token': '{{ csrf_token() }}',
            }

            $.ajax({
                type: "post",
                url: "{{ route('status_computation') }}" + "/" + id,
                data: data,
                dataType: "json",
                success: function(response) {
                    showAlertMessage('state');
                    window.location.reload();
                    // console.log(response.status);
                }
            });

        };
        // status_PreNoti
        function status_prenoti() {

            var id = $("#status_prenoti_id").val();
            var state = $("#prenoti_state").val();
            var reminders_emails = $("#PreNoti_reminders_emails").val();
            var reminders_days = $("#PreNoti_reminders_days").val();
            var data = {
                'state': state,
                'reminders_emails': reminders_emails,
                'reminders_days': reminders_days,
                '_token': '{{ csrf_token() }}',
            }

            $.ajax({
                type: "post",
                url: "{{ route('status_prenoti') }}" + "/" + id,
                data: data,
                dataType: "json",
                success: function(response) {
                    showAlertMessage('state');
                    window.location.reload();
                    // console.log(response.status);
                }
            });

        };
        // status_regprac
        function status_regprac() {

            var id = $("#status_regprac_id").val();
            var state = $("#regprac_state").val();
            var reminders_emails = $("#regprac_reminders_emails").val();
            var reminders_days = $("#regprac_reminders_days").val();
            var data = {
                'state': state,
                'reminders_emails': reminders_emails,
                'reminders_days': reminders_days,
                '_token': '{{ csrf_token() }}',
            }

            $.ajax({
                type: "post",
                url: "{{ route('status_regprac') }}" + "/" + id,
                data: data,
                dataType: "json",
                success: function(response) {
                    showAlertMessage('state');
                    window.location.reload();
                    // console.log(response.status);
                }
            });

        };
        // status_workstarted
        function status_workstarted() {

            var id = $("#status_workst_id").val();
            var state = $("#workst_state").val();
            var work_started_date = $("#work_started_date").val();
            var reminders_emails = $("#workst_reminders_emails").val();
            var reminders_days = $("#workst_reminders_days").val();
            var data = {
                'state': state,
                'work_started_date': work_started_date,
                'reminders_emails': reminders_emails,
                'reminders_days': reminders_days,
                '_token': '{{ csrf_token() }}',
            }

            $.ajax({
                type: "post",
                url: "{{ route('status_workstarted') }}" + "/" + id,
                data: data,
                dataType: "json",
                success: function(response) {
                    showAlertMessage('state');
                    window.location.reload();
                    // console.log(response.status);
                }
            });

        };
        // status_sal
        function status_sal() {

            var id = $("#sal_id").val();
            var state = $("#sal_state").val();
            var select_accountant = $("#select_accountant").val();
            var data = {
                'state': state,
                'select_accountant': select_accountant,
                '_token': '{{ csrf_token() }}',
            }

            $.ajax({
                type: "post",
                url: "{{ route('status_sal') }}" + "/" + id,
                data: data,
                dataType: "json",
                success: function(response) {
                    showAlertMessage('state');
                    window.location.reload();
                    // console.log(response.status);
                }
            });

        };
        // status_eneablnc
        function status_eneablnc() {
            var id = $("#eneablnce_id").val();
            var state = $("#eneablnce_state").val();
            var select_accountant = $("#eneablnce_select_accountant").val();
            var data = {
                'state': state,
                'select_accountant': select_accountant,
                '_token': '{{ csrf_token() }}',
            }
            $.ajax({
                type: "post",
                url: "{{ route('status_eneablnc') }}" + "/" + id,
                data: data,
                dataType: "json",
                success: function(response) {
                    showAlertMessage('state');
                    window.location.reload();
                    // console.log(response.status);
                }
            });

        };
        // status_workclose
        function status_workclose() {

            var id = $("#workclose_id").val();
            var state = $("#workclose_state").val();
            var data = {
                'state': state,
                '_token': '{{ csrf_token() }}',
            }

            $.ajax({
                type: "post",
                url: "{{ route('status_workclose') }}" + "/" + id,
                data: data,
                dataType: "json",
                success: function(response) {
                    showAlertMessage('state');
                    window.location.reload();
                    // console.log(response.status);
                }
            });

        };
    </script>
@endsection
