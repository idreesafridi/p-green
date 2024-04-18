<div class="row ">
    <div class="col-md-3 mt-3">
        <select class="form-select form-control" name="material_list_id[]" onchange = "getMatListinfo(this)" required>
            <option selected disabled>Seleziona materiale</option>
            @foreach ($list as $item)
                <option value="{{ $item->id }}">{{ $item->name }}</option>
            @endforeach
        </select>
        {{-- <input type="hidden" id="list_array" value="{{ $list }}"> --}}
    </div>
    <div class="col-md-3 mt-3">
        <div class="input-price-fixed">

        
        <input type="number" name="quantity[]" class="form-control"  placeholder="Quantità" onkeyup="calculateTotalPrice(this)" required>
        <span>
            <span class="unit unit-fixed" id="get_units" >pz</span>
        </span>
    </div>
    </div>
</div>
