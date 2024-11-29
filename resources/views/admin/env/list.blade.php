@extends('admin.layouts.app')
@section('title', 'ENV')
@section('header-css')
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
@endsection

@section('main')
<div class="pb-5 content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="mb-2 row">
                <div class="col-sm-6">
                    <h1 class="m-0">ENV (Do Not Change Any Fields)</h1>
                </div>
            </div>
            {{-- <div class="my-4 button-group">
                <a href="javascript:void(0);" onclick="handleManageDocumentType(0)"
                    class="btn btn-sm text-light buttons-print" style="background-color: var(--wb-renosand)">
                    <i class="mr-1 fa fa-plus"></i> Add New
                </a>
            </div> --}}
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="container mt-5">
                <div class="row">
                    <!-- Displaying Environment Variables -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="text-white card-header bg-secondary">
                                Environment Variables
                            </div>
                            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                                <ul class="list-group">
                                    @foreach($envVariables as $key => $value)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <strong>{{ $key }}</strong>
                                            <span>{{ $value }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Update Environment Variable Form -->
                    <div class="col-md-6">
                        <div class="card">
                            <div class="text-center text-white card-header bg-primary">
                                Update Environment Variable
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('admin.env.update') }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="key" class="form-label">Environment Key</label>
                                        <input type="text" class="form-control" id="key" name="key" placeholder="Enter the key" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="value" class="form-label">Value</label>
                                        <input type="text" class="form-control" id="value" name="value" placeholder="Enter the value" required>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100">Update Environment Variable</button>
                                </form>
                            </div>
                        </div>
                        @if(session('success'))
                            <div class="mt-3 alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>


@endsection

@section('footer-script')
    <script src="//cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>

@endsection
