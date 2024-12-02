<div class="vertical-menu">

    <div data-simplebar class="h-100">


        <div class="user-sidebar text-center">
            <div class="dropdown">
                <div class="user-img">
                    <img src="{{ asset('assets/images/users/avatar-7.jpg') }}" alt="" class="rounded-circle">
                    <span class="avatar-online bg-success"></span>
                </div>
                <div class="user-info">
                    <h5 class="mt-3 font-size-16 text-white">{{ Auth::user()->name }}</h5>
                    <span class="font-size-13 text-white-50">{{ Auth::user()->roles[0]->name }}</span>
                </div>
            </div>
        </div>

        <!--- Sidemenu -->
        <div id="sidebar-menu">
            <!-- Left Menu Start -->
            <ul class="metismenu list-unstyled" id="side-menu">
                <li class="menu-title">Menu</li>
                @can('read-dashboard')
                <li>
                    <a href="{{ route('dashboard') }}" class="waves-effect">
                        <i class="dripicons-home"></i>
                        {{-- <span class="badge rounded-pill bg-info float-end">3</span> --}}
                        <span>Dashboard</span>
                    </a>
                </li>
                @endcan

                <li class="{{ request()->is('admin/project') ? 'mm-activate' : '' }}">
                    <a href="{{ route('project') }}" class=" waves-effect">
                        <i class="dripicons-calendar"></i>
                        <span>Project</span>
                    </a>
                </li>

                @canany(['read-vendors','read-paymentvendors'])
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="dripicons-browser"></i>
                        <span>Vendor Data</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        @can('read-vendors')
                        <li><a href="{{ route('vendor') }}">Vendor</a></li>
                        @endcan
                        @can('read-paymentvendors')
                        <li><a href="{{ route('payment') }}">Payment Vendor</a></li>
                        @endcan
                    </ul>
                </li>
                @endcanany

                @canany(['read-itemtypes','read-companies','read-units','read-items','read-projecttypes'])
                <li class="menu-title">Data Master</li>
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="dripicons-suitcase"></i>
                        <span>Master Data</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        @can('read-items')
                        <li><a href="{{ route('item') }}">Items</a></li>
                        @endcan
                        @can('read-itemtypes')
                        <li><a href="{{ route('itemtype') }}">Items Type</a></li>
                        @endcan
                        @can('read-units')
                        <li><a href="{{ route('unit') }}">Unit/Satuan</a></li>
                        @endcan
                        @can('read-companies')
                        <li><a href="{{ route('company') }}">Company</a></li>
                        @endcan
                        @can('read-projecttypes')
                        <li><a href="{{ route('projecttype') }}">Tipe Project</a></li>
                        @endcan
                    </ul>
                </li>
                @endcanany

                @can('read-projectreviews')
                <li class="menu-title">Review</li>
                @can('read-projectreviews')
                <li class="{{ request()->is('admin/review') ? 'mm-activate' : '' }}">
                    <a href="{{ route('review') }}" class=" waves-effect">
                        <i class="dripicons-blog"></i>
                        <span>Project Review</span>
                    </a>
                </li>
                @endcan
                @endcan

                @canany(['read-tasks','read-project-timeline'])
                <li class="menu-title">Task Management</li>
                @can('read-task')
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="dripicons-suitcase"></i>
                        <span>
                            Task Data
                        </span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('tasks') }}">Tasks</a></li>
                    </ul>
                </li>
                @endcan
                @can('read-project-timeline')
                <li class="{{ request()->is('/admin/timeline') ? 'mm-activate' : '' }}">
                    <a href="{{ route('timeline') }}" class=" waves-effect">
                        <i class="dripicons-blog"></i>
                        <span>Project Timeline</span>
                    </a>
                </li>
                @endcan
                @endcanany
                @canany(['read-report-project','read-reportvendors'])
                <li class="menu-title">Laporan</li>
                @can('read-reportvendors')
                <li class="{{ request()->is('admin/report') ? 'mm-activate' : '' }}">
                    <a href="{{ route('report') }}" class=" waves-effect">
                        <i class="dripicons-to-do"></i>
                        <span>Report</span>
                    </a>
                </li>
                @endcan
                @can('read-report-project')
                <li class="{{ request()->is('admin/report/project') ? 'mm-activate' : '' }}">
                    <a type="button" data-bs-toggle="modal" data-bs-target="#ReportProjectModal" class=" waves-effect">
                        <i class="dripicons-to-do"></i>
                        <span>Report Project</span>
                    </a>
                </li>
                @endcan
                @endcanany

                @canany(['read-roles','read-users','setting-aplication','read-logs'])
                    
                @endcanany
                <li class="menu-title">SETTING</li>
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="dripicons-gear"></i>
                        <span>Setting</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="true">
                        @can('read-users')
                        <li><a href="{{ route('user') }}">User</a></li>
                        @endcan
                        @can('read-roles')
                        <li><a href="{{ route('role') }}">Role</a></li>
                        @endcan
                        @can('setting-aplication')
                        <li><a href="{{ route('aplication') }}">Setting Aplikasi</a></li>
                        @endcan
                        @can('read-logs')
                        <li><a href="{{ route('log') }}">Log Aplikasi</a></li>
                        @endcan
                    </ul>
                </li>

            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>