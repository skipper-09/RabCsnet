<!doctype html>
<html lang="en">

<head>


    <meta charset="utf-8" />
    <title>Dashboard | Morvin - Admin & Dashboard Template</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesdesign" name="author" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">

    @stack('css')
    @include('layout.partials.css')

</head>


<body>

    <!-- Begin page -->
    <div id="layout-wrapper">

        {{-- header --}}
        @include('layout.partials.header')

        <!-- ========== Left Sidebar Start ========== -->
        @include('layout.partials.sidebar')
        <!-- Left Sidebar End -->

        <!-- ============================================================== -->
        <!-- Start right Content here -->
        <!-- ============================================================== -->
        <div class="main-content">

            <div class="page-content">

               @yield('content')
            </div>
            <!-- End Page-content -->

           {{-- footer --}}
           @include('layout.partials.footer')
        </div>
        <!-- end main content-->

    </div>
    <!-- END layout-wrapper -->

    <!-- Right Sidebar -->
   @include('layout.partials.rightsidebar')
    <!-- /Right-bar -->

    <!-- Right bar overlay-->
    <div class="rightbar-overlay"></div>

    @include('layout.partials.js')
    @stack('js')

</body>

</html>