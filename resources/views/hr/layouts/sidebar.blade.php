@php
if (Auth::guard('hr')->check()) {
    $auth_user = Auth::guard('hr')->user();
}
$uri =  Route::currentRouteName();
@endphp

<aside class="main-sidebar sidebar-dark-danger" style="background: var(--wb-dark-red);">
    <a href="{{ route('hr.dashboard') }}" class="text-center brand-link">
        <img src="{{ asset('wb-logo2.webp') }}" alt="AdminLTE Logo" style="width: 80% !important;">
    </a>
    <div class="sidebar">
        <div class="pb-3 mt-3 mb-3 user-panel d-flex align-items-center">
            <div class="image">
                <a href="javascript:void(0);" onclick="handle_view_image('{{$auth_user->profile_image}}', '{{'hr.team.updateProfileImage', $auth_user->id}}')">
                    <img src="{{$auth_user->profile_image}}" onerror="this.src = null; this.src='{{asset('/images/default-user.png')}}'" class="img-circle elevation-2" alt="User Image" style="width: 43px; height: 43px;">
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
                    <a href="{{ route('hr.dashboard') }}" class="nav-link {{ $uri == 'hr.dashboard' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-home"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('hr.employee.list') }}" class="nav-link {{ $uri == 'hr.employee.list' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-user"></i>
                        <p>Employee</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('hr.salary-type.list') }}" class="nav-link {{ $uri == 'hr.salary-type.list' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-money-bill-alt"></i>
                        <p>Salary Type</p>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('hr.document-type.list') }}" class="nav-link {{ $uri == 'hr.document-type.list' ? 'active' : '' }}">
                        <i class="nav-icon fas fa-file"></i>
                        <p>Document Type</p>
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