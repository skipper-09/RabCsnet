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
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="header-title mb-4 float-sm-start">Progress Project</h4>
                            <div class="clearfix"></div>
                            <div class="row align-items-center">
                                <div class="col-12 table-responsive">
                                    <table id="datatable" class="table  table-hover" style="width: 100%;">
                                        <thead>
                                            <tr>
                                                <th>Nama Projek</th>
                                                <th>Perusahaan</th>
                                                <th>Status</th>
                                                <th>Penanggung Jawab</th>
                                                <th>Tanggal Selesai</th>
                                                <th>Progress</th>
                                            </tr>
                                        </thead>
                                        {{-- <tbody>
                                        @foreach ($project as $item)
                                        <tr>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->company->name }}</td>
                                            <td>{{ $item->status }}</td>
                                            <td>{{ $item->responsible_person }}</td>
                                            <td>{{ $item->end_date }}</td>
                                            <td>{{ $item->end_date }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody> --}}
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-4 col-md-4">

                    <div class="card">
                        <div class="card-body">
                            <div class="text-left">
                                <p class="font-size-16 fw-bold">Overall Progress</p>
                                {{-- <div id="list-chart-2" class="apex-charts" dir="ltr"></div> --}}
                                <div class="text-center" dir="ltr">
                                    <input data-plugin="knob" data-width="120" data-height="120" data-linecap=round
                                        data-fgColor="#50a5f1" value="{{ $projectprogress }}" data-skin="tron"
                                        data-readOnly=true />
                                </div>
                                <div class="row no-gutters mt-4">
                                    <div class="col-3">
                                        <div class="mt-1 fw-bold">
                                            <h4>{{ $projectall }}</h4>
                                            <p class="text-muted mb-1">Total Project</p>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="mt-1">
                                            <h4 class="text-success">{{ $projeccomplate }}</h4>
                                            <p class="text-success mb-1">Complate</p>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="mt-1">
                                            <h4 class="text-primary">{{ $projectinprogres }}</h4>
                                            <p class="text-primary mb-1">In Progress</p>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="mt-1">
                                            <h4 class="text-info">{{ $projectpending }}</h4>
                                            <p class="text-info mb-1">Pending</p>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div> <!-- container-fluid -->


    @push('js')
        <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('assets/js/pages/jquery-knob.init.js') }}"></script>

        <script>
            $(document).ready(function() {

                // Initialize DataTable
                $("#datatable").DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '{{ route('dashboard.data') }}',
                    columns: [
                        // {
                        //     data: 'DT_RowIndex',
                        //     orderable: false,
                        //     searchable: false,
                        //     class: 'text-center',
                        // },
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
                            data: 'responsible_person',
                            name: 'responsible_person'
                        },
                        {
                            data: 'end_date',
                            name: 'end_date'
                        },
                        {
                            data: 'progress',
                            name: 'progress'
                        },

                    ],
                    drawCallback: function() {
                        $('[data-plugin="knob"]').knob();
                    }
                });
                $(".dataTables_length select").addClass("form-select form-select-sm");
                $('[data-plugin="knob"]').knob();
            });
        </script>
    @endpush
@endsection
