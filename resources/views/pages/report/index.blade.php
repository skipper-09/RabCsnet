@extends('layout.base')

@section('tittle', $tittle)

@push('css')
    <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}"
        rel="stylesheet" />
    <link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text-css" />
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
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
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            @can('create-reportvendors')
                                <div class="mb-3">
                                    <a href="{{ route('report.add') }}" class="btn btn-primary btn-sm">Tambah
                                        {{ $tittle }}</a>
                                </div>
                            @endcan
                            @if (!Auth::user()->hasRole('Vendor'))
                                <div class="row mb-3">
                                    <div class="form-group col-12 col-md-4">
                                        <label>Filter Vendor <span class="text-danger">*</span></label>
                                        <select class="form-control select2" id="FilterVendor" name="vendor_filter">
                                            <option value="">All Vendors</option>
                                            @foreach ($vendor as $item)
                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group col-12 col-md-4">
                                        <label>Filter Project <span class="text-danger">*</span></label>
                                        <select class="form-control select2" id="FilterProject" name="project_filter">
                                            <option value="">All Projects</option>
                                            @foreach ($project as $item)
                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @endif
                            <div class="table-responsive">
                                <table id="datatable" class="table table-hover" style="width: 100%;">
                                    <thead>
                                        <tr>
                                            <th class="text-center" style="width: 5%">No</th>
                                            <th style="width: 15%">Gambar</th>
                                            <th style="width: 15%">Project</th>
                                            <th style="width: 15%">Vendor</th>
                                            <th style="width: 15%">Nama</th>
                                            <th>Deskripsi</th>
                                            @canany(['update-reportvendors', 'delete-reportvendors'])
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
        </div>
    </div>

    @push('js')
        <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
        <script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
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

            $(document).ready(function() {
                // Initialize Select2 for vendor and project filters
                $('#FilterVendor').select2({
                    placeholder: "Pilih Vendor",
                });

                $('#FilterProject').select2({
                    placeholder: "Pilih Project",
                });

                // Initialize DataTable
                var table = $("#datatable").DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route('report.getdata') }}',
                        type: 'GET',
                        data: function(d) {
                            d.vendor_filter = $('#FilterVendor').find(":selected").val();
                            d.project_filter = $('#FilterProject').find(":selected").val();
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
                            data: 'image',
                            name: 'image',
                            orderable: false,
                            className: 'align-middle',
                            render: function(data, type, row) {
                                if (data) {
                                    const imageUrl =
                                        `{{ asset('storage/images/reportvendor') }}/${data}`;
                                    return `<img src="${imageUrl}" alt="Report Image" class="image-thumbnail">`;
                                }
                                return '<span class="text-muted">No image</span>';
                            }
                        },
                        {
                            data: 'project',
                            name: 'project.name',
                            className: 'align-middle'
                        },
                        {
                            data: 'vendor',
                            name: 'vendor.name',
                            className: 'align-middle'
                        },
                        {
                            data: 'title',
                            name: 'title',
                            className: 'align-middle'
                        },
                        {
                            data: 'description',
                            name: 'description',
                            className: 'align-middle',
                            render: function(data, type, row) {
                                return data ? data.substring(0, 100) + (data.length > 100 ? '...' :
                                    '') : '-';
                            }
                        },
                        @canany(['update-reportvendors', 'delete-reportvendors'])
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

                // Handle vendor filter change
                $('#FilterVendor').on('change', function() {
                    $('#FilterProject').val('').trigger('change.select2'); // Reset Project filter
                    table.ajax.reload(null, false);
                });

                // Handle project filter change
                $('#FilterProject').on('change', function() {
                    $('#FilterVendor').val('').trigger('change.select2'); // Reset Vendor filter
                    table.ajax.reload(null, false);
                });

                $(".dataTables_length select").addClass("form-select form-select-sm");
            });
        </script>
    @endpush
@endsection
