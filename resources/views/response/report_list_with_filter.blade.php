<table class="table table-responsive table-striped">
        <tr>
            <th>Sr</th>
            <th>Nome</th>
            @foreach ($columnName as $item)
                <th>{{ ucwords(str_replace("_", " ", $item)) }}</th>
            @endforeach
        </tr>
        @if (count($conData) > 0)
            @if($modelName == 'ConstructionMaterial')
                @foreach ($conData as $itemCon)                
                    @foreach ($conFilters as $itemConMat)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $itemCon->surename }} {{ $itemCon->name }}</td>
                            @foreach ($columnName as $itemColum)
                                @if($itemColum == 'consegnato')
                                    <td>{{ $itemConMat->$itemColum == 1 ? 'Yes' : 'No' }}</td>
                                @elseif($itemColum == 'montato')
                                    <td>{{ $itemConMat->$itemColum == 1 ? 'Yes' : 'No' }}</td>
                                @elseif($itemColum == 'updated_by')
                                    <td>{{ $itemConMat->User->name }}</td>
                                @else
                                    <td>{{ $itemConMat->$itemColum }}</td>
                                @endif
                            @endforeach
                        </tr>
                    @endforeach 
                @endforeach
            @else
                @foreach ($conData as $itemCon)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $itemCon->surename }} {{ $itemCon->name }}</td>
                    @foreach ($columnName as $itemColum)
                        <td>{{$itemCon->$modelName->$itemColum}}</td>
                    @endforeach
                </tr>
                @endforeach
            @endif
        @else
            <tr>
                <td>Nessun record trovato</td>
            </tr>         
        @endif
    </table>