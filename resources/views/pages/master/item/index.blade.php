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
                        <li class="breadcrumb-item active">Master</li>
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
                            <a href="{{ route('item.add') }}" class="btn btn-primary btn-sm">Tambah
                                {{ $tittle }}</a>
                            @can('export-items')
                            <div class="align-self-end ">
                                <a href="{{ route('item.export') }}" class="btn btn-primary btn-sm">Export
                                    {{ $tittle }}</a>
                                @endcan
                            </div>
                        </div>
                        <table id="datatable" class="table table-responsive  table-hover" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>Item Code</th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Unit</th>
                                    <th>Material Price</th>
                                    <th>Service Price</th>
                                    <th>Deskripsi</th>
                                    <th>Action</th>
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
                    ajax: '{{ route('item.getdata') }}',
                    columns: [{
                            data: 'item_code',
                            name: 'item_code'
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'type', // Ensure this matches the column name from the server
                            name: 'type',
                        },
                        {
                            data: 'unit',
                            name: 'unit'
                        },
                        {
                            data: 'material_price',
                            name: 'material_price',
                            render: function(data) {
                                return new Intl.NumberFormat('id-ID', {
                                    style: 'currency',
                                    currency: 'IDR'
                                }).format(data);
                            }
                        },
                        {
                            data: 'service_price',
                            name: 'service_price',
                            render: function(data) {
                                return new Intl.NumberFormat('id-ID', {
                                    style: 'currency',
                                    currency: 'IDR'
                                }).format(data);
                            }
                        },
                        {
                            data: 'description',
                            name: 'description'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ],
                });


                $(".dataTables_length select").addClass("form-select form-select-sm");
            });
</script>
@endpush
@endsection