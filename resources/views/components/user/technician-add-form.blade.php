<div class="col-md-4 col-12 mt-3">
    <label class="col-form-label">Nome &amp; Cognome</label>
    <input type="text" name="name" id="name" class="form-control mb-3" placeholder="name">
    @if ($errors->has('name'))
        <span class="text-danger">{{ $errors->first('name') }}</span>
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
    
    <input type="text" name="phone" id="phone" class="form-control mb-3 phone w-100" placeholder="328 985 52 52">
    @if ($errors->has('phone'))
        <span class="text-danger">{{ $errors->first('phone') }}</span>
    @endif
</div>
<div class="col-md-4 col-12 mt-3">
    <label class="col-form-label">Comune di provenienza</label>
    <input type="text" name="residence_city" id="residence_city" class="form-control mb-3"
        placeholder="Castellana Grotte">
    @if ($errors->has('residence_city'))
        <span class="text-danger">{{ $errors->first('residence_city') }}</span>
    @endif
</div>

