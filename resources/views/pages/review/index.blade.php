@extends('layout.base')

@section('tittle', $tittle)

@push('css')
    <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}"
        rel="stylesheet" />
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .action-buttons {
            white-space: nowrap;
        }

        .truncate-text {
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
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
                            <li class="breadcrumb-item active">Review</li>
                            <li class="breadcrumb-item active">{{ $tittle }}</li>
                        </ol>
                    </div>
                </div>
                @can('create-projectreviews')
                    <div class="col-sm-6 text-end">
                        <a href="{{ route('review.add') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus me-1"></i> Tambah {{ $tittle }}
                        </a>
                    </div>
                @endcan
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="page-content-wrapper">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="datatable" class="table table-hover" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 5%">No</th>
                                            <th style="width: 15%">Project</th>
                                            <th style="width: 15%">Reviewer</th>
                                            <th>Note</th>
                                            <th style="width: 10%">Status</th>
                                            <th style="width: 15%">Tanggal Review</th>
                                            <th class="text-center" style="width: 10%">Aksi</th>
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
        {{-- custom swetaert --}}
        <script src="{{ asset('assets/js/custom.js') }}"></script>


        <script>
            @if (Session::has('message'))
                Swal.fire({
                    title: `{{ Session::get('status') }}`,
                    text: `{{ Session::get('message') }}`,
                    icon: "{{ strtolower(Session::get('status')) == 'success' ? 'success' : 'error' }}",
                    showConfirmButton: false,
                    timer: 3000
                });
            @endif
            $(document).ready(function() {
                // Initialize DataTable
                $("#datatable").DataTable({
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
                        @canany(['update-projectreviews', 'delete-projectreviews'])
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

                $(".dataTables_length select").addClass("form-select form-select-sm");
            });
        </script>
    @endpush
@endsection
