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
            <ul class="metismenu list-unstyled" id="side-menu" aria-label="Main Navigation">
                
                {{-- Dashboard --}}
                @can('read-dashboard')
                    <li class="menu-title">Master Data</li>
                    <li class="{{ request()->routeIs('dashboard') ? 'mm-active' : '' }}">
                        <a href="{{ route('dashboard') }}" class="waves-effect">
                            <i class="dripicons-home"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                @endcan

                {{-- Project Management --}}
                <li class="{{ request()->routeIs('project') ? 'mm-active' : '' }}">
                    <a href="{{ route('project') }}" class="waves-effect">
                        <i class="dripicons-calendar"></i>
                        <span>Projects</span>
                    </a>
                </li>

                {{-- Vendor Data --}}
                @canany(['read-vendors', 'read-paymentvendors'])
                    <li>
                        <a href="javascript:void(0);" class="has-arrow waves-effect">
                            <i class="dripicons-browser"></i>
                            <span>Vendor Data</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            @can('read-vendors')
                                <li><a href="{{ route('vendor') }}">Vendors</a></li>
                            @endcan

                            @can('read-paymentvendors')
                                <li><a href="{{ route('payment') }}">Payment Vendors</a></li>
                            @endcan
                        </ul>
                    </li>
                @endcanany

                {{-- Master Data --}}
                @canany(['read-itemtypes', 'read-companies', 'read-units', 'read-items', 'read-projecttypes'])
                    <li class="menu-title">Master Data</li>
                    <li>
                        <a href="javascript:void(0);" class="has-arrow waves-effect">
                            <i class="dripicons-suitcase"></i>
                            <span>Master Data</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            @can('read-items')
                                <li><a href="{{ route('item') }}">Items</a></li>
                            @endcan

                            @can('read-itemtypes')
                                <li><a href="{{ route('itemtype') }}">Item Types</a></li>
                            @endcan

                            @can('read-units')
                                <li><a href="{{ route('unit') }}">Units</a></li>
                            @endcan

                            @can('read-companies')
                                <li><a href="{{ route('company') }}">Companies</a></li>
                            @endcan

                            @can('read-projecttypes')
                                <li><a href="{{ route('projecttype') }}">Project Types</a></li>
                            @endcan
                        </ul>
                    </li>
                @endcanany

                {{-- Project Review --}}
                @can('read-projectreviews')
                    <li class="menu-title">Review</li>
                    <li class="{{ request()->routeIs('review') ? 'mm-active' : '' }}">
                        <a href="{{ route('review') }}" class="waves-effect">
                            <i class="dripicons-blog"></i>
                            <span>Project Review</span>
                        </a>
                    </li>
                @endcan

                {{-- Task Management --}}
                @canany(['read-tasks', 'read-project-timeline'])
                    <li class="menu-title">Task Management</li>
                    <li>
                        <a href="javascript:void(0);" class="has-arrow waves-effect">
                            <i class="dripicons-suitcase"></i>
                            <span>Task Data</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="false">
                            @can('read-tasks')
                                <li><a href="{{ route('tasks') }}">Tasks</a></li>
                            @endcan
                        </ul>
                    </li>

                    {{-- @can('read-project-timeline')
                        <li class="{{ request()->routeIs('timeline') ? 'mm-active' : '' }}">
                            <a href="{{ route('timeline') }}" class="waves-effect">
                                <i class="dripicons-blog"></i>
                                <span>Project Timeline</span>
                            </a>
                        </li>
                    @endcan --}}
                @endcanany

                {{-- Reporting --}}
                @canany(['read-report-project', 'read-reportvendors'])
                    <li class="menu-title">Reports</li>

                    @can('read-reportvendors')
                        <li class="{{ request()->routeIs('report') ? 'mm-active' : '' }}">
                            <a href="{{ route('report') }}" class="waves-effect">
                                <i class="dripicons-to-do"></i>
                                <span>Reports</span>
                            </a>
                        </li>
                    @endcan

                    @can('read-report-project')
                        <li>
                            <a href="#" data-bs-toggle="modal" data-bs-target="#ReportProjectModal" class="waves-effect">
                                <i class="dripicons-to-do"></i>
                                <span>Project Reports</span>
                            </a>
                        </li>
                    @endcan
                @endcanany

                {{-- System Settings --}}
                @canany(['read-users', 'read-roles', 'setting-aplication', 'read-logs'])
                    <li class="menu-title">System Settings</li>
                    <li>
                        <a href="javascript:void(0);" class="has-arrow waves-effect">
                            <i class="dripicons-gear"></i>
                            <span>Settings</span>
                        </a>
                        <ul class="sub-menu" aria-expanded="true">
                            @can('read-users')
                                <li><a href="{{ route('user') }}">Users</a></li>
                            @endcan

                            @can('read-roles')
                                <li><a href="{{ route('role') }}">Roles</a></li>
                            @endcan

                            @can('setting-aplication')
                                <li><a href="{{ route('aplication') }}">Application Settings</a></li>
                            @endcan

                            @can('read-logs')
                                <li><a href="{{ route('log') }}">Application Logs</a></li>
                            @endcan
                        </ul>
                    </li>
                @endcanany
            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
