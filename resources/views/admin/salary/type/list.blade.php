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
            <div class="my-4 button-group">
                <a href="javascript:void(0);" onclick="handleManageSalaryType(0)"
                    class="btn btn-sm text-light buttons-print" style="background-color: var(--wb-renosand)">
                    <i class="mr-1 fa fa-plus"></i> Add New
                </a>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="table-responsive">
                <table id="salaryTypeTable" class="table text-sm">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Value</th>
                            <th>Category</th>
                            <th class="text-center no-sort">Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </section>
    <style>
        .custom-table {
            width: 100%;
            border: 1px solid #dee2e6;
            border-collapse: collapse;
        }
        .custom-table th, .custom-table td {
            border: 1px solid #000;
            padding: 8px;
        }
        .custom-table thead th {
            background-color: #a50000;
            color: #fff;
        }
        .custom-table tfoot th {
            background-color: #a50000;
            color: #fff;
        }
    </style>
    <section class="content">
        <div class="container-fluid">
            <div class="table-responsive">
                <table id="salaryTypeTable" class="custom-table">
                    <thead>
                        <tr style="background-color: #a50000; color: #fff;">
                            <th>Particulars</th>
                            <th>Per Month</th>
                            <th>Per Annum</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalPerMonth = 0;
                            $totalPerAnnum = 0;
                        @endphp

                        @foreach($salarySummary as $item)
                            @if($item['per_month'] > 0)
                                <tr>
                                    <td>{{ $item['name'] }}</td>
                                    <td>{{ number_format($item['per_month'], 2) }}</td>
                                    <td>{{ number_format($item['per_annum'], 2) }}</td>
                                </tr>
                                @php
                                    $totalPerMonth += $item['per_month'];
                                    $totalPerAnnum += $item['per_annum'];
                                @endphp
                            @endif
                        @endforeach

                        <tr style="background-color: #a50000; color: #fff;">
                            <th>TOTAL GROSS SALARY</th>
                            <th>{{ number_format($totalPerMonth, 2) }}</th>
                            <th>{{ number_format($totalPerAnnum, 2) }}</th>
                        </tr>

                        @php
                            $epf = 0;
                            $esic = 0;
                            $professionalTax = 0;
                        @endphp

                        @if ($epf > 0)
                            <tr>
                                <td>EPF (12%)</td>
                                <td>{{ number_format($epf, 2) }}</td>
                                <td>{{ number_format($epf * 12, 2) }}</td>
                            </tr>
                        @else
                            <tr>
                                <td>EPF (12%)</td>
                                <td>NA</td>
                                <td>NA</td>
                            </tr>
                        @endif

                        @if ($esic > 0)
                            <tr>
                                <td>ESIC/Health Insurance (0.75%)</td>
                                <td>{{ number_format($esic, 2) }}</td>
                                <td>{{ number_format($esic * 12, 2) }}</td>
                            </tr>
                        @else
                            <tr>
                                <td>ESIC/Health Insurance (0.75%)</td>
                                <td>NA</td>
                                <td>NA</td>
                            </tr>
                        @endif

                        @if ($professionalTax > 0)
                            <tr>
                                <td>Professional Tax</td>
                                <td>{{ number_format($professionalTax, 2) }}</td>
                                <td>{{ number_format($professionalTax * 12, 2) }}</td>
                            </tr>
                        @else
                            <tr>
                                <td>Professional Tax</td>
                                <td>NA</td>
                                <td>NA</td>
                            </tr>
                        @endif

                        <!-- Net Salary -->
                        <tr style="background-color: #a50000; color: #fff;">
                            <th>NET SALARY (In Hand)</th>
                            <th>{{ number_format($totalPerMonth - $epf - $esic - $professionalTax, 2) }}</th>
                            <th>{{ number_format(($totalPerAnnum - $epf * 12 - $esic * 12 - $professionalTax * 12), 2) }}</th>
                        </tr>

                        <!-- Employer Contributions (Example) -->
                        @php
                            $employerPf = 0; // Fetch from backend if applicable
                            $employerEsi = 0; // Fetch from backend if applicable
                        @endphp

                        @if ($employerPf > 0)
                            <tr>
                                <td>EMPLOYER PF CONTRIBUTION (13%)</td>
                                <td>{{ number_format($employerPf, 2) }}</td>
                                <td>{{ number_format($employerPf * 12, 2) }}</td>
                            </tr>
                        @else
                            <tr>
                                <td>EMPLOYER PF CONTRIBUTION (13%)</td>
                                <td>NA</td>
                                <td>NA</td>
                            </tr>
                        @endif

                        @if ($employerEsi > 0)
                            <tr>
                                <td>EMPLOYER ESI CONTRIBUTION (3.25%)</td>
                                <td>{{ number_format($employerEsi, 2) }}</td>
                                <td>{{ number_format($employerEsi * 12, 2) }}</td>
                            </tr>
                        @else
                            <tr>
                                <td>EMPLOYER ESI CONTRIBUTION (3.25%)</td>
                                <td>NA</td>
                                <td>NA</td>
                            </tr>
                        @endif

                        <tr>
                            <td>OTHER BENEFITS</td>
                            <td>NA</td>
                            <td>NA</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr style="background-color: #a50000; color: #fff;">
                            <th>Total Fixed Cost to Company (CTC)</th>
                            <th>{{ number_format($totalPerMonth, 2) }}</th>
                            <th>{{ number_format($totalPerAnnum, 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="manageSalaryType" tabindex="-1" aria-labelledby="manageSalaryTypeLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="manageSalaryTypeForm" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 id="form_title" class="modal-title">Manage Salary Type</h5>
                    <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" id="name" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="value" class="form-label">Value</label>
                        <input type="number" id="value" name="value" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <select id="category" name="category" class="form-control" required>
                            <option value="PARTICULARS">PARTICULARS</option>
                            <option value="TOTAL GROSS SALARY">TOTAL GROSS SALARY</option>
                            <option value="NET SALARY ( In Hand )">NET SALARY ( In Hand )</option>
                            <option value="Total Fixed Cost to Company ( CTC)">Total Fixed Cost to Company ( CTC)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="deleteSalaryType" tabindex="-1" aria-labelledby="deleteSalaryTypeLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="deleteSalaryTypeForm" method="POST">
            @csrf
            @method('DELETE')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Delete Salary Type</h5>
                    <button type="button" class="btn text-secondary" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"></i></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this salary type?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@section('footer-script')
    <script src="//cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready(function() {
            const salaryTypeTable = $('#salaryTypeTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.salary-type.ajax_list') }}",
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'name', name: 'name' },
                    { data: 'value', name: 'value' },
                    {
                        data: 'category',
                        name: 'category',
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        className: 'text-center',
                        render: function(data, type, row) {
                            return `
                                <button class="btn btn-sm btn-primary" onclick="handleManageSalaryType('${row.id}', '${row.name}', '${row.value}', '${row.category}')">
                                    <i class="fa fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="handleDeleteSalaryType('${row.id}')">
                                    <i class="fa fa-trash"></i>
                                </button>
                            `;
                        }
                    }
                ]
            });

            window.handleManageSalaryType = function(id, name, value, category) {
                const modal = new bootstrap.Modal(document.getElementById('manageSalaryType'));
                const form = $('#manageSalaryTypeForm');
                const title = $('#form_title');
                const url = id
                    ? `{{ route('admin.salary-type.manage_process', ':id') }}`.replace(':id', id)
                    : "{{ route('admin.salary-type.manage_process') }}";

                if (id) {
                    $('#name').val(name);
                    $('#value').val(value);
                    $('#category').val(category);
                    title.text("Edit Salary Type");
                } else {
                    form.trigger('reset');
                    title.text("Add New Salary Type");
                }

                form.attr('action', url);
                modal.show();
            };

            window.handleDeleteSalaryType = function(id) {
                const modal = new bootstrap.Modal(document.getElementById('deleteSalaryType'));
                const form = $('#deleteSalaryTypeForm');
                const url = `{{ route('admin.salary-type.destroy', ':id') }}`.replace(':id', id);

                form.attr('action', url);
                modal.show();
            };

            $('#manageSalaryTypeForm').on('submit', function(e) {
                e.preventDefault();

                const form = $(this);
                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        $('#manageSalaryType').modal('hide');
                        salaryTypeTable.ajax.reload();
                        toastr.success(response.success, 'Success');
                    },
                    error: function(error) {
                        const errors = error.responseJSON.errors;
                        for (let key in errors) {
                            toastr.error(errors[key][0], 'Error');
                        }
                    }
                });
            });

            $('#deleteSalaryTypeForm').on('submit', function(e) {
                e.preventDefault();

                const form = $(this);
                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        $('#deleteSalaryType').modal('hide');
                        salaryTypeTable.ajax.reload();
                        toastr.success(response.success, 'Success');
                    },
                    error: function() {
                        toastr.error('An error occurred while deleting the salary type.', 'Error');
                    }
                });
            });
        });
    </script>
@endsection
