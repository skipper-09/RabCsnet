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
                    <h4>
                        {{ $tittle }}
                    </h4>
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item active">Master</li>
                        <li class="breadcrumb-item active">
                            {{ $tittle }}
                        </li>
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
                            <a href="{{ route('unit.add') }}" class="btn btn-primary btn-sm">Tambah {{ $tittle }}</a>
                        </div>
                        <table id="datatable" class="table dt-responsive nowrap table-hover" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>Name</th>
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


<script>
    $(document).ready(function () {
        // Initialize DataTable
        $("#datatable").DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('unit.getdata') }}',
            columns: [
                { data: 'name', name: 'name' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
        });

        
        $(".dataTables_length select").addClass("form-select form-select-sm");

        // Handle delete action using SweetAlert
        $("#datatable").on("click", ".action", function () {
            let route = $(this).data("route");

            Swal.fire({
                title: "Apakah Kamu Yakin?",
                text: "Menghapus data ini bersifat permanen",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, hapus!",
                cancelButtonText: "Batal",
                dangerMode: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: route,
                        method: "DELETE",
                        headers: {
                            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        },
                        success: function (res) {
                            $("#datatable").DataTable().ajax.reload();

                            if (res.status === "success") {
                                Swal.fire("Deleted!", res.message, "success");
                            } else {
                                Swal.fire("Error!", res.message, "error");
                            }
                        },
                        error: function () {
                            Swal.fire("Error!", "Terjadi kesalahan pada server.", "error");
                        },
                    });
                }
            });
        });
    });
</script>
@endpush
@endsection
