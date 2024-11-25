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

                <li>
                    <a href="{{ route('dashboard') }}" class="waves-effect">
                        <i class="dripicons-home"></i>
                        {{-- <span
                            class="badge rounded-pill bg-info float-end">3</span> --}}
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="{{ request()->is('admin/project') ? 'mm-activate' : '' }}">
                    <a href="{{ route('project') }}" class=" waves-effect">
                        <i class="dripicons-calendar"></i>
                        <span>Project</span>
                    </a>
                </li>


                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="dripicons-browser"></i>
                        <span>Vendor Data</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('vendor') }}">Vendor</a></li>
                        <li><a href="{{ route('payment') }}">Payment Vendor</a></li>
                    </ul>
                </li>


                <li class="menu-title">Data Master</li>
                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="dripicons-suitcase"></i>
                        <span>Master Data</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="false">
                        <li><a href="{{ route('item') }}">Items</a></li>
                        <li><a href="{{ route('itemtype') }}">Items Type</a></li>
                        <li><a href="{{ route('unit') }}">Unit/Satuan</a></li>
                        <li><a href="{{ route('company') }}">Company</a></li>
                        <li><a href="{{ route('projecttype') }}">Tipe Project</a></li>
                    </ul>
                </li>

                <li class="menu-title">Laporan</li>
                <li class="{{ request()->is('admin/report') ? 'mm-activate' : '' }}">
                    <a href="{{ route('report') }}" class=" waves-effect">
                        <i class="dripicons-to-do"></i>
                        <span>Report</span>
                    </a>
                </li>


                <li class="menu-title">SETTING</li>

                <li>
                    <a href="javascript: void(0);" class="has-arrow waves-effect">
                        <i class="dripicons-checklist"></i>
                        <span>Setting</span>
                    </a>
                    <ul class="sub-menu" aria-expanded="true">
                        <li><a href="{{ route('user') }}">User</a></li>
                        <li><a href="{{ route('role') }}">Role</a></li>
                        <li><a href="{{ route('aplication') }}">Setting Aplikasi</a></li>
                        <li><a href="{{ route('log') }}">Log Aplikasi</a></li>

                    </ul>
                </li>

            </ul>
        </div>
        <!-- Sidebar -->
    </div>
</div>
