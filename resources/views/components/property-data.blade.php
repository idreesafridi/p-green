<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Property Data step3') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form action="{{ route('property_data_store') }}"class="row g-3 needs-validation" method="post">
                        @csrf
                        <div class="col-md-6 position-relative">
                            <label for="property_address" class="form-label">property address</label>
                            <input type="text" name="property_address" class="form-control" id="property_address" ">
                              @if ($errors->has('property_address'))
                            {{ $errors->first('property_address') }}
                            @endif
                        </div>
                        <div class="col-md-3 position-relative">
                            <label for="address" class="form-label">address</label>
                            <input type="text" name="address" class="form-control" id="address">
                            @if ($errors->has('address'))
                                {{ $errors->first('address') }}
                            @endif
                        </div>
                        <div class="col-md-3 position-relative">
                            <label for="house_number" class="form-label">house number</label>
                            <input type="text" name="house_number" class="form-control" id="house_number">
                            @if ($errors->has('house_number'))
                                {{ $errors->first('house_number') }}
                            @endif
                        </div>
                        {{--  --}}
                        <div class="col-md-6 position-relative">
                            <label for="common" class="form-label">common</label>
                            <input type="text" name="common" class="form-control" id="common">

                            @if ($errors->has('common'))
                                {{ $errors->first('common') }}
                            @endif
                        </div>
                        <div class="col-md-3 position-relative">
                            <label for="zip_code" class="form-label">fiscal document code</label>
                            <input type="text" name="zip_code" class="form-control" id="zip_code">
                            @if ($errors->has('zip_code'))
                                {{ $errors->first('zip_code') }}
                            @endif
                        </div>
                        <div class="col-md-3 position-relative">
                            <label for="province" class="form-label">province</label>
                            <input type="text" name="province" class="form-control" id="province">
                            @if ($errors->has('province'))
                                {{ $errors->first('province') }}
                            @endif
                        </div>
                        {{-- 3rd line --}}
                        <div class="col-md-6 position-relative">
                            <label for="cadastral_section" class="form-label">cadastral section</label>
                            <input type="text" name="cadastral_section" class="form-control" id="cadastral_section">
                            @if ($errors->has('cadastral_section'))
                                {{ $errors->first('cadastral_section') }}
                            @endif
                        </div>
                        <div class="col-md-3 position-relative">
                            <label for="cadastral_sheet" class="form-label">cadastral sheet</label>
                            <input type="text" name="cadastral_sheet" class="form-control" id="cadastral_sheet">
                            @if ($errors->has('cadastral_sheet'))
                                {{ $errors->first('cadastral_sheet') }}
                            @endif
                        </div>
                        <div class="col-md-3 position-relative">
                            <label for="cadastral_particle" class="form-label">cadastral particle</label>
                            <input type="text" name="cadastral_particle" class="form-control"
                                id="cadastral_particle">
                            @if ($errors->has('cadastral_particle'))
                                {{ $errors->first('cadastral_particle') }}
                            @endif
                        </div>
                        <div class="col-md-3 position-relative">
                            <label for="sub_ordinate" class="form-label">sub ordinate</label>
                            <input type="text" name="sub_ordinate" class="form-control" id="sub_ordinate">
                            @if ($errors->has('sub_ordinate'))
                                {{ $errors->first('sub_ordinate') }}
                            @endif
                        </div>
                        <div class="col-md-3 position-relative">
                            <label for="pod_codes" class="form-label">pod codes</label>
                            <input type="text" name="pod_codes" class="form-control" id="pod_codes">
                            @if ($errors->has('pod_codes'))
                                {{ $errors->first('pod_codes') }}
                            @endif
                        </div>
                        <div class="col-12">
                            <button class="btn btn-primary" type="submit">Inviare il modulo</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
