@extends('admin.layouts.app')
@section('title', 'Dashboard | Admin')
@section('header-css')
    <link rel="stylesheet" href="{{ asset('plugins/charts/chart.css') }}">
@endsection
@section('main')
    <div class="pb-5 content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="mb-2 row">
                    <div class="col-sm-6">
                        <h1 class="m-0">Dashboard</h1>
                    </div>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="container-fluid">
                <div class="row" id="admin-dashboard-cards">
                    <div class="col-lg-3 col-6">
                        <div class="text-sm small-box text-light" style="background: var(--wb-renosand);">
                            <div class="inner">
                                <h3>{{$total_users}}</h3>
                                <p>Total Users</p>
                            </div>
                            <div class="icon">
                                <i class="ion ion-bag"></i>
                            </div>
                            <a href="{{ ('admin.vendor.list') }}" class="small-box-footer">More info <i
                                    class="fas fa-arrow-circle-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@section('footer-script')
    <script src="{{ asset('plugins/charts/chart.bundle.min.js') }}"></script>
</script>
@endsection
@endsection
