<table class="table table-hover">
    <thead class="justify-content-around ">
        <th>Cognome</th>
        <th>Nome</th>
        <th>Common</th>
        <th>Indirizzo</th>
        <th></th>
    </thead>
    <tbody>
        @foreach ($cons as $item)
            <tr>
                <td>{{ $item->ConstructionSite->surename }}</td>
                <td>{{ $item->ConstructionSite->name }}</td>
                <td>{{ $item->ConstructionSite->residence_common }}</td>
                <td>{{ $item->ConstructionSite->residence_street . ' ' . $item->ConstructionSite->residence_house_number . ' ' . $item->ConstructionSite->residence_postal_code }}
                </td>
                <td>
                    <button class="btn btn-outline-primary btn-sm" title="Edit {{ $item->ConstructionSite->surename }}"
                        onclick="editMatCantri('{{ $item->id }}')">
                        <i class="fa fa-edit"></i>
                    </button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
