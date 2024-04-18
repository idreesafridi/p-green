<x-app-layout pageTitle="Material list">
    @section('styles')
    @endsection

    <div class="col-12">
        <div class="card p-2">
            <div class="card-header bg-transparent color-blue border-bottom-0">
                <h5>Lista materiali</h5>
            </div>
            <div class="card-body">
                <div class="card-body userList-page-table p-4 table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th scope="col">{{ __('Name') }}</th>
                                <th scope="col">{{ __('Type') }}</th>
                                <th scope="col">{{ __('Parameter') }}</th>
                                <th scope="col">{{ __('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($data['all'] as $item)
                                <tr>
                                    <td>{{ $item['name'] }}</td>
                                    <td>{{ $item['type'] }}</td>
                                    <td>{{ $item['parameter'] }}</td>
                                    <td>
                                        <button type="button" class="btn btn-link btn-sm" data-bs-toggle="modal"
                                            data-bs-target="#editUser{{ $item['id'] }}" Update {{ $item['name'] }}>
                                            <i class="fa fa-pencil me-2"></i>
                                        </button>
                                        <button type="button" class="btn btn-link btn-sm text-danger"
                                            data-bs-toggle="modal" data-bs-target="#warningModal_{{ $item['id'] }}">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>

                                <!-- Warning Modal -->
                                <div class="modal fade" id="warningModal_{{ $item['id'] }}"
                                    aria-labelledby="exampleModalLabel" aria-modal="true" role="dialog">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">Attenzione</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <form action="" method="POST">
                                                <div class="modal-body">
                                                    <p class="text-center m-0">Sei sicuro di voler procedere?</p>
                                                </div>
                                                <div class="modal-footer mb-3">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Indietro</button>
                                                    <button type="submit" name="reset-pass"
                                                        class="btn btn-danger">Procedi</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- End Warning Modal -->
                            @empty
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @section('scripts')
    @endsection
</x-app-layout>
