<x-doc-app-layout>

    @section('styles')
        <style>
            .normal_body {
                background-color: white !important;
            }
        </style>
    @endsection

    @php
        $comp = 'construction-stampa.c' . $page;
    @endphp
    <div>
        <x-dynamic-component :component="$comp" :construction="$data" :total="$total" :combinedData="$combinedData"  />
    </div>


    <script src="{{ asset('assets/js/jquery-3.6.1.min.js') }}"></script>

    <script>
        $(document).ready(function() {
            let numericValue = 0;
            let result = '';
            function convertNumericInput() {
                const resultContainer = document.getElementById('resultContainer');
                result = NumToChar(numericValue);
                resultContainer.textContent = result;
            }


            function NumToChar(startValue) {
                let strValue, strNum, strResult = '';

                // Initial checks (handles up to 999,999,999,999)
                if (isNaN(startValue)) {
                    return '** Valore non calcolabile **';
                }

                if (startValue === 0) {
                    return 'Zero';
                }

                const isNegative = startValue < 0;
                startValue = Math.abs(startValue).toFixed(2).toString(); // Ensure two decimal places for cents
                const parts = startValue.split('.');
                const dollars = parseInt(parts[0]);
                const cents = parseInt(parts[1]);

                if (dollars.toString().length > 12) {
                    return '** Valore non calcolabile **';
                }

                if (dollars === 0) {
                    return 'Zero';
                }

                startValue = dollars.toString().padStart(12, '0');

                // Conversion
                for (let i = 1; i <= 4; i++) {
                    strValue = startValue.substr(i * 3 - 3, 3);

                    // Hundreds
                    strNum = strValue[0];
                    switch (strNum) {
                        case '1':
                            strResult += 'Cento';
                            break;
                        case '2':
                            strResult += 'Duecento';
                            break;
                        case '3':
                            strResult += 'Trecento';
                            break;
                        case '4':
                            strResult += 'Quattrocento';
                            break;
                        case '5':
                            strResult += 'Cinquecento';
                            break;
                        case '6':
                            strResult += 'Seicento';
                            break;
                        case '7':
                            strResult += 'Settecento';
                            break;
                        case '8':
                            strResult += 'Ottocento';
                            break;
                        case '9':
                            strResult += 'Novecento';
                            break;
                    }

                    // Tens
                    strNum = strValue[1];
                    switch (strNum) {
                        case '1':
                            switch (strValue[2]) {
                                case '0':
                                    strResult += 'Dieci';
                                    break;
                                case '1':
                                    strResult += 'Undici';
                                    break;
                                case '2':
                                    strResult += 'Dodici';
                                    break;
                                case '3':
                                    strResult += 'Tredici';
                                    break;
                                case '4':
                                    strResult += 'Quattordici';
                                    break;
                                case '5':
                                    strResult += 'Quindici';
                                    break;
                                case '6':
                                    strResult += 'Sedici';
                                    break;
                                case '7':
                                    strResult += 'Diciassette';
                                    break;
                                case '8':
                                    strResult += 'Diciotto';
                                    break;
                                case '9':
                                    strResult += 'Diciannove';
                                    break;
                            }
                            break;
                        case '2':
                            strResult += (strValue[2] === '1') ? 'VentUno' : (strValue[2] === '8') ? 'VentOtto' :
                                'Venti';
                            break;
                        case '3':
                            strResult += (strValue[2] === '1') ? 'TrentUno' : (strValue[2] === '8') ? 'TrentOtto' :
                                'Trenta';
                            break;
                        case '4':
                            strResult += (strValue[2] === '1') ? 'QuarantUno' : (strValue[2] === '8') ?
                                'QuarantOtto' : 'Quaranta';
                            break;
                        case '5':
                            strResult += (strValue[2] === '1') ? 'CinquantUno' : (strValue[2] === '8') ?
                                'CinquantOtto' : 'Cinquanta';
                            break;
                        case '6':
                            strResult += (strValue[2] === '1') ? 'SessantUno' : (strValue[2] === '8') ?
                                'SessantOtto' : 'Sessanta';
                            break;
                        case '7':
                            strResult += (strValue[2] === '1') ? 'SettantUno' : (strValue[2] === '8') ?
                                'SettantOtto' : 'Settanta';
                            break;
                        case '8':
                            strResult += (strValue[2] === '1') ? 'OttantUno' : (strValue[2] === '8') ?
                                'OttantOtto' : 'Ottanta';
                            break;
                        case '9':
                            strResult += (strValue[2] === '1') ? 'NovantUno' : (strValue[2] === '8') ?
                                'NovantOtto' : 'Novanta';
                            break;
                    }

                    // Units (only if tens are not 10)
                    if (strValue[1] !== '1') {
                        strNum = strValue[2];
                        switch (strNum) {
                            case '1':
                                strResult += 'Uno';
                                break;
                            case '2':
                                strResult += 'Due';
                                break;
                            case '3':
                                strResult += 'Tre';
                                break;
                            case '4':
                                strResult += 'Quattro';
                                break;
                            case '5':
                                strResult += 'Cinque';
                                break;
                            case '6':
                                strResult += 'Sei';
                                break;
                            case '7':
                                strResult += 'Sette';
                                break;
                            case '8':
                                strResult += 'Otto';
                                break;
                            case '9':
                                strResult += 'Nove';
                                break;
                        }
                    }

                    // Adjustments
                    switch (i) {
                        case 1:
                            if (strValue !== '000') strResult += 'Miliardi';
                            break;
                        case 2:
                            if (strValue !== '000') strResult += 'Milioni';
                            break;
                        case 3:
                            if (strValue !== '000') strResult += 'Mila';
                            break;
                    }

                    switch (strResult) {
                        case 'UnoMila':
                            strResult = 'Mille';
                            break;
                        case 'UnoMilioni':
                            strResult = 'UnMilione';
                            break;
                        case 'UnoMiliardi':
                            strResult = 'UnMiliardo';
                            break;
                    }
                }

                // Add cents if present
                if (cents > 0) {
                    strResult += 'virgola' + NumToChar(cents);
                }

                if (isNegative) {
                    strResult = 'Menos ' + strResult;
                }

                strResult = strResult.toLowerCase().trim();
                return strResult; //NUMBER TO PICK


            }

            function setNumericValue(value) {
                numericValue = value;
                convertNumericInput();
            }


            function setNumericValue(value) {
                numericValue = value;

                convertNumericInput();
            }
            @if (isset($total))
                let total = {{ $total }};

                setNumericValue(total);
            @endif
        });
    </script>

</x-doc-app-layout>
