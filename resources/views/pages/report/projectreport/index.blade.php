@extends('layout.base')

@section('tittle', $tittle)

@push('css')
<link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}"
    rel="stylesheet">
<link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
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
            <div class="col-lg-12">
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
                                                                    storage/files/excel/{$project->Projectfile->excel}")
                                                                    }}">Download</a>
                                                            </li>

                                                            <li class="mb-2">
                                                                <i class="mdi mdi-map text-danger me-2"></i>
                                                                <strong>KMZ File:</strong>
                                                                <a class="btn btn-primary btn-sm" href="{{ asset("
                                                                    storage/files/kmz/{$project->Projectfile->kmz}")
                                                                    }}">Download</a>
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
                                            <div class="col-md-6">
                                                <div id="projectSummaryDetails" class="mb-3">
                                                    <h6 class="text-primary mb-3">
                                                        <i class="mdi mdi-chart-line me-2"></i>Project Summary
                                                    </h6>
                                                    <div class="alert alert-soft-success">
                                                        @if ($project->summary)
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <span>
                                                                <i class="mdi mdi-cash me-2 text-success"></i>
                                                                <strong>Total Summary:</strong>
                                                            </span>
                                                            <span class="h5 mb-0 text-primary">{{
                                                                formatRupiah($project->summary->total_summary) }}</span>
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
                </div>
            </div>
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
<script src="{{ asset('assets/js/pages/form-validation.init.js') }}"></script>
<script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/form-advanced.init.js') }}"></script>
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
                $(".dataTables_length select").addClass("form-select form-select-sm");
            });
</script>


{{-- timeline --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
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
            right: 'resourceTimelineYear,resourceTimelineMonth,resourceTimelineWeek'
        },
        initialView: 'resourceTimelineMonth',
        views: {
            resourceTimelineYear: {
                type: 'resourceTimeline',
                duration: { years: 3 },
                buttonText: 'Year',
                slotDuration: { months: 1 },
                slotLabelFormat: [{ year: 'numeric' }, { month: 'short' }]
            },
            resourceTimelineMonth: {
                type: 'resourceTimeline',
                duration: { month: 4 },
                buttonText: 'Month',
                slotDuration: { days: 1 },
                slotLabelFormat: [{month:'long'},{ weekday: 'short', day: 'numeric', omitCommas: true }]
            },
            resourceTimelineWeek: {
                type: 'resourceTimeline',
                duration: { week: 4 },
                buttonText: 'Week',
                slotDuration: { days: 1 },
                slotLabelFormat: [{ month: 'short'},{ weekday: 'long', day: 'numeric', omitCommas: true }]
            }
        },
        resourceAreaWidth: '40%',
        resourceAreaColumns: [  
            {
                headerContent: 'Task',
                field: 'task',
                cellClassNames: 'task',
            },
            {
                headerContent: 'Progress',
                field: 'progress'
            }
        ],
        resources: function(fetchInfo, successCallback, failureCallback) {
            var project_id = @json($id);
            fetch(`{{ route('tasks.data') }}?id=${project_id}`)
                .then(response => response.json())
                .then(data => {
                    successCallback(data.resources); 
                })
                .catch(error => {
                    console.error('Error fetching resources:', error);
                    failureCallback(error); 
                });
        },
        events: function(fetchInfo, successCallback, failureCallback) {
            var project_id = @json($id);
            fetch(`{{ route('tasks.data') }}?id=${project_id}`)
                .then(response => response.json())
                .then(data => {
                    successCallback(data.events); 
                })
                .catch(error => {
                    console.error('Error fetching events:', error);
                    failureCallback(error); 
                });
        }
    });

    calendar.render();
});

</script>
@endpush
@endsection