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
                                                    <form
                                                        action="{{ route('tasks.toggle-completion', ['id' => $subtask->id]) }}"
                                                        method="POST">
                                                        @csrf
                                                        <input type="checkbox" class="task-completion-checkbox"
                                                            data-id="{{ $subtask->id }}"
                                                            {{ $subtask->status === 'complated' ? 'checked' : '' }}>
                                                    </form>
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
                // Handle task completion toggle
                $('#datatable').on('change', '.task-completion-checkbox', function() {
                    const taskId = $(this).data('id');
                    const isChecked = $(this).is(':checked');
                    const checkbox = $(this);
                    const row = checkbox.closest('tr'); // The row containing this checkbox

                    $.ajax({
                        url: `{{ route('tasks.toggle-completion', ':id') }}`.replace(':id', taskId),
                        type: 'POST',
                        data: {
                            _token: $('meta[name="csrf-token"]').attr('content'),
                            status: isChecked ? 'complated' : 'pending'
                        },
                        success: function(response) {
                            if (response.status === 'success') {

                                // Show success notification
                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    icon: 'success',
                                    title: response.message,
                                    showConfirmButton: false,
                                    timer: 1000
                                }).then(() => {
                                    // Reload the page after the notification disappears
                                    window.location.reload();
                                });
                            } else {
                                // Revert checkbox if failed
                                checkbox.prop('checked', !isChecked);

                                // Show error notification
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
            });
        </script>
    @endpush
@endsection
