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
                            <li class="breadcrumb-item active"><a href="{{ route('tasks') }}">Task</a></li>
                            <li class="breadcrumb-item active">{{ $tittle }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">
                            {{ $task->title }}
                            @if ($isSubTask)
                                <span class="badge bg-secondary ms-2">Sub Task</span>
                            @else
                                <span class="badge bg-primary ms-2">Main Task</span>
                            @endif

                            {{-- @php
                                $currentUser = Auth::user();
                                $currentUserRole = $currentUser->roles->first()->name;
                                $isVendor = $currentUserRole === 'Vendor';
                                $canReport =
                                    $isVendor &&
                                    $task->status === 'in_progres' &&
                                    (!$isSubTask || ($isSubTask && $task->parent_task->status === 'in_progres'));
                            @endphp

                            @if ($canReport)
                                <button class="btn btn-sm btn-primary task-report-button float-end"
                                    data-id="{{ $task->id }}">
                                    Report Task
                                </button>
                            @endif --}}
                        </h4>

                        <div class="task-description mb-4">
                            <strong>Description:</strong>
                            <p>{{ $task->description ?? 'No description provided' }}</p>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <strong>Start Date:</strong>
                                    <p>{{ $task->start_date ? \Carbon\Carbon::parse($task->start_date)->format('d M, Y') : 'Not set' }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <strong>End Date:</strong>
                                    <p>{{ $task->end_date ? \Carbon\Carbon::parse($task->end_date)->format('d M, Y') : 'Not set' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <strong>Status:</strong>
                                    <p>{!! $task->getStatusBadge() !!}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <strong>Priority:</strong>
                                    <p>{!! $task->getPriorityBadge() !!}</p>
                                </div>
                            </div>
                        </div>

                        <div class="task-progress">
                            <strong>Overall Progress:</strong>
                            <div class="progress mt-2">
                                <div class="progress-bar" role="progressbar" style="width: {{ $progressPercentage }}%"
                                    aria-valuenow="{{ $progressPercentage }}" aria-valuemin="0" aria-valuemax="100">
                                    {{ $progressPercentage }}%
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($task->subTasks->count() > 0)
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Subtasks ({{ $task->subTasks->count() }})</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="datatable">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Status</th>
                                            <th>Start Date</th>
                                            <th>End Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($task->subTasks as $subtask)
                                            <tr>
                                                <td>{{ $subtask->title }}</td>
                                                <td>{!! $subtask->getStatusBadge() !!}</td>
                                                <td>{{ $subtask->start_date ? \Carbon\Carbon::parse($subtask->start_date)->format('d M, Y') : '-' }}
                                                </td>
                                                <td>{{ $subtask->end_date ? \Carbon\Carbon::parse($subtask->end_date)->format('d M, Y') : '-' }}
                                                </td>
                                                <td>
                                                    @php
                                                        $currentUser = Auth::user();
                                                        $currentUserRole = $currentUser->roles->first()->name;
                                                        $isVendor = $currentUserRole === 'Vendor';
                                                        $canReportSubtask =
                                                            $isVendor &&
                                                            $subtask->status === 'in_progres' &&
                                                            $task->status === 'in_progres';
                                                    @endphp

                                                    @if ($canReportSubtask)
                                                        <button class="btn btn-sm btn-primary task-report-button"
                                                            data-id="{{ $subtask->id }}">
                                                            Report
                                                        </button>
                                                    @elseif (!$isVendor)
                                                        <input type="checkbox" class="task-completion-checkbox"
                                                            data-id="{{ $subtask->id }}"
                                                            {{ $subtask->status === 'complated' ? 'checked' : '' }}>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Task Information</h4>

                        <div class="mb-3">
                            <strong>Project:</strong>
                            <p>{{ $task->project->name ?? 'No Project' }}</p>
                        </div>

                        <div class="mb-3">
                            <strong>Vendor:</strong>
                            <p>{{ $task->vendor->name ?? 'No Vendor' }}</p>
                        </div>

                        <div class="mb-3">
                            <strong>Created At:</strong>
                            <p>{{ $task->created_at->format('d M, Y H:i') }}</p>
                        </div>

                        @if ($isSubTask && $parentTask)
                            <div class="mb-3">
                                <strong>Parent Task:</strong>
                                <p>
                                    <a href="{{ route('tasks.details', $parentTask->id) }}">
                                        {{ $parentTask->title }}
                                    </a>
                                </p>
                            </div>
                        @endif
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
            $(document).ready(function() {
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

                // Task completion toggle for non-vendors
                @if (!$isVendor)
                    $('.task-completion-checkbox').on('change', function() {
                        const taskId = $(this).data('id');
                        const isChecked = $(this).is(':checked');
                        const checkbox = $(this);

                        $.ajax({
                            url: `{{ route('tasks.toggle-completion', ':id') }}`.replace(':id',
                                taskId),
                            type: 'POST',
                            data: {
                                _token: $('meta[name="csrf-token"]').attr('content'),
                                status: isChecked ? 'complated' : 'pending'
                            },
                            success: function(response) {
                                if (response.status === 'success') {
                                    Swal.fire({
                                        toast: true,
                                        position: 'top-end',
                                        icon: 'success',
                                        title: response.message,
                                        showConfirmButton: false,
                                        timer: 1000
                                    }).then(() => {
                                        window.location.reload();
                                    });
                                } else {
                                    // Revert checkbox if failed
                                    checkbox.prop('checked', !isChecked);

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
                                checkbox.prop('checked', !isChecked);

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
                @endif
            });
        </script>
    @endpush
@endsection
