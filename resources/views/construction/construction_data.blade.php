<x-app-layout pageTitle="Construction Data">
    @section('styles')
    @endsection
    <br>

                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        <table id="example" class="table table-striped table-bordered" style="width:100%">
                            <thead>
                                <tr>
                                    <th>name</th>
                                    <th>surename</th>
                                    <th>date_of_birth</th>
                                    <th>town_of_birth</th>
                                    <th>province</th>
                                    <th>residence_address</th>
                                    <th>residence_street</th>
                                    <th>residence_zip</th>
                                    <th>residence_common</th>
                                    <th>residence_province</th>
                                    <th>page_status</th>
                                    <th>Edit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($all as $all_data)
                                    <tr>
                                        <td>{{$all_data->name}}</td>
                                        <td>{{$all_data->surename}}</td>
                                        <td>{{$all_data->date_of_birth}}</td>
                                        <td>{{$all_data->town_of_birth}}</td>
                                        <td>{{$all_data->province}}</td>
                                        <td>{{$all_data->residence_address}}</td>
                                        <td>{{$all_data->residence_street}}</td>
                                        <td>{{$all_data->residence_zip}}</td>
                                        <td>{{$all_data->residence_common}}</td>
                                        <td>{{$all_data->residence_province}}</td>
                                        <td>{{$all_data->page_status}}</td>
                                        <td><a href="{{route('construction_edit',$all_data->id)}}" target="_blank" rel="noopener noreferrer">fa <i class="fa fa-pencil" aria-hidden="true"></i></a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>


    @section('scripts')
    <script>
        $(document).ready(function () {
            $('#example').DataTable();
        });
    </script>
    @endsection
</x-app-layout>
