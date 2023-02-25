@extends('layouts.master')

@section('content-header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Report Info <span class=" text-capitalize">{{ $report->type }}</span></h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <button class="btn btn-danger btn-sm m-1 delReport" type="1">Delete <span
                        class=" text-capitalize">{{ $report->type }}</button>
                <button class="btn btn-warning btn-sm m-1 delReport" type="2">Delete Report</button>
                <button class="btn btn-warning btn-sm m-1 delReport" type="3">Delete All Report</button>
            </ol>
        </div><!-- /.col -->
    </div><!-- /.row -->
@endsection

@section('contact')
    <div class="row">
        <div class="col-md-1"></div>
        <div class="col-md-10">
            @if ($report)
                <div class="card">
                    <div class="card-header">
                        @if ($report->type == 'user')
                            @if ($report->report_user)
                                {{ $report->report_user->name }}
                            @else
                                <span class="text-danger">User is not found</span>
                            @endif
                        @elseif ($report->type == 'post')
                            @if ($report->report_post)
                                {{ $report->report_post->name }}
                            @else
                                <span class="text-danger">Post is not found</span>
                            @endif
                        @endif
                    </div>
                    <div class="card-body">
                        <h5 class="card-title text-capitalize">-{{ $report->report_type }}</h5>
                        <p class="card-text">{{ $report->description }}</p>
                        <hr>
                        {{-- For Post Detail  --}}
                        @if ($report->type == 'post' && $report->report_post)
                            <div>
                                <h5>Post Info</h5>
                                <table class="table">
                                    <tbody>
                                        <tr>
                                            <td>Images</td>
                                            <td>
                                                @php
                                                    $report_images = json_decode($report->report_post->images);
                                                @endphp
                                                <div class="d-flex flex-wrap">
                                                    @foreach ($report_images as $img)
                                                        <a href="{{ asset('storage/images/' . $img) }}">
                                                            <img src="{{ asset('storage/images/' . $img) }}" alt=""
                                                                class="img-fluid img-70" srcset="">
                                                        </a>
                                                    @endforeach
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Category</td>
                                            <td>{{ $report->report_post->category_name->name }}</td>
                                        </tr>
                                        <tr>
                                            <td>Prie</td>
                                            <td>{{ $report->report_post->price }} {{ $report->report_post->mmk }}</td>
                                        </tr>
                                        <tr>
                                            <td>Description</td>
                                            <td>{{ $report->report_post->description }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        @endif
                        @if ($report->type == 'user' && $report->report_user)
                            <div>
                                <h5>User Info</h5>
                            <table>
                                <tbody class="table">
                                    <tr>
                                        <td>Profile Image</td>
                                        <td><a href="{{ asset('storage/images/'.$report->report_user->profile_photo_path) }}"><img class="img-fluid img-70" src="{{ asset('storage/images/'.$report->report_user->profile_photo_path) }}" alt="" srcset=""></a></td>
                                    </tr>
                                    <tr>
                                        <td>Email</td>
                                        <td>{{ $report->report_user->email }}</td>
                                    </tr>
                                    <tr>
                                        <td>Created at</td>
                                        <td>{{ $report->report_user->created_at }} <small>{{ $report->report_user->created_at->diffForHumans() }}</small></td>
                                    </tr>
                                    <tr>
                                        <td>Action</td>
                                        <td><a href="{{ route('user.edit',$report->report_id) }}" class=""><i class="fa-solid fa-gear"></i> User Setting</a></td>
                                    </tr>
                                </tbody>
                            </table>
                            </div>
                        @endif
                    </div>
                </div>
                @if (count($other_reports) > 0)
                <div class="mt-5">
                    <h5>Other report of this {{ $report->type }}</h5>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Report Type</th>
                                <th>Created at</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($other_reports as $o_report)
                                <tr>
                                    <td>{{ $o_report->id }}</td>
                                    <td>{{ $o_report->report_type }}</td>
                                    <td>{{ $o_report->created_at }} <small class="text-muted">{{ $o_report->created_at->diffForHumans() }}</small></td>
                                    <td>
                                        <a href="{{ route('report.show', $o_report->id) }}"
                                            class="btn btn-primary btn-sm m-1">More</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            @endif
        </div>
    </div>
@endsection


@push('script')
    <script>
        let _token = "{{ csrf_token() }}"
        $(document).ready(function() {
            $('.delReport').click(function(e) {
                e.preventDefault();
                let type = $(this).attr('type');
                let id = "{{ $report->id }}";
                let url = "{{ route('report.destroy', ':id') }}"
                url = url.replace(':id', id);
                $.ajax({
                    type: "DELETE",
                    url: url,
                    data: {
                        type,
                        _token
                    },
                    dataType: "JSON",
                    success: function(response) {
                        noti(response);
                        if (response.success) {
                            window.location.href = "{{ route('report.index') }}";
                        }
                    }
                });
            });
        });
    </script>
@endpush
