<div class="row">
    <strong class="mb-3">Seleziona le colonne con cui vuoi lavorare</strong>
    @foreach ($arr as $item)
        <div class="col-md-3 mb-3">
            <input type="checkbox" name="model_column[]" id="model_column{{$item}}" value="{{$item}}">
            <label for="model_column{{$item}}">{{ ucwords(str_replace("_", " ", $item)) }}</label>
        </div>
    @endforeach
    <div class="col-md-3 mb-3">
        <input type="checkbox" name="model_column[]" id="model_columnrimuovi_closed" value="closed">
        <label for="model_columnrimuovi_closed">Rimuovi CHIUSI</label>
    </div>
    <div class="col-md-3 mb-3">
        <input type="checkbox" name="model_column[]" id="model_columnrimuovi_archive" value="archive">
        <label for="model_columnrimuovi_archive">Rimuovi ARICHIVIATI</label>
    </div>
</div>
<div class="row">
    <div class="col-md-3">
        <button type="submit" class="btn btn-sm btn-outline-primary">Ricerca</button>
    </div>
</div>
