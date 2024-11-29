@extends('layout.base')

@section('tittle', $tittle)

@push('css')
    <!-- DataTables CSS -->
    <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
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
                            <li class="breadcrumb-item active">Task Management</li>
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
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="mb-3">
                                <a href="{{ route('tasks.assign.add') }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus"></i> Tambah {{ $tittle }}
                                </a>
                            </div>
                            <table id="datatable" class="table table-responsive table-hover" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Task</th>
                                        <th>Vendor</th>
                                        <th>Finish Date</th>
                                        <th>Progress</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Modal -->
    <div class="modal fade" id="progressModal" tabindex="-1" role="dialog" aria-labelledby="progressModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="progressModalLabel">Update Progress</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="progressForm">
                        @csrf
                        <input type="hidden" id="taskAssignId" name="task_assign_id">
                        <div class="form-group">
                            <label for="progressInput">Progress (%)</label>
                            <input type="range" class="custom-range" id="progressInput" name="progress" min="0"
                                max="100" step="1">
                            <small id="progressValue" class="form-text text-muted">0%</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveProgress">Save Progress</button>
                </div>
            </div>
        </div>
    </div>

    @push('js')
        <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
        <!-- Custom JS -->
        <script src="{{ asset('assets/js/custom.js') }}"></script>

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
            $(function() {
                // Initialize DataTable
                const table = $('#datatable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '{{ route('tasks.assign.getdata') }}',
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false,
                        },
                        {
                            data: 'task',
                            name: 'task'
                        },
                        {
                            data: 'vendor',
                            name: 'vendor'
                        },
                        {
                            data: 'finish_date',
                            name: 'finish_date'
                        },
                        {
                            data: null,
                            render: function(data) {
                                let progressBar = `
            <div class="progress">
                <div class="progress-bar" role="progressbar" 
                    style="width: ${data.progress || 0}%" 
                    aria-valuenow="${data.progress || 0}" 
                    aria-valuemin="0" 
                    aria-valuemax="100">
                    ${data.progress || 0}%
                </div>
            </div>`;

                                let updateButton = data.progress < 100 ? `
            <button class="btn btn-sm btn-outline-primary update-progress mt-1" 
                data-id="${data.id}" 
                data-current-progress="${data.progress || 0}">
                Update Progress
            </button>` : '';

                                return progressBar + updateButton;
                            },
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ]
                });

                // Progress Input Range Handling
                $(document).on('input', '#progressInput', function() {
                    $('#progressValue').text($(this).val() + '%');
                });

                // Open Progress Modal
                $(document).on('click', '.update-progress', function() {
                    const taskAssignId = $(this).data('id');
                    const currentProgress = $(this).data('current-progress');

                    $('#taskAssignId').val(taskAssignId);
                    $('#progressInput').val(currentProgress);
                    $('#progressValue').text(currentProgress + '%');

                    $('#progressModal').modal('show');
                });

                // Save Progress
                $('#saveProgress').on('click', function() {
                    const taskAssignId = $('#taskAssignId').val();
                    const progress = $('#progressInput').val();

                    $.ajax({
                        url: `{{ route('tasks.assign.progress.update', ['id' => '__ID__']) }}`.replace(
                            '__ID__', taskAssignId),
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            progress: progress
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: response.message,
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                                $('#progressModal').modal('hide');
                                table.ajax.reload(null, false);
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Failed to update progress'
                            });
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
