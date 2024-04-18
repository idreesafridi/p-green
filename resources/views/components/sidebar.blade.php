<td class="column-1 hideInMobile hideInTablet">
    <div>
        <button class="btn btn-outline-danger mb-4" onclick="remove_filter()">
            <i class="fa fa-remove"> </i>
            Rimuovi filtro
        </button>

        <ul class="list-unstyled list-group list-group-custom list-group-flush mb-4 filter-list-block">
            <strong>Preanalisi</strong>
            <li class="list-group-item">
                <input class="form-check-input me-1 preanalysis" onchange="optionStoreSession('preanalysis')"
                    id="preanalysis_to_be_invoiced" type="checkbox" value="To be invoiced">
                <label for="preanalysis_to_be_invoiced">Da fatturare</label>
            </li>
            <li class="list-group-item">
                <input class="form-check-input me-1 preanalysis" onchange="optionStoreSession('preanalysis')"
                    id="preanalysis_revenue" type="checkbox" value="Revenue">
                <label for="preanalysis_revenue">Fatturato</label>
            </li>
            <li class="list-group-item">
                <input class="form-check-input me-1 preanalysis" onchange="optionStoreSession('preanalysis')"
                    id="preanalysis_cashed_out" type="checkbox" value="Cashed out">
                <label for="preanalysis_cashed_out">Incassato</label>
            </li>
            <li class="list-group-item">
                <input class="form-check-input me-1 preanalysis" onchange="optionStoreSession('preanalysis')"
                    id="preanalysis_not_due" type="checkbox" value="Not due">
                <label for="preanalysis_not_due">Non dovuta</label>
            </li>
        </ul>

        <ul class="list-unstyled list-group list-group-custom list-group-flush mb-4 filter-list-block">
            <strong>Tecnico</strong>
            <li class="list-group-item">
                <input class="form-check-input me-1 tecnico" onchange="optionStoreSession('tecnico')" type="checkbox"
                    id="tecnico_not_assigned" value="Not Assigned">
                <label for="tecnico_not_assigned">Assegnato</label>
            </li>
            <li class="list-group-item">
                <input class="form-check-input me-1 tecnico" onchange="optionStoreSession('tecnico')" type="checkbox"
                    id="tecnico_assigned" value="Assigned">
                <label for="tecnico_assigned">Da assegnare</label>
            </li>
        </ul>

        <ul class="list-unstyled list-group list-group-custom list-group-flush mb-4 filter-list-block">
            <strong>Rilievo</strong>
            <li class="list-group-item">
                <input class="form-check-input me-1 relaif" onchange="optionStoreSession('relaif')" type="checkbox"
                    value="Received" id="relaif_received">
                <label for="relaif_received">Ricevuto</label>
            </li>
            <li class="list-group-item">
                <input class="form-check-input me-1 relaif" onchange="optionStoreSession('relaif')" type="checkbox"
                    value="To assign" id="relaif_to_assign">
                <label for="relaif_to_assign">Do assegnare</label>
            </li>

        </ul>

        <ul class="list-unstyled list-group list-group-custom list-group-flush mb-4 filter-list-block">
            <strong>Legge 10 </strong>
            <li class="list-group-item">
                <input class="form-check-input me-1 law_10" onchange="optionStoreSession('law_10')" type="checkbox"
                    id="law_10_completed" value="Completed">
                <label for="law_10_completed">Completata</label>
            </li>
            <li class="list-group-item">
                <input class="form-check-input me-1 law_10" onchange="optionStoreSession('law_10')" type="checkbox"
                    id="law_10_waiting" value="Waiting">
                <label for="law_10_waiting">In Atteso</label>
            </li>
        </ul>

        <ul class="list-unstyled list-group list-group-custom list-group-flush mb-4 filter-list-block">
            <strong>Computo</strong>
            <li class="list-group-item">
                <input class="form-check-input me-1 computo" onchange="optionStoreSession('computo')" type="checkbox"
                    id="computo_completed" value="Completed">
                <label for="computo_complete">Completata</label>
            </li>
            <li class="list-group-item">
                <input class="form-check-input me-1 computo" onchange="optionStoreSession('computo')" type="checkbox"
                    id="computo_waiting" value="Waiting">
                <label for="computo_wait">In Attesa</label>
            </li>
        </ul>

        <ul class="list-unstyled list-group list-group-custom list-group-flush mb-4 filter-list-block">
            <strong>Notifica Preliminare</strong>
            <li class="list-group-item">
                <input class="form-check-input me-1 pre_noti" onchange="optionStoreSession('pre_noti')" type="checkbox"
                    id="pre_noti_completed" value="Completed">
                <label for="pre_noti_completed">Completata</label>
            </li>
            <li class="list-group-item">
                <input class="form-check-input me-1 pre_noti" onchange="optionStoreSession('pre_noti')"
                    type="checkbox" id="pre_noti_waiting" value="Waiting">
                <label for="pre_noti_waiting">In Attesa</label>
            </li>
        </ul>

        <ul class="list-unstyled list-group list-group-custom list-group-flush mb-4 filter-list-block">
            <strong>Pratica Protocollato</strong>
            <li class="list-group-item">
                <input class="form-check-input me-1 register_practice"
                    onchange="optionStoreSession('register_practice')" type="checkbox"
                    id="register_practice_completed" value="Completed">
                <label for="register_practice_completed">Completata</label>
            </li>
            <li class="list-group-item">
                <input class="form-check-input me-1 register_practice"
                    onchange="optionStoreSession('register_practice')" type="checkbox" id="register_practice_waiting"
                    value="Waiting">
                <label for="register_practice_waiting">In Attesa</label>
            </li>
        </ul>

        <ul class="list-unstyled list-group list-group-custom list-group-flush mb-4 filter-list-block">
            <strong>Lavori iniziati</strong>
            <li class="list-group-item">
                <input class="form-check-input me-1 work_started" onchange="optionStoreSession('work_started')"
                    type="checkbox" id="work_started_initiates" value="Initiates">
                <label for="work_started_initiates">Iniziati</label>
            </li>
            <li class="list-group-item">
                <input class="form-check-input me-1 work_started" onchange="optionStoreSession('work_started')"
                    type="checkbox" id="work_started_to_start" value="To start">
                <label for="work_started_to_start">Da iniziare</label>
            </li>
        </ul>

        <ul class="list-unstyled list-group list-group-custom list-group-flush mb-4 filter-list-block">
            <strong>SAL</strong>
            <li class="list-group-item">
                <input class="form-check-input me-1 sal" onchange="optionStoreSession('sal')" type="checkbox"
                    id="sal_completed" value="Completed">
                <label for="sal_completed">In Attesa</label>
            </li>
            <li class="list-group-item">
                <input class="form-check-input me-1 sal" onchange="optionStoreSession('sal')" type="checkbox"
                    id="sal_waiting" value="Waiting">
                <label for="sal_waiting">Completato</label>
            </li>
        </ul>

        <ul class="list-unstyled list-group list-group-custom list-group-flush mb-4 filter-list-block">
            <strong>SALDO ENEA</strong>
            <li class="list-group-item">
                <input class="form-check-input me-1 balance_enea" onchange="optionStoreSession('balance_enea')"
                    type="checkbox" id="balance_enea_completed" value="Completed">
                <label for="balance_enea_completed">In Attesa</label>
            </li>
            <li class="list-group-item">
                <input class="form-check-input me-1 balance_enea" onchange="optionStoreSession('balance_enea')"
                    type="checkbox" id="balance_enea_waiting" value="Waiting">
                <label for="balance_enea_waiting">Completato</label>
            </li>
        </ul>

        <ul class="list-unstyled list-group list-group-custom list-group-flush mb-4 filter-list-block">
            <strong>Chiuso</strong>
            <li class="list-group-item">
                <input class="form-check-input me-1 locked_down" onchange="optionStoreSession('locked_down')"
                    type="checkbox" id="locked_down_completed" value="Completed">
                <label for="locked_down_completed">In Attesa</label>
            </li>
            <li class="list-group-item">
                <input class="form-check-input me-1 locked_down" onchange="optionStoreSession('locked_down')"
                    type="checkbox" id="locked_down_locked_down" value="Locked down">
                <label for="locked_down_locked_down">Chiuso</label>
            </li>
        </ul>

    </div>
</td>
