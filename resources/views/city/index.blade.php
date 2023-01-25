@extends('layouts.master')

@section('content-header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Regions & Cities</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#insertRegion">
                    <i class="fa-solid fa-plus"></i> Create</button>
            </ol>
        </div><!-- /.col -->
    </div><!-- /.row -->
@endsection

@section('contact')
    <div class="row">
        <div class="col-md-12 ">
            <table id="city_table" class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">Id</th>
                        <th scope="col">Region</th>
                        <th scope="col">City</th>
                        <th scope="col">Created at</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($regions as $region)
                        <tr>
                            <td><span class="id d-none">{{ $region->id }}</span>{{ $loop->index + 1 }}</td>
                            <td class="region text-capitalize">{{ $region->region }}</td>
                            <td class="city text-capitalize">{{ $region->city }}</td>
                            <td>{{ $region->created_at }}</td>
                            <td>
                                <div>
                                    <button class="btn btn-primary btn-sm editBtn" data-bs-toggle="modal"
                                        data-bs-target="#editRegion">Edit</button>
                                    <button class="btn btn-danger btn-sm delBtn">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Insert Modal -->
        <div class="modal fade" id="insertRegion" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Create Region</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form class="regionAddForm">
                        <div class="modal-body">
                            <div class="form-group position-relative">
                                <label for="insertRegion">Region</label>
                                <input type="text" class="form-control inputAddRegion" placeholder="Enter Region">
                            </div>
                            <div class="form-group position-relative mt-2">
                                <label for="insertRegion">City</label>
                                <input type="text" class="form-control inputAddCity" placeholder="Enter City">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div class="modal fade" id="editRegion" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
            aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Edit Region</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form class="regionEditForm">
                        <input type="text" class="inputEditId d-none">
                        <div class="modal-body">
                            <div class="form-group position-relative">
                                <label for="insertRegion">Region</label>
                                <input type="text" class="form-control inputEditRegion" placeholder="Enter Region">
                            </div>
                            <div class="form-group position-relative mt-2">
                                <label for="insertRegion">City</label>
                                <input type="text" class="form-control inputEditCity" placeholder="Enter City">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

{{-- Script  --}}
@push('script')
    <script>
        let _token = '{{ csrf_token() }}'
        $(document).ready(function() {
            var dbTable = $('#city_table').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'excel','csv','print'
                ]
            });
            $('.side_bar_region').addClass('active');
            $('.regionAddForm').submit(function(e) {
                e.preventDefault();
                let region = $('.inputAddRegion').val().toLowerCase();
                let city = $('.inputAddCity').val().toLowerCase();
                $.ajax({
                    type: "POST",
                    url: "{{ route('city.store') }}",
                    data: {
                        city,
                        region,
                        _token
                    },
                    dataType: "JSON",
                    success: function(response) {
                        noti(response);
                        if (response.success) {
                            window.location.reload();
                        }
                    }
                });
            });
            $('.regionEditForm').submit(function(e) {
                e.preventDefault();
                let region = $('.inputEditRegion').val().toLowerCase();
                let city = $('.inputEditCity').val().toLowerCase();
                let id = $('.inputEditId').val();
                let url = "{{ route('city.update', ':id') }}";
                url = url.replace(':id', id);
                $.ajax({
                    type: "PUT",
                    url: url,
                    data: {
                        region,
                        city,
                        _token
                    },
                    dataType: "JSON",
                    success: function(response) {
                        noti(response);
                        if (response.success) {
                            window.location.reload();
                        }
                    }
                });
            })

            function removeTableRow(parent) {
                dbTable.row(parent).remove().draw();
            }
        });
        $('.editBtn').click(function(e) {
            e.preventDefault();
            let parent = $(this).closest('tr');
            $('.inputEditCity').val(parent.find('.city').html());
            $('.inputEditRegion').val(parent.find('.region').html());
            $('.inputEditId').val(parent.find('.id').html())
        });
        $('.delBtn').click(function(e) {
            e.preventDefault();
            let parent = $(this).closest('tr');
            let id = parent.find('.id').html();
            let url = "{{ route('city.destroy', ':id') }}";
            url = url.replace(':id', id);
            $.ajax({
                type: "DELETE",
                url: url,
                data: {
                    _token
                },
                dataType: "JSON",
                success: function(response) {
                    noti(response);
                    window.location.reload();
                }
            });
        });
    </script>
@endpush
