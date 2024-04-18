@if ($data != null)

    @foreach ($data as $data)
        <tr>
            <input class="bg-white" name="assistanse_id[]" value="{{ $data->id }}" type="hidden">
            <td>
                <input class="bg-white" name="machine_model[]" value="{{ $data->machine_model }}" disabled="disabled"
                    type="text">
            </td>
            <td class="hideInMobile hideInTablet">
                <input class="bg-white" name="freshman[]" value="{{ $data->freshman }}" disabled="disabled"
                    type="text">
            </td>
            <td>
                @php
                $expiryDate = $data->expiry_date;
                $formattedDate = date("Y-m-d", strtotime($expiryDate));
                @endphp
                    <input class="bg-white" name="start_date[]" value="{{ $data->start_date }}" disabled="disabled"
                    type="hidden">
                   
                    <input class="bg-white" name="expiry_date[]" value="{{ $formattedDate }}" disabled="disabled"
                    type="date">
            </td>
            <td class="hideInMobile hideInTablet">
                <a href="#">
                    <i class="fa fa-money fa-2x"></i>
                </a>
            </td>
            <td class="hideInMobile hideInTablet">
                <a href="#">
                    <i class="fa fa-list fa-2x"></i>
                </a>
            </td>
            <td class="hideInMobile">
                <input class="bg-white" name="notes[]" value="{{ $data->notes }}" disabled="disabled" type="text">
            </td>
            <td class="hideInMobile">
                <select class="bg-gray text-dark  p-2" type="" name="state[]" disabled="disabled" style="background:{{$data->state == 'Da completare' ? '#FFBA33' : '#198754'}};">
                    <option value="Da completare" {{ $data->state == 'Da completare' ? 'selected' : '' }}>
                        Da completare
                    </option>
                    <option value="Completato" {{ $data->state == 'Completato' ? 'selected' : '' }}>
                        Completato
                    </option>
                </select>
            </td>
            <td class="hideInMobile">
                <button type="button" onclick="assist_id('{{ $data->id }}')"
                    class="p-0 btn btn-link btn-sm text-danger">
                    <i class="fa fa-trash f-17 me-2"></i>
                </button>
            </td>
        </tr>
    @endforeach

@endif
