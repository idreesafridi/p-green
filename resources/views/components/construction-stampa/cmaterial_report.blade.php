<div class="matarial_report">
   
<div class="container0" style="position: relative;">
<img src="{{ asset('assets/stampa/MaterialReport/materialPriceReport1.png') }}"
            style="height: 1162px;width: 800px;position: relative; margin-top: 3px;">
            <div class="dataNascita">{{ $construction->company_name }} </div>
            {{-- <div class="dataNascita">company name 1 </div> --}}
            
<div class="comuneNascita">{{ $construction->user['name'] }}</div>
{{-- <div class="comuneNascita">user name 2</div> --}}
<div class="provinciaNascita">{{ $construction->user['residence_city'] }} {{ $construction->user['birth_country']}}</div>
{{-- <div class="provinciaNascita">residence city 3</div> --}}
<div class="comuneResid">{{ $construction->user['residence'] }}</div>
{{-- <div class="comuneResid">residence 4</div> --}}
<div class="provinciaResid">{{ $construction->user['fiscal_code'] }}</div>
{{-- <div class="provinciaResid"> fiscal_code 5</div> --}}
<div class="viaResid">{{ $total }}</div>
{{-- <div class="viaResid">total 6</div> --}}
<div id="resultContainer" class="comuneImm"></div>
</div>

<div class="container-fluid">

  <b> Impresa:</b> {{$construction->user['name']}} </br>
  <b> Cantiere: </b> {{$construction['constructionName']}}


  <table class="table dt-responsive mt-1" style="border: 2px solid black; background-color: #e2efd9;">
    <thead style="background-color: #8eaadb; color: black;">
        <tr>
            <th scope="col" colspan="3">LAVORAZIONE </th>
            <th scope="col">€/U.D.M. </th>
            <th scope="col">U.D.M.</th>
            <th scope="col">q.tà</th>
            <th scope="col" colspan="3">TOT</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($combinedData as $item)
        <tr>
            <td colspan="3">{{ $item['Descrizionelavorazione'] }}</td>
            <td>{{ number_format($item['Prezzo_per_unita'], 2, '.', ',') }}</td>
            <td>pz</td>
            <td>{{$item['quantity']}}</td>
            <td colspan="3">{{$item['SumOfPrezzo_per_unita']}}</td>
        </tr>
        
        @endforeach
        <tr>
            <td colspan="3"></td>
            <td></td>
            <td></td>
            <td><b>Totale:</b></td>
            <td colspan="3">{{$total}}</td>
        </tr>
    </tbody>
</table>



</div>
