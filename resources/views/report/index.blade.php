@extends('layouts.master')

@section('content-header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1 class="m-0">Reports</h1>
        </div><!-- /.col -->
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <div class="d-flex align-items-center">
                    Type
                    <select  class="form-select ml-1 selectType">
                        <option value="">All</option>
                        <option @selected(request('type')=='user') value="user">User</option>
                        <option @selected(request('type')=='post') value="post">Post</option>
                    </select>
                </div>
            </ol>
        </div><!-- /.col -->
    </div><!-- /.row -->
@endsection

@section('contact')
    <div class="row">
        <div class="col-md-12">
            <table id="reportTable" class="table">
                <thead>
                    <tr>
                        <th scope="col">Id</th>
                        <th scope="col">Type</th>
                        <th scope="col">Report to</th>
                        <th scope="col">Report Type</th>
                        <th>Created At</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($reports as $r)
                    <tr>
                        <td>{{ $r->id }}</td>
                        <td>{{ $r->type }}</td>
                        <td>
                            @if ($r->type == 'user')
                            @if ($r->report_user)
                            {{ $r->report_user->name }}
                            @else
                            <span class="text-danger">User is not found</span>
                            @endif

                            @elseif ($r->type == 'post')
                            @if ($r->report_post)
                            {{ $r->report_post->name }}
                            @else
                            <span class="text-danger">Post is not found</span>
                            @endif
                            @endif
                        </td>
                        <td><span class="badge bg-danger">{{ $r->report_type }}</span></td>
                        <td>{{ $r->created_at }} <small class="text-muted">{{ $r->created_at->diffForHumans() }}</small></td>
                        <td>
                            <a href="{{ route('report.show',$r->id) }}" class="btn btn-primary btn-sm m-1">More</a>
                            <a href="{{ route('report.destroy',$r->id) }}" class="btn btn-danger btn-sm m-1">Delete</a>
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
        $(document).ready(function () {
            $('.side_bar_report').addClass('active');
            $('#reportTable').DataTable();

            $('.selectType').change(function (e) {
                let value = $(this).val();
                window.location.href = "{{ route('report.index') }}?type="+value
            });
        });
    </script>
@endpush
