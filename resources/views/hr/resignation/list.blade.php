@extends('hr.layouts.app')
@section('header-css')
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
@endsection
@section('title', $page_heading . ' | HRMS')
@section('main')
    <div class="pb-5 content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="mb-2 d-flex justify-content-between">
                    <h1 class="m-0">{{ $page_heading }}</h1>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="container-fluid">
                <div class="table-responsive" style="overflow-x: visible;">
                    <table id="serverTable" class="table text-sm">
                        <thead class="sticky_head bg-light" style="position: sticky; top: 0;">
                            <tr>
                                <th class="text-nowrap">ID</th>
                                <th class="">Emp Code</th>
                                <th class="">Name</th>
                                <th class="">Type</th>
                                <th class="">Resign On</th>
                                <th class="text-nowrap">Details</th>
                                <th class="text-nowrap">Approved by</th>
                                <th class="text-nowrap">Approved At</th>
                                <th class="text-nowrap">Notice Period</th>
                                <th class="">Action</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </section>
        <div class="modal fade" id="approvalModal" tabindex="-1" role="dialog" aria-labelledby="approvalModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="approvalModalLabel">Approve Resignation</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Do you want to approve the resignation immediately or with a notice period?</p>
                        <div class="form-group">
                            <label for="noticePeriod">Notice Period (in days, leave empty for immediate):</label>
                            <input type="number" class="form-control" id="noticePeriod"
                                placeholder="Enter notice period in days">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="approveButton">Approve</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('footer-script')
    <script src="//cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
    <script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
    <script>
        let selectedRow = null;

        function openApprovalModal(row) {
            selectedRow = row;
            $('#approvalModal').modal('show');
        }

        $('#approveButton').on('click', function() {
            const noticePeriod = $('#noticePeriod').val();
            const approvalData = {
                id: selectedRow.id,
                notice_period: noticePeriod || 0,
            };

            $.ajax({
                url: "{{ route('hr.resign.approve') }}",
                method: 'POST',
                data: approvalData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#approvalModal').modal('hide');
                    $('#serverTable').DataTable().ajax.reload();
                    toastr.success('Resignation approved successfully');
                },
                error: function(error) {
                    console.error('Error approving resignation:', error);
                    toastr.error('An error occurred while approving the resignation');
                }
            });
        });

        function escapeHtml(text) {
            var map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;',
                "`": '&#x60;',
            "\\": '&#x5C;',
            "\n": ' ',
            "\r": ''
        };
        return text.replace(/[&<>"'`\\]/g, function(m) {
                return map[m];
            });
        }

        $(document).ready(function() {
            $('#serverTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('hr.resign.ajax_list') }}",
                    type: 'GET'
                },
                columns: [{
                        data: 'id',
                        name: 'id',
                        visible: false,
                    },
                    {
                        data: 'emp_code',
                        name: 'emp_code',
                        render: function(data, type, row) {
                            return `<a href="{{ route('hr.employee.view') }}/${row.emp_code}" class="mt-2 btn" style="border: 1px solid var(--wb-wood); background-color: var(--wb-wood--light); font-weight: 600">${data}</a>`;
                        }
                    },
                    {
                        data: 'employee.name',
                        name: 'employee.name',
                    },
                    {
                        data: 'type',
                        name: 'type',
                    },
                    {
                        data: 'resign_at',
                        name: 'resign_at',
                    },
                    {
                        data: 'detail',
                        name: 'detail',
                        render: function(data, type, row) {
                            var escapedData = data.replace(/(\r\n|\n|\r)/gm, " ").replace(/"/g,
                                '\\"');
                            return `<button class="btn" onclick='handle_view_message("${escapedData}")'><i class="fa fa-comment-dots" style="color: var(--wb-renosand);"></i></button>`;
                        }
                    },
                    {
                        data: 'accepted_by',
                        name: 'accepted_by',
                        render: function(data, type, row) {
                            return data ? data :
                                'N/A'; // Return the data if it exists, otherwise return 'N/A'
                        }
                    },
                    {
                        data: 'accepted_at',
                        name: 'accepted_at',
                        render: function(data, type, row) {
                            return data ? new Date(data).toLocaleDateString() :
                                'Pending';
                        }
                    },
                    {
                        data: 'notice_period',
                        name: 'notice_period',
                        render: function(data, type, row) {
                            return data ? data + ' days' :
                                'Not Applicable';
                        }
                    },
                    {
                        data: 'accepted_by',
                        name: 'accepted_by',
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            if (row.accepted_at != null) {
                                return `<span class="p-2 badge badge-success">Approved</span>`;
                            } else {
                                return `<button class="btn btn-success" onclick='openApprovalModal(${JSON.stringify(row)})'>Approve</button>`;
                            }
                        }
                    }
                ],
                order: [
                    [0, 'desc']
                ],
                responsive: true,
                autoWidth: false,
                pageLength: 10,
                lengthChange: true
            });
        });
    </script>
@endsection
