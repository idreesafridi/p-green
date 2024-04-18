<div class="mb-3">
    @foreach ($cons as $item)
        <button type="button" class="btn btn-primary">
            {{ $item->ConstructionSite->name }} {{ $item->ConstructionSite->surename }}
            <span class="badge" onclick="removeShippingCentri('{{ $item->id }}')">
                <i class="fa fa-times"></i>
            </span>
        </button>
    @endforeach
</div>
