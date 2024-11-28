@extends('layout.base')

@section('tittle', $tittle)

@push('css')
<link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
<link href="{{ asset('assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}"
    rel="stylesheet">
<link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
<link href="assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
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
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="project_id" class="form-label">
                                        Pilih Project
                                    </label>
                                    <select name="project_id" id="project_id"
                                        class="form-control select2 @error('project_id') is-invalid @enderror" required>
                                        <option value="">Pilih Project</option>
                                        @foreach ($projects as $project)
                                        <option value="{{ $project->id }}" {{ request('project_id')==$project->id ?
                                            'selected' : '' }}>
                                            {{ $project->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('project_id')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-12 d-none detail">
                                <h3 class="mt-4">{{ $projects[0]->name }}</h3>
                                <h6>Perusahaan</h6>
                                <p>Mulai Project Sampai</p>
                            </div>

                            <div class="col-12 d-none detail">
                                <div class="card mb-5">
                                    <div class="card-body">
                                        <h5>File Project</h5>
                                        <table id="datatablefileproject" class="table table-hover table-responsive"
                                            style="width: 100%;">
                                            <thead>
                                                <tr>
                                                    <th class="text-center" style="width: 5%">No</th>
                                                    <th class="text-center">Excel</th>
                                                    <th class="text-center">Kmz</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>


                            <div class="col-12 d-none detail">
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
            </div>
        </div>
        <!-- end row -->
    </div>
</div>


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
{{-- <script src="{{ asset('assets/js/custom.js') }}"></script> --}}


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
                // Initialize DataTable
                let datatableFileProject = $("#datatablefileproject").DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
            url: '{{ route('report.project.getdatafile') }}',
            data: function (e) {
                e.project_id = $('#project_id').val(); 
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
                            data: 'excel',
                            name: 'excel',
                        },
                        {
                            data: 'kmz',
                            name: 'kmz',
                        },
                    ],
                });


                //table review
              let tablereview =  $("#datatable").DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '{{ route('review.getdata') }}',
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

                $('#project_id').on('change', function () {
                    console.log('Project ID Selected:', $(this).val());
                    if ($(this).val() == '') {
                        $('.detail').addClass('d-none');
                    }else{
                        $('.detail').removeClass('d-none');
                        datatableFileProject.ajax.reload();
                        tablereview.ajax.reload();
                    } 
                });

                $(".dataTables_length select").addClass("form-select form-select-sm");
            });
</script>
@endpush
@endsection