<div class="row mx-0">
    <div class="col-md-6 mt-3">
        <select class="form-select" name="material_list_id" id="mat_list_ajax" required>
            <option selected disabled>Seleziona materiale</option>
            @foreach ($list as $item)
                <option value="{{ $item->id }}">{{ $item->name }}</option>
            @endforeach
        </select>
        <input type="hidden" id="list_array" value="{{ $list }}">
    </div>
    <div class="col-md-6 mt-3">
        <input type="number" name="quantity" class="form-control" placeholder="Quantità" required>
        <span >
            <span class="unit" id="get_units" >pz</span>
        </span>
    </div>
</div>
