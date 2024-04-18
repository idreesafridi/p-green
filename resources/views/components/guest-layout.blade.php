<x-front.links pageTitle="{{ $pageTitle }}" />

<div class="hero">
    <div class="container">
        <div class="row d-flex justify-content-center align-items-center">
            <div class="col-10 col-md-7 px-lg-4">
                <img src="{{ asset('assets/images/LOGO-GREEN-3840x2160.png') }}" class="img-fluid pe-lg-5"
                    alt="Greengen logo">
            </div>
            <div class="col-12 col-md-9 col-lg-4">
                {{-- <div class="LogIn-block"> --}}
                    {{ $slot }}
                {{-- </div> --}}
            </div>
        </div>
    </div>
</div>

<x-front.footer_main />
<x-front.footer />
