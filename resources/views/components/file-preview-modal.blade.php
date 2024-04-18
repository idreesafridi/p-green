<div class="modal fade" id="{{$modelId}}" aria-labelledby="exampleModalLabel" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Anteprima Documento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if ($filepath == null)
                <p>File non caricato. Per favore carica prima.</p>
            @elseif (isset($filename) && $filename == 'Dwg')
                @php
                    $filePath = public_path('construction-assets/' . $filepath);
                    if (file_exists($filePath)) {
                        $fileContent = file_get_contents($filePath);
                        try {
                            $decryptedContent = Crypt::decrypt($fileContent);
                            $base64EncodedContent = base64_encode($decryptedContent);
                        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                            // If decryption fails, set the content to null or handle the error
                            $base64EncodedContent = null;
                        }
                    } else {
                        // File does not exist, handle this case accordingly
                        $base64EncodedContent = null;
                    }
                @endphp
                @if ($base64EncodedContent)
                    <embed id="view-file-frame" src="data:application/pdf;base64,{{ $base64EncodedContent }}" type="application/pdf" width="100%" height="600px"></embed>
                @else
                <embed id="view-file-frame" src="{{ asset('construction-assets/' . $filepath) }}?v={{ time() }}" type="application/pdf" width="100%" height="600px"></embed>
                    <p>Error: Il file non è stato trovato.</p>
                @endif
            @else
                @php
                    $filePath = public_path('construction-assets/' . $filepath); // Corrected variable name
                    if (file_exists($filePath)) {
                        $fileContent = file_get_contents($filePath);
                        try {
                            $decryptedContent = Crypt::decrypt($fileContent);
                            $base64EncodedContent = base64_encode($decryptedContent);
                        } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                            // If decryption fails, set the content to null or handle the error
                            $base64EncodedContent = null;
                        }
                    } else {
                        // File does not exist, handle this case accordingly
                        $base64EncodedContent = null;
                    }
                @endphp
                @if ($base64EncodedContent)
                    <iframe id="view-file-frame" src="data:application/pdf;base64,{{ $base64EncodedContent }}" type="application/pdf" width="100%" height="600px"></iframe>
                @else
                @php
                $file_path = str_replace('%20', ' ', str_replace('&#039;', "'", $filepath));
                @endphp
                <iframe id="view-file-frame" src="{{ asset('construction-assets/' . $file_path) }}?v={{ time() }}" type="application/pdf" width="100%" height="600px"></iframe>
                @endif
            @endif
            
            </div>
        </div>
    </div>
</div>
