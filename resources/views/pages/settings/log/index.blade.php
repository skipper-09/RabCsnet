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
                            <li class="breadcrumb-item active">Settings</li>
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
                        <div class="card-header">
                            <button id="clearLogsBtn" class="btn btn-primary">Delete
                                {{ $tittle }}</button>
                        </div>
                        <div class="card-body">
                            <table id="datatable" class="table table-responsive  table-hover" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama</th>
                                        <th>Deskripsi</th>
                                        <th>Tanggal</th>
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
        {{-- custom swetaert --}}
        <script src="{{ asset('assets/js/custom.js') }}"></script>
        <script>
            $(document).ready(function() {

                $("#clearLogsBtn").on("click", function() {
                    swal({
                        title: "Apakah Kamu Yakin?",
                        text: "Semua Log akan terhapus",
                        icon: "warning",
                        buttons: true,
                        dangerMode: true,
                    }).then((willDelete) => {
                        if (willDelete) {
                            $.ajax({
                                url: "{{ route('log.clean') }}",
                                method: "DELETE",
                                type: "DELETE",
                                headers: {
                                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                                        "content"
                                    ),
                                },
                                success: function(res) {
                                    //reload table
                                    $("#dataTable").DataTable().ajax.reload();
                                    // Do something with the result
                                    if (res.status === "success") {
                                        swal("Deleted!", res.message, {
                                            icon: "success",
                                        });
                                    } else {
                                        swal("Error!", res.message, {
                                            icon: "error",
                                        });
                                    }
                                },
                            });
                        }
                    });
                });


                $('#dataTable').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '{{ route('log.getdata') }}',
                    columns: [{
                            data: 'DT_RowIndex',
                            orderable: false,
                            searchable: false,
                            width: '10px',
                            class: 'text-center'
                        },
                        {
                            data: 'causer',
                            name: 'causer',
                            orderable: false,
                        },
                        {
                            data: 'description',
                            name: 'description',
                            orderable: false,
                        },
                        {
                            data: 'created_at',
                            name: 'created_at',
                            orderable: false,
                        },
                    ]
                });

            });
        </script>
    @endpush
@endsection
