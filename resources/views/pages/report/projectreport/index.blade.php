@extends('layout.base')

@section('tittle', $tittle)

@push('css')
    <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
    <style>
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        .image-thumbnail {
            max-width: 100px;
            height: auto;
            border-radius: 4px;
        }

        .action-buttons {
            white-space: nowrap;
        }

        /* timeline style */
        .fc-license-message {
            display: none;
        }

        .task {
            font-weight: bold;
            font-size: 16px
        }
    </style>
@endpush

@section('content')
    <div class="page-title-box">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <div class="page-title">
                        <h4>{{ $tittle }}</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Report</li>
                            <li class="breadcrumb-item active">{{ $tittle }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="container-fluid">
        <div class="page-content-wrapper">
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body">
                            <!-- Nav tabs -->
                            <ul class="nav nav-tabs nav-tabs-custom nav-justified" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" data-bs-toggle="tab" href="#home1" role="tab">
                                        <span class="d-block d-sm-none"><i class="fas fa-home"></i></span>
                                        <span class="d-none d-sm-block">Overview</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#distribusi" role="tab">
                                        <span class="d-block d-sm-none"><i class="far fa-user"></i></span>
                                        <span class="d-none d-sm-block">Distribusi</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#messages1" role="tab">
                                        <span class="d-block d-sm-none"><i class="far fa-envelope"></i></span>
                                        <span class="d-none d-sm-block">Task List</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#gantchart" role="tab">
                                        <span class="d-block d-sm-none"><i class="fas fa-cog"></i></span>
                                        <span class="d-none d-sm-block">Gant Chart</span>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#payvendor" role="tab">
                                        <span class="d-block d-sm-none"><i class="fas fa-cog"></i></span>
                                        <span class="d-none d-sm-block">Payment Vendor</span>
                                    </a>
                                </li>
                            </ul>

                            <!-- Tab panes -->
                            <div class="tab-content p-3 text-muted">
                                <div class="tab-pane active" id="home1" role="tabpanel">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="col-md-12 mb-3">
                                                        <h4 class="header-title fw-bold">Progress Proyek</h4>

                                                        <div class="text-center mb-4">
                                                            <input data-plugin="knob" data-width="250" data-height="250"
                                                                data-linecap="round" data-fgColor="#34c38f"
                                                                value="{{ $progres }}" data-skin="tron"
                                                                data-readOnly="true" />
                                                        </div>
                                                        <div class="mt-3">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <p><strong>Project Start:</strong>
                                                                        {{ formatDate($project->start_date) ?? 'Belum diset' }}
                                                                    </p>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <p><strong>Project End:</strong>
                                                                        {{ formatDate($project->end_date) ?? 'Belum diset' }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                            <div class="mt-2">
                                                                <p><strong>Remaining Days:</strong> <span
                                                                        class="text-danger">{{ $remainingdays }}
                                                                        days</span></p>
                                                            </div>
                                                        </div>
                                                    </div>


                                                </div>
                                                <div class="col-md-6">
                                                    <h4 class="header-title fw-bold">Progress Tasks</h4>
                                                    <canvas id="taskStatusChart" width="300" height="300"></canvas>

                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div id="projectFileDetails" class="mb-3">
                                                        <h6 class="text-primary mb-3">
                                                            <i class="mdi mdi-file-document-outline me-2"></i>Project
                                                            Files
                                                        </h6>
                                                        <div class="alert alert-soft-primary">
                                                            @if ($project->Projectfile)
                                                                <ul class="list-unstyled mb-0">
                                                                    <li class="mb-2">
                                                                        <i
                                                                            class="mdi mdi-file-excel text-success me-2"></i>
                                                                        <strong>Excel File:</strong>
                                                                        <a class="btn btn-primary btn-sm"
                                                                            href="{{ asset("
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                storage/files/excel/{$project->Projectfile->excel}") }}">Download</a>
                                                                    </li>

                                                                    <li class="mb-2">
                                                                        <i class="mdi mdi-map text-danger me-2"></i>
                                                                        <strong>KMZ File:</strong>
                                                                        <a class="btn btn-primary btn-sm"
                                                                            href="{{ asset("
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                storage/files/kmz/{$project->Projectfile->kmz}") }}">Download</a>
                                                                    </li>
                                                                </ul>
                                                            @else
                                                                No files available
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div id="projectInfo" class="mb-3">
                                                        <h6 class="text-primary mb-3">
                                                            <i class="mdi mdi-chart-line me-2"></i>Project Information
                                                        </h6>
                                                        <div class="alert alert-soft-success">
                                                            @if ($project)
                                                                <ul class="list-unstyled mb-0">
                                                                    <li class="mb-2">
                                                                        <i class="mdi mdi-account text-success me-2"></i>
                                                                        <strong>Vendor:</strong>
                                                                        {{ $project->vendor->name ?? 'Belum Dipilih' }}
                                                                    </li>
                                                                    <li class="mb-2">
                                                                        <i class="mdi mdi-cash text-primary me-2"></i>
                                                                        <strong>Project Amount:</strong>
                                                                        {{ formatRupiah($project->amount) }}
                                                                    </li>
                                                                    <li class="mb-2">
                                                                        <i class="mdi mdi-calendar text-primary me-2"></i>
                                                                        <strong>Project Start:</strong>
                                                                        {{ formatDate($project->start_date) ?? 'Belum diset' }}
                                                                    </li>
                                                                    <li class="mb-2">
                                                                        <i class="mdi mdi-calendar text-primary me-2"></i>
                                                                        <strong>Project End:</strong>
                                                                        {{ formatDate($project->end_date) ?? 'Belum diset' }}
                                                                    </li>
                                                                </ul>
                                                            @else
                                                                No information available
                                                            @endif

                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-12">
                                                    <div id="projectSummaryDetails" class="mb-3">
                                                        <h6 class="text-primary mb-3">
                                                            <i class="mdi mdi-chart-line me-2"></i>Project Summary
                                                        </h6>
                                                        <div class="alert alert-soft-success">
                                                            @if ($project->summary)
                                                                <div
                                                                    class="d-flex justify-content-between align-items-center">
                                                                    <span>
                                                                        <i class="mdi mdi-cash me-2 text-success"></i>
                                                                        <strong>Total Summary:</strong>
                                                                    </span>
                                                                    <span
                                                                        class="h5 mb-0 text-primary">{{ formatRupiah($project->summary->total_summary) }}</span>
                                                                </div>
                                                            @else
                                                                No summary available
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                                <div class="tab-pane" id="distribusi" role="tabpanel">
                                    <h5>Distribusi Project</h5>
                                    <div class="table-responsive">
                                        <table id="datatabledistribusi" class="table table-hover" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th class="text-center" style="width: 5%">No</th>
                                                    <th>Project</th>
                                                    <th>Tipe</th>
                                                    <th>Code</th>
                                                    <th>Name</th>
                                                    <th>Deskripsi</th>
                                                    <th>Item</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane" id="messages1" role="tabpanel">
                                    <div class="card-title d-flex justify-content-between align-items-center mb-2">
                                        <ul class="nav nav-pills gap-2 mb-3" id="task-view-tabs" role="tablist">
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link active" id="list-tab" data-bs-toggle="pill"
                                                    data-bs-target="#list-view" type="button" role="tab">
                                                    List Tasks
                                                </button>
                                            </li>
                                            <li class="nav-item" role="presentation">
                                                <button class="nav-link" id="kanban-tab" data-bs-toggle="pill"
                                                    data-bs-target="#kanban-view" type="button" role="tab">
                                                    Kanban
                                                </button>
                                            </li>
                                        </ul>
                                        @can('create-tasks')
                                            <div class="mb-3">
                                                <a href="{{ route('tasks.add') }}" class="btn btn-primary btn-sm">Tambah
                                                    {{ $tittle }}</a>
                                            </div>
                                        @endcan
                                    </div>
                                    <div class="tab-content" id="task-view-content">
                                        <!-- List View Tab -->
                                        <div class="tab-pane fade show active" id="list-view" role="tabpanel">
                                            <div class="table-responsive">
                                                <table id="datatask" class="table table-hover" style="width: 100%;">
                                                    <thead>
                                                        <tr>
                                                            <th class="text-center" style="width: 5%">No</th>
                                                            <th style="width: 15%">Judul</th>
                                                            <th style="width: 15%">Project</th>
                                                            <th style="width: 15%">Vendor</th>
                                                            <th style="width: 10%">Tanggal Mulai</th>
                                                            <th style="width: 10%">Tanggal Selesai</th>
                                                            <th style="width: 10%">Status</th>
                                                            <th style="width: 10%">Prioritas</th>
                                                            @canany(['complete-tasks', 'update-tasks', 'delete-tasks'])
                                                                <th style="width: 10%" class="text-center">Action</th>
                                                            @endcanany
                                                        </tr>
                                                    </thead>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="tab-pane fade" id="kanban-view" role="tabpanel">
                                            <div class="row">
                                                @foreach ($statuses as $statusKey => $statusLabel)
                                                    <div class="col-md-4">
                                                        <div class="card border-1">
                                                            <div class="card-body">
                                                                {{ $statusLabel }} <!-- Human-readable status label -->
                                                            </div>
                                                            <div class="card-footer kanban-column"
                                                                data-status="{{ $statusKey }}">
                                                                @forelse ($kanbanTasks->get($statusKey, collect()) as $task)
                                                                    <div class="card mb-2 task-card"
                                                                        data-task-id="{{ $task->id }}">
                                                                        <div class="card-body">
                                                                            <h6 class="card-title"><a
                                                                                    href="{{ route('tasks.details', ['id' => $task->id]) }}">
                                                                                    {{ $task->title }}
                                                                                </a></h6>
                                                                            <p class="card-text small">
                                                                                Proyek: {{ $task->project->name ?? 'N/A' }}
                                                                            </p>
                                                                            <div
                                                                                class="d-flex justify-content-between align-items-center">
                                                                                <span
                                                                                    class="badge 
                                                                                @switch($task->priority)
                                                                                    @case('low') bg-info @break
                                                                                    @case('medium') bg-warning @break
                                                                                    @case('high') bg-danger @break
                                                                                @endswitch
                                                                            ">
                                                                                    {{ ucfirst($task->priority) }}
                                                                                </span>
                                                                                <small>{{ $task->start_date }} -
                                                                                    {{ $task->end_date }}</small>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @empty
                                                                    <div></div>
                                                                @endforelse
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane" id="gantchart" role="tabpanel">
                                    <div class="row">
                                        <div class="col-xl-12">
                                            <div class="card mb-0">
                                                <div class="card-body">
                                                    <h5 class="mb-3">Timeline Project</h5>
                                                    <div id="calendar"></div>
                                                </div>
                                            </div>
                                        </div> <!-- end col -->
                                    </div>
                                </div>
                                <div class="tab-pane" id="payvendor" role="tabpanel">
                                    <div class="table-responsive">
                                        <table id="datapayment" class="table table-hover" style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th class="text-center" style="width: 5%">No</th>
                                                    <th style="width: 15%">Bukti Pembayaran</th>
                                                    <th style="width: 15%">Tanggal Pembayaran</th>
                                                    {{-- <th style="width: 15%">Project</th> --}}
                                                    <th style="width: 15%">Vendor</th>
                                                    <th style="width: 15%">Amount</th>
                                                    <th>Note</th>
                                                    @canany(['update-paymentvendors', 'delete-paymentvendors'])
                                                        <th class="text-center" style="width: 10%">Action</th>
                                                    @endcanany
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- <div class="col-lg-12">
                    <div class="">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card border-primary">
                                        <div
                                            class="card-header bg-primary text-white d-flex justify-content-between align-items-center align-content-center">
                                            <h5 class="card-title mb-0">
                                                <i class="mdi mdi-information-outline me-2"></i>Project Details
                                            </h5>
                                            <span class="h6 text-white text-uppercase">{{ $project->name . ' - ' .
                                                $project->code }}</span>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div id="projectFileDetails" class="mb-3">
                                                        <h6 class="text-primary mb-3">
                                                            <i class="mdi mdi-file-document-outline me-2"></i>Project
                                                            Files
                                                        </h6>
                                                        <div class="alert alert-soft-primary">
                                                            @if ($project->Projectfile)
                                                            <ul class="list-unstyled mb-0">
                                                                <li class="mb-2">
                                                                    <i class="mdi mdi-file-excel text-success me-2"></i>
                                                                    <strong>Excel File:</strong>
                                                                    <a class="btn btn-primary btn-sm" href="{{ asset("
                                                                        storage/files/excel/{$project->Projectfile->excel}")}}">Download</a>
                                                                </li>

                                                                <li class="mb-2">
                                                                    <i class="mdi mdi-map text-danger me-2"></i>
                                                                    <strong>KMZ File:</strong>
                                                                    <a class="btn btn-primary btn-sm" href="{{ asset("
                                                                        storage/files/kmz/{$project->Projectfile->kmz}")}}">Download</a>
                                                                </li>
                                                            </ul>
                                                            @else
                                                            No files available
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div id="projectInfo" class="mb-3">
                                                        <h6 class="text-primary mb-3">
                                                            <i class="mdi mdi-chart-line me-2"></i>Project Information
                                                        </h6>
                                                        <div class="alert alert-soft-success">
                                                            @if ($project)
                                                            <ul class="list-unstyled mb-0">
                                                                <li class="mb-2">
                                                                    <i class="mdi mdi-account text-success me-2"></i>
                                                                    <strong>Vendor:</strong>
                                                                    {{ $project->vendor->name ?? 'Belum Dipilih' }}
                                                                </li>
                                                                <li class="mb-2">
                                                                    <i class="mdi mdi-cash text-primary me-2"></i>
                                                                    <strong>Project Amount:</strong>
                                                                    {{ formatRupiah($project->amount) }}
                                                                </li>
                                                                <li class="mb-2">
                                                                    <i class="mdi mdi-calendar text-primary me-2"></i>
                                                                    <strong>Project Start:</strong>
                                                                    {{ formatDate($project->start_date) ?? 'Belum diset'
                                                                    }}
                                                                </li>
                                                                <li class="mb-2">
                                                                    <i class="mdi mdi-calendar text-primary me-2"></i>
                                                                    <strong>Project End:</strong>
                                                                    {{ formatDate($project->end_date) ?? 'Belum diset'
                                                                    }}
                                                                </li>
                                                            </ul>
                                                            @else
                                                            No information available
                                                            @endif

                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div id="projectSummaryDetails" class="mb-3">
                                                        <h6 class="text-primary mb-3">
                                                            <i class="mdi mdi-chart-line me-2"></i>Project Summary
                                                        </h6>
                                                        <div class="alert alert-soft-success">
                                                            @if ($project->summary)
                                                            <div
                                                                class="d-flex justify-content-between align-items-center">
                                                                <span>
                                                                    <i class="mdi mdi-cash me-2 text-success"></i>
                                                                    <strong>Total Summary:</strong>
                                                                </span>
                                                                <span class="h5 mb-0 text-primary">{{
                                                                    formatRupiah($project->summary->total_summary)
                                                                    }}</span>
                                                            </div>
                                                            @else
                                                            No summary available
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-12 ">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5>Distribusi Project</h5>
                                            <table id="datatabledistribusi" class="table table-hover table-responsive"
                                                style="width: 100%;">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center" style="width: 5%">No</th>
                                                        <th>Project</th>
                                                        <th>Tipe</th>
                                                        <th>Code</th>
                                                        <th>Name</th>
                                                        <th>Deskripsi</th>
                                                        <th>Item</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>



                                <div class="col-12 ">
                                    <div class="card">
                                        <div class="card-body">
                                            <h5>Review Project</h5>
                                            <table id="datatable" class="table table-hover table-responsive"
                                                style="width: 100%;">
                                                <thead>
                                                    <tr>
                                                        <th class="text-center" style="width: 5%">No</th>
                                                        <th style="width: 15%">Project</th>
                                                        <th style="width: 15%">Reviewer</th>
                                                        <th>Note</th>
                                                        <th style="width: 10%">Status</th>
                                                        <th style="width: 15%">Tanggal Review</th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                </div> --}}
                </div>
                <!-- end row -->
            </div>
        </div>

        @include('layout.component.modalreportproject')

        @push('js')
            <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
            <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
            <script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
            <script src="{{ asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
            <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
            <script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
            <!-- chart js -->

            <script src="{{ asset('assets/libs/chart.js/chart.umd.js') }}"></script>
            <script src="assets/js/pages/chartjs.init.js"></script>

            <script src="{{ asset('assets/js/pages/jquery-knob.init.js') }}"></script>

            {{-- custom swetaert --}}
            <script src="{{ asset('assets/js/custom.js') }}"></script>

            {{-- calender --}}
            <script src="{{ asset('assets/libs/moment/min/moment.min.js') }}"></script>
            <script src="{{ asset('assets/libs/jquery-ui-dist/jquery-ui.min.js') }}"></script>
            <script src="{{ asset('assets/libs/fullcalendar_new/dist/index.global.js') }}"></script>


            <script>
                @if (Session::has('message'))
                    Swal.fire({
                        title: `{{ Session::get('status') }}`,
                        text: `{{ Session::get('message') }}`,
                        icon: "success",
                        showConfirmButton: false,
                        timer: 3000
                    });
                    // Swal.fire(`{{ Session::get('status') }}`, `{{ Session::get('message') }}`, "success");
                @endif
                $(document).ready(function() {
                    var project_id = @json($id)
                    // Initialize DataTable
                    let tablereview = $("#datatable").DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: {
                            url: '{{ route('report.project.getdatareview') }}',
                            data: function(e) {
                                e.project_id = project_id
                            }
                        },
                        columns: [{
                                data: 'DT_RowIndex',
                                name: 'DT_RowIndex',
                                orderable: false,
                                searchable: false,
                                className: 'text-center align-middle'
                            },
                            {
                                data: 'project',
                                name: 'project.name',
                                className: 'align-middle'
                            },
                            {
                                data: 'reviewer',
                                name: 'reviewer.name',
                                className: 'align-middle'
                            },
                            {
                                data: 'review_note',
                                name: 'review_note',
                                className: 'align-middle truncate-text',
                                render: function(data, type, row) {
                                    return data ? data.substring(0, 100) + (data.length > 100 ? '...' :
                                        '') : '-';
                                }
                            },
                            {
                                data: 'status_pengajuan',
                                name: 'project.status_pengajuan',
                                className: 'align-middle text-center'
                            },
                            {
                                data: 'review_date',
                                name: 'review_date',
                                className: 'align-middle'
                            },
                        ],
                    });

                    let tabledistribusi = $("#datatabledistribusi").DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: {
                            url: '{{ route('report.project.getdetailproject') }}',
                            data: function(e) {
                                e.project_id = project_id
                            }
                        },
                        columns: [{
                                data: 'DT_RowIndex',
                                name: 'DT_RowIndex',
                                orderable: false,
                                searchable: false,
                                className: 'text-center align-middle'
                            },
                            {
                                data: 'project',
                                name: 'project',
                                className: 'align-middle'
                            },
                            {
                                data: 'type',
                                name: 'type',
                                className: 'align-middle'
                            },
                            {
                                data: 'code',
                                name: 'code',
                                className: 'align-middle'
                            },
                            {
                                data: 'name',
                                name: 'name',
                                className: 'align-middle'
                            },
                            {
                                data: 'description',
                                name: 'description',
                                className: 'align-middle'
                            },
                            {
                                data: 'action',
                                name: 'action',
                                className: 'align-middle'
                            },
                        ],
                    });

                    var table = $("#datatask").DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: {
                            url: '{{ route('tasks.getdata') }}',
                            type: 'GET',
                            data: function(d) {
                                d.project_id = project_id
                            }
                        },
                        columns: [{
                                data: 'DT_RowIndex',
                                orderable: false,
                                searchable: false,
                                class: 'text-center',
                            },
                            {
                                data: 'title',
                                name: 'title'
                            },
                            {
                                data: 'project',
                                name: 'project.name'
                            },
                            {
                                data: 'vendor',
                                name: 'vendor.name'
                            },
                            {
                                data: 'start_date',
                                name: 'start_date'
                            },
                            {
                                data: 'end_date',
                                name: 'end_date'
                            },
                            {
                                data: 'status',
                                name: 'status'
                            },
                            {
                                data: 'priority',
                                name: 'priority'
                            },
                            @canany(['complete-tasks', 'update-tasks', 'delete-tasks'])
                                {
                                    data: 'action',
                                    name: 'action',
                                    orderable: false,
                                    searchable: false,
                                    class: 'text-center'
                                }
                            @endcanany
                        ],
                    });

                    var tablePayment = $("#datapayment").DataTable({
                        processing: true,
                        serverSide: true,
                        ajax: {
                            url: '{{ route('payment.getdata') }}',
                            type: 'GET',
                            data: function(d) {
                                d.project_id = project_id
                            }
                        },
                        columns: [{
                                data: 'DT_RowIndex',
                                name: 'DT_RowIndex',
                                orderable: false,
                                searchable: false,
                                className: 'text-center align-middle'
                            },
                            {
                                data: 'bukti_pembayaran',
                                name: 'bukti_pembayaran',
                                orderable: false,
                                className: 'align-middle',
                                render: function(data, type, row) {
                                    if (data) {
                                        const imageUrl = `{{ asset('storage/images/payment') }}/${data}`;
                                        return `<img src="${imageUrl}" alt="Report Image" class="image-thumbnail">`;
                                    }
                                    return '<span class="text-muted">No image</span>';
                                }
                            },
                            {
                                data: 'payment_date',
                                name: 'payment_date',
                                className: 'align-middle'
                            },
                            {
                                data: 'vendor',
                                name: 'vendor.name',
                                className: 'align-middle'
                            },
                            {
                                data: 'amount',
                                name: 'amount',
                                className: 'align-middle'
                            },
                            {
                                data: 'note',
                                name: 'note',
                                className: 'align-middle',
                                render: function(data, type, row) {
                                    return data ? data.substring(0, 100) + (data.length > 100 ? '...' :
                                        '') : '-';
                                }
                            },
                            @canany(['update-paymentvendors', 'delete-paymentvendors'])
                                {
                                    data: 'action',
                                    name: 'action',
                                    orderable: false,
                                    searchable: false,
                                    className: 'text-center align-middle action-buttons'
                                }
                            @endcanany
                        ],
                    });

                    // Handle task completion toggle
                    $('#datatask').on('click', '.task-completion-button', function() {
                        const taskId = $(this).data('id');
                        const button = $(this);
                        const currentStatus = button.hasClass('btn-success') ? 'complated' : 'in_progres';

                        $.ajax({
                            url: `{{ route('tasks.toggle-completion', ':id') }}`.replace(':id', taskId),
                            type: 'POST',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                                status: currentStatus
                            },
                            success: function(response) {
                                if (response.status === 'success') {
                                    // Reload the datatable to reflect changes
                                    table.ajax.reload(null, false);

                                    Swal.fire({
                                        toast: true,
                                        position: 'top-end',
                                        icon: 'success',
                                        title: response.message,
                                        showConfirmButton: false,
                                        timer: 3000
                                    });
                                } else {
                                    // Show error toast
                                    Swal.fire({
                                        toast: true,
                                        position: 'top-end',
                                        icon: 'error',
                                        title: response.message,
                                        showConfirmButton: false,
                                        timer: 3000
                                    });
                                }
                            },
                            error: function() {
                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    icon: 'error',
                                    title: 'Failed to update task status',
                                    showConfirmButton: false,
                                    timer: 3000
                                });
                            }
                        });
                    });

                    $('#datatask').on('click', '.task-report-button', function() {
                        const taskId = $(this).data('id');

                        // Create a modal with more detailed form
                        Swal.fire({
                            title: 'Laporan Tugas',
                            html: `
                            <form id="taskReportForm" class="text-start needs-validation" novalidate>
                                <div class="form-group">
                                    <label for="description" class="form-label required">Deskripsi Laporan (Wajib)</label>
                                    <textarea 
                                        id="description" 
                                        name="description" 
                                        class="form-control" 
                                        placeholder="Masukkan deskripsi laporan" 
                                        rows="4" 
                                        required
                                    ></textarea>
                                    <div class="invalid-feedback">Deskripsi laporan wajib diisi</div>
                                </div>
                                <div class="form-group mt-3">
                                    <label for="issue" class="form-label">Kendala/Masalah (Opsional)</label>
                                    <textarea 
                                        id="issue" 
                                        name="issue" 
                                        class="form-control" 
                                        placeholder="Masukkan kendala atau masalah" 
                                        rows="4"
                                    ></textarea>
                                </div>
                                <div class="form-group mt-3">
                                    <label for="image" class="form-label">Unggah Gambar</label>
                                    <input 
                                        type="file" 
                                        name="image" 
                                        id="image" 
                                        class="form-control" 
                                        accept="image/jpeg,image/png,image/jpg,image/gif"
                                    >
                                    <small class="text-muted">Format yang diterima: JPEG, PNG, JPG, GIF. Ukuran maksimal: 5MB</small>
                                    <div class="preview-container mt-2" style="display:none;">
                                        <img 
                                            id="imagePreview" 
                                            src="#" 
                                            alt="Preview" 
                                            class="img-fluid" 
                                            style="max-height: 200px; display:none;"
                                        >
                                    </div>
                                </div>
                            </form>
                            `,
                            showCancelButton: true,
                            confirmButtonText: 'Kirim Laporan',
                            cancelButtonText: 'Batal',
                            preConfirm: () => {
                                const form = document.getElementById('taskReportForm');

                                // HTML5 form validation
                                if (!form.checkValidity()) {
                                    form.classList.add('was-validated');
                                    return false;
                                }

                                const description = document.getElementById('description').value.trim();
                                const imageFile = document.getElementById('image').files[0];

                                // Create FormData for file upload
                                const formData = new FormData();
                                formData.append('task_id', taskId);
                                formData.append('description', description);

                                // Optional issue field
                                const issue = document.getElementById('issue').value.trim();
                                if (issue) {
                                    formData.append('issue', issue);
                                }

                                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

                                // Image validation
                                if (imageFile) {
                                    const validTypes = ['image/jpeg', 'image/png', 'image/jpg',
                                        'image/gif'
                                    ];

                                    // Validate file type
                                    if (!validTypes.includes(imageFile.type)) {
                                        Swal.showValidationMessage('Format gambar tidak valid');
                                        return false;
                                    }

                                    // Validate file size (5MB)
                                    if (imageFile.size > 5 * 1024 * 1024) {
                                        Swal.showValidationMessage('Ukuran gambar maksimal 5MB');
                                        return false;
                                    }

                                    formData.append('image', imageFile);
                                }

                                // AJAX submission with improved error handling
                                return $.ajax({
                                    url: '{{ route('tasks.report') }}',
                                    method: 'POST',
                                    data: formData,
                                    processData: false,
                                    contentType: false,
                                    dataType: 'json',
                                    xhr: function() {
                                        const xhr = new window.XMLHttpRequest();
                                        xhr.upload.addEventListener('progress', function(
                                            evt) {
                                            if (evt.lengthComputable) {
                                                const percentComplete = evt.loaded /
                                                    evt.total * 100;
                                                Swal.update({
                                                    title: 'Mengunggah...',
                                                    html: `Progress: ${Math.round(percentComplete)}%`
                                                });
                                            }
                                        }, false);
                                        return xhr;
                                    }
                                }).fail(function(xhr) {
                                    Swal.showValidationMessage(
                                        xhr.responseJSON?.message ||
                                        'Terjadi kesalahan saat melaporkan tugas'
                                    );
                                });
                            },
                            didRender: () => {
                                // Image preview functionality
                                const imageInput = document.getElementById('image');
                                const imagePreview = document.getElementById('imagePreview');
                                const previewContainer = document.querySelector('.preview-container');

                                imageInput.addEventListener('change', function(e) {
                                    const file = e.target.files[0];
                                    if (file) {
                                        const reader = new FileReader();
                                        reader.onload = function(event) {
                                            imagePreview.src = event.target.result;
                                            imagePreview.style.display = 'block';
                                            previewContainer.style.display = 'block';
                                        };
                                        reader.readAsDataURL(file);
                                    } else {
                                        imagePreview.src = '#';
                                        imagePreview.style.display = 'none';
                                        previewContainer.style.display = 'none';
                                    }
                                });

                                // Ensure description textarea is focused
                                document.getElementById('description').focus();
                            },
                            allowOutsideClick: () => !Swal.isLoading()
                        }).then((result) => {
                            if (result.isConfirmed && result.value.success) {
                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    icon: 'success',
                                    title: result.value.message,
                                    showConfirmButton: false,
                                    timer: 3000
                                });

                                // Safely reload the table if it exists
                                if (typeof table !== 'undefined' && table.ajax) {
                                    table.ajax.reload(null, false);
                                }
                            }
                        });
                    });

                    // Image preview function (kept for compatibility)
                    function previewImage(input) {
                        const preview = document.getElementById('imagePreview');
                        const previewContainer = document.querySelector('.preview-container');

                        if (input.files && input.files[0]) {
                            const reader = new FileReader();

                            reader.onload = function(e) {
                                preview.src = e.target.result;
                                preview.style.display = 'block';
                                previewContainer.style.display = 'block';
                            };

                            reader.readAsDataURL(input.files[0]);
                        }
                    }

                    // Inisialisasi drag and drop untuk kanban
                    $('.kanban-column').sortable({
                        connectWith: '.kanban-column',
                        placeholder: 'task-placeholder',
                        handle: '.card-body',
                        cursor: 'move',
                        tolerance: 'pointer',

                        // Before the move starts
                        start: function(event, ui) {
                            ui.item.addClass('dragging');
                            ui.placeholder.height(ui.item.outerHeight());
                        },

                        // When dragging stops
                        stop: function(event, ui) {
                            ui.item.removeClass('dragging');
                        },

                        // When item is updated in a column
                        update: function(event, ui) {
                            // Check if the item has actually changed columns
                            if (this === ui.item.parent()[0]) {
                                var taskId = ui.item.data('task-id');
                                var newStatus = ui.item.parent().data('status');

                                // Disable sorting during AJAX to prevent multiple requests
                                $('.kanban-column').sortable('disable');

                                $.ajax({
                                    url: `{{ route('tasks.update-status', ':id') }}`.replace(':id',
                                        taskId),
                                    method: 'PATCH',
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                                    },
                                    data: {
                                        status: newStatus,
                                        project_id: project_id
                                    },
                                    success: function(response) {
                                        // Show success SweetAlert
                                        Swal.fire({
                                            toast: true,
                                            position: 'top-end',
                                            icon: 'success',
                                            title: response.message ||
                                                'Status tugas berhasil diperbarui',
                                            showConfirmButton: false,
                                            timer: 3000
                                        });

                                        // Optional: Reload the DataTable
                                        $('#datatask').DataTable().ajax.reload(null, false);
                                    },
                                    error: function(xhr) {
                                        // Revert the sorting if the update fails
                                        $(event.target).sortable('cancel');

                                        // Show error SweetAlert with more detailed message
                                        Swal.fire({
                                            toast: true,
                                            position: 'top-end',
                                            icon: 'error',
                                            title: xhr.responseJSON?.message ||
                                                'Gagal memperbarui status task',
                                            showConfirmButton: false,
                                            timer: 3000
                                        });
                                    },
                                    complete: function() {
                                        // Re-enable sorting
                                        $('.kanban-column').sortable('enable');
                                    }
                                });
                            }
                        }
                    });

                    // Add some CSS to improve preview styling
                    const style = document.createElement('style');
                    style.innerHTML = `
                    .preview-container {
                        margin-top: 10px;
                        text-align: center;
                    }
                    .image-preview {
                        max-height: 200px;
                        max-width: 100%;
                        object-fit: contain;
                        display: none;
                    }
                    `;
                    document.head.appendChild(style);

                    $('<style>')
                        .prop('type', 'text/css')
                        .html(`
            .dragging {
                opacity: 0.5;
                transform: scale(1.02);
                transition: all 0.2s ease;
            }
            .task-placeholder {
                background-color: #f0f0f0;
                border: 2px dashed #007bff;
                margin-bottom: 10px;
                visibility: visible !important;
            }
            .kanban-column .card-body {
                min-height: 100px;
            }
            .ui-sortable-handle {
                cursor: move;
            }
        `)
                        .appendTo('head');




                    $(".dataTables_length select").addClass("form-select form-select-sm");
                });
            </script>


            {{-- timeline --}}
            <script>
                $(document).ready(function() {
                    var calendarInitialized = false; // Untuk mencegah inisialisasi ulang

                    // Event ketika tab diaktifkan
                    $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
                        var target = $(e.target).attr("href"); // Mendapatkan ID tab yang diaktifkan

                        if (target === "#gantchart" && !calendarInitialized) {
                            calendarInitialized = true;
                            initializeCalendar();
                        }
                    });


                    function initializeCalendar() {
                        var calendarEl = document.getElementById('calendar');

                        var calendar = new FullCalendar.Calendar(calendarEl, {
                            initialDate: new Date(),
                            editable: false,
                            selectable: false,
                            nowIndicator: false,
                            aspectRatio: 2.0,
                            headerToolbar: {
                                left: 'today prev,next',
                                center: 'title',
                                right: 'resourceTimelineYear,resourceTimelineMonth,resourceTimelineWeek',
                            },
                            initialView: 'resourceTimelineMonth',
                            views: {
                                resourceTimelineYear: {
                                    type: 'resourceTimeline',
                                    duration: {
                                        years: 3
                                    },
                                    buttonText: 'Year',
                                    slotDuration: {
                                        months: 1
                                    },
                                    slotLabelFormat: [{
                                            year: 'numeric'
                                        },
                                        {
                                            month: 'short'
                                        },
                                    ],
                                },
                                resourceTimelineMonth: {
                                    type: 'resourceTimeline',
                                    duration: {
                                        month: 4
                                    },
                                    buttonText: 'Month',
                                    slotDuration: {
                                        days: 1
                                    },
                                    slotLabelFormat: [{
                                            month: 'long'
                                        },
                                        {
                                            weekday: 'short',
                                            day: 'numeric',
                                            omitCommas: true
                                        },
                                    ],
                                },
                                resourceTimelineWeek: {
                                    type: 'resourceTimeline',
                                    duration: {
                                        week: 4
                                    },
                                    buttonText: 'Week',
                                    slotDuration: {
                                        days: 1
                                    },
                                    slotLabelFormat: [{
                                            month: 'short'
                                        },
                                        {
                                            weekday: 'long',
                                            day: 'numeric',
                                            omitCommas: true
                                        },
                                    ],
                                },
                            },
                            resourceAreaWidth: '40%',
                            resourceAreaColumns: [{
                                    headerContent: 'Task',
                                    field: 'task',
                                    cellClassNames: 'task',
                                },
                                {
                                    headerContent: 'Progress',
                                    field: 'progress',
                                },
                            ],
                            resources: function(fetchInfo, successCallback, failureCallback) {
                                var project_id = @json($id);
                                fetch(`{{ route('tasks.data') }}?id=${project_id}`)
                                    .then((response) => response.json())
                                    .then((data) => {
                                        successCallback(data.resources);
                                    })
                                    .catch((error) => {
                                        console.error('Error fetching resources:', error);
                                        failureCallback(error);
                                    });
                            },
                            events: function(fetchInfo, successCallback, failureCallback) {
                                var project_id = @json($id);
                                fetch(`{{ route('tasks.data') }}?id=${project_id}`)
                                    .then((response) => response.json())
                                    .then((data) => {
                                        successCallback(data.events);
                                    })
                                    .catch((error) => {
                                        console.error('Error fetching events:', error);
                                        failureCallback(error);
                                    });
                            },
                        });

                        calendar.render(); // Render kalender
                    }
                });
            </script>

            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const chartData = @json($chartData);
                    const customLabels = @json($customLabels);


                    const data = {
                        labels: customLabels,
                        datasets: [{
                            data: Object.values(chartData),
                            backgroundColor: [
                                '#f1b44c',
                                '#50a5f1',
                                '#34c38f',
                                '#f46a6a'
                            ],
                            hoverBackgroundColor: [
                                '#f1b44c', //pending
                                '#50a5f1', //in progress
                                '#34c38f', //complated
                                '#f46a6a' //overdue
                            ]
                        }]
                    };

                    // Konfigurasi chart
                    const options = {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'bottom',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(tooltipItem) {
                                        const label = tooltipItem.label || '';
                                        const percentage = tooltipItem.raw || 0;
                                        return `${label}: ${percentage}%`;
                                    }
                                }
                            }
                        }
                    };


                    const ctx = document.getElementById('taskStatusChart').getContext('2d');
                    const taskStatusChart = new Chart(ctx, {
                        type: 'doughnut',
                        data: data,
                        options: options
                    });
                });
            </script>
        @endpush
    @endsection
