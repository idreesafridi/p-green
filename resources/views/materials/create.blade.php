<x-app-layout pageTitle="Nuovo Materiale">
    @section('styles')
    @endsection

    <div class="col-12">
        <div class="card p-2">
            <div class="card-header bg-transparent color-blue border-bottom-0">
                <h5>Crea un nuovo Materiale</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('material_store') }}" method="post">
                    @csrf
                    <div class="user-registration row">
                        <div class="col-md-4 col-12">
                            <label class="col-form-label">Nome</label>
                            <input type="text" name="name" class="form-control"
                                placeholder="Pompa di calore Carrier" required>
                        </div>
                        <div class="col-md-4 col-12">
                            <x-material-type-list />
                        </div>
                        <div class="col-md-4 col-1">
                            <label class="col-form-label">Parametro</label>
                            <select name="unit" id="unit" class="selectmenuinput form-control" required>
                                <option selected disabled value="">Seleziona parametro</option>
                                <option value="m²">m²</option>
                                <option value="mm">mm</option>
                                <option value="kW">kW</option>
                                <option value="pz">pz</option>
                            </select>
                        </div>

                        <div class="text-end mt-3">
                            <button type="submit" class="btn btn-sm btn-outline-primary step-btn">Salva e
                                Chiudi</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @section('scripts')
    @endsection
</x-app-layout>
