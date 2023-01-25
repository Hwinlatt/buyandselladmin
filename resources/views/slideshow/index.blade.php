@extends('layouts.master')

@section('content-header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Slideshows</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <a href="{{ route('slideshow.create') }}" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i> Create</a>
            </ol>
        </div><!-- /.col -->
    </div><!-- /.row -->
@endsection

@section('contact')
    <div class="row">
        <div class="col-md-12">
            <table class="table table-hover" id="slideShowTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Image</th>
                        <th>Created at</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($slides as $slide)
                        <tr>
                            <td class="align-center">{{ $slide->id }}</td>
                            <td class="align-center">{{ $slide->title }}</td>
                            <td class="align-center">
                                <a href="{{ asset('storage/images/' . $slide->image) }}"><img style="width: 100px;height:50px"
                                        src="{{ asset('storage/images/' . $slide->image) }}" alt=""
                                        srcset=""></a>
                            </td>
                            <td class="align-center">{{ $slide->created_at }}</td>
                            <td class="align-center">
                                <div>
                                    <a href="{{ route('slideshow.edit',$slide->id) }}" class="btn btn-link btn-sm">Edit</a>
                                    <button onclick="deleteSlide({{ $slide->id }})" class="btn btn-danger btn-sm">Delete</button>
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
        let _token = "{{ csrf_token() }}"
        $(document).ready(function() {
            $('.side_bar_slideshow').addClass('active')
            $('#slideShowTable').DataTable();
        });
        function deleteSlide(id){
            let url = "{{ route('slideshow.destroy',':id') }}"
            url = url.replace(':id',id);
            $.ajax({
                type: "DELETE",
                url: url,
                data: {_token},
                dataType: "JSON",
                success: function (response) {
                    noti(response)
                    if (response.success) {
                        window.location.reload();
                    }
                }
            });
        }
    </script>
@endpush
