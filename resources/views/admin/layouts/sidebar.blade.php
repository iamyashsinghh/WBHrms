@php
if (Auth::guard('admin')->check()) {
    $auth_user = Auth::guard('admin')->user();
}
$uri =  Route::currentRouteName();
@endphp

<aside class="main-sidebar sidebar-dark-danger" style="background: var(--wb-dark-red);">
    <a href="{{ route('admin.dashboard') }}" class="text-center brand-link">
        <img src="{{ asset('wb-logo2.webp') }}" alt="AdminLTE Logo" style="width: 80% !important;">
    </a>
    <div class="sidebar">
        <div class="pb-3 mt-3 mb-3 user-panel d-flex align-items-center">
            <div class="image">
                <a href="javascript:void(0);" onclick="handleViewImage('{{ $auth_user->profile_img }}', '{{route('updateProfileImage')}}/{{ $auth_user->emp_code }}')">
                    <img
                        src="{{ $auth_user->profile_img ?? asset('/images/default-user.png') }}"
                        onerror="this.onerror=null; this.src='{{ asset('/images/default-user.png') }}';"
                        class="img-circle elevation-2"
                        alt="User Image"
                        style="width: 43px; height: 43px;">
                </a>
            </div>
            <div class="py-0 text-center info">
                <a href="javascript:void(0);" class="d-block">{{$auth_user->name}} - {{$auth_user->get_role->name}}</a>
                <span class="text-xs text-bold" style="color: #c2c7d0;">{{$auth_user->venue_name ?: 'N/A'}}</span>
            </div>
        </div>
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ $uri == 'admin.dashboard' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.employee.list') }}" class="nav-link {{ $uri == 'admin.employee.list' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Employee</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.attendance.daily') }}" class="nav-link {{ $uri == 'admin.attendance.daily' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Attendance</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.approval.list') }}" class="nav-link {{ $uri == 'admin.approval.list' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-calendar-check"></i>
                        <p>Leave Management</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.resign.list') }}" class="nav-link {{ $uri == 'admin.resign.list' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-calendar-check"></i>
                        <p>Resignation Mgmt</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.role.list') }}" class="nav-link {{ $uri == 'admin.role.list' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user-tag"></i>
                        <p>Roles</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.holiday.list') }}" class="nav-link {{ $uri == 'admin.holiday.list' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-coins"></i>
                        <p>Holiday</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.salary-type.list') }}" class="nav-link {{ $uri == 'admin.salary-type.list' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-coins"></i>
                        <p>Salary Type</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.payroll.index') }}" class="nav-link {{ $uri == 'admin.payroll.index' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-file-invoice-dollar"></i>
                        <p>Payroll</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.notification.list') }}" class="nav-link {{ $uri == 'admin.notification.list' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-bell"></i>
                        <p>Notification</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.document-type.list') }}" class="nav-link {{ $uri == 'admin.document-type.list' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-folder-open"></i>
                        <p>Document Type</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.geo.index_all') }}" class="nav-link {{ $uri == 'admin.geo.index_all' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-map-marker-alt"></i>
                        <p>Live Location</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.fcm.index') }}" class="nav-link {{ $uri == 'admin.fcm.index' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-mobile-alt"></i>
                        <p>App Notification</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.env.index') }}" class="nav-link {{ $uri == 'admin.env.index' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-cogs"></i>
                        <p>Hrms Settings</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>

<script>
    $('.nav-sidebar').tree();

    function initialize_sidebar_collapse() {
        const sidebar_collapsible_elem = document.getElementById('sidebar_collapsible_elem');
        const localstorage_value = localStorage.getItem('sidebar_collapse');
        if (localstorage_value !== null) {
            if (localstorage_value == "true") {
                sidebar_collapsible_elem.setAttribute('data-collapse', 0); // 0 means: collapse
                document.body.classList.add('sidebar-collapse');
            }
        }
    }
    initialize_sidebar_collapse();
</script>
