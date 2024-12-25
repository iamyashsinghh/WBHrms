<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
    <link rel="stylesheet" href="{{asset('plugins/fontawesome/css/all.min.css')}}">
    <link rel="shortcut icon" href="{{asset('favicon.jpg')}}" type="image/x-icon">
    <link rel="stylesheet" href="{{asset('adminlte/css/adminlte.min.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/toastr/toastr.min.css')}}">
    <link rel="stylesheet" href="{{asset('css/common.css')}}">
    <script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>
    <title>@yield('title') | {{env('APP_NAME')}}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield('header-css')
    @yield('header-script')
</head>
<style>
.table-responsive{
    overflow-x: visible !important;
}

.table-responsive #serverTable thead{
    position: sticky !important;
    top: 0;
}

.vendor-list {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
}

.vendor-badge {
    background-color: var(--wb-dark-red);
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 14px;
    color: #fff;
    display: inline-block;
    white-space: nowrap;
}


</style>

<body class="sidebar-mini layout-fixed">
    @include('includes.preloader')
    @include('admin.layouts.navbar')
    @include('admin.layouts.sidebar')

    <div class="wrapper">
        @section('main')
        @show
        @include('includes.footer')
    </div>

    <script src="{{asset('adminlte/js/adminlte.js')}}"></script>
    <script src="{{asset('plugins/toastr/toastr.min.js')}}"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{asset('js/common.js')}}"></script>
    @php
    if(session()->has('status')){
    $type = session('status');
    $alert_type = $type['alert_type'];
    $msg = $type['message'];
    echo "<script>
        toastr['$alert_type'](`$msg`);
    </script>";
    }
    @endphp
    @yield('footer-script')
    @include('ai.chatModal');
    <script>
        function initialize_datatable(){
            document.getElementById("clientTable").DataTable({
                pageLength: 10,
                language: {
                    "search": "_INPUT_",
                    "searchPlaceholder": "Type here to search..",
                    processing: `<i class="fa fa-spinner fa-spin fa-3x fa-fw"></i><span class="sr-only">Loading...</span>`,
                },
            });
        }

        function common_ajax(request_url, method, body = null) {
            return fetch(request_url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': "{{csrf_token()}}",
                },
                body: body
            })
        }
    </script>
</body>

</html>
