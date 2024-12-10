@extends('layout.base')

@section('tittle', $tittle)

@push('css')
    <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text-css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.css">
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
                            <div class="card-title d-flex justify-content-between align-items-center mb-2">
                                <ul class="nav nav-pills gap-2 mb-3" id="task-view-tabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="list-tab" data-bs-toggle="pill"
                                            data-bs-target="#list-view" type="button" role="tab">
                                            List Tasks
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="kanban-tab" data-bs-toggle="pill"
                                            data-bs-target="#kanban-view" type="button" role="tab">
                                            Kanban
                                        </button>
                                    </li>
                                </ul>
                                @can('create-tasks')
                                    <div class="mb-3">
                                        <a href="{{ route('tasks.add') }}" class="btn btn-primary btn-sm">Tambah
                                            {{ $tittle }}</a>
                                    </div>
                                @endcan
                            </div>
                            <div class="tab-content" id="task-view-content">
                                <!-- List View Tab -->
                                <div class="tab-pane fade show active" id="list-view" role="tabpanel">
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
                                                <select class="form-control select2" id="FilterProject"
                                                    name="project_filter">
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
                                                    <th style="width: 15%">Judul</th>
                                                    <th style="width: 15%">Project</th>
                                                    <th style="width: 15%">Vendor</th>
                                                    <th style="width: 10%">Tanggal Mulai</th>
                                                    <th style="width: 10%">Tanggal Selesai</th>
                                                    <th style="width: 10%">Status</th>
                                                    <th style="width: 10%">Prioritas</th>
                                                    <th style="width: 10%" class="text-center">Action</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="kanban-view" role="tabpanel">
                                    <div class="row">
                                        @foreach ($statuses as $statusKey => $statusLabel)
                                            <div class="col-md-4">
                                                <div class="card border-1">
                                                    <div class="card-body">
                                                        {{ $statusLabel }} <!-- Human-readable status label -->
                                                    </div>
                                                    <div class="card-footer kanban-column"
                                                        data-status="{{ $statusKey }}">
                                                        @forelse ($kanbanTasks->get($statusKey, collect()) as $task)
                                                            <div class="card mb-2 task-card"
                                                                data-task-id="{{ $task->id }}">
                                                                <div class="card-body">
                                                                    <h6 class="card-title"><a
                                                                            href="{{ route('tasks.details', ['id' => $task->id]) }}">
                                                                            {{ $task->title }}
                                                                        </a></h6>
                                                                    <p class="card-text small">
                                                                        Proyek: {{ $task->project->name ?? 'N/A' }}
                                                                    </p>
                                                                    <div
                                                                        class="d-flex justify-content-between align-items-center">
                                                                        <span
                                                                            class="badge 
                                                                            @switch($task->priority)
                                                                                @case('low') bg-info @break
                                                                                @case('medium') bg-warning @break
                                                                                @case('high') bg-danger @break
                                                                            @endswitch
                                                                        ">
                                                                            {{ ucfirst($task->priority) }}
                                                                        </span>
                                                                        <small>{{ $task->start_date }} -
                                                                            {{ $task->end_date }}</small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @empty
                                                            <div></div>
                                                        @endforelse
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
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
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
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
                $('#FilterVendor, #FilterProject').select2({
                    placeholder: "Select an option",
                });
                var table = $("#datatable").DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: {
                        url: '{{ route('tasks.getdata') }}',
                        type: 'GET',
                        data: function(d) {
                            d.vendor_filter = $('#FilterVendor').find(":selected").val();
                            d.project_filter = $('#FilterProject').find(":selected").val();
                        }
                    },
                    columns: [{
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
                        @canany(['complete-tasks', 'update-tasks', 'delete-tasks'])
                            {
                                data: 'action',
                                name: 'action',
                                orderable: false,
                                searchable: false,
                                class: 'text-center'
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

                // Handle task completion toggle
                $('#datatable').on('click', '.task-completion-button', function() {
                    const taskId = $(this).data('id');
                    const button = $(this);
                    const currentStatus = button.hasClass('btn-success') ? 'complated' : 'in_progres';

                    $.ajax({
                        url: `{{ route('tasks.toggle-completion', ':id') }}`.replace(':id', taskId),
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            status: currentStatus
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                // Reload the datatable to reflect changes
                                table.ajax.reload(null, false);

                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    icon: 'success',
                                    title: response.message,
                                    showConfirmButton: false,
                                    timer: 3000
                                });
                            } else {
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

                // Image preview function
                function previewImage(input) {
                    const preview = document.getElementById('imagePreview');
                    const previewContainer = preview.closest('.preview-container');

                    if (input.files && input.files[0]) {
                        const reader = new FileReader();

                        reader.onload = function(e) {
                            preview.src = e.target.result;
                            preview.style.display = 'block';
                            previewContainer.style.display = 'block';
                        }

                        reader.readAsDataURL(input.files[0]);

                        // Validate file size
                        const fileSize = input.files[0].size / 1024 / 1024; // in MB
                        if (fileSize > 5) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Ukuran File Terlalu Besar',
                                text: 'Ukuran gambar maksimal 5MB'
                            });
                            input.value = ''; // Clear the file input
                            preview.src = '#';
                            preview.style.display = 'none';
                            previewContainer.style.display = 'none';
                        }
                    } else {
                        preview.src = '#';
                        preview.style.display = 'none';
                        previewContainer.style.display = 'none';
                    }
                }

                // Task report button click handler
                $('#datatable').on('click', '.task-report-button', function() {
                    const taskId = $(this).data('id');

                    // Create a modal with more detailed form
                    Swal.fire({
                        title: 'Laporan Tugas',
                        html: `
                            <form id="taskReportForm">
                                <div class="form-group">
                                    <label for="description" class="form-label">Deskripsi Laporan (Wajib)</label>
                                    <textarea id="description" name="description" class="form-control" placeholder="Masukkan deskripsi laporan" rows="4" required></textarea>
                                </div>
                                <div class="form-group mt-3">
                                    <label for="image" class="form-label">Unggah Gambar</label>
                                    <input type="file" name="image" id="image" 
                                        class="form-control" 
                                        accept="image/*"
                                        onchange="previewImage(this)">
                                    <small class="text-muted">Format yang diterima: JPEG, PNG, JPG, GIF. Ukuran maksimal: 5MB</small>
                                    <div class="preview-container mt-2" style="display:none;">
                                        <img id="imagePreview" src="#" alt="Preview" class="img-fluid" style="max-height: 200px; display:none;">
                                    </div>
                                </div>
                            </form>
                        `,
                        showCancelButton: true,
                        confirmButtonText: 'Kirim Laporan',
                        cancelButtonText: 'Batal',
                        preConfirm: () => {
                            const description = document.getElementById('description').value;
                            const imageFile = document.getElementById('image').files[0];

                            // Validate description
                            if (!description.trim()) {
                                Swal.showValidationMessage('Deskripsi laporan wajib diisi');
                                return false;
                            }

                            // Create FormData for file upload
                            const formData = new FormData();
                            formData.append('task_id', taskId);
                            formData.append('description', description);
                            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

                            // Append image if selected
                            if (imageFile) {
                                // Additional file type validation
                                const validTypes = ['image/jpeg', 'image/png', 'image/jpg',
                                    'image/gif'
                                ];
                                if (!validTypes.includes(imageFile.type)) {
                                    Swal.showValidationMessage('Format gambar tidak valid');
                                    return false;
                                }

                                // File size validation (5MB)
                                if (imageFile.size > 5 * 1024 * 1024) {
                                    Swal.showValidationMessage('Ukuran gambar maksimal 5MB');
                                    return false;
                                }

                                formData.append('image', imageFile);
                            }

                            return $.ajax({
                                url: '{{ route('tasks.report') }}',
                                method: 'POST',
                                data: formData,
                                processData: false,
                                contentType: false,
                                dataType: 'json'
                            }).fail(function(xhr) {
                                Swal.showValidationMessage(
                                    xhr.responseJSON.message ||
                                    'Terjadi kesalahan saat melaporkan tugas'
                                );
                            });
                        },
                        didRender: () => {
                            // Ensure textarea is focused
                            document.getElementById('description').focus();
                        },
                        allowOutsideClick: () => !Swal.isLoading()
                    }).then((result) => {
                        if (result.isConfirmed && result.value.success) {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: result.value.message,
                                showConfirmButton: false,
                                timer: 3000
                            });

                            // Reload the table
                            table.ajax.reload(null, false);
                        }
                    });
                });

                // Inisialisasi drag and drop untuk kanban
                $('.kanban-column').sortable({
                    connectWith: '.kanban-column',
                    placeholder: 'task-placeholder',
                    handle: '.card-body',
                    cursor: 'move',
                    tolerance: 'pointer',

                    // Before the move starts
                    start: function(event, ui) {
                        ui.item.addClass('dragging');
                        ui.placeholder.height(ui.item.outerHeight());
                    },

                    // When dragging stops
                    stop: function(event, ui) {
                        ui.item.removeClass('dragging');
                    },

                    // When item is updated in a column
                    update: function(event, ui) {
                        // Check if the item has actually changed columns
                        if (this === ui.item.parent()[0]) {
                            var taskId = ui.item.data('task-id');
                            var newStatus = ui.item.parent().data('status');

                            // Disable sorting during AJAX to prevent multiple requests
                            $('.kanban-column').sortable('disable');

                            $.ajax({
                                url: `{{ route('tasks.update-status', ':id') }}`.replace(':id',
                                    taskId),
                                method: 'PATCH',
                                data: {
                                    status: newStatus,
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function(response) {
                                    // Show success SweetAlert
                                    Swal.fire({
                                        toast: true,
                                        position: 'top-end',
                                        icon: 'success',
                                        title: response.message ||
                                            'Status tugas berhasil diperbarui',
                                        showConfirmButton: false,
                                        timer: 3000
                                    });

                                    // Optional: Reload the DataTable
                                    $('#datatable').DataTable().ajax.reload(null, false);
                                },
                                error: function(xhr) {
                                    // Revert the sorting if the update fails
                                    $(event.target).sortable('cancel');

                                    // Show error SweetAlert with more detailed message
                                    Swal.fire({
                                        toast: true,
                                        position: 'top-end',
                                        icon: 'error',
                                        title: xhr.responseJSON?.message ||
                                            'Gagal memperbarui status task',
                                        showConfirmButton: false,
                                        timer: 3000
                                    });
                                },
                                complete: function() {
                                    // Re-enable sorting
                                    $('.kanban-column').sortable('enable');
                                }
                            });
                        }
                    }
                });

                // Add some CSS to improve preview styling
                const style = document.createElement('style');
                style.innerHTML = `
                    .preview-container {
                        margin-top: 10px;
                        text-align: center;
                    }
                    .image-preview {
                        max-height: 200px;
                        max-width: 100%;
                        object-fit: contain;
                        display: none;
                    }
                    `;
                document.head.appendChild(style);

                $('<style>')
                    .prop('type', 'text/css')
                    .html(`
            .dragging {
                opacity: 0.5;
                transform: scale(1.02);
                transition: all 0.2s ease;
            }
            .task-placeholder {
                background-color: #f0f0f0;
                border: 2px dashed #007bff;
                margin-bottom: 10px;
                visibility: visible !important;
            }
            .kanban-column .card-body {
                min-height: 100px;
            }
            .ui-sortable-handle {
                cursor: move;
            }
        `)
                    .appendTo('head');
            });
        </script>
    @endpush
@endsection
