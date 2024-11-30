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
                                    <div class="col-md-6">
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
                                                Sub Tugas
                                            </label>
                                            <select name="parent_id"
                                                class="form-control select2 @error('parent_id') is-invalid @enderror"
                                                aria-label="Default select example" id="parent_id">
                                                <option selected>Pilih Sub Tugas</option>
                                                @foreach ($parentTasks as $project)
                                                    <option value="{{ $parentTask->id }}">{{ $parentTask->title }}
                                                    </option>
                                                @endforeach
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
                                            <label for="validationCustom01" class="form-label required">
                                                Project
                                            </label>
                                            <select name="project_id"
                                                class="form-control select2 @error('project_id') is-invalid @enderror"
                                                aria-label="Default select example" id="project_select">
                                                <option selected>Pilih Project</option>
                                                @foreach ($projects as $project)
                                                    <option value="{{ $project->id }}"
                                                        data-start="{{ $project->start_date }}"
                                                        data-end="{{ $project->end_date }}">
                                                        {{ $project->name }}
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
                                            <label for="validationCustom01" class="form-label required">
                                                Vendor
                                            </label>
                                            <select name="vendor_id"
                                                class="form-control select2 @error('vendor_id') is-invalid @enderror"
                                                aria-label="Default select example">
                                                <option selected>Pilih Vendor</option>
                                                @foreach ($vendors as $vendor)
                                                    <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('vendor_id')
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
                    Project yang Anda pilih belum dimulai. Harap mulai project terlebih dahulu.
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
        <script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('assets/libs/metismenu/metisMenu.min.js') }}"></script>
        <script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
        <script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
        <script src="{{ asset('assets/libs/parsleyjs/parsley.min.js') }}"></script>
        <script src="{{ asset('assets/js/pages/form-validation.init.js') }}"></script>
        <script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
        <script src="{{ asset('assets/js/pages/form-advanced.init.js') }}"></script>
        <script>
            // Script to handle project selection and validation
            $('#project_select').on('change', function() {
                var selectedProject = $(this).find('option:selected');
                var projectStartDate = selectedProject.data('start');
                var projectEndDate = selectedProject.data('end');

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
                    showValidationModal('Tanggal mulai task tidak boleh lebih awal dari tanggal mulai project.');
                    return false;
                }

                // Check if end date is after project end date
                if (new Date(taskEndDate) > new Date(projectEndDate)) {
                    e.preventDefault(); // Prevent form submission
                    showValidationModal('Tanggal selesai task tidak boleh lebih lambat dari tanggal selesai project.');
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
        </script>
    @endpush
@endsection
