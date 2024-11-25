@extends('layout.base')

@section('tittle', $tittle)

@push('css')
    <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
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
                            <li class="breadcrumb-item active">Payment Vendor</li>
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
                                <a href="{{ route('payment.add') }}" class="btn btn-primary btn-sm">Tambah
                                    {{ $tittle }}</a>
                            </div>
                            <table id="datatable" class="table table-responsive  table-hover" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 5%">No</th>
                                        <th style="width: 15%">Bukti Pembayaran</th>
                                        <th style="width: 15%">Tanggal Pembayaran</th>
                                        {{-- <th style="width: 15%">Project</th> --}}
                                        <th style="width: 15%">Vendor</th>
                                        <th style="width: 15%">Amount</th>
                                        <th>Note</th>
                                        <th class="text-center" style="width: 10%">Action</th>
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
                $("#datatable").DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '{{ route('payment.getdata') }}',
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
                        // {
                        //     data: 'project',
                        //     name: 'project.name',
                        //     className: 'align-middle'
                        // },
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
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            className: 'text-center align-middle action-buttons'
                        }
                    ],
                });

                $(".dataTables_length select").addClass("form-select form-select-sm");
            });
        </script>
    @endpush
@endsection
