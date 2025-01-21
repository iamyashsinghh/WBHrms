@extends('admin.layouts.app')
@section('title', $page_heading)
@section('header-css')
<link rel="stylesheet" href="//cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
@endsection
@section('main')
<div class="pb-5 content-wrapper">
    <section class="content-header">
        <div class="container-fluid">
            <div class="mb-2 row">
                <div class="col-sm-6">
                    <h1 class="m-0">{{ $page_heading }}</h1>
                </div>
            </div>
        </div>
    </section>
    <section class="content">
        <div class="container-fluid">
            <div class="d-flex justify-content-end">
                <form action="javascript:void(0);" id="filter-form" class="form-inline">
                    <div class="mb-2 form-group">
                        <select name="employee" id="employee" class="form-control">
                            <option value="all">All</option>
                            @foreach ($users as $user)
                            <option value="{{$user->emp_code}}">{{$user->name}}--{{$user->emp_code}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2 form-group mx-sm-1">
                        <select name="month" id="month" class="form-control">
                            @foreach(range(1, 12) as $m)
                            <option value="{{ $m }}">
                                {{\Carbon\Carbon::createFromDate(null, $m)->format('F') }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2 form-group">
                        <select name="year" id="year" class="form-control">
                            @for($y = now()->year; $y >= now()->year - 5; $y--)
                            <option value="{{ $y }}">{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                    <button type="button" id="filter-button" class="mb-2 btn btn-primary mx-sm-1"
                        style="background-color: #891010; border: none;">
                        <i id="loading-icon" class="fa fa-spinner fa-spin" style="display: none;"></i> Generate Payslips
                    </button>
                </form>
            </div>
            {{-- <div class="table-responsive">
                <table id="documentTypeTable" class="table text-sm">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Emp Code</th>
                            <th>Name</th>
                            <th>Is Paid</th>
                            <th class="text-center no-sort">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div> --}}
        </div>
    </section>
</div>
@endsection

@section('footer-script')
<script src="//cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script>
//     $(document).ready(function() {
//             const documentTypeTable = $('#documentTypeTable').DataTable({
//     processing: true,
//     serverSide: true,
//     ajax: "{{ route('admin.notification.ajax_list') }}",
//     columns: [
//         { data: 'id', name: 'id' },
//         { data: 'value', name: 'value' },
//         {
//             data: 'id',
//             name: 'actions',
//             orderable: false,
//             searchable: false,
//             className: 'text-center',
//             render: function(data, type, row) {
//                 return `
//                    Hello
//                 `;
//             }
//         }
//     ]
// })
// })
//
    document.getElementById('filter-button').addEventListener('click', function () {
        const button = this;
        const loadingIcon = document.getElementById('loading-icon');
        const employee = document.getElementById('employee').value;
        const month = document.getElementById('month').value;
        const year = document.getElementById('year').value;

        button.disabled = true;
        loadingIcon.style.display = 'inline-block';

        fetch(`{{ route('admin.payroll.generate') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ employee, month, year })
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    toastr.success(data.message || 'Payslips generated successfully!');
                } else {
                    toastr.error(data.message || 'An error occurred while generating payslips.');
                }
            })
            .catch(error => {
                toastr.error('An unexpected error occurred.');
            })
            .finally(() => {
                button.disabled = false;
                loadingIcon.style.display = 'none';
            });
    });
</script>
@endsection
