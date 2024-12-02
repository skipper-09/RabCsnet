@extends('layout.base')

@section('tittle', $tittle)

@push('css')
    <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}"
        rel="stylesheet" />
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
                            <li class="breadcrumb-item">Settings</li>
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
                            @can('clean-logs')
                                <div class="mb-3">
                                    <button onclick="clearLog()" class="btn btn-danger btn-sm">
                                        <i class="fas fa-trash-alt me-1"></i>Clear Log
                                    </button>
                                </div>
                            @endcan
                            <div class="table-responsive">
                                <table id="datatable" class="table table-hover" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Pengguna</th>
                                            <th>Deskripsi</th>
                                            <th>Waktu</th>
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

    @push('js')
        <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
        <script src="{{ asset('assets/js/custom.js') }}"></script>

        <script>
            $(document).ready(function() {
                $("#datatable").DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '{{ route('log.getdata') }}',
                    columns: [{
                            data: 'DT_RowIndex',
                            orderable: false,
                            searchable: false,
                        },
                        {
                            data: 'causer',
                            name: 'causer'
                        },
                        {
                            data: 'description',
                            name: 'description'
                        },
                        {
                            data: 'created_at',
                            name: 'created_at'
                        },
                    ],
                });

                $(".dataTables_length select").addClass("form-select form-select-sm");
            });

            function clearLog() {
                // Show confirmation dialog using SweetAlert2
                Swal.fire({
                    title: 'Are you sure?',
                    text: "All activity logs will be permanently deleted!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Yes, delete all logs!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Send AJAX request to clean logs
                        $.ajax({
                            url: '{{ route('log.clean') }}',
                            type: 'POST',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content')
                            },
                            success: function(response) {
                                if (response.success) {
                                    // Show success message
                                    Swal.fire(
                                        'Deleted!',
                                        response.message,
                                        'success'
                                    );

                                    // Refresh the DataTable
                                    $('#datatable').DataTable().ajax.reload();
                                } else {
                                    // Show error message if success is false
                                    Swal.fire(
                                        'Error!',
                                        response.message,
                                        'error'
                                    );
                                }
                            },
                            error: function(xhr, status, error) {
                                // Show error message if request fails
                                Swal.fire(
                                    'Error!',
                                    'Failed to clean logs. Please try again.',
                                    'error'
                                );
                                console.error(error);
                            }
                        });
                    }
                });
            }
        </script>
    @endpush
@endsection
