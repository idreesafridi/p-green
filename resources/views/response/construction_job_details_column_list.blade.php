@if(isset($arr))
    <div class="row">
        <strong class="mb-3">Seleziona le colonne con cui vuoi lavorare</strong>
        <div class="col-md-3 mb-3">
            <input type="radio" name="model_column[]" id="model_columnassigned" value="assigned">
            <label for="model_columassigned">CARICATO</label>
        </div>
        <div class="col-md-3 mb-3">
            <input type="radio" name="model_column[]" id="model_columnmissing" value="missing">
            <label for="model_columnmissing">MANCANTE</label>
        </div>
    </div>
@else
    <div class="row">
        <strong class="mb-3">Seleziona le colonne con cui vuoi lavorare</strong>
        <div class="col-md-3 mb-3">
            <input type="radio" name="model_column[]" id="model_columnfixtures" value="fixtures">
            <label for="model_columnfixtures">Impresa Infissi</label>
        </div>
        <div class="col-md-3 mb-3">
            <input type="radio" name="model_column[]" id="model_columnplumbing" value="plumbing">
            <label for="model_columnplumbing">Impresa Impianti Idraulico</label>
        </div>
        <div class="col-md-3 mb-3">
            <input type="radio" name="model_column[]" id="model_columnelectrical" value="electrical">
            <label for="model_columnelectrical">Impresa Impianti Elettrico</label>
        </div>
        <div class="col-md-3 mb-3">
            <input type="radio" name="model_column[]" id="model_columnconstruction" value="construction">
            <label for="model_columnconstruction">Impresa Edile 1</label>
        </div>
        <div class="col-md-3 mb-3">
            <input type="radio" name="model_column[]" id="model_columnconstruction2" value="construction2">
            <label for="model_columnconstruction2">Impresa Edile 2</label>
        </div>
        <div class="col-md-3 mb-3">
            <input type="radio" name="model_column[]" id="model_columnphotovoltaic" value="photovoltaic">
            <label for="model_columnphotovoltaic">Fotovoltaico</label>
        </div>
        <div class="col-md-3 mb-3">
            <input type="radio" name="model_column[]" id="model_columncoordinator" value="coordinator">
            <label for="model_columncoordinator">Coordinatore</label>
        </div>
        <div class="col-md-3 mb-3">
            <input type="radio" name="model_column[]" id="model_columnconstruction_manager" value="construction_manager">
            <label for="model_columnconstruction_manager">Direttore dei lavori</label>
        </div>
    </div>
@endif

<div class="row">
    <div class="col-md-3">
        <button type="submit" class="btn btn-sm btn-outline-primary">Ricerca</button>
    </div>
</div>
