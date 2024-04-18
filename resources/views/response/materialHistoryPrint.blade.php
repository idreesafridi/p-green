<div style="background-color: #fff; position: relative;">
    <div class="container mt-3">
          
        @if(count($data) > 0)
        <table class="table table-striped table-responsive">
            <thead>
                <tr style="background-color: #020202;">
                    <th class="text-white">UTENTE</th>
                    <th class="text-white">Prima Di Modifica</th>
                    <th class="text-white">Dopo Modifica</th>
                    <th class="text-white">Motivazione</th>
                    <th class="text-white">DATA</th>
                </tr>
            </thead>
            <tbody>
               
                @foreach($data as $index => $row)
                <tr>
                    <td>{{ $row->User->name }}</td>
                    <td>
                        @if($row->Original != null)
                        {{ strtoupper($row->updated_field) }} = {{ $row->Original }}
                        @else
                        -
                        @endif
                    </td>
                    <td>
                        @if($row->Updated_to != null)
                        {{ strtoupper($row->updated_field) }} =  {{ $row->Updated_to }}
                        @else
                        -
                        @endif 
                    </td>
                    
                        <td>{{$row->reason}} </td>
                    <td>{{ \Carbon\Carbon::parse($row->updated_at)->format('d/m/Y') }}</td>
                </tr>
                @endforeach
                
            </tbody>
        </table>
        @else
        <p>Nessun record trovato</p>
    @endif
    </div>
</div>