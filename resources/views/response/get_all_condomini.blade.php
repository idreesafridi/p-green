@forelse ($data as $item)

    @if($item->ConstructionCondomini != null && optional($item->ConstructionCondomini)->ConstructionSiteSetting->type_of_property != 'Condominio')
    <tr>
        <td>
            <a target="_blank"
                href="{{ route('construction_detail', ['id' => $item->ConstructionCondomini->id, 'pagename' => 'Cantiere']) }}">{{ $item->ConstructionCondomini->name }}
                {{ $item->ConstructionCondomini->surename }}</a>
        </td>
        <td>{{ $item->ConstructionCondomini->residence_common }}</td>
   
        <td>{{ optional($item->PropertyDataRevers)->property_street . ' ' . optional($item->PropertyDataRevers)->property_house_number . ' ' . optional($item->PropertyDataRevers)->property_postal_code }}</td>

        <td>
            @if (
                $item->ConstructionCondomini->StatusTechnician != null &&
                    $item->ConstructionCondomini->StatusTechnician->user != null)
                {{ $item->ConstructionCondomini->StatusTechnician->user->name }}
            @endif
        </td>
        <td>0/13</td>
        <td>
            <span class="badge bg-green">{{ $item->ConstructionCondomini->latest_status }}</span>
        </td>
        <td>
            <form action="{{ route('condo_delete') }}" method="post">
                @csrf
                @method('DELETE')
                <input type="hidden" name="condo_id" value="{{ $item->id }}">
                <button type="submit" class="btn fa fa-trash text-danger"></button>
            </form>
        </td>
    </tr>
    @endif
@empty
    <tr>
        <td colspan="7" class="text-center">Nessun condominio</td>
    </tr>
@endforelse
