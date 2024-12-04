@extends('layout.base')
@section('tittle', $tittle)

@push('css')
    <link href="assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
@endpush

@section('content')
    <!-- start page title -->
    <div class="page-title-box">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <div class="page-title">
                        <h4>Tambah {{ $tittle }}</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('tasks') }}">{{ $tittle }}</a></li>
                            <li class="breadcrumb-item active">Tambah {{ $tittle }}</li>
                        </ol>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- end page title -->

    <div class="container-fluid">
        <div class="page-content-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('tasks.store') }}" method="POST" enctype="multipart/form-data"
                                class="needs-validation" novalidate>
                                @csrf
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="validationCustom01" class="form-label required">Judul</label>
                                            <input type="text" name="title" value="{{ old('title') }}"
                                                class="form-control @error('title') is-invalid @enderror"
                                                id="validationCustom01">
                                            @error('title')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="validationCustom01" class="form-label required">
                                                Project
                                            </label>
                                            <select name="project_id"
                                                class="form-control select2 @error('project_id') is-invalid @enderror"
                                                aria-label="Default select example" id="project_select">
                                                <option value="">Pilih Project</option>
                                                @foreach ($projects as $project)
                                                    <option value="{{ $project->id }}"
                                                        data-start="{{ $project->start_date }}"
                                                        data-end="{{ $project->end_date }}"
                                                        data-vendor="{{ $project->vendor->name ?? 'No Vendor' }}"
                                                        data-vendor-id="{{ $project->vendor_id }}">
                                                        {{ $project->name }} - {{ $project->vendor->name ?? 'No Vendor' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('project_id')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="parent_id" class="form-label">
                                                Sub Tugas
                                            </label>
                                            <select name="parent_id"
                                                class="form-control select2 @error('parent_id') is-invalid @enderror"
                                                id="parent_id">
                                                <option value="">Pilih Sub Tugas (Opsional)</option>
                                            </select>
                                            @error('parent_id')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div id="vendor-info"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="start_date" class="form-label required">Tanggal Mulai</label>
                                            <input type="date"
                                                class="form-control @error('start_date') is-invalid @enderror"
                                                id="start_date" name="start_date" value="{{ old('start_date') }}">
                                            @error('start_date')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="end_date" class="form-label required">Tanggal Selesai</label>
                                            <input type="date"
                                                class="form-control @error('end_date') is-invalid @enderror" id="end_date"
                                                name="end_date" value="{{ old('end_date') }}">
                                            @error('end_date')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="validationCustom01" class="form-label required">
                                                Prioritas
                                            </label>
                                            <select name="priority"
                                                class="form-control select2 @error('priority') is-invalid @enderror"
                                                aria-label="Default select example">
                                                <option selected value="">Pilih Prioritas</option>
                                                <option value="low">
                                                    Low
                                                </option>
                                                <option value="medium">Medium</option>
                                                <option value="high">High</option>
                                            </select>
                                            @error('priority')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="validationCustom01" class="form-label">
                                                Deskripsi
                                            </label>
                                            <textarea id="textarea" name="description" class="form-control @error('description') is-invalid @enderror"
                                                maxlength="225" rows="3" placeholder="Enter Description"></textarea>
                                            @error('description')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <button class="btn btn-primary" type="submit">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end row -->
        </div>
    </div>

    <!-- Validation Modal -->
    <div class="modal fade" id="validationModal" tabindex="-1" aria-labelledby="validationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="validationModalLabel">Peringatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="validationMessage"></p> <!-- This will display the validation error message -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Notification modal -->
    <div class="modal fade" id="projectAlertModal" tabindex="-1" aria-labelledby="projectAlertModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="projectAlertModalLabel">Perhatian</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Pilih project terlebih dahulu sebelum menambahkan tugas.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    @push('js')
        <!-- JAVASCRIPT -->
        <script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
        <script src="{{ asset('assets/libs/metismenu/metisMenu.min.js') }}"></script>
        <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
        <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
        <script src="{{ asset('assets/libs/parsleyjs/parsley.min.js') }}"></script>
        <script src="{{ asset('assets/js/pages/form-validation.init.js') }}"></script>
        <script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
        <script src="{{ asset('assets/js/pages/form-advanced.init.js') }}"></script>
        <script>
            $(document).ready(function() {
                // Script to handle project selection and validation
                $('#project_select').on('change', function() {
                    var selectedVendorId = $(this).find('option:selected').data('vendor-id');
                    var selectedProject = $(this).find('option:selected');
                    var projectStartDate = selectedProject.data('start');
                    var projectEndDate = selectedProject.data('end');
                    var projectVendor = selectedProject.data('vendor');

                    // Show vendor information
                    $('#vendor-info').html(`
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <i class="mdi mdi-account text-success me-2"></i>
                                <strong>Vendor:</strong>  ${projectVendor}
                            </li>
                        </ul>
                    `);

                    // AJAX untuk memuat parent tasks sesuai vendor project
                    $.ajax({
                        url: '{{ route('tasks.get-parent-tasks', ':projectId') }}'.replace(
                            ':projectId', selectedProject.val()),
                        method: 'GET',
                        success: function(parentTasks) {
                            // Reset dropdown parent task
                            $('#parent_id').html(
                                '<option value="">Pilih Sub Tugas (Opsional)</option>');

                            // Tambahkan parent tasks yang sesuai
                            parentTasks.forEach(function(task) {
                                $('#parent_id').append(
                                    `<option 
                            value="${task.id}" 
                            data-project-id="${task.project_id}"
                            data-vendor-id="${task.vendor_id}">
                            ${task.title} 
                            (Project: ${task.project ? task.project.name : 'Tidak ada project'})
                        </option>`
                                );
                            });

                            // Inisialisasi ulang select2
                            $('#parent_id').select2();
                        },
                        error: function() {
                            console.error('Error fetching parent tasks');
                        }
                    });

                    // Validate and set dates if project dates are available
                    if (!projectStartDate || !projectEndDate) {
                        $('#projectAlertModal').modal('show');
                        // Disable start and end dates input if project dates are missing
                        $('#start_date').prop('disabled', true);
                        $('#end_date').prop('disabled', true);
                    } else {
                        // Enable the date fields and set values
                        $('#start_date').prop('disabled', false);
                        $('#end_date').prop('disabled', false);
                        $('#start_date').val(projectStartDate);
                        $('#end_date').val(projectEndDate);
                    }
                });

                // Validate start_date and end_date to be within project dates
                $('form').on('submit', function(e) {
                    var projectStartDate = $('#project_select').find('option:selected').data('start');
                    var projectEndDate = $('#project_select').find('option:selected').data('end');
                    var taskStartDate = $('#start_date').val();
                    var taskEndDate = $('#end_date').val();

                    // Check if start date is before project start date
                    if (new Date(taskStartDate) < new Date(projectStartDate)) {
                        e.preventDefault(); // Prevent form submission
                        showValidationModal(
                            'Tanggal mulai task tidak boleh lebih awal dari tanggal mulai project.');
                        return false;
                    }

                    // Check if end date is after project end date
                    if (new Date(taskEndDate) > new Date(projectEndDate)) {
                        e.preventDefault(); // Prevent form submission
                        showValidationModal(
                            'Tanggal selesai task tidak boleh lebih lambat dari tanggal selesai project.');
                        return false;
                    }
                });

                // Function to show the validation modal
                function showValidationModal(message) {
                    $('#validationMessage').text(message); // Set custom validation message
                    $('#validationModal').modal('show'); // Show the modal
                }

                // Reload page after modal is closed (optional)
                $('#validationModal').on('hidden.bs.modal', function() {
                    location.reload(); // Uncomment if you want to reload the page after closing
                });
            });
        </script>
    @endpush
@endsection
