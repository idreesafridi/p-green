@if (isset($columnName))
    <table class="table table-responsive table-striped">
        <tr>
            <th>Sr</th>
            <th>Nome</th>
            @if($modelName != 'PrNotDocFile')
                @foreach ($columnName as $item)
                    <th>
                        {{ 
                            $item == "material_list_id" ? 'Materials' : 
                            ($item == "fixtures" ? 'Impresa Infissi' :
                            ($item == "plumbing" ? 'Impresa Impianti Idraulico' :
                            ($item == "electrical" ? 'Impresa Impianti Elettrico' :
                            ($item == "construction" ? 'Impresa Edile 1' :
                            ($item == "construction2" ? 'Impresa Edile 2' :
                            ($item == "photovoltaic" ? 'Fotovoltaico' :
                            ($item == "coordinator" ? 'Coordinatore' :
                            ($item == "construction_manager" ? 'Direttore dei lavor' :
                            ucwords(str_replace("_", " ", $item))
                            ))))))))
                        }}                    
                    </th>
                @endforeach
            @endif
        </tr>
        @php
            $sr = 1;
        @endphp
        @if (count($conData) > 0)
            @if($modelName == 'ConstructionMaterial')
                @php
                    $totalCon = 0;
                @endphp
                @foreach ($conData as $itemCon)
                    @php
                        $first = 0;
                        if($filter == null){
                            $conFilters = $itemCon->$modelName;
                        }else{
                            // $modelInstance = $itemCon->$modelName()->first();
                            // if($modelInstance == null){
                            //     $conFilters = $itemCon->$modelName;
                            // }
                            // else{
                            //     // $conFilters = $modelInstance->where(function ($query) use ($filter) {
                            //     //     foreach ($filter as $column => $values) {
                            //     //         $query->where($column, $values);
                            //     //     }
                            //     // })->first();
                            //     // $first = 1;

                            //     $conFilters = \App\Models\MaterialList::where(function ($query) use ($filter) {
                            //         foreach ($filter as $column => $values) {
                            //             $query->where($column, $values);
                            //         }
                            //     })->where('construction_site_id', $itemCon->id)->get();
                            // //     $first = 1;
                            // // }
                            // $conFilters = \App\Models\MaterialList::where(function ($query) use ($filter) {
                            //         foreach ($filter as $column => $values) {
                            //             $query->where($column, $values);
                            //         }
                            //     })->where('construction_site_id', $itemCon->id)->get();
                            //dd($filter);
                            $conFilters = \App\Models\ConstructionMaterial::where(function ($query) use ($filter) {
                                    foreach ($filter as $column => $values) {
                                        //$query->where($column, $values);
                                        if ($column === 'consegnato' && $values === "0") {
                                            $query->where(function ($innerQuery) use ($column) {
                                                $innerQuery->whereNull('consegnato')
                                                        ->orWhere('consegnato', "0");
                                            });
                                        }
                                        else if ($column === 'montato' && $values === "0") {
                                            $query->where(function ($innerQuery) use ($column) {
                                                $innerQuery->whereNull('montato')
                                                        ->orWhere('montato', "0");
                                            });
                                        } else {
                                            $query->where($column, $values);
                                        }
                                    }
                                })->where('construction_site_id', $itemCon->id)->get();
                                $first = 1;
                        }
                    @endphp

                    @if ($first == 1)
                        @if ($conFilters != null)
                            @foreach ($conFilters as $itemConMat)
                                <tr>
                                    <td>{{ $sr++ }}</td>
                                    <td>{{ $itemCon->surename }} {{ $itemCon->name }}</td>
                                    @foreach ($columnName as $itemColum)
                                        @if($itemColum == 'consegnato')
                                            <td>{{ $itemConMat->$itemColum == 1 ? 'Yes' : 'No' }}</td>
                                        @elseif($itemColum == 'montato')
                                            <td>{{ $itemConMat->$itemColum == 1 ? 'Yes' : 'No' }}</td>
                                        @elseif($itemColum == 'updated_by')
                                            <td>{{ $itemConMat->User == null ? '' : $itemConMat->User->name }}</td>
                                        @elseif($itemColum == 'material_list_id')
                                            <td>{{ $itemConMat->MaterialList == null ? '' : $itemConMat->MaterialList->name }}</td>
                                        @else
                                            <td>{{ $itemConMat->$itemColum }}</td>
                                        @endif
                                    @endforeach
                                </tr>
                                @php
                                    $totalCon++;
                                @endphp
                            @endforeach
                        @endif
                    @else
                        @foreach ($conFilters as $itemConMat)
                            <tr>
                                <td>{{ $sr++ }}</td>
                                <td>{{ $itemCon->surename }} {{ $itemCon->name }}</td>
                                @foreach ($columnName as $itemColum)
                                    @if($itemColum == 'consegnato')
                                        <td>{{ $itemConMat->$itemColum == 1 ? 'Yes' : 'No' }}</td>
                                    @elseif($itemColum == 'montato')
                                        <td>{{ $itemConMat->$itemColum == 1 ? 'Yes' : 'No' }}</td>
                                    @elseif($itemColum == 'updated_by')
                                        <td>{{ $itemConMat->User == null ? '' : $itemConMat->User->name }}</td>
                                    @elseif($itemColum == 'material_list_id')
                                        <td>{{ $itemConMat->MaterialList == null ? '' : $itemConMat->MaterialList->name }}</td>
                                    @else
                                        <td>{{ $itemConMat->$itemColum }}</td>
                                    @endif
                                @endforeach
                            </tr>
                            @php
                                $totalCon++;
                            @endphp
                        @endforeach
                    @endif
                    
                @endforeach
                {{-- <tr>
                    <td colspan="6" class="text-center">{{ $conData->links('pagination::bootstrap-4') }}</td>
                </tr> --}}
                <tr>
                    <td colspan="{{ count($columnName) }}">Total records: {{$totalCon}}</td>
                </tr>
            @elseif($modelName == 'ConstructionJobDetail')
                @php
                    $totalCon = 0;
                @endphp
                @foreach ($conData as $item) 
                    @php
                        $totalCon++;
                    @endphp
                    <tr>
                        <td>{{ $sr++ }}</td>
                        <td>{{ $item->surename }} {{ $item->name }}</td>
                        @foreach ($columnName as $itemColum)
                            <td>
                                @if ($item && $modelName && $itemColum && $item->$modelName && $item->$modelName->$itemColum)
                                    @php
                                        $data = \App\Models\User::find($item->$modelName->$itemColum);
                                    @endphp
                                    {{$data->name}}
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
                <tr>
                    <td colspan="{{ count($columnName) }}">Total records: {{$totalCon}}</td>
                </tr>
            @elseif($modelName == 'PrNotDocFile')
                @php
                    $totalCon = 0;
                @endphp
                @foreach ($conData as $item) 
                    @php
                        $totalCon++;
                    @endphp
                    <tr>
                        <td>{{ $sr++ }}</td>
                        <td>{{ $item->surename }} {{ $item->name }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="{{ count($columnName) }}">Total records: {{$totalCon}}</td>
                </tr>
            @else
                @php
                    $searchFilter = isset($filter['search_filter']) ? $filter['search_filter'] : null;
                    if($searchFilter != null){
                        // $cons = $conData->where('name', 'LIKE', '%'.$searchFilter.'%')->orWhere('surename', 'LIKE', '%'.$searchFilter.'%')->paginate(20);
                        // Assuming $conData is an Eloquent model or query builder instance
                        $cons = $conData->where(function ($query) use ($searchFilter) {
                            $query->where('name', 'LIKE', '%' . $searchFilter . '%')
                                ->orWhere('surename', 'LIKE', '%' . $searchFilter . '%');
                        })->all();

                        if(count($cons) <= 0){
                            $columnArr = [];
                            foreach($columnName as $column){
                                // $cons = $conData->whereHas($modelName, function ($query) use ($column, $searchFilter){
                                //     $query->where($column, 'LIKE', '%'.$searchFilter.'%');
                                // })->get();

                                $cons = $conData->filter(function ($item) use ($modelName, $column, $searchFilter) {
                                    // Check if $modelName is a valid property on $item
                                    if (isset($item->$modelName)) {
                                        // Check if the column exists and matches the search filter exactly
                                        return isset($item->$modelName->$column) &&
                                            $item->$modelName->$column === $searchFilter;
                                    }
                                    return false;
                                });

                                if(count($cons) > 0){
                                    break;
                                }
                            }
                        }
                    }
                    else{
                        $cons = $conData;
                    }
                @endphp
                @foreach ($cons as $item)
                    <tr>
                        <td>{{ $sr++ }}</td>
                        <td>{{ $item->surename }} {{ $item->name }}</td>
                        @foreach ($columnName as $itemColum)
                            {{-- <td>{{$item->$modelName->$itemColum}}</td> --}}
                            {{-- @dd($modelName) --}}
                            <td>
                                @if ($item->$modelName !== null)
                                    {{$item->$modelName->$itemColum}}
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
                <tr>
                    <td>Total records: {{count($conData)}}</td>
                </tr>
            @endif
        @else
            <tr>
                <td colspan="{{ count($columnName) }}">No record</td>
            </tr>
        @endif
    </table>
@else
    <span>Seleziona prima il nome della colonna</span>
@endif
