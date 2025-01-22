@extends('admin.layouts.app')
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
                            <th class="">Action</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </section>
</div>
@endsection
@section('footer-script')
<script src="//cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>
<script src="{{ asset('plugins/moment/moment.min.js') }}"></script>
<script>
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
    return text.replace(/[&<>"'`\\]/g, function(m) { return map[m]; });
}

    $(document).ready(function() {
        $('#serverTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('admin.resign.ajax_list') }}",
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
                    render: function (data, type, row){
                        return `<a href="{{route('admin.employee.view')}}/${row.emp_code}" class="mt-2 btn" style="border: 1px solid var(--wb-wood); background-color: var(--wb-wood--light); font-weight: 600">${data}</a>`;
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
                     render: function (data, type, row){
                        var escapedData = data.replace(/(\r\n|\n|\r)/gm, " ").replace(/"/g, '\\"');
                        return `<button class="btn" onclick='handle_view_message("${escapedData}")'><i class="fa fa-comment-dots" style="color: var(--wb-renosand);"></i></button>`;
                     }
                },

                {
                    data: 'accepted_by',
                    name: 'accepted_by',
                    // orderable: false,
                    // searchable: false,
                    // render: function(data, type, row) {
                    //     if(data == 0){
                    //         return `<div class="my-1 button-group d-flex">
                    //                         <a href="{{route('admin.approval.update_status')}}/${row.id}/2" class="mx-1 btn text-light btn-sm buttons-print" style="background-color: var(--wb-dark-red)">Reject</a>
                    //                         <a href="{{route('admin.approval.update_status')}}/${row.id}/1" class="mx-1 btn text-light btn-sm buttons-print" style="background-color: var(--wb-renosand)">Approve</a>
                    //                     </div>`;
                    //     }else if(data == 1){
                    //         return `<div class="my-1 button-group d-flex">
                    //                         <a href="#" class="mx-1 btn text-light btn-sm buttons-print" style="background-color: var(--wb-renosand)">Approved</a>
                    //                     </div>`;
                    //     }else{
                    //         return `<div class="my-1 button-group d-flex">
                    //                         <a href="#" class="mx-1 btn text-light btn-sm buttons-print" style="background-color: var(--wb-dark-red)">Rejected</a>
                    //                     </div>`;
                    //     }
                    // }
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
