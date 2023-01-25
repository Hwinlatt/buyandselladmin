@extends('layouts.master')

@section('content-header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Category</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <a class="btn btn-primary" href="{{ route('category.create') }}">
                    <i class="fa-solid fa-plus"></i> Create</a>
            </ol>
        </div><!-- /.col -->
    </div><!-- /.row -->
@endsection

@section('contact')
    <div class="row">
        <div class="col-md-12">
            <table id="cateoryTable" class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">Id</th>
                        <th scope="col">Name</th>
                        <th scope="col">Icon</th>
                        <th scope="col">Created at</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categories as $category)
                        <tr>
                            <td>{{ $category->id }}</td>
                            <td>{{ $category->name }}</td>
                            <td>
                                <span class="fs-4">{!! $category->icon !!}</span>
                            </td>
                            <td>{{ $category->created_at }}</td>
                            <td>
                                <div>
                                    <a href="{{ route('category.edit',$category->id) }}" class="btn btn-link btn-sm">Edit</a>
                                    <button onclick="delCateory({{ $category->id }})" class="btn btn-danger btn-sm">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@push('script')
    <script>
        let _token = "{{ csrf_token() }}";
        $(document).ready(function () {
            $('.side_bar_category').addClass('active')
            $('#cateoryTable').DataTable({
                dom:'Bfrtip',
                buttons: [
                    'copy', 'excel','csv','print'
                ]
            });
        });
        function delCateory(id){
            console.log(id);
            let url = "{{ route('category.destroy',':id') }}";
            url = url.replace(':id',id);
            $.ajax({
                type: "DELETE",
                url: url,
                data: {_token},
                dataType: "JSON",
                success: function (response) {
                    noti(response);
                    if (response.success) {
                        window.location.reload()
                    }
                }
            });
        }
    </script>
@endpush
