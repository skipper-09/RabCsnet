@extends('layout.base')

@section('tittle', $tittle)

@push('css')
    <!-- DataTables CSS -->
    <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" type="text/css" />
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
                                <a href="{{ route('tasks.assign.add') }}" class="btn btn-primary btn-sm">Tambah {{ $tittle }}</a>
                            </div>
                            <table id="datatable" class="table table-responsive table-hover" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Task</th>
                                        <th>Vendor</th>
                                        <th>Finish Date</th>
                                        <th>Status</th>
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
            @endif

            $(function() {
                // Initialize DataTable
                $('#datatable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '{{ route('tasks.assign.getdata') }}',
                    columns: [
                        {
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
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
                                return `
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input task-status-toggle" 
                                            id="taskStatus${data.id}" 
                                            data-id="${data.id}"
                                            ${data.finish_date !== '-' ? 'checked' : ''}>
                                        <label class="custom-control-label" for="taskStatus${data.id}"></label>
                                    </div>
                                `;
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

                // Task Status Toggle
                $(document).on('change', '.task-status-toggle', function() {
                    const taskId = $(this).data('id');
                    const isCompleted = $(this).is(':checked');

                    $.ajax({
                        url: `{{ route('tasks.assign.status', ['id' => '__ID__']) }}`.replace('__ID__', taskId),
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            is_completed: isCompleted
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                toastr.success(response.message);
                                $('#datatable').DataTable().ajax.reload(null, false);
                            } else {
                                toastr.error(response.message);
                                $(this).prop('checked', !isCompleted);
                            }
                        },
                        error: function() {
                            toastr.error('Failed to update task status');
                            $(this).prop('checked', !isCompleted);
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection