<x-app-layout pageTitle="Document And Contact">
    @section('styles')
    @endsection
    <div class="row">
        <div class="col-md-12">
            <x-construction-site-nav />
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="site-registration">
                        <x-document-and-contact :constructionData="$data" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    @section('scripts')
    @endsection
</x-app-layout>
