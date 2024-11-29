@extends('hr.layouts.app')
@section('header-css')
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
@endsection
@section('title', $page_heading)
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
                <div class="text-sm card">
                    <div class="card-header text-light" style="background-color: var(--wb-renosand)">
                        <h3 class="card-title">{{ $page_heading }}</h3>
                    </div>
                    <form action="{{ route('hr.employee.manage_process', $user->emp_code) }}" id="employeeForm" method="post"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="card-body">
                            <div class="row">
                                <div class="mb-3 col-sm-4">
                                    <div class="form-group">
                                        <label>Role <span class="text-danger">*</span></label>
                                        <select class="form-control" name="role_id" required>
                                            <option selected disabled>Select</option>
                                            @foreach ($roles as $list)
                                                <option value="{{ $list->id }}" {{ old('role_id', $user->role_id) == $list->id ? 'selected' : '' }}>
                                                    {{ ucfirst(str_replace('_', ' ', $list->name)) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('role_id')
                                            <span class="ml-1 text-sm text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mb-3 col-sm-4">
                                    <div class="form-group">
                                        <label>Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" placeholder="Enter Name" name="name"
                                            value="{{ old('name', $user->name) }}" required>
                                        @error('name')
                                            <span class="ml-1 text-sm text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mb-3 col-sm-4">
                                    <div class="form-group">
                                        <label>Phone <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" placeholder="Enter Phone" name="phone"
                                            value="{{ old('phone', $user->phone) }}" required>
                                        @error('phone')
                                            <span class="ml-1 text-sm text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mb-3 col-sm-4">
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email" class="form-control" placeholder="Enter Email" name="email"
                                            value="{{ old('email', $user->email) }}">
                                        @error('email')
                                            <span class="ml-1 text-sm text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mb-3 col-sm-4">
                                    <div class="form-group">
                                        <label>Gender <span class="text-danger">*</span></label>
                                        <select class="form-control" name="gender" required>
                                            <option selected disabled>Select</option>
                                            <option value="female" {{ old('gender', $user->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                            <option value="male" {{ old('gender', $user->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                            <option value="others" {{ old('gender', $user->gender) == 'others' ? 'selected' : '' }}>Others</option>
                                        </select>
                                        @error('gender')
                                            <span class="ml-1 text-sm text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mb-3 col-sm-4">
                                    <div class="form-group">
                                        <label>Employee Designation <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" placeholder="Enter Employee Designation" name="employee_designation"
                                            value="{{ old('employee_designation', $user->employee_designation) }}" required>
                                        @error('employee_designation')
                                            <span class="ml-1 text-sm text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mb-3 col-sm-4">
                                    <div class="form-group">
                                        <label>DOB (<i>Date of birth</i>)<span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" placeholder="Enter DOB" name="dob"
                                            value="{{ old('dob', $user->dob) }}" required>
                                        @error('dob')
                                            <span class="ml-1 text-sm text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mb-3 col-sm-4">
                                    <div class="form-group">
                                        <label>DOJ (<i>Date of Joining</i>)<span class="text-danger">*</span></label>
                                        <input type="date" class="form-control" placeholder="Enter DOJ" name="doj"
                                            value="{{ old('doj', $user->doj) }}" required>
                                        @error('doj')
                                            <span class="ml-1 text-sm text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mb-3 col-sm-4">
                                    <div class="form-group">
                                        <label>Marital Status</label>
                                        <select class="form-control" name="marital_status">
                                            <option selected disabled>Select</option>
                                            <option value="single" {{ old('marital_status', $user->marital_status) == 'single' ? 'selected' : '' }}>Single</option>
                                            <option value="married" {{ old('marital_status', $user->marital_status) == 'married' ? 'selected' : '' }}>Married</option>
                                            <option value="divorced" {{ old('marital_status', $user->marital_status) == 'divorced' ? 'selected' : '' }}>Divorced</option>
                                            <option value="widowed" {{ old('marital_status', $user->marital_status) == 'widowed' ? 'selected' : '' }}>Widowed</option>
                                        </select>
                                        @error('marital_status')
                                            <span class="ml-1 text-sm text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mb-3 col-sm-4">
                                    <div class="form-group">
                                        <label>Employee Type<span class="text-danger">*</span></label>
                                        <select class="form-control" name="emp_type" required>
                                            <option selected disabled>Select</option>
                                            <option value="Fulltime" {{ old('emp_type', $user->emp_type) == 'Fulltime' ? 'selected' : '' }}>Fulltime</option>
                                            <option value="Intern" {{ old('emp_type', $user->emp_type) == 'Intern' ? 'selected' : '' }}>Intern</option>
                                        </select>
                                        @error('emp_type')
                                            <span class="ml-1 text-sm text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mb-3 col-sm-4">
                                    <div class="form-group">
                                        <label>Nationality</label>
                                        <input type="text" class="form-control" placeholder="Enter Nationality" name="nationality"
                                            value="{{ old('nationality', $user->nationality) }}">
                                        @error('nationality')
                                            <span class="ml-1 text-sm text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mb-3 col-sm-4">
                                    <div class="form-group">
                                        <label>Department</label>
                                        <input type="text" class="form-control" placeholder="Enter Department" name="department"
                                            value="{{ old('department', $user->department) }}">
                                        @error('department')
                                            <span class="ml-1 text-sm text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mb-3 col-sm-4">
                                    <div class="form-group">
                                        <label>Blood Group</label>
                                        <input type="text" class="form-control" placeholder="Enter Blood Group" name="blood_group"
                                            value="{{ old('blood_group', $user->blood_group) }}">
                                        @error('blood_group')
                                            <span class="ml-1 text-sm text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mb-3 col-sm-4">
                                    <div class="form-group">
                                        <label>Reporting Manager<span class="text-danger">*</span></label>
                                        <select class="form-control" name="reporting_manager" required>
                                            <option selected disabled>Select</option>
                                            @foreach ($employees as $list)
                                                <option value="{{ $list->emp_code }}" {{ old('reporting_manager', $user->reporting_manager) == $list->emp_code ? 'selected' : '' }}>
                                                    {{ ucfirst(str_replace('_', ' ', $list->name)) }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('reporting_manager')
                                            <span class="ml-1 text-sm text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mb-3 col-sm-4">
                                    <div class="form-group">
                                        <label>Alternate Phone</label>
                                        <input type="text" class="form-control" placeholder="Enter Alternate Phone" name="alt_phone"
                                            value="{{ old('alt_phone', $user->alt_phone) }}">
                                        @error('alt_phone')
                                            <span class="ml-1 text-sm text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mb-3 col-sm-6">
                                    <div class="form-group">
                                        <label>Permanent Address</label>
                                        <textarea class="form-control" placeholder="Enter Permanent Address" name="permanent_address">{{ old('permanent_address', $user->permanent_address) }}</textarea>
                                        @error('permanent_address')
                                            <span class="ml-1 text-sm text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mb-3 col-sm-6">
                                    <div class="form-group">
                                        <label>Current Address</label>
                                        <textarea class="form-control" placeholder="Enter Current Address" name="current_address">{{ old('current_address', $user->current_address) }}</textarea>
                                        @error('current_address')
                                            <span class="ml-1 text-sm text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <h3 class="col-12"><b>Official Details</b></h3>
                                <div class="mb-3 col-sm-4">
                                    <div class="form-group">
                                        <label>Office Number</label>
                                        <input type="text" class="form-control" placeholder="Enter Office Number" name="office_number"
                                            value="{{ old('office_number', $user->office_number) }}">
                                        @error('office_number')
                                            <span class="ml-1 text-sm text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mb-3 col-sm-4">
                                    <div class="form-group">
                                        <label>Office Email</label>
                                        <input type="text" class="form-control" placeholder="Enter Office Email" name="office_email"
                                            value="{{ old('office_email', $user->office_email) }}">
                                        @error('office_email')
                                            <span class="ml-1 text-sm text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mb-3 col-sm-4">
                                    <div class="form-group">
                                        <label>Office Email Password</label>
                                        <input type="text" class="form-control" placeholder="Enter Office Email Password" name="office_email_password"
                                            value="{{ old('office_email_password', $user->office_email_password) }}">
                                        @error('office_email_password')
                                            <span class="ml-1 text-sm text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mb-3 col-sm-12">
                                    <div class="form-group">
                                        <label>Office Email Recovery Info</label>
                                        <textarea class="form-control" placeholder="Enter Office Email Recovery Info" name="office_email_recovery_info">{{ old('office_email_recovery_info', $user->office_email_recovery_info) }}</textarea>
                                        @error('office_email_recovery_info')
                                            <span class="ml-1 text-sm text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <h3 class="col-12"><b>Emergency Details</b></h3>
                                <div class="mb-3 col-sm-4">
                                    <div class="form-group">
                                        <label>Emergency Name</label>
                                        <input type="text" class="form-control" placeholder="Enter Emergency Name" name="e_name"
                                            value="{{ old('e_name', $user->e_name) }}">
                                        @error('e_name')
                                            <span class="ml-1 text-sm text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mb-3 col-sm-4">
                                    <div class="form-group">
                                        <label>Emergency Number</label>
                                        <input type="text" class="form-control" placeholder="Enter Emergency Number" name="e_phone"
                                            value="{{ old('e_phone', $user->e_phone) }}">
                                        @error('e_phone')
                                            <span class="ml-1 text-sm text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mb-3 col-sm-4">
                                    <div class="form-group">
                                        <label>Emergency Relation</label>
                                        <input type="text" class="form-control" placeholder="Enter Emergency Relation" name="e_relation"
                                            value="{{ old('e_relation', $user->e_relation) }}">
                                        @error('e_relation')
                                            <span class="ml-1 text-sm text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mb-3 col-sm-6">
                                    <div class="form-group">
                                        <label>Emergency Address</label>
                                        <textarea class="form-control" placeholder="Enter Emergency Address" name="e_address">{{ old('e_address', $user->e_address) }}</textarea>
                                        @error('e_address')
                                            <span class="ml-1 text-sm text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mb-3 col-sm-6">
                                    <div class="form-group">
                                        <label>Medical Condition</label>
                                        <textarea class="form-control" placeholder="Enter Medical Condition" name="medical_condition">{{ old('medical_condition', $user->medical_condition) }}</textarea>
                                        @error('medical_condition')
                                            <span class="ml-1 text-sm text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <h3 class="col-12"><b>Bank Account Details</b></h3>
                                <div class="mb-3 col-sm-4">
                                    <div class="form-group">
                                        <label>Bank Name</label>
                                        <input type="text" class="form-control" placeholder="Enter Bank Name" name="bank_name"
                                            value="{{ old('bank_name', $user->bank_name) }}">
                                        @error('bank_name')
                                            <span class="ml-1 text-sm text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mb-3 col-sm-4">
                                    <div class="form-group">
                                        <label>Branch Name</label>
                                        <input type="text" class="form-control" placeholder="Enter Branch Name" name="branch_name"
                                            value="{{ old('branch_name', $user->branch_name) }}">
                                        @error('branch_name')
                                            <span class="ml-1 text-sm text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mb-3 col-sm-4">
                                    <div class="form-group">
                                        <label>Account Number</label>
                                        <input type="text" class="form-control" placeholder="Enter Account Number" name="account_number"
                                            value="{{ old('account_number', $user->account_number) }}">
                                        @error('account_number')
                                            <span class="ml-1 text-sm text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mb-3 col-sm-4">
                                    <div class="form-group">
                                        <label>IFSC Code</label>
                                        <input type="text" class="form-control" placeholder="Enter IFSC Code" name="ifsc_code"
                                            value="{{ old('ifsc_code', $user->ifsc_code) }}">
                                        @error('ifsc_code')
                                            <span class="ml-1 text-sm text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="mb-3 col-sm-4">
                                    <div class="form-group">
                                        <label>Account Holder Name</label>
                                        <input type="text" class="form-control" placeholder="Enter Account Holder Name" name="holder_name"
                                            value="{{ old('holder_name', $user->holder_name) }}">
                                        @error('holder_name')
                                            <span class="ml-1 text-sm text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <div class="row">
                                <div class="mb-3 col">
                                    <p>
                                        <span class="text-danger text-bold">*</span>
                                        Fields are required.
                                    </p>
                                </div>
                                <div class="text-right col">
                                    <a href="{{ route('hr.employee.list') }}"
                                        class="m-1 btn btn-sm bg-secondary">Back</a>
                                    <button type="submit" class="m-1 btn btn-sm text-light"
                                        style="background-color: var(--wb-dark-red);">Submit</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection
@section('footer-script')
@endsection
