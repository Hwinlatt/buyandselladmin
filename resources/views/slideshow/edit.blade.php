@extends('layouts.master')

@section('content-header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0"> Edit Slideshows</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <ul class="breadcrumb-item"><a href="{{ route('slideshow.index') }}">SlideShows</a></ul>
                <ul class="breadcrumb-item active">{{ $slide->title }}</ul>
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
                    <form action="{{ route('slideshow.update',$slide->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="form-group ">
                            <label class=" form-label">Title</label>
                            <input type="text" name="title" value="{{ old('title', $slide->title) }}"
                                class="form-control @error('title') is-invalid @enderror" required
                                placeholder="Enter Title">
                            @error('title')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="">
                            <img class="img-fluid pointer insertImgBtn @error('image') is-invalid @enderror"
                                style="width: 200px;height:100px"
                                src="{{ asset('storage/images/'.$slide->image) }}" alt="insertImage"
                                srcset="">
                            <input type="file" name="image">
                            @error('image')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="">
                            <label class=" form-label">Link</label>
                            <input type="text" value="{{ old('link', $slide->link) }}" name="link"
                                class="form-control @error('link') is-invalid @enderror" required
                                placeholder="Enter the Link to go">
                            @error('link')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mt-3 text-end">
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
            $('.insertImgBtn').click(function() {
                $('input[name="image"]').click();
            })

            $('input[name="image"]').change(function(e) {
                e.preventDefault();
                let url = URL.createObjectURL(e.target.files[0]);
                $('.insertImgBtn').attr('src', url)
            });
        });
    </script>
@endpush
