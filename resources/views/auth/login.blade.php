<x-guest-layout pageTitle="Login">
    @section('styles')
    <style type="text/css">
        @font-face {
          font-weight: 400;
          font-style:  normal;
          font-family: circular;

          src: url('chrome-extension://liecbddmkiiihnedobmlmillhodjkdmb/fonts/CircularXXWeb-Book.woff2') format('woff2');
        }

        @font-face {
          font-weight: 700;
          font-style:  normal;
          font-family: circular;

          src: url('chrome-extension://liecbddmkiiihnedobmlmillhodjkdmb/fonts/CircularXXWeb-Bold.woff2') format('woff2');
        }

        .c1 {

                    background-color: #ffffff;
                    border-radius: 10px;
                    box-shadow: 0px 0px 25px rgba(0, 0, 0, 0.3);
                    overflow: hidden;
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    align-items: center;
                    padding: 0px 0px;
                }

                .p-head1 {
                    font-weight: bold;
                    color: #ffffff;
                    text-align: center;
                    background-color: #076D32;
                    font-size: 20px;
                    width: 100%;
                    padding: 25px;
                }

                /* .logo-container img {
                    max-width: 100%;
                    height: auto;
                    margin-bottom: 10px;
                } */

                .google-signin-container {
                    padding: 20px;
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                    align-items: center;
                    width: 100%;
                }

              .google-signin-button {
    display: inline-block;
    width: 100%;
    padding: 15px;
    border: none;
    border-radius: 8px;
    font-size: 16px;
    font-weight: bold;
    background-color: #076D32;
    color: #ffffff;
    text-align: center;
    text-decoration: none; /* Remove underline from link */
    cursor: pointer;
    transition: background-color 0.3s ease;
    outline: none;
}

.google-signin-button:hover {
    background-color: #ffffff;
    color: #076D32;
    border: 1px solid black;
}


                .google-icon {
                    margin-right: 10px;
                    font-size: 22px;
                }

                .footer {
                    background-color: #e9e9e9;
                    padding: 20px;
                    text-align: center;
                    color: #000000;
                    width: 100%;
                    border-top: 1px solid #dddddd;
                }
                .modal-btn{
                    text-decoration: underline;
                    cursor: pointer;
                }
                .clr{
                    background-color: #076D32;
                    color: white;
                }
                .clr:hover{
                    background-color: #076D32;
                    color: white;
                }

         @media screen and (max-width : 1100px){
            .google-signin-button {
                    font-size: 15px;
                }

                .google-icon {
                    font-size: 19px;
                }
         }

         @media screen and (max-width : 500px){
            .google-signin-button {
                    font-size: 12px;
                }

                .google-icon {
                    font-size: 16px;
                }
                .footer{
                    font-size: 14px;
                }
         }

         @media screen and (max-width : 400px){
            .google-signin-button {
                    font-size: 10px;
                }

                .google-icon {
                    font-size: 13px;
                }
                .footer{
                    font-size: 12px;
                }
                .p-head1{
                    font-size: 15px;
                }
         }

        </style>
    @endsection

    <p class="p-head mb-0">{{ __('Portale Greengen') }}</p>
     <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="col-md-12">
            <input id="email" type="email" class="email @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="Email di accesso">

            @error('email')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <br>
        <br>

        <div class="col-md-12">
            <input id="password" type="password" class="password @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Password di accesso">

            @error('password')
            <span class="invalid-feedback" role="alert">
                <strong>{{ $message }}</strong>
            </span>
            @enderror
        </div>
        <br>

        <div class="col-md-12">
            <button type="submit" class="button">
                {{ __('Accedi') }}
            </button>
        </div>
        <br>

        <div class="col-md-12 text-center">
            <a href="{{route('admin_reset_password_form')}}">Resetta password</a>
        </div><br>
    </form>

    {{-- <div class="col-md-12">
        <a href =  "{{ url('auth/google') }}"  class="button">
            {{ __('Accedi con google') }}
        </a>
    </div> --}}
    {{-- <div class="col-md-12">
        @if(session('message'))
        <span class="invalid-feedback" role="alert">
            <strong>{{ session('message') }}</strong>
        </span>
    @endif
    </div> --}}
    {{-- <div class="col-md-12 text-center">
        <a href="{{route('admin_reset_password_form')}}">Resetta password</a>
    </div><br>


        {{-- <div class="container c1">

            <h5 class="p-head1 mb-0" id="portable_green">Portale Greengen</h5>

            <div class="google-signin-container">
                <a href =  "{{ url('auth/google') }}" class="google-signin-button" >
                    <span class="google-icon"><i class="fab fa-google"></i></span>
                    Sign In with Google
                </a>
            </div>
            <div class="footer">
                Problemi con l'accesso? <span class="modal-btn" data-bs-toggle="modal" data-bs-target="#sendInviaEmailModal">Manda&nbsp;una&nbsp;email</span>
            </div>
        </div>
        <div class="modal fade" id="sendInviaEmailModal" aria-labelledby="sendInviaEmailModal" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="sendInviaEmailModal">Invia email</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{route('sendInviaEmail')}}" method="POST">
                    {{ csrf_field() }}
                    {{-- <input type="hidden" name="_token" value="2zsJdHlXUlIWV8XpJN8qtLSoGoJDrNahue6AlQiQ">                  --}}
                       <div class="modal-body">
                        <div class="row send-email">
                            <div class="col-12">
                                <label class="col-form-label">Destinatario</label>
                                <input type="text" name="email" class="form-control" id="clr2" value="nome@esempio.com" placeholder="nome@esempio.com" readonly required="">
                            </div>
                            <div class="col-12">
                                <label class="col-form-label">Oggetto</label>
                                {{-- <input type="text" name="subject" value="RIF. FOTO DIMOSTRATIVE AAA- — Email da ANTHONY" class="form-control" placeholder="Oggetto della email" required=""> --}}
                                <input type="text" name="subject" value="" class="form-control" placeholder="Oggetto della email" required="">
                            </div>
                            <div class="col-12">
                                <label class="col-form-label">Contenuto</label>
                                <textarea required="" class="form-control" name="msg" id="exampleFormControlTextarea1" rows="5" placeholder="Scrivi qui il messaggio della tua email"></textarea>
                                {{-- <textarea required="" class="form-control" name="msg" id="exampleFormControlTextarea1" rows="5" placeholder="Scrivi qui il messaggio della tua email">!!! Non rispondere a questa email ma contatta &lt;&nbsp;galianoanthony581@gmail.com&nbsp;&gt;
———</textarea> --}}
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer mb-3">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Chiudi</button>
                        <button type="submit" class="btn clr">Invia email</button>
                    </div>
                </form>
            </div>
        </div>
    </div> --}}
        <!-- <div class="LogIn-block border-1 border border-danger" >
            <br>
            <p class="p-head mb-0" id="portable_green">Portale Greengen</p>

<div class="col-md-12">
<a href="https://greengen.crm-labloid.com/auth/google" class="button" id="google_access">
    <button class="google-signin-button">
        <span class="google-icon"><i class="fab fa-google"></i></span>
        Accedi con google
    </button>
</a>
</div>
<br><br>
        </div> -->

    @section('scripts')
    @endsection
</x-guest-layout>
