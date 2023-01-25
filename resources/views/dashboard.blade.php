@extends('layouts.master')

@section('content-header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Admin Dashboard</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            {{-- <ol class="breadcrumb float-sm-right">
                <a href="{{ route('slideshow.create') }}" class="btn btn-primary">
                    <i class="fa-solid fa-plus"></i> Create</a>
            </ol> --}}
        </div><!-- /.col -->
    </div><!-- /.row -->
@endsection

@section('contact')
    <div class="row">
        <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $data->user_count }}</h3>
                    <p>Users<br>
                    Today SingUp - {{ $data->today_singUp }}
                    </p>
                </div>
                <div class="icon">
                    <i class="fa-solid fa-users"></i>
                </div>
                <a href="{{ route('user.index') }}" class="small-box-footer">More info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-danger">
                <div class="inner">
                    @php
                        $total_report_count = 0;
                        foreach ($data->report_count as $rep) {
                            $total_report_count += $rep->count;
                        }
                    @endphp
                    <h3>
                        {{ $total_report_count }}
                    </h3>

                    <p>Reports <br>
                        @foreach ($data->report_count as $r)
                            {{ $r->report_type }} - <span class=" font-weight-bold">{{ $r->count }}</span> <br>
                        @endforeach
                    </p>
                </div>
                <div class="icon">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <a href="{{ route('report.index') }}" class="small-box-footer">More info <i
                        class="fas fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <!-- small box -->
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $data->post_count }}</h3>

                    <p>Posts <br>
                        Today - {{ $data->today_post }}
                    </p>
                </div>
                <div class="icon">
                    <i class="fa-solid fa-clipboard"></i>
                </div>
                <a href="#" class="small-box-footer">Total</a>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            $('.side_bar_dashboard').addClass('active')
        });
    </script>
@endpush
