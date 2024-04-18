<div class="col-md-4 col-12 mt-3">
    <label class="col-form-label">Nome Impresa</label>
    <input type="text" name="company_name" id="company_name" class="form-control mb-3" placeholder="nome-impresa">
    @if ($errors->has('company_name'))
        <span class="text-danger">{{ $errors->first('company_name') }}</span>
    @endif
</div>

<div class="col-md-4 col-12 mt-3">
    <label class="col-form-label">Nome &amp; Cognome Contatto</label>
    <input type="text" name="name" id="name" class="form-control mb-3" placeholder="nome">
    @if ($errors->has('name'))
        <span class="text-danger">{{ $errors->first('name') }}</span>
    @endif
</div>

<div class="col-md-4 col-12 mt-3">
    <label class="col-form-label" for="company_type">Tipologia di impresa </label>
    <select name="company_type" id="company_type" class="form-select mb-3" placeholder="Tipo di azienda">

        <option value="Idraulico">Idraulico</option>
        <option value="Elettricista">Elettricista</option>
        <option value="Edile">Edile</option>
        <option value="Infissi">Infissi</option>
    </select>
    @if ($errors->has('company_type'))
        <span class="text-danger">{{ $errors->first('company_type') }}</span>
    @endif
</div>

<div class="col-md-4 col-12 mt-3">
    <label class="col-form-label">Email</label>
    <input type="text" name="email" id="email" class="form-control mb-3" placeholder="Mario.Rossi@gmail.com">
    @if ($errors->has('email'))
        <span class="text-danger">{{ $errors->first('email') }}</span>
    @endif
</div>
<div class="col-md-4 col-12 mt-3">
    <label class="col-form-label">Numero di telefono</label>
    <input type="text" name="phone" id="phone" class="form-control mb-3 phone" placeholder="328 985 52 52">
    @if ($errors->has('phone'))
        <span class="text-danger">{{ $errors->first('phone') }}</span>
    @endif
</div>
<div class="col-md-4 col-12 mt-3">
    <label class="col-form-label">Comune di provenienza</label>
    <input type="text" name="municipality_of_origin" id="municipality_of_origin" class="form-control mb-3"
        placeholder="Castellana Grotte">
    @if ($errors->has('municipality_of_origin'))
        <span class="text-danger">{{ $errors->first('municipality_of_origin') }}</span>
    @endif
</div>
