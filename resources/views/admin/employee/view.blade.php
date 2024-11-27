@extends('admin.layouts.app')
@section('title', $page_heading)
@section('header-css')
    <style>
        .profile-info-title-max-width {
            max-width: 130px;
            width: 130px;
        }
    </style>
@endsection

@section('main')
    <div class="pb-3 content-wrapper">
        <section class="mt-3 content">
            <div class="container-fluid">
                <div class="mb-1 card">
                    <div class="card-header text-light" style="background-color: var(--wb-dark-red);">
                        <h3 class="card-title">{{ $page_heading }} --
                            {{ ucfirst(str_replace('_', ' ', $user->get_role->name)) }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-4 col-md-5 col-sm-12">
                                <div class="text-center profile-section">
                                    <img src="{{ $user->profile_picture_url ?? asset('images/default-user.png') }}"
                                        class="mb-3 border img-fluid rounded-circle border-secondary" alt="Profile Picture"
                                        style="width: 150px; height: 150px;">
                                    <h4 class="mt-2">{{ $user->name }}</h4>
                                    <p class="text-muted">{{ ucfirst(str_replace('_', ' ', $user->employee_designation)) }}
                                    </p>
                                    <div class="badge bg-primary">{{ ucfirst($user->emp_type) }}</div>
                                </div>
                                <div class="mt-3 mb-3 shadow-lg card">
                                    <div class="card-header text-light" style="background-color: var(--wb-dark-red);">
                                        <h3 class="card-title">Documents</h3>
                                    </div>
                                    <div class="card-body">
                                        <div class="list-group">
                                            @foreach ($doc_type as $type)
                                                @php
                                                    $document = $type->get_user_doc($user->emp_code);
                                                @endphp
                                                <div class="list-group-item d-flex justify-content-between align-items-center"
                                                    style="padding: 15px;">
                                                    <div>
                                                        <h5 class="mb-1">{{ $type->name }}</h5>
                                                    </div>
                                                    <div>
                                                        @if ($document)
                                                            <button type="button"
                                                                class="btn btn-outline-primary btn-sm view-btn"
                                                                data-doc-path="{{ asset($document->path) }}"
                                                                data-doc-type="{{ pathinfo($document->path, PATHINFO_EXTENSION) }}"
                                                                data-bs-toggle="modal" data-bs-target="#viewDocumentModal">
                                                                View
                                                            </button>
                                                            <button type="button"
                                                                class="btn btn-outline-secondary btn-sm reupload-btn"
                                                                data-doc-id="{{ $document->id }}"
                                                                data-doc-type="{{ $type->id }}" data-bs-toggle="modal"
                                                                data-bs-target="#reuploadModal">
                                                                Upload
                                                            </button>
                                                        @else
                                                            <button type="button"
                                                                class="btn btn-outline-secondary btn-sm reupload-btn"
                                                                data-doc-id="" data-doc-type="{{ $type->id }}"
                                                                data-bs-toggle="modal" data-bs-target="#reuploadModal">
                                                                Upload
                                                            </button>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3 mb-3 shadow-lg card">
                                    <div class="card-header text-light" style="background-color: var(--wb-dark-red);">
                                        <div class="row align-items-center">
                                            <div class="col-6">
                                                <h3 class="mb-0 card-title">Salary</h3>
                                            </div>
                                            <div class="col-6">
                                                <input type="number" class="form-control" id="monthly_salary"
                                                    placeholder="Monthly Salary" value="{{ $ctc }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="list-group">
                                            <div class="list-group-item d-flex justify-content-between align-items-center"
                                                style="padding: 15px;">
                                                <div>
                                                    <div class="mb-1">PARTICULARS</div>
                                                </div>
                                                <div>
                                                    <div class="mx-5 mb-1">PER MONTH</div>
                                                </div>
                                            </div>
                                            @foreach ($salary_type as $type)
                                                @php
                                                    $salary = $type->get_user_salary($user->emp_code);
                                                @endphp
                                                <div class="list-group-item d-flex justify-content-between align-items-center"
                                                    style="padding: 15px;">
                                                    <div>
                                                        <h5 class="mb-1">{{ $type->name }}</h5>
                                                    </div>
                                                    <div style="max-width: 120px;">
                                                        <input type="number" class="form-control salary-type-input"
                                                            placeholder="Monthly {{ $type->name }}"
                                                            value="{{ $salary->salary ?? 0 }}"
                                                            data-type-value="{{ $type->value }}"
                                                            data-salary-type="{{ $type->id }}" readonly>
                                                    </div>
                                                </div>
                                            @endforeach
                                            <div class="list-group-item d-flex justify-content-between align-items-center"
                                                style="padding: 15px;">
                                                <div>
                                                    <h5 class="mb-1">Total CTC</h5>
                                                </div>
                                                <div>
                                                    <div class="mb-1" style="max-width: 160px;"><input type="number"
                                                            class="form-control" placeholder="Total Ctc"
                                                            value="{{ $total_ctc ?? 0 }}" id="total_ctc_value" readonly>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-8 col-md-7 col-sm-12">
                                <div class="shadow-lg card info-scroll">
                                    <div class="card-body">
                                        <h2 class="text-center">Information</h2>
                                        <h5 class="card-title profile-info-title-max-width"><i
                                                class="mr-2 fas fa-info-circle"></i>Personal</h5>
                                        <div class="row">
                                            <div class="mb-3 col-md-6">
                                                <strong>Phone:</strong> <span
                                                    class="text-muted">{{ $user->phone }}</span>
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <strong>Alternate Phone:</strong> <span
                                                    class="text-muted">{{ $user->alt_phone }}</span>
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <strong>Email:</strong> <span
                                                    class="text-muted">{{ $user->email ?? 'N/A' }}</span>
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <strong>Gender:</strong> <span
                                                    class="text-muted">{{ ucfirst($user->gender) }}</span>
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <strong>DOB:</strong> <span
                                                    class="text-muted">{{ $user->dob ? $user->dob : 'N/A' }}</span>
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <strong>Marital Status:</strong> <span
                                                    class="text-muted">{{ ucfirst($user->marital_status) ?? 'N/A' }}</span>
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <strong>Nationality:</strong> <span
                                                    class="text-muted">{{ $user->nationality ?? 'N/A' }}</span>
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <strong>Blood Group:</strong> <span
                                                    class="text-muted">{{ $user->blood_group ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                        <hr>
                                        <h5 class="card-title profile-info-title-max-width"><i
                                                class="mr-2 fas fa-heartbeat"></i>Medical</h5>
                                        <div class="row">
                                            <div class="mb-3 col-md-6">
                                                <strong>:</strong> <span
                                                    class="text-muted">{{ $user->medical_condition ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                        <hr>
                                        <h5 class="card-title profile-info-title-max-width"><i
                                                class="mr-2 fas fa-briefcase"></i>Professional</h5>
                                        <div class="row">
                                            <div class="mb-3 col-md-6">
                                                <strong>Department:</strong> <span
                                                    class="text-muted">{{ $user->department ?? 'N/A' }}</span>
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <strong>Date of Joining:</strong> <span
                                                    class="text-muted">{{ $user->doj ? $user->doj : 'N/A' }}</span>
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <strong>Reporting Manager:</strong> <span
                                                    class="text-muted">{{ $user->get_reporting_manager->name ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                        <hr>
                                        <h5 class="card-title profile-info-title-max-width"><i
                                                class="mr-2 fas fa-briefcase"></i>Official</h5>
                                        <div class="row">
                                            <div class="mb-3 col-md-6">
                                                <strong>Email:</strong> <span
                                                    class="text-muted">{{ $user->office_email ?? 'N/A' }}</span>
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <strong>Phone:</strong> <span
                                                    class="text-muted">{{ $user->office_number ?? 'N/A' }}</span>
                                            </div>
                                            <div class="mb-3 col-md-12">
                                                <strong>Email Password:</strong>
                                                <span class="text-muted" id="password-span">
                                                    {{ $user->office_email_password ? '*********' : 'N/A' }}
                                                </span>
                                                <button type="button" class="btn" id="toggle-password"
                                                    style="{{ !$user->office_email_password ? 'display:none;' : '' }}">
                                                    <i class="fas fa-eye" id="eye-icon"></i>
                                                </button>
                                            </div>
                                            <div class="mb-3 col-md-12">
                                                <strong>Recovery Info:</strong> <span
                                                    class="text-muted">{{ $user->office_email_recovery_info ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                        <hr>
                                        <h5 class="card-title profile-info-title-max-width"><i
                                                class="mr-2 fas fa-home"></i>Address</h5>
                                        <div class="row">
                                            <div class="mb-3 col-md-12">
                                                <strong>Current :</strong> <span
                                                    class="text-muted">{{ $user->current_address ?? 'N/A' }}</span>
                                            </div>
                                            <div class="mb-3 col-md-12">
                                                <strong>Permanent :</strong> <span
                                                    class="text-muted">{{ $user->permanent_address ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                        <hr>
                                        <h5 class="card-title profile-info-title-max-width"><i
                                                class="mr-2 fas fa-heartbeat"></i>Emergency</h5>
                                        <div class="row">
                                            <div class="mb-3 col-md-6">
                                                <strong>Contact Name:</strong> <span
                                                    class="text-muted">{{ $user->e_name ?? 'N/A' }}</span>
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <strong>Phone:</strong> <span
                                                    class="text-muted">{{ $user->e_phone ?? 'N/A' }}</span>
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <strong>Relationship:</strong> <span
                                                    class="text-muted">{{ $user->e_relation ?? 'N/A' }}</span>
                                            </div>
                                            <div class="mb-3 col-md-12">
                                                <strong>Address:</strong> <span
                                                    class="text-muted">{{ $user->e_address ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                        <hr>
                                        <h5 class="card-title profile-info-title-max-width"><i
                                                class="mr-2 fas fa-university"></i>Bank Details</h5>
                                        <div class="row">
                                            <div class="mb-3 col-md-6">
                                                <strong>Bank Name:</strong> <span
                                                    class="text-muted">{{ $user->bank_name ?? 'N/A' }}</span>
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <strong>Branch Name:</strong> <span
                                                    class="text-muted">{{ $user->branch_name ?? 'N/A' }}</span>
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <strong>Account Number:</strong> <span
                                                    class="text-muted">{{ $user->account_number ?? 'N/A' }}</span>
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <strong>IFSC Code:</strong> <span
                                                    class="text-muted">{{ $user->ifsc_code ?? 'N/A' }}</span>
                                            </div>
                                            <div class="mb-3 col-md-6">
                                                <strong>Holder Name:</strong> <span
                                                    class="text-muted">{{ $user->holder_name ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <div class="modal fade" id="viewDocumentModal" tabindex="-1" aria-labelledby="viewDocumentModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="viewDocumentModalLabel">View Document</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="text-center modal-body" id="document-preview">
                    </div>
                    <div class="modal-footer">
                        <a href="#" id="download-link" class="btn btn-primary" download>Download</a>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="reuploadModal" tabindex="-1" role="dialog" aria-labelledby="reuploadModalLabel"
            aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form action="{{ route('admin.document.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="reuploadModalLabel">Upload Document</h5>
                            <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="document_id" id="document_id">
                            <input type="hidden" name="emp_code" id="emp_code" value="{{ $user->emp_code }}">
                            <input type="hidden" name="doc_type" id="doc_type">
                            <div class="form-group">
                                <label for="document">Select Document</label>
                                <input type="file" class="form-control" name="document" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Upload</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('footer-script')
    <script>
        document.getElementById('toggle-password').addEventListener('click', function() {
            var passwordSpan = document.getElementById('password-span');
            var password = "{{ $user->office_email_password ?? '' }}";
            var eyeIcon = document.getElementById('eye-icon');
            var eyeSlashIcon = document.getElementById('eye-slash-icon');

            if (passwordSpan.textContent === '*********') {
                passwordSpan.textContent = password;
                eyeIcon.style.display = 'none';
                eyeSlashIcon.style.display = 'inline';
            } else {
                passwordSpan.textContent = '*********';
                eyeIcon.style.display = 'inline';
                eyeSlashIcon.style.display = 'none';
            }
        });


        document.addEventListener('DOMContentLoaded', function() {
            $('.upload-btn, .reupload-btn').on('click', function() {
                var docId = $(this).data('doc-id') || '';
                var docType = $(this).data('doc-type');

                $('#document_id').val(docId);
                $('#doc_type').val(docType);
            });

            $('.view-btn').on('click', function() {
                var docPath = $(this).data('doc-path');
                var docType = $(this).data('doc-type').toLowerCase();
                var previewContainer = $('#document-preview');
                var downloadLink = $('#download-link');

                previewContainer.html('');

                if (docType === 'jpg' || docType === 'jpeg' || docType === 'png' || docType === 'gif') {
                    previewContainer.html('<img src="' + docPath + '" alt="Document" class="img-fluid">');
                } else if (docType === 'pdf') {
                    previewContainer.html('<embed src="' + docPath +
                        '" type="application/pdf" width="100%" height="500px">');
                } else {
                    previewContainer.html(
                        '<p class="text-danger">Unable to preview this document type.</p>');
                }
                downloadLink.attr('href', docPath);
            });
        });


        document.addEventListener('DOMContentLoaded', function() {
            const monthlySalaryInput = document.getElementById('monthly_salary');

            monthlySalaryInput.addEventListener('input', function() {
                const monthlySalary = parseFloat(monthlySalaryInput.value) || 0;

                document.getElementById('total_ctc_value').value = monthlySalary * 12;

                document.querySelectorAll('.salary-type-input').forEach(function(input) {
                    const typeValue = parseFloat(input.getAttribute('data-type-value')) || 0;
                    const calculatedSalary = (monthlySalary * typeValue) / 100;
                    input.value = calculatedSalary.toFixed(2);
                });
            });

            monthlySalaryInput.addEventListener('blur', function() {
                const monthlySalary = parseFloat(monthlySalaryInput.value) || 0;
                const salaryData = [];

                document.querySelectorAll('.salary-type-input').forEach(function(input) {
                    const typeValue = parseFloat(input.getAttribute('data-type-value')) || 0;
                    const calculatedSalary = (monthlySalary * typeValue) / 100;

                    const salaryType = input.getAttribute('data-salary-type');
                    salaryData.push({
                        salary_type: salaryType,
                        emp_code: '{{ $user->emp_code }}',
                        salary: calculatedSalary.toFixed(2),
                    });
                });

                saveSalaries(salaryData);
            });

            function saveSalaries(salaryData) {
                const saveUrl = `{{ route('admin.salary.save_all') }}`;
                const csrfToken = `{{ csrf_token() }}`;
                toastr.info('Updating started please wait for success.');
                fetch(saveUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                        },
                        body: JSON.stringify({
                            salaryData: salaryData
                        }),
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data?.success) {
                            toastr.success(data.message || 'Salaries updated successfully.');
                        } else {
                            toastr.error(data.message || 'Failed to update salaries.');
                            console.error(data.error || 'An unknown error occurred while updating salaries.');
                        }
                    })
                    .catch(error => {
                        console.error('Error updating salaries:', error);
                    });
            }
        });
    </script>
@endsection
