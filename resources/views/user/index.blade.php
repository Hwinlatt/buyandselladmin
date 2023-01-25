@extends('layouts.master')

@section('content-header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Users</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <div class="d-flex align-items-center">
                    <i class="fa-solid fa-filter me-1"></i>
                    <select name="" class="form-select filterByRole" id="">
                        <option value="">All</option>
                        <option class=" text-capitalize" @selected(request('role') == 'admin') value="admin">admin</option>
                        <option class=" text-capitalize" @selected(request('role') == 'user') value="user">user</option>
                        <option class=" text-capitalize" @selected(request('role') == 'suspend') value="suspend">suspend</option>
                    </select>
                </div>
            </ol>
        </div><!-- /.col -->
    </div><!-- /.row -->
@endsection

@section('contact')
    <div class="row">
        <div class="col-md-12">
            <table id="userTable" class="table">
                <thead>
                    <tr>
                        <th scope="col">User id</th>
                        <th scope="col">Name</th>
                        <th scope="col">Email</th>
                        <th scope="col">Phone</th>
                        <th scope="col">Role</th>
                        <th scope="col">Created_at</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr scope="row">
                            <td><span class="userId">{{ $user->id }}</span></td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone }}</td>
                            <td>
                                @if (Auth::user()->id != $user->id)
                                    <select class="form-select roleChangeBtn">
                                        <option @selected($user->role == 'admin') value="admin">admin</option>
                                        <option @selected($user->role == 'user') value="user">user</option>
                                        <option @selected($user->role == 'suspend') value="suspend">suspend</option>
                                    </select>
                                @endif
                            </td>
                            <td>{{ $user->created_at }}</td>
                            <td>
                                @if (Auth::user()->id != $user->id)
                                <div>
                                    <a class="btn btn-link" href="{{ route('user.edit', $user->id) }}">Edit</a>
                                    <button class="btn btn-danger delBtn">Delete</button>
                                </div>
                                @endif
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
            $('.side_bar_user').addClass('active');
            $('#userTable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'copy', 'excel','csv','print'
                ]
            });
            $('.filterByRole').change(function(e) {
                e.preventDefault();
                window.location.href = "{{ route('user.index') }}" + "?role=" + $(this).val();
            })

        });
        $('.roleChangeBtn').change(function(e) {
            e.preventDefault();
            let role = $(this).val();
            let id = $(this).closest('tr').find('.userId').html();
            $.ajax({
                type: "POST",
                url: "{{ route('user.role_change') }}",
                data: {
                    _token,
                    role,
                    id
                },
                dataType: "JSON",
                success: function(response) {

                },
                error: function(err) {
                    console.log(err);
                }
            });
        });

        $('.delBtn').click(function(e) {
            e.preventDefault();
            const id = $(this).closest('tr').find('.userId').html();
            let url = "{{ route('user.destroy', ':id') }}";
            url = url.replace(':id', id);
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "DELETE",
                        url: url,
                        data: {
                            _token
                        },
                        dataType: "JSON",
                        success: function(response) {
                            noti(response);
                            if (response.success) {
                                window.location.reload()
                            }
                        },
                        error: function(err) {
                            console.log(err);
                        }
                    });
                }
            })
        })
    </script>
@endpush
