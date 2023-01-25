@extends('layouts.master')

@section('content-header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Edit User</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <ul class="breadcrumb-item"><a href="{{ route('user.index') }}">User</a></ul>
                <ul class="breadcrumb-item active">{{ $user->name }}</ul>
            </ol>
        </div><!-- /.col -->
    </div><!-- /.row -->
@endsection

@section('contact')
    <div class="row">
        <form class="row" action="{{ route('user.update', $user->id) }}" method="POST">
            @method('PUT')
            @csrf
            <div class="form-group col-md-4">
                <label for="nameInput form-label">Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}"
                    class="form-control @error('name') is-invalid @enderror">
                @error('name')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group col-md-4">
                <label for="nameInput form-label">Email</label>
                <input type="email" name="email" value="{{ old('name', $user->email) }}" required
                    class="form-control @error('email') is-invalid @enderror">
                @error('email')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            <div class="form-group col-md-4">
                <label for="nameInput form-label">Role</label>
                <select class="form-select @error('role') is-invalid @enderror" name="role">
                    <option @selected(old('name', $user->role) == 'admin') value="admin">admin</option>
                    <option @selected(old('name', $user->role) == 'user') value="user">user</option>
                    <option @selected(old('name', $user->role) == 'suspend') value="suspend">suspend</option>
                </select>
                @error('role')
                    <span class="invalid-feedback">{{ $message }}</span>
                @enderror
            </div>
            <div class="text-end">
                <button class="btn btn-primary">Submit</button>
            </div>
        </form>
    </div>
    <hr>
    <div class="row">
        <h3 class="m-0"><i class="fa-solid fa-lock"></i> Password</h3>
        <div class="form-group col-md-4 mt-2">
            <form action="{{ route('user.store') }}" method="POST">
                @csrf
                <label for="nameInput form-label">New Password</label>
                <input type="text" class="d-none" required name="id" value="{{ $user->id }}">
                <input type="password" name="password" required min="6" class="form-control">
                <div class="mt-2 text-end">
                    <button class="btn btn-secondary" type="submit"><i class="fa-solid fa-key"></i> Change</button>
                </div>
            </form>
        </div>
        <div class="col-md-2"></div>
        <div class="col-md-4">
            <div>
                <label for="">Other Actions</label> <br>
                <button class="btn btn-danger delBtn">Delete User</button>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        let _token = "{{ csrf_token() }}"
        $(document).ready(function () {
            $('.delBtn').click(function(e) {
            e.preventDefault();
            const id = "{{ $user->id }}";
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
                                history.back();
                            }
                        },
                        error: function(err) {
                            console.log(err);
                        }
                    });
                }
            })
        })
        });
    </script>
@endpush
