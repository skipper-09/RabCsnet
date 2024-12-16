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
                        <div class="card-body">
                            <div class="mb-3">
                                <a href="{{ route('user.add') }}" class="btn btn-primary btn-sm">Tambah
                                    {{ $tittle }}</a>
                            </div>
                            <table id="datatable" class="table table-responsive  table-hover" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Image</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
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
                    ajax: '{{ route('user.getdata') }}',
                    columns: [{
                            data: 'DT_RowIndex',
                            orderable: false,
                            searchable: false,
                            class: 'text-center',
                        },
                        {
                            data: 'picture',
                            name: 'picture',
                            orderable: false,
                            className: 'align-middle',
                            render: function(data, type, row) {
                                if (data) {
                                    const imageUrl =
                                        `{{ asset('storage/images/user') }}/${data}`;
                                    return `<img src="${imageUrl}" alt="Profile Image" class="rounded-circle header-profile-user">`;
                                }
                                return '<span class="text-muted">No image</span>';
                            }
                        },
                        {
                            data: 'name',
                            name: 'name',

                        },
                        {
                            data: 'email',
                            name: 'email'
                        },
                        {
                            data: 'role',
                            name: 'role'
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
