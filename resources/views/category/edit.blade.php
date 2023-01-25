@extends('layouts.master')

@section('content-header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Edit Category</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class=" breadcrumb-item"><a href="{{ route('category.index') }}">Category</a></li>
                <li class=" breadcrumb-item active">{{ $category->name }}</li>
            </ol>
        </div><!-- /.col -->
    </div><!-- /.row -->
@endsection

@section('contact')
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('category.update',$category->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label class="form-label">Name</label>
                            <input type="text" value="{{ old('name',$category->name) }}" name="name" required placeholder="Enter Category Name"
                                class="form-control @error('name') is-invalid @enderror">
                            @error('name')
                                <span class=" invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label class="form-label">Icon</label> <span class="iconShow fs-2">{!! $category->icon !!}</span>
                            <input type="text" value="{{ old('name',$category->icon) }}" name="icon" required placeholder="Enter Icon"
                                class="form-control iconInput @error('icon') is-invalid @enderror">
                            @error('icon')
                                <span class=" invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="text-end">
                            <button class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            $('.iconInput').keyup(function(e) {
                $('.iconShow').html($(this).val())
            });
        });
    </script>
@endpush
