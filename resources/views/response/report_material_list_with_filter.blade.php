<table class="table table-responsive table-striped">
    <tr>
        <th>Sr</th>
        <th>Nome</th>
        @foreach ($columnName as $item)
            <th>{{ ucwords(str_replace('_', ' ', $item)) }}</th>
        @endforeach
    </tr>
    @if (count($matData) > 0)
        @dd($matData)
        <tr>
            <td></td>
        </tr>
    @else
        <tr>
            <td>Nessun record trovato</td>
        </tr>
    @endif
</table>
