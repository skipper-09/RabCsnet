@extends('layout.base')
@section('tittle', $tittle)

@push('css')
    <!-- DataTables CSS -->
    <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
@endpush
@section('content')
    <!-- start page title -->
    <div class="page-title-box">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <div class="page-title">
                        <h4>Dashboard</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Morvin</a></li>
                            {{-- <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li> --}}
                            <li class="breadcrumb-item active">Dashboard</li>
                        </ol>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="container-fluid">
        <div class="page-content-wrapper">
            <div class="row">
                
                <div class="col-md-12">
                    <div class="row">
                        <!-- Kartu: Total Proyek Belum Direview -->
                        <div class="col-xl-4 col-md-4">
                            <a href="{{ route('review') }}">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="text-center">
                                            <p class="font-size-16">Proyek Belum Direview</p>
                                            <div class="mini-stat-icon mx-auto mb-4 mt-3">
                                                <span class="avatar-title rounded-circle bg-soft-primary">
                                                    <i class="mdi mdi-alert-outline text-primary font-size-20"></i>
                                                </span>
                                            </div>
                                            <h5 class="font-size-22">{{ $totalProjectsNotReviewed }}</h5>
                                            <p class="text-muted">Proyek yang menunggu review</p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <!-- Kartu: Total Proyek Selesai -->
                        <div class="col-xl-4 col-md-4">
                            <a href="{{ route('project') }}">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="text-center">
                                            <p class="font-size-16">Proyek Selesai</p>
                                            <div class="mini-stat-icon mx-auto mb-4 mt-3">
                                                <span class="avatar-title rounded-circle bg-soft-success">
                                                    <i class="mdi mdi-check-circle-outline text-success font-size-20"></i>
                                                </span>
                                            </div>
                                            <h5 class="font-size-22">{{ $totalCompletedProjects }}</h5>
                                            <p class="text-muted">Proyek berhasil diselesaikan</p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>

                        <!-- Kartu: Proyek untuk Direview (Berdasarkan Role) -->
                        <div class="col-xl-4 col-md-4">
                            <a href="{{ route('review') }}">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="text-center">
                                            <p class="font-size-16">Proyek untuk Direview</p>
                                            <div class="mini-stat-icon mx-auto mb-4 mt-3">
                                                <span class="avatar-title rounded-circle bg-soft-warning">
                                                    <i class="mdi mdi-pencil-outline text-warning font-size-20"></i>
                                                </span>
                                            </div>
                                            <h5 class="font-size-22">{{ $projectsToReview }}</h5>
                                            <p class="text-muted">Proyek yang ditugaskan untuk direview</p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title mb-4 float-sm-start">Project Summary</h4>
                            <div class="clearfix"></div>
                            <table id="datatable" class="table table-responsive  table-hover" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Project</th>
                                        <th>Perusahaan</th>
                                        <th>Status</th>
                                        <th>Penanggung Jawab</th>
                                        <th>Tanggal Selesai</th>
                                        <th>Status</th>
                                        <th>Progress</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- <div class="row">

                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-body">

                            <h4 class="header-title mb-4">Product Traking</h4>


                            <ul class="list-unstyled activity-wid mb-0">

                                <li class="activity-list activity-border">
                                    <div class="activity-icon avatar-sm">

                                        <img src="assets/images/users/avatar-7.jpg" class="avatar-sm rounded-circle"
                                            alt="">

                                    </div>
                                    <div class="media">
                                        <div class="me-3">
                                            <h5 class="font-size-15 mb-1">Your Manager Posted</h5>
                                            <p class="text-muted font-size-14 mb-0">James Raphael</p>
                                        </div>

                                        <div class="media-body">
                                            <div class="text-end d-none d-md-block">
                                                <p class="text-muted font-size-13 mt-2 pt-1 mb-0"><i
                                                        class="mdi mdi-timer-outline font-size-15 text-primary"></i>
                                                    3 days</p>
                                            </div>
                                        </div>

                                    </div>
                                </li>

                                <li class="activity-list activity-border">
                                    <div class="activity-icon avatar-sm">
                                        <span class="avatar-title bg-soft-primary text-primary rounded-circle">
                                            <i class="ti-shopping-cart font-size-16"></i>
                                        </span>
                                    </div>
                                    <div class="media">
                                        <div class="me-3">
                                            <h5 class="font-size-15 mb-1">You have 5 pending order.</h5>
                                            <p class="text-muted font-size-14 mb-0">America</p>
                                        </div>

                                        <div class="media-body">
                                            <div class="text-end d-none d-md-block">
                                                <p class="text-muted font-size-13 mt-2 pt-1 mb-0"><i
                                                        class="mdi mdi-timer-outline font-size-15 text-primary"></i>
                                                    1 days</p>
                                            </div>
                                        </div>


                                    </div>
                                </li>

                                <li class="activity-list activity-border">
                                    <div class="activity-icon avatar-sm">
                                        <span class="avatar-title bg-soft-success text-success rounded-circle">
                                            <i class="ti-user font-size-16"></i>
                                        </span>
                                    </div>
                                    <div class="media">
                                        <div class="me-3">
                                            <h5 class="font-size-15 mb-1">New Order Received</h5>
                                            <p class="text-muted font-size-14 mb-0">Thank You</p>
                                        </div>

                                        <div class="media-body">
                                            <div class="text-end d-none d-md-block">
                                                <p class="text-muted font-size-13 mt-2 pt-1 mb-0"><i
                                                        class="mdi mdi-timer-outline font-size-15 text-primary"></i>
                                                    Today</p>
                                            </div>
                                        </div>
                                    </div>
                                </li>

                                <li class="activity-list activity-border">
                                    <div class="activity-icon avatar-sm">

                                        <img src="assets/images/users/avatar-7.jpg" class="avatar-sm rounded-circle"
                                            alt="">

                                    </div>
                                    <div class="media">
                                        <div class="me-3">
                                            <h5 class="font-size-15 mb-1">Your Manager Posted</h5>
                                            <p class="text-muted font-size-14 mb-0">James Raphael</p>
                                        </div>

                                        <div class="media-body">
                                            <div class="text-end d-none d-md-block">
                                                <p class="text-muted font-size-13 mt-2 pt-1 mb-0"><i
                                                        class="mdi mdi-timer-outline font-size-15 text-primary"></i>
                                                    3 days</p>
                                            </div>
                                        </div>

                                    </div>
                                </li>

                                <li class="activity-list activity-border">
                                    <div class="activity-icon avatar-sm">
                                        <span class="avatar-title bg-soft-primary text-primary rounded-circle">
                                            <i class="ti-shopping-cart font-size-16"></i>
                                        </span>
                                    </div>
                                    <div class="media">
                                        <div class="me-3">
                                            <h5 class="font-size-15 mb-1">You have 1 pending order.</h5>
                                            <p class="text-muted font-size-14 mb-0">Dubai</p>
                                        </div>

                                        <div class="media-body">
                                            <div class="text-end d-none d-md-block">
                                                <p class="text-muted font-size-13 mt-2 pt-1 mb-0"><i
                                                        class="mdi mdi-timer-outline font-size-15 text-primary"></i>
                                                    1 days</p>
                                            </div>
                                        </div>

                                    </div>
                                </li>

                                <li class="activity-list">
                                    <div class="activity-icon avatar-sm">
                                        <span class="avatar-title bg-soft-success text-success rounded-circle">
                                            <i class="ti-user font-size-16"></i>
                                        </span>
                                    </div>
                                    <div class="media">
                                        <div class="me-3">
                                            <h5 class="font-size-15 mb-1">New Order Received</h5>
                                            <p class="text-muted font-size-14 mb-0">Thank You</p>
                                        </div>

                                        <div class="media-body">
                                            <div class="text-end d-none d-md-block">
                                                <p class="text-muted font-size-13 mt-2 pt-1 mb-0"><i
                                                        class="mdi mdi-timer-outline font-size-15 text-primary"></i>
                                                    Today</p>
                                            </div>
                                        </div>
                                    </div>
                                </li>


                            </ul>

                        </div>
                    </div>
                </div>


                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title mb-4">Earning Goal</h4>

                            <div class="mt-2 text-center">


                                <div class="row">
                                    <div class="col-md-6">

                                        <div class="mt-4 mt-sm-0">


                                            <div id="list-chart-1" class="apex-charts" dir="ltr"></div>
                                            <p class="text-muted mb-2 mt-2 pt-1">Total Earning:</p>
                                            <h5 class="font-size-18 mb-1">USD 13,545.65</h5>
                                        </div>
                                    </div>



                                    <div class="col-md-6 dash-goal">

                                        <div class="mt-4 mt-sm-0">

                                            <div id="list-chart-2" class="apex-charts" dir="ltr"></div>

                                            <p class="text-muted mb-2 mt-2 pt-1">Earning Goal:</p>
                                            <h5 class="font-size-18 mb-1">USD 84,265.45</h5>


                                        </div>


                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>

                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title mb-4">Best Selling Product</h4>



                            <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
                                <div class="carousel-indicators">
                                    <button type="button" data-bs-target="#carouselExampleIndicators"
                                        data-bs-slide-to="0" class="active" aria-current="true"
                                        aria-label="Slide 1"></button>
                                    <button type="button" data-bs-target="#carouselExampleIndicators"
                                        data-bs-slide-to="1" aria-label="Slide 2"></button>
                                    <button type="button" data-bs-target="#carouselExampleIndicators"
                                        data-bs-slide-to="2" aria-label="Slide 3"></button>
                                </div>
                                <div class="carousel-inner">

                                    <div class="carousel-item active">
                                        <div class="row align-items-center mb-5">
                                            <div class="col-md-4">
                                                <img src="assets/images/product/img-3.png" class="img-fluid me-3"
                                                    alt="">
                                            </div>
                                            <div class="col-md-7 offset-md-1">

                                                <div class="mt-4 mt-sm-0">
                                                    <p class="text-muted mb-2">Headphone</p>

                                                    <h5 class="text-primary">Blue Headphone</h5>



                                                    <div class="row no-gutters mt-4">

                                                        <div class="col-4">

                                                            <div class="mt-1">
                                                                <h4>1200</h4>
                                                                <p class="text-muted mb-1">Sold</p>
                                                            </div>

                                                        </div>
                                                        <div class="col-4">

                                                            <div class="mt-1">
                                                                <h4>450</h4>
                                                                <p class="text-muted mb-1">Stock</p>
                                                            </div>


                                                        </div>

                                                        <div class="col-4">
                                                            <div class="mt-4 pt-1">
                                                                <a href="" class="btn btn-primary btn-sm">Buy
                                                                    Now</a>
                                                            </div>
                                                        </div>


                                                    </div>
                                                </div>


                                            </div>
                                        </div>
                                    </div>

                                    <div class="carousel-item">
                                        <div class="row align-items-center mb-5">
                                            <div class="col-md-4">
                                                <img src="assets/images/product/img-5.png" class="img-fluid me-3"
                                                    alt="">
                                            </div>
                                            <div class="col-md-7 offset-md-1">

                                                <div class="mt-4 mt-sm-0">
                                                    <p class="text-muted mb-2">T-shirt</p>

                                                    <h5 class="text-primary">Blue T-shirt</h5>



                                                    <div class="row no-gutters mt-4">

                                                        <div class="col-4">

                                                            <div class="mt-1">
                                                                <h4>800</h4>
                                                                <p class="text-muted mb-1">Sold</p>
                                                            </div>

                                                        </div>
                                                        <div class="col-4">

                                                            <div class="mt-1">
                                                                <h4>250</h4>
                                                                <p class="text-muted mb-1">Stock</p>
                                                            </div>


                                                        </div>

                                                        <div class="col-4">
                                                            <div class="mt-4 pt-1">
                                                                <a href="" class="btn btn-primary btn-sm">Buy
                                                                    Now</a>
                                                            </div>
                                                        </div>


                                                    </div>
                                                </div>


                                            </div>
                                        </div>
                                    </div>



                                    <div class="carousel-item">
                                        <div class="row align-items-center mb-5">
                                            <div class="col-md-4">
                                                <img src="assets/images/product/img-1.png" class="img-fluid me-3"
                                                    alt="">
                                            </div>
                                            <div class="col-md-7 offset-md-1">

                                                <div class="mt-4 mt-sm-0">
                                                    <p class="text-muted mb-2">Sonic</p>

                                                    <h5 class="text-primary">Alarm clock</h5>



                                                    <div class="row no-gutters mt-4">

                                                        <div class="col-4">

                                                            <div class="mt-1">
                                                                <h4>600</h4>
                                                                <p class="text-muted mb-1">Sold</p>
                                                            </div>

                                                        </div>
                                                        <div class="col-4">

                                                            <div class="mt-1">
                                                                <h4>150</h4>
                                                                <p class="text-muted mb-1">Stock</p>
                                                            </div>


                                                        </div>

                                                        <div class="col-4">
                                                            <div class="mt-4 pt-1">
                                                                <a href="" class="btn btn-primary btn-sm">Buy
                                                                    Now</a>
                                                            </div>
                                                        </div>


                                                    </div>
                                                </div>


                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>







                </div>


                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-body">

                            <h4 class="header-title mb-4">Sales by State</h4>

                            <div id="world-map-markers" style="height: 230px"></div>

                            <div class="px-4 py-3 mt-4">
                                <p class="mb-1">USA <span class="float-right">75%</span></p>
                                <div class="progress mt-2" style="height: 6px;">
                                    <div class="progress-bar progress-bar-striped bg-primary" role="progressbar"
                                        style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="75">
                                    </div>
                                </div>

                                <p class="mt-3 mb-1">Russia <span class="float-right">55%</span></p>
                                <div class="progress mt-2" style="height: 6px;">
                                    <div class="progress-bar progress-bar-striped bg-primary" role="progressbar"
                                        style="width: 55%" aria-valuenow="55" aria-valuemin="0" aria-valuemax="55">
                                    </div>
                                </div>

                                <p class="mt-3 mb-1">Australia <span class="float-right">85%</span></p>
                                <div class="progress mt-2" style="height: 6px;">
                                    <div class="progress-bar progress-bar-striped bg-primary" role="progressbar"
                                        style="width: 85%" aria-valuenow="85" aria-valuemin="0" aria-valuemax="85">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-xl-4">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title mb-4">Sales By Social Source
                            </h4>
                            <ul class="inbox-wid list-unstyled mb-0">
                                <li class="inbox-list-item">
                                    <a href="#">
                                        <div class="media">
                                            <div class="me-3 align-self-center">
                                                <div class="avatar-sm rounded bg-primary align-self-center">
                                                    <span class="avatar-title">
                                                        <i class="ti-facebook text-white font-size-18"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="media-body overflow-hidden mt-1">
                                                <h5 class="font-size-15 mb-1">Facebook Ads</h5>
                                                <p class="text-muted mb-0">3.2k Sale - 4.2k Like</p>
                                            </div>
                                            <p class="ms-2 pt-3">
                                                <i class="mdi mdi-arrow-top-right text-success me-1"></i>50%
                                            </p>
                                        </div>
                                    </a>
                                </li>
                                <li class="inbox-list-item">
                                    <a href="#">
                                        <div class="media">
                                            <div class="me-3 align-self-center">
                                                <div class="avatar-sm rounded bg-info align-self-center">
                                                    <span class="avatar-title">
                                                        <i class="ti-twitter-alt text-white font-size-18"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="media-body overflow-hidden mt-1">
                                                <h5 class="font-size-15 mb-1">Twitter Ads</h5>
                                                <p class="text-muted mb-0">3.1k Sale - 3.7k Like</p>
                                            </div>
                                            <p class="ms-2 pt-3">
                                                <i class="mdi mdi-arrow-top-right text-success me-1"></i>45%
                                            </p>
                                        </div>
                                    </a>
                                </li>
                                <li class="inbox-list-item">
                                    <a href="#">
                                        <div class="media">
                                            <div class="me-3 align-self-center">
                                                <div class="avatar-sm rounded bg-danger align-self-center">
                                                    <span class="avatar-title">
                                                        <i class="ti-linkedin text-white font-size-18"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="media-body overflow-hidden mt-1">
                                                <h5 class="font-size-15 mb-1">linkedin Ads</h5>
                                                <p class="text-muted mb-0">4.3k Sale - 4.3k Like</p>
                                            </div>
                                            <p class="ms-2 pt-3">
                                                <i class="mdi mdi-arrow-bottom-right text-danger me-1"></i>30%
                                            </p>
                                        </div>
                                    </a>
                                </li>


                                <li class="inbox-list-item">
                                    <a href="#">
                                        <div class="media">
                                            <div class="me-3 align-self-center">
                                                <div class="avatar-sm rounded bg-danger align-self-center">
                                                    <span class="avatar-title">
                                                        <i class="ti-youtube text-white font-size-18"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="media-body overflow-hidden mt-1">
                                                <h5 class="font-size-15 mb-1">Youtube Ads</h5>
                                                <p class="text-muted mb-0">4.2k Sale - 3.7k Like</p>
                                            </div>
                                            <p class="ms-2 pt-3">
                                                <i class="mdi mdi-arrow-top-right text-success me-1"></i>35%
                                            </p>
                                        </div>
                                    </a>
                                </li>

                                <li class="inbox-list-item">
                                    <a href="#" class="pb-0">
                                        <div class="media">
                                            <div class="me-3 align-self-center">
                                                <div class="avatar-sm rounded bg-dark align-self-center">
                                                    <span class="avatar-title">
                                                        <i class="ti-github text-white font-size-18"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="media-body overflow-hidden mt-1">
                                                <h5 class="font-size-15 mb-1">GitHub Ads</h5>
                                                <p class="text-muted mb-2">4.9k Sale - 4.1k Like</p>
                                            </div>
                                            <p class="ms-2 pt-3">
                                                <i class="mdi mdi-arrow-top-right text-success me-1"></i>40%
                                            </p>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-xl-8">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title mb-4">Products of the Month</h4>
                            <div class="table-responsive">
                                <table class="table table-centered table-nowrap mb-0">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>ID</th>
                                            <th>Product</th>
                                            <th>Customer</th>
                                            <th>Price</th>
                                            <th>Invoice</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>#2356</td>
                                            <td><img src="assets/images/product/img-7.png" width="42" class="me-3"
                                                    alt="">Green Chair</td>
                                            <td>Kenneth Gittens</td>
                                            <td>$200.00</td>
                                            <td>42</td>
                                            <td><span
                                                    class="badge badge-pill badge-soft-primary font-size-13">Pending</span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>#2564</td>
                                            <td><img src="assets/images/product/img-8.png" width="42" class="me-3"
                                                    alt="">Office Chair</td>
                                            <td>Alfred Gordon</td>
                                            <td>$242.00</td>
                                            <td>54</td>
                                            <td><span
                                                    class="badge badge-pill badge-soft-success font-size-13">Active</span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>#2125</td>
                                            <td><img src="assets/images/product/img-10.png" width="42" class="me-3"
                                                    alt="">Gray Chair</td>
                                            <td>Keena Reyes</td>
                                            <td>$320.00</td>
                                            <td>65</td>
                                            <td><span
                                                    class="badge badge-pill badge-soft-success font-size-13">Active</span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>#8587</td>
                                            <td><img src="assets/images/product/img-11.png" width="42" class="me-3"
                                                    alt="">Steel Chair</td>
                                            <td>Timothy Zuniga</td>
                                            <td>$342.00</td>
                                            <td>52</td>
                                            <td><span
                                                    class="badge badge-pill badge-soft-primary font-size-13">Pending</span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>#2354</td>
                                            <td><img src="assets/images/product/img-12.png" width="42" class="me-3"
                                                    alt="">Home Chair</td>
                                            <td>Joann Wiliams</td>
                                            <td>$320.00</td>
                                            <td>25</td>
                                            <td><span
                                                    class="badge badge-pill badge-soft-primary font-size-13">Pending</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <!-- end table-responsive -->
                        </div>
                    </div>
                </div>
            </div> --}}
        </div>
    </div> <!-- container-fluid -->


    @push('js')
    <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
        <script>
             $(document).ready(function() {
                // Initialize DataTable
                $("#datatable").DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '{{ route('project.getdata') }}',
                    columns: [
                        {
                            data: 'DT_RowIndex',
                            orderable: false,
                            searchable: false,
                            class: 'text-center',
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'company',
                            name: 'company'
                        },
                        {
                            data: 'status',
                            name: 'status'
                        },
                        {
                            data: 'status_pengajuan',
                            name: 'status_pengajuan'
                        },
                        {
                            data: 'review',
                            name: 'review'
                        }
                    ],
                });
                $(".dataTables_length select").addClass("form-select form-select-sm");
            });
        </script>
    @endpush
@endsection
