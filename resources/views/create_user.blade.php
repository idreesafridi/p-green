<x-app-layout pageTitle="Add {{$userrole}}">
    @section('styles')
    @endsection

    @php
        $arr = ['technician', 'businessconsultant', 'photovoltaic', 'user', 'worker'];
    @endphp

    <div class="col-12">
        <div class="card p-2">
            <div class="card-header pt-5 bg-transparent color-blue border-bottom-0">
                @if (in_array($userrole, $arr) || $userrole == 'business')
               
                    <h5><i class="fa fa-user me-2"></i>Crea un nuovo {{ $userrole ==  'worker' ? 'operaio' : ($userrole ==  'business' ? 'impresa' : ($userrole == 'technician' ? 'tecnico' : ($userrole == 'businessconsultant' ? 'commercialista' : ($userrole == 'photovoltaic' ? 'ingegnere fotovoltaico': ($userrole == 'user' ? 'utente' : $userrole)))) )}}</h5>
                @else
                
                    <h5 class="text-danger">Check your role</h5>
                @endif
            </div>
            <div class="card-body">
                <form action="{{ route('addUser', $userrole) }}" method="post">
                    @csrf
                    <div class="user-registration row">

                        @if (in_array($userrole, $arr))
                            <x-user.technician-add-form />
                        @elseif($userrole == 'business')
                            <x-user.business-add-form />
                        @endif

                        @if (in_array($userrole, $arr) || $userrole == 'business')
                            <div class="text-end mt-3">
                                <button type="submit" name="submit"
                                    class="btn btn-sm btn-outline-primary step-btn">Salva</button>
                            </div>
                        @endif

                    </div>
                </form>
            </div>
        </div>
    </div>

    @section('scripts')
    @endsection
</x-app-layout>
