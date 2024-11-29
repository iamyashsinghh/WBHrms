@extends('hr.layouts.app')
@section('title', $page_heading)
@section('header-css')
<style>
    .profile-info-title-max-width {
        max-width: 130px;
        width: 130px;
    }

    input[disabled] {
        background-color: transparent !important;
    }

    select[disabled] {
        background-color: transparent !important;
    }

    textarea[disabled] {
        background-color: transparent !important;
    }

    .read-only-input {
        border: none;
        background-color: #ffffff;
        pointer-events: none;
        color: #6c757d;
        padding-left: 0;
    }

    .editable-input {
        border: 1px solid #ced4da;
        background-color: #ffffff;
    }
</style>
@endsection

@section('main')
<div class="pb-3 content-wrapper">
    <section class="mt-3 content">
        <div class="container-fluid">
            <div class="mb-1 card">
                <div class="card-header text-light" style="background-color: var(--wb-dark-red);">
                    <div class="d-flex justify-content-between" style="align-items: center;">
                        <h3 class="card-title">{{ $page_heading }} --
                            {{ ucfirst(str_replace('_', ' ', $user->get_role->name)) }}</h3>
                        <div>
                            <a href="{{ route('send.hr.mail') }}/{{ $user->emp_code }}/offerletter"
                                class="btn btn-sm btn-warning">Release Offer Letter</a>
                                <a href="{{ route('hr.employee.manage') }}/{{ $user->emp_code }}" class="btn btn-sm btn-info" target="_blank"><i
                                    class="fa fa-edit"
                                    style=""></i></a>

                        </div>
                    </div>
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
                                                <button type="button" class="btn btn-outline-primary btn-sm view-btn"
                                                    data-doc-path="{{ asset($document->path) }}"
                                                    data-doc-type="{{ pathinfo($document->path, PATHINFO_EXTENSION) }}"
                                                    data-bs-toggle="modal" data-bs-target="#viewDocumentModal">
                                                    View
                                                </button>
                                                <button type="button"
                                                    class="btn btn-outline-secondary btn-sm reupload-btn"
                                                    data-doc-id="{{ $document->id }}" data-doc-type="{{ $type->id }}"
                                                    data-bs-toggle="modal" data-bs-target="#reuploadModal">
                                                    Upload
                                                </button>
                                                @else
                                                <button type="button"
                                                    class="btn btn-outline-secondary btn-sm reupload-btn" data-doc-id=""
                                                    data-doc-type="{{ $type->id }}" data-bs-toggle="modal"
                                                    data-bs-target="#reuploadModal">
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
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h2 class="text-center w-100">Information</h2>
                                    </div>
                                    <h5 class="card-title profile-info-title-max-width"><i
                                            class="mr-2 fas fa-info-circle"></i>Personal</h5>
                                    <div class="row">
                                        <div class="mb-3 col-md-12">
                                            <div class="d-flex justify-content-end">
                                                <button class="btn btn-outline-primary btn-sm" id="editPersonalBtn"
                                                    onclick="toggleEditPersonal()">
                                                    <i class="fa fa-edit"></i> Edit
                                                </button>
                                                <button class="mx-2 btn btn-danger btn-sm d-none" id="cancelPersonalBtn"
                                                    onclick="toggleEditPersonal()">
                                                    Cancel
                                                </button>
                                                <button class="btn btn-success btn-sm d-none" id="savePersonalBtn"
                                                    onclick="savePersonalInfo()">
                                                    Save
                                                </button>
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <strong>Phone:</strong> <span class="text-muted">
                                                <input type="text" class="form-control personal-input read-only-input"
                                                    id="phone" value="{{ $user->phone ?? '' }}" disabled>
                                            </span>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <strong>Alt Phone:</strong> <span class="text-muted">
                                                <input type="text" class="form-control personal-input read-only-input"
                                                    id="alt_phone" value="{{ $user->alt_phone ?? '' }}" disabled>
                                            </span>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <strong>Email:</strong> <span class="text-muted">
                                                <input type="email" class="form-control personal-input read-only-input"
                                                    id="email" value="{{ $user->email ?? '' }}" disabled>
                                            </span>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <strong>Gender:</strong> <span class="text-muted">
                                                <select class="form-control personal-input read-only-input" id="gender"
                                                    disabled>
                                                    <option value="male" {{ $user->gender == 'male' ? 'selected' : ''
                                                        }}>Male</option>
                                                    <option value="female" {{ $user->gender == 'female' ? 'selected' :
                                                        '' }}>Female</option>
                                                    <option value="others" {{ $user->gender == 'others' ? 'selected' : ''
                                                        }}>Others</option>
                                                </select>
                                            </span>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <strong>Date of Birth:</strong> <span class="text-muted">
                                                <input type="date" class="form-control personal-input read-only-input"
                                                    id="dob" value="{{ $user->dob ?? '' }}" disabled>
                                            </span>
                                        </div>

                                        <div class="mb-3 col-md-6">
                                            <strong>Marital Status:</strong> <span class="text-muted">
                                                <select class="form-control personal-input read-only-input" id="marital_status"
                                                    disabled>
                                                    <option value="single" {{ $user->marital_status == 'single' ? 'selected' :
                                                        '' }}>Single</option>
                                                    <option value="married" {{ $user->marital_status == 'married' ? 'selected' :
                                                        '' }}>Married</option>
                                                    <option value="divorced" {{ $user->marital_status == 'divorced' ? 'selected'
                                                        : '' }}>Divorced</option>
                                                    <option value="widowed" {{ $user->marital_status == 'widowed' ? 'selected' :
                                                        '' }}>Widowed</option>
                                                </select>
                                            </span>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <strong>Nationality:</strong> <span class="text-muted"><input type="text"
                                                    class="form-control personal-input read-only-input" id="nationality"
                                                    value="{{ $user->nationality ?? '' }}" disabled></span>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <strong>Blood Group:</strong> <span class="text-muted"> <input type="text"
                                                    class="form-control personal-input read-only-input" id="blood_group"
                                                    value="{{ $user->blood_group ?? '' }}" disabled></span>
                                        </div>
                                    </div>
                                    <hr>

                                    <h5 class="card-title profile-info-title-max-width">
                                        <i class="mr-2 fas fa-briefcase"></i>Employment
                                    </h5>
                                    <div class="row">
                                        <div class="mb-3 col-md-12">
                                            <div class="d-flex justify-content-end">
                                                <button class="btn btn-outline-primary btn-sm" id="editEmploymentBtn"
                                                    onclick="toggleEditEmployment()">
                                                    <i class="fa fa-edit"></i> Edit
                                                </button>
                                                <button class="mx-2 btn btn-danger btn-sm d-none"
                                                    id="cancelEmploymentBtn" onclick="toggleEditEmployment()">
                                                    Cancel
                                                </button>
                                                <button class="btn btn-success btn-sm d-none" id="saveEmploymentBtn"
                                                    onclick="saveEmploymentInfo()">
                                                    Save
                                                </button>
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <strong>UAN:</strong>
                                            <span class="text-muted">
                                                <input type="text" class="form-control employment-input read-only-input"
                                                    id="uan" value="{{ $user->uan ?? '' }}" disabled>
                                            </span>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <strong>PAN Number:</strong>
                                            <span class="text-muted">
                                                <input type="text" class="form-control employment-input read-only-input"
                                                    id="pan_number" value="{{ $user->pan_number ?? '' }}" disabled>
                                            </span>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <strong>Aadhaar Number:</strong>
                                            <span class="text-muted">
                                                <input type="text" class="form-control employment-input read-only-input"
                                                    id="aadhaar_number" value="{{ $user->aadhaar_number ?? '' }}"
                                                    disabled>
                                            </span>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <strong>PF Number:</strong>
                                            <span class="text-muted">
                                                <input type="text" class="form-control employment-input read-only-input"
                                                    id="pf_number" value="{{ $user->pf_number ?? '' }}" disabled>
                                            </span>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <strong>PF Joining Date:</strong>
                                            <span class="text-muted">
                                                <input type="date" class="form-control employment-input read-only-input"
                                                    id="pf_joining_date" value="{{ $user->pf_joining_date ?? '' }}"
                                                    disabled>
                                            </span>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <strong>PF Eligible:</strong>
                                            <span class="text-muted">
                                                <select class="form-control employment-input read-only-input"
                                                    id="pf_eligible" disabled>
                                                    <option value="1" {{ $user->pf_eligible ? 'selected' : '' }}>
                                                        Yes
                                                    </option>
                                                    <option value="0" {{ !$user->pf_eligible ? 'selected' : '' }}>No
                                                    </option>
                                                </select>
                                            </span>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <strong>ESI Eligible:</strong>
                                            <span class="text-muted">
                                                <select class="form-control employment-input read-only-input"
                                                    id="esi_eligible" disabled>
                                                    <option value="1" {{ $user->esi_eligible ? 'selected' : '' }}>Yes
                                                    </option>
                                                    <option value="0" {{ !$user->esi_eligible ? 'selected' : '' }}>No
                                                    </option>
                                                </select>
                                            </span>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <strong>ESI Number:</strong>
                                            <span class="text-muted">
                                                <input type="text" class="form-control employment-input read-only-input"
                                                    id="esi_number" value="{{ $user->esi_number ?? '' }}" disabled>
                                            </span>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <strong>PT Eligible:</strong>
                                            <span class="text-muted">
                                                <select class="form-control employment-input read-only-input"
                                                    id="pt_eligible" disabled>
                                                    <option value="1" {{ $user->pt_eligible ? 'selected' : '' }}>
                                                        Yes
                                                    </option>
                                                    <option value="0" {{ !$user->pt_eligible ? 'selected' : '' }}>No
                                                    </option>
                                                </select>
                                            </span>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <strong>LWF Eligible:</strong>
                                            <span class="text-muted">
                                                <select class="form-control employment-input read-only-input"
                                                    id="lwf_eligible" disabled>
                                                    <option value="1" {{ $user->lwf_eligible ? 'selected' : '' }}>Yes
                                                    </option>
                                                    <option value="0" {{ !$user->lwf_eligible ? 'selected' : '' }}>No
                                                    </option>
                                                </select>
                                            </span>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <strong>EPS Eligible:</strong>
                                            <span class="text-muted">
                                                <select class="form-control employment-input read-only-input"
                                                    id="eps_eligible" disabled>
                                                    <option value="1" {{ $user->eps_eligible ? 'selected' : '' }}>Yes
                                                    </option>
                                                    <option value="0" {{ !$user->eps_eligible ? 'selected' : '' }}>No
                                                    </option>
                                                </select>
                                            </span>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <strong>EPS Joining Date:</strong>
                                            <span class="text-muted">
                                                <input type="date" class="form-control employment-input read-only-input"
                                                    id="eps_joining_date" value="{{ $user->eps_joining_date ?? '' }}"
                                                    disabled>
                                            </span>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <strong>EPS Exit Date:</strong>
                                            <span class="text-muted">
                                                <input type="date" class="form-control employment-input read-only-input"
                                                    id="eps_exit_date" value="{{ $user->eps_exit_date ?? '' }}"
                                                    disabled>
                                            </span>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <strong>HPS Eligible:</strong>
                                            <span class="text-muted">
                                                <select class="form-control employment-input read-only-input"
                                                    id="hps_eligible" disabled>
                                                    <option value="1" {{ $user->hps_eligible ? 'selected' : '' }}>Yes
                                                    </option>
                                                    <option value="0" {{ !$user->hps_eligible ? 'selected' : '' }}>No
                                                    </option>
                                                </select>
                                            </span>
                                        </div>
                                    </div>
                                    <hr>
                                    <h5 class="card-title profile-info-title-max-width"><i
                                            class="mr-2 fas fa-heartbeat"></i>Medical</h5>
                                    <div class="row">
                                        <div class="mb-3 col-md-12">
                                            <div class="d-flex justify-content-end">
                                                <button class="btn btn-outline-primary btn-sm" id="editMedicalBtn"
                                                    onclick="toggleEditMedical()">
                                                    <i class="fa fa-edit"></i> Edit
                                                </button>
                                                <button class="mx-2 btn btn-danger btn-sm d-none" id="cancelMedicalBtn"
                                                    onclick="toggleEditMedical()">
                                                    Cancel
                                                </button>
                                                <button class="btn btn-success btn-sm d-none" id="saveMedicalBtn"
                                                    onclick="saveMedicalInfo()">
                                                    Save
                                                </button>
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-12">
                                            <span class="text-muted"> <textarea
                                                    class="form-control medical-input read-only-input"
                                                    id="medical_condition">{{ $user->medical_condition }}</textarea>
                                            </span>
                                        </div>
                                    </div>
                                    <hr>
                                    <h5 class="card-title profile-info-title-max-width"><i
                                            class="mr-2 fas fa-briefcase"></i>Professional</h5>
                                    <div class="row">
                                        <div class="mb-3 col-md-12">
                                            <div class="d-flex justify-content-end">
                                                <button class="btn btn-outline-primary btn-sm" id="editProfessionalBtn"
                                                    onclick="toggleEditProfessional()">
                                                    <i class="fa fa-edit"></i> Edit
                                                </button>
                                                <button class="mx-2 btn btn-danger btn-sm d-none"
                                                    id="cancelProfessionalBtn" onclick="toggleEditProfessional()">
                                                    Cancel
                                                </button>
                                                <button class="btn btn-success btn-sm d-none" id="saveProfessionalBtn"
                                                    onclick="saveProfessionalInfo()">
                                                    Save
                                                </button>
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <strong>Department:</strong> <span class="text-muted"><input type="text"
                                                    class="form-control professional-input read-only-input"
                                                    id="department" value="{{ $user->department ?? 'N/A' }}"
                                                    disabled></span>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <strong>Date of Joining:</strong> <span class="text-muted"><input
                                                    type="date" class="form-control professional-input read-only-input"
                                                    id="doj" value="{{ $user->doj ?? '' }}" disabled></span>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <strong>Reporting Manager:</strong> <span class="text-muted">
                                                <select class="form-control professional-input read-only-input" id="reporting_manager" disabled>
                                                    <option value="">Select Manager</option>
                                                    @foreach($managers as $manager)
                                                        <option value="{{ $manager->emp_code }}" {{ $user->reporting_manager == $manager->emp_code ? 'selected' : '' }}>
                                                            {{ $manager->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                               </span>
                                        </div>
                                    </div>
                                    <hr>
                                    <h5 class="card-title profile-info-title-max-width"><i
                                            class="mr-2 fas fa-home"></i>Address</h5>
                                    <div class="row">
                                        <div class="mb-3 col-md-12">
                                            <div class="d-flex justify-content-end">
                                                <button class="btn btn-outline-primary btn-sm" id="editAddressBtn"
                                                    onclick="toggleEditAddress()">
                                                    <i class="fa fa-edit"></i> Edit
                                                </button>
                                                <button class="mx-2 btn btn-danger btn-sm d-none" id="cancelAddressBtn"
                                                    onclick="toggleEditAddress()">
                                                    Cancel
                                                </button>
                                                <button class="btn btn-success btn-sm d-none" id="saveAddressBtn"
                                                    onclick="saveAddressInfo()">
                                                    Save
                                                </button>
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-12">
                                            <strong>Current:</strong> <span class="text-muted"><textarea
                                                    class="form-control address-input read-only-input"
                                                    id="current_address"
                                                    disabled>{{ $user->current_address ?? 'N/A' }}</textarea></span>
                                        </div>
                                        <div class="mb-3 col-md-12">
                                            <strong>Permanent:</strong> <span class="text-muted"><textarea
                                                    class="form-control address-input read-only-input"
                                                    id="permanent_address"
                                                    disabled>{{ $user->permanent_address ?? 'N/A' }}</textarea></span>
                                        </div>
                                    </div>
                                    <hr>
                                    <h5 class="card-title profile-info-title-max-width"><i
                                            class="mr-2 fas fa-briefcase"></i>Official</h5>
                                    <div class="row">
                                        <div class="mb-3 col-md-12">
                                            <div class="d-flex justify-content-end">
                                                <button class="btn btn-outline-primary btn-sm" id="editOfficialBtn"
                                                    onclick="toggleEditOfficial()">
                                                    <i class="fa fa-edit"></i> Edit
                                                </button>
                                                <button class="mx-2 btn btn-danger btn-sm d-none" id="cancelOfficialBtn"
                                                    onclick="toggleEditOfficial()">
                                                    Cancel
                                                </button>
                                                <button class="btn btn-success btn-sm d-none" id="saveOfficialBtn"
                                                    onclick="saveOfficialInfo()">
                                                    Save
                                                </button>
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <strong>Email:</strong>
                                            <input type="email" class="form-control official-input read-only-input"
                                                id="office_email" value="{{ $user->office_email ?? 'N/A' }}" disabled>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <strong>Phone:</strong>
                                            <input type="text" class="form-control official-input read-only-input"
                                                id="office_number" value="{{ $user->office_number ?? 'N/A' }}" disabled>
                                        </div>
                                        <div class="mb-3 col-md-12">
                                            <strong>Email Password:</strong>
                                            <div class="d-flex">
                                                <input type="password"
                                                    class="form-control official-input read-only-input"
                                                    id="office_email_password"
                                                    value="{{ $user->office_email_password ?? '' }}" disabled>
                                                <button type="button" class="btn" id="toggle-password"
                                                    style="{{ !$user->office_email_password ? 'display:none;' : '' }}">
                                                    <i class="fas fa-eye" id="eye-icon"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-12">
                                            <strong>Recovery Info:</strong>
                                            <textarea type="text" class="form-control official-input read-only-input" id="office_email_recovery_info" disabled>{{ $user->office_email_recovery_info }}</textarea>
                                        </div>
                                    </div>
                                    <hr>

                                    <h5 class="card-title profile-info-title-max-width"><i
                                            class="mr-2 fas fa-heartbeat"></i>Emergency</h5>
                                    <div class="row">
                                        <div class="mb-3 col-md-12">
                                            <div class="d-flex justify-content-end">
                                                <button class="btn btn-outline-primary btn-sm" id="editEmergencyBtn"
                                                    onclick="toggleEditEmergency()">
                                                    <i class="fa fa-edit"></i> Edit
                                                </button>
                                                <button class="mx-2 btn btn-danger btn-sm d-none"
                                                    id="cancelEmergencyBtn" onclick="toggleEditEmergency()">
                                                    Cancel
                                                </button>
                                                <button class="btn btn-success btn-sm d-none" id="saveEmergencyBtn"
                                                    onclick="saveEmergencyInfo()">
                                                    Save
                                                </button>
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <strong>Contact Name:</strong> <span class="text-muted"> <input type="text"
                                                    class="form-control emergency-input read-only-input" id="e_name"
                                                    value="{{ $user->e_name ?? '' }}" disabled></span>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <strong>Phone:</strong> <span class="text-muted">
                                                <input type="text" class="form-control emergency-input read-only-input"
                                                    id="e_phone" value="{{ $user->e_phone ?? '' }}" disabled></span>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <strong>Relationship:</strong> <span class="text-muted"> <input type="text"
                                                    class="form-control emergency-input read-only-input" id="e_relation"
                                                    value="{{ $user->e_relation ?? '' }}" disabled></span>
                                        </div>
                                        <div class="mb-3 col-md-12">
                                            <strong>Address:</strong> <span class="text-muted"> <textarea type="text"
                                                    class="form-control emergency-input read-only-input" id="e_address"
                                                    disabled>{{ $user->e_address ?? 'N/A' }}</textarea></span>
                                        </div>
                                    </div>
                                    <hr>
                                    <h5 class="card-title profile-info-title-max-width"><i
                                            class="mr-2 fas fa-university"></i>Bank Details</h5>
                                    <div class="row">
                                        <div class="mb-3 col-md-12">
                                            <div class="d-flex justify-content-end">
                                                <button class="btn btn-outline-primary btn-sm" id="editBankBtn"
                                                    onclick="toggleEditBank()">
                                                    <i class="fa fa-edit"></i> Edit
                                                </button>
                                                <button class="mx-2 btn btn-danger btn-sm d-none" id="cancelBankBtn"
                                                    onclick="toggleEditBank()">
                                                    Cancel
                                                </button>
                                                <button class="btn btn-success btn-sm d-none" id="saveBankBtn"
                                                    onclick="saveBankInfo()">
                                                    Save
                                                </button>
                                            </div>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <strong>Bank Name:</strong> <span class="text-muted">
                                                <input type="text" class="form-control bank-input read-only-input"
                                                    id="bank_name" value="{{ $user->bank_name ?? 'N/A' }}" disabled>
                                            </span>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <strong>Branch Name:</strong> <span class="text-muted">
                                                <input type="text" class="form-control bank-input read-only-input"
                                                    id="branch_name" value="{{ $user->branch_name ?? 'N/A' }}" disabled>
                                            </span>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <strong>Account Number:</strong> <span class="text-muted">
                                                <input type="text" class="form-control bank-input read-only-input"
                                                    id="account_number" value="{{ $user->account_number ?? 'N/A' }}"
                                                    disabled>
                                            </span>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <strong>IFSC Code:</strong> <span class="text-muted">
                                                <input type="text" class="form-control bank-input read-only-input"
                                                    id="ifsc_code" value="{{ $user->ifsc_code ?? 'N/A' }}" disabled>
                                            </span>
                                        </div>
                                        <div class="mb-3 col-md-6">
                                            <strong>Holder Name:</strong> <span class="text-muted">
                                                <input type="text" class="form-control bank-input read-only-input"
                                                    id="holder_name" value="{{ $user->holder_name ?? 'N/A' }}" disabled>
                                            </span>
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
    <section class="mt-3 content">
        <div class="container-fluid">
            <div class="mb-1 card">
                <div class="card-header text-light" style="background-color: var(--wb-dark-red);">
                    <div class="d-flex justify-content-between" style="align-items: center;">
                        <h3 class="card-title">{{ $page_heading }} -- Documents</h3>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">

                        <div class="table-responsive">
                            <table id="serverTable" class="table mb-0" style="background-color: #fdfd7b5c">
                                <thead>
                                    <tr>
                                        <th class="text-nowrap">S.No</th>
                                        <th class="text-nowrap">Created At</th>
                                        <th class="">Updated At</th>
                                        <th class="text-nowrap">Name</th>
                                        <th class="">Doc</th>
                                    </tr>
                                </thead>

                                <body>
                                    @if (sizeof($user->get_documents) > 0)
                                    @foreach ($user->get_documents as $key => $list)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ date('d-M-Y h:i a', strtotime($list->created_at)) }}</td>
                                        <td>{{ date('d-M-Y h:i a', strtotime($list->updated_at)) }}</td>
                                        <td>{{ $list->doc_name ?? '' }}</td>
                                        <td>
                                            <button type="button" class="btn btn-outline-primary btn-sm view-btn"
                                                data-doc-path="{{ asset($list->path) }}"
                                                data-doc-type="{{ pathinfo($list->path, PATHINFO_EXTENSION) }}"
                                                data-bs-toggle="modal" data-bs-target="#viewDocumentModal">
                                                View
                                            </button>
                                        </td>

                                    </tr>
                                    @endforeach
                                    @else
                                    <tr>
                                        <td class="text-center text-muted" colspan="5">No Document Available!
                                        </td>
                                    </tr>
                                    @endif
                                </body>
                            </table>
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
                <form action="{{ route('hr.document.upload') }}" method="POST" enctype="multipart/form-data">
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
                const saveUrl = `{{ route('hr.salary.save_all') }}`;
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

        function toggleEdit(section) {
        const editBtn = document.getElementById(`edit${section}Btn`);
        const saveBtn = document.getElementById(`save${section}Btn`);
        const cancelBtn = document.getElementById(`cancel${section}Btn`);
        const inputs = document.querySelectorAll(`.${section.toLowerCase()}-input`);

        if (editBtn.classList.contains('d-none')) {
            editBtn.classList.remove('d-none');
            saveBtn.classList.add('d-none');
            cancelBtn.classList.add('d-none');

            inputs.forEach(input => {
                input.disabled = true;
                input.classList.remove('editable-input');
                input.classList.add('read-only-input');
            });
        } else {
            editBtn.classList.add('d-none');
            saveBtn.classList.remove('d-none');
            cancelBtn.classList.remove('d-none');

            inputs.forEach(input => {
                input.disabled = false;
                input.classList.remove('read-only-input');
                input.classList.add('editable-input');
            });
        }
    }

    function saveInfo(section) {
        const data = {};
        const inputs = document.querySelectorAll(`.${section.toLowerCase()}-input`);
        inputs.forEach(input => {
            data[input.id] = input.value;
        });
        data.emp_code = `{{$user->emp_code}}`;

        const saveUrl = `{{ route('hr.employee.updateEmploymentInfo') }}`;
        const csrfToken = `{{ csrf_token() }}`;

        fetch(saveUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: JSON.stringify(data),
        })
        .then(response => response.json())
        .then(data => {
            if (data?.success) {
                toastr.success(data.message || `${section} information updated successfully.`);
            } else {
                toastr.error(data.message || `Failed to update ${section} information.`);
            }
        })
        .catch(error => {
            console.error(`Error updating ${section} information:`, error);
        });

        toggleEdit(section);
    }

    function toggleEditPersonal() {
        toggleEdit('Personal');
    }

    function savePersonalInfo() {
        saveInfo('Personal');
    }

    function toggleEditEmployment() {
        toggleEdit('Employment');
    }

    function saveEmploymentInfo() {
        saveInfo('Employment');
    }

    function toggleEditMedical() {
        toggleEdit('Medical');
    }

    function saveMedicalInfo() {
        saveInfo('Medical');
    }

    function toggleEditBank() {
        toggleEdit('Bank');
    }

    function saveBankInfo() {
        saveInfo('Bank');
    }

    function toggleEditEmergency() {
        toggleEdit('Emergency');
    }

    function saveEmergencyInfo() {
        saveInfo('Emergency');
    }

    function toggleEditAddress(){
        toggleEdit('Address')
    }
    function saveAddressInfo(){
        saveInfo('Address')
    }

    function toggleEditProfessional() {
    toggleEdit('Professional');
}

function saveProfessionalInfo() {
    saveInfo('Professional');
}

function toggleEditOfficial() {
    toggleEdit('Official');
}

function saveOfficialInfo() {
    saveInfo('Official');
}

function toggleEditOfficial() {
    toggleEdit('Official');
}

function saveOfficialInfo() {
    const data = {};
    data.office_email = document.getElementById('office_email').value;
    data.office_number = document.getElementById('office_number').value;
    data.office_email_password = document.getElementById('office_email_password').value;
    data.office_email_recovery_info = document.getElementById('office_email_recovery_info').value;
    data.emp_code = `{{$user->emp_code}}`;

    const saveUrl = `{{ route('hr.employee.updateEmploymentInfo') }}`;
    const csrfToken = `{{ csrf_token() }}`;

    fetch(saveUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify(data),
    })
    .then(response => response.json())
    .then(data => {
        if (data?.success) {
            toastr.success(data.message || `Official information updated successfully.`);
        } else {
            toastr.error(data.message || `Failed to update Official information.`);
        }
    })
    .catch(error => {
        console.error(`Error updating Official information:`, error);
    });

    toggleEdit('Official');
}

document.getElementById('toggle-password').addEventListener('click', function() {
    const passwordInput = document.getElementById('office_email_password');
    const eyeIcon = document.getElementById('eye-icon');

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.classList.remove('fa-eye');
        eyeIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        eyeIcon.classList.remove('fa-eye-slash');
        eyeIcon.classList.add('fa-eye');
    }
});
</script>
@endsection
