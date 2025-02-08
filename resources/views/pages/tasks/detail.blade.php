@extends('layout.base')
@section('tittle', $tittle)

@push('css')
    <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
@endpush

@php
    use App\Models\ReportVendor;
@endphp

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

                                                    {{-- Vendor Report View Button --}}
                                                    @php
                                                        $subtaskReport = ReportVendor::where(
                                                            'task_id',
                                                            $subtask->id,
                                                        )->first();
                                                    @endphp
                                                    @if ($subtaskReport)
                                                        <div class="mt-2">
                                                            <button type="button" class="btn btn-sm btn-info"
                                                                data-bs-toggle="modal"
                                                                data-bs-target="#vendorReportModal-{{ $subtask->id }}">
                                                                <i class="mdi mdi-file-document-edit"></i> View Report
                                                            </button>
                                                        </div>

                                                        <!-- Subtask Vendor Report Modal -->
                                                        <div class="modal fade" id="vendorReportModal-{{ $subtask->id }}"
                                                            tabindex="-1"
                                                            aria-labelledby="vendorReportModalLabel-{{ $subtask->id }}"
                                                            aria-hidden="true">
                                                            <div class="modal-dialog modal-lg">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h5 class="modal-title"
                                                                            id="vendorReportModalLabel-{{ $subtask->id }}">
                                                                            Vendor Report for Subtask:
                                                                            {{ $subtask->title }}
                                                                        </h5>
                                                                        <button type="button" class="btn-close"
                                                                            data-bs-dismiss="modal"
                                                                            aria-label="Close"></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <div class="mb-3">
                                                                            <strong>Report Title:</strong>
                                                                            <p>{{ $subtaskReport->title ?? 'No title provided' }}
                                                                            </p>
                                                                        </div>

                                                                        <div class="mb-3">
                                                                            <strong>Report Description:</strong>
                                                                            <p>{{ $subtaskReport->description ?? 'No description provided' }}
                                                                            </p>
                                                                        </div>

                                                                        <div class="mb-3">
                                                                            <strong>Report Issue:</strong>
                                                                            <p>{{ $subtaskReport->issue ?? 'No issue provided' }}
                                                                            </p>
                                                                        </div>

                                                                        @if ($subtaskReport->reportImages->count() > 0)
                                                                            <div class="mb-3">
                                                                                <strong>Attached Images:</strong>
                                                                                <div class="mt-2">
                                                                                    @foreach ($subtaskReport->reportImages as $reportImage)
                                                                                        <img src="{{ asset('storage/images/reportimages/' . $reportImage->image) }}"
                                                                                            alt="Vendor Report Image"
                                                                                            class="img-fluid rounded mb-2"
                                                                                            style="max-height: 300px; object-fit: cover;">
                                                                                    @endforeach
                                                                                </div>
                                                                            </div>
                                                                        @endif

                                                                        <div class="mb-3">
                                                                            <strong>Reported At:</strong>
                                                                            <p>{{ $subtaskReport->created_at->format('d M, Y H:i') }}
                                                                            </p>
                                                                        </div>
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                        <button type="button" class="btn btn-secondary"
                                                                            data-bs-dismiss="modal">Close</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
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

                        {{-- @php
                            $mainTaskReport = ReportVendor::where('task_id', $task->id)->first();
                        @endphp

                        <div class="mb-3">
                            @if ($mainTaskReport)
                                <button type="button" class="btn btn-info" data-bs-toggle="modal"
                                    data-bs-target="#mainTaskVendorReportModal">
                                    <i class="mdi mdi-file-document-edit"></i> View Vendor Report
                                </button>

                                <!-- Main Task Vendor Report Modal -->
                                <div class="modal fade" id="mainTaskVendorReportModal" tabindex="-1"
                                    aria-labelledby="mainTaskVendorReportModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="mainTaskVendorReportModalLabel">
                                                    Vendor Report for Task: {{ $task->title }}
                                                </h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <strong>Report Title:</strong>
                                                    <p>{{ $mainTaskReport->title ?? 'No title provided' }}</p>
                                                </div>

                                                <div class="mb-3">
                                                    <strong>Report Description:</strong>
                                                    <p>{{ $mainTaskReport->description ?? 'No description provided' }}</p>
                                                </div>

                                                @if ($mainTaskReport->image)
                                                    <div class="mb-3">
                                                        <strong>Attached Image:</strong>
                                                        <div class="mt-2">
                                                            <img src="{{ asset('storage/images/reportvendor/' . $mainTaskReport->image) }}"
                                                                alt="Vendor Report Image" class="img-fluid rounded"
                                                                style="max-height: 300px; object-fit: cover;">
                                                        </div>
                                                    </div>
                                                @endif

                                                <div class="mb-3">
                                                    <strong>Reported At:</strong>
                                                    <p>{{ $mainTaskReport->created_at->format('d M, Y H:i') }}</p>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary"
                                                    data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <p class="text-warning">No vendor report found for this task</p>
                            @endif
                        </div> --}}
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

                    // Create a modal with a more detailed form
                    Swal.fire({
                        title: 'Laporan Tugas',
                        html: `
            <form id="taskReportForm" class="text-start needs-validation" novalidate>
                <div class="form-group">
                    <label for="description" class="form-label required">Deskripsi Laporan (Wajib)</label>
                    <textarea 
                        id="description" 
                        name="description" 
                        class="form-control" 
                        placeholder="Masukkan deskripsi laporan" 
                        rows="4" 
                        required
                    ></textarea>
                    <div class="invalid-feedback">Deskripsi laporan wajib diisi</div>
                </div>
                <div class="form-group mt-3">
                    <label for="issue" class="form-label">Kendala/Masalah (Opsional)</label>
                    <textarea 
                        id="issue" 
                        name="issue" 
                        class="form-control" 
                        placeholder="Masukkan kendala atau masalah" 
                        rows="4"
                    ></textarea>
                </div>
                <div class="form-group mt-3">
                    <label for="images" class="form-label">Unggah Gambar</label>
                    <input 
                        type="file" 
                        name="images[]" 
                        id="images" 
                        class="form-control" 
                        accept="image/jpeg,image/png,image/jpg,image/gif" 
                        multiple
                    >
                    <small class="text-muted">Format yang diterima: JPEG, PNG, JPG, GIF. Ukuran maksimal: 5MB per gambar</small>
                    <div class="preview-container mt-2" style="display:none;">
                        <div id="imagePreviews"></div>
                    </div>
                </div>
            </form>
        `,
                        showCancelButton: true,
                        confirmButtonText: 'Kirim Laporan',
                        cancelButtonText: 'Batal',
                        preConfirm: () => {
                            const form = document.getElementById('taskReportForm');

                            // HTML5 form validation
                            if (!form.checkValidity()) {
                                form.classList.add('was-validated');
                                return false;
                            }

                            const description = document.getElementById('description').value.trim();
                            const issue = document.getElementById('issue').value.trim();
                            const imageFiles = document.getElementById('images').files;

                            // Create FormData for file upload
                            const formData = new FormData();
                            formData.append('task_id', taskId);
                            formData.append('description', description);
                            formData.append('issue', issue);

                            // Append images to the FormData object
                            Array.from(imageFiles).forEach(file => {
                                formData.append('images[]', file);
                            });

                            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

                            // AJAX submission with improved error handling
                            return $.ajax({
                                url: '{{ route('tasks.report') }}',
                                method: 'POST',
                                data: formData,
                                processData: false,
                                contentType: false,
                                dataType: 'json',
                                xhr: function() {
                                    const xhr = new window.XMLHttpRequest();
                                    xhr.upload.addEventListener('progress', function(
                                        evt) {
                                        if (evt.lengthComputable) {
                                            const percentComplete = evt.loaded /
                                                evt.total * 100;
                                            Swal.update({
                                                title: 'Mengunggah...',
                                                html: `Progress: ${Math.round(percentComplete)}%`
                                            });
                                        }
                                    }, false);
                                    return xhr;
                                }
                            }).fail(function(xhr) {
                                Swal.showValidationMessage(
                                    xhr.responseJSON?.message ||
                                    'Terjadi kesalahan saat melaporkan tugas'
                                );
                            });
                        },
                        didRender: () => {
                            // Image preview functionality
                            const imageInput = document.getElementById('images');
                            const imagePreviews = document.getElementById('imagePreviews');
                            const previewContainer = document.querySelector('.preview-container');

                            imageInput.addEventListener('change', function(e) {
                                imagePreviews.innerHTML = '';
                                const files = e.target.files;
                                if (files.length > 0) {
                                    previewContainer.style.display = 'block';
                                    Array.from(files).forEach(file => {
                                        const reader = new FileReader();
                                        reader.onload = function(event) {
                                            const img = document.createElement(
                                                'img');
                                            img.src = event.target.result;
                                            img.style.maxHeight = '200px';
                                            img.style.marginRight = '10px';
                                            img.classList.add('img-fluid');
                                            imagePreviews.appendChild(img);
                                        };
                                        reader.readAsDataURL(file);
                                    });
                                } else {
                                    previewContainer.style.display = 'none';
                                }
                            });

                            // Ensure description textarea is focused
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

                            // Safely reload the table if it exists
                            if (typeof table !== 'undefined' && table.ajax) {
                                table.ajax.reload(null, false);
                            }
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
