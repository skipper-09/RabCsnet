@extends('layout.base')

@section('tittle', $tittle)

@push('css')
    <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
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
                                <a href="{{ route('tasks.add') }}" class="btn btn-primary btn-sm">Tambah
                                    {{ $tittle }}</a>
                            </div>
                            <table id="datatable" class="table table-responsive table-hover" style="width: 100%;">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 5%">No</th>
                                        <th style="width: 15%">Judul</th>
                                        <th style="width: 15%">Project</th>
                                        <th style="width: 15%">Vendor</th>
                                        <th style="width: 10%">Mulai</th>
                                        <th style="width: 10%">Selesai</th>
                                        <th style="width: 10%">Status</th>
                                        <th style="width: 10%">Prioritas</th>
                                        <th style="width: 5%" class="text-center">Selesai</th>
                                        <th style="width: 10%" class="text-center">Action</th>
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
        {{-- custom sweetalert --}}
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
                // Initialize DataTable
                var table = $("#datatable").DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '{{ route('tasks.getdata') }}',
                    columns: [
                        {
                            data: 'DT_RowIndex',
                            orderable: false,
                            searchable: false,
                            class: 'text-center',
                        },
                        {
                            data: 'title',
                            name: 'title'
                        },
                        {
                            data: 'project',
                            name: 'project.name'
                        },
                        {
                            data: 'vendor',
                            name: 'vendor.name'
                        },
                        {
                            data: 'start_date',
                            name: 'start_date'
                        },
                        {
                            data: 'end_date',
                            name: 'end_date'
                        },
                        {
                            data: 'status',
                            name: 'status'
                        },
                        {
                            data: 'priority',
                            name: 'priority'
                        },
                        {
                            data: 'completion',
                            name: 'completion',
                            orderable: false,
                            searchable: false,
                            class: 'text-center'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false,
                            class: 'text-center'
                        }
                    ],
                });

                $(".dataTables_length select").addClass("form-select form-select-sm");

                // Handle task completion toggle
                $('#datatable').on('change', '.task-completion-checkbox', function() {
                    const taskId = $(this).data('id');
                    const checkbox = $(this);
                    
                    $.ajax({
                        url: `{{ route('tasks.toggle-completion', ':id') }}`.replace(':id', taskId),
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                // Reload the datatable to reflect changes
                                table.ajax.reload(null, false);
                                
                                // Show success toast
                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    icon: 'success',
                                    title: response.message,
                                    showConfirmButton: false,
                                    timer: 3000
                                });
                            } else {
                                // Revert checkbox if failed
                                checkbox.prop('checked', !checkbox.is(':checked'));
                                
                                // Show error toast
                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    icon: 'error',
                                    title: response.message,
                                    showConfirmButton: false,
                                    timer: 3000
                                });
                            }
                        },
                        error: function() {
                            // Revert checkbox if failed
                            checkbox.prop('checked', !checkbox.is(':checked'));
                            
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'error',
                                title: 'Failed to update task status',
                                showConfirmButton: false,
                                timer: 3000
                            });
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection