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
                            <li class="breadcrumb-item"><a href="{{ route('review') }}">{{ $tittle }}</a></li>
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
            <!-- Project Details Container -->
            <div id="projectDetailsContainer" class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="card-title mb-0">Detail Project</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Project File Details</h6>
                                    <div id="projectFileDetails"></div>
                                </div>
                                <div class="col-md-6">
                                    <h6>Project Summary</h6>
                                    <div id="projectSummaryDetails"></div>
                                </div>
                            </div>
                            <hr />
                            <div class="row">
                                <div class="col-md-6">
                                    <h6>Project Information</h6>
                                    <ul>
                                        <li><strong>Name:</strong> {{ $projects->first()->name }}</li>
                                        <li><strong>Start Date:</strong> {{ $projects->first()->start_date }}</li>
                                        <li><strong>End Date:</strong> {{ $projects->first()->end_date }}</li>
                                        <li><strong>Description:</strong> {{ $projects->first()->description }}</li>
                                        <li><strong>Status:</strong> {{ $projects->first()->status }}</li>
                                        <li><strong>Code:</strong> {{ $projects->first()->code }}</li>
                                        <li><strong>Amount:</strong> {{ number_format($projects->first()->amount, 2, ',', '.') }}</li>
                                        <li><strong>Status Pengajuan:</strong> {{ $projects->first()->status_pengajuan }}</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('review.store') }}" method="POST" id="reviewForm"
                                class="needs-validation" novalidate>
                                @csrf
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="project_id" class="form-label required">
                                                Project
                                            </label>
                                            <select name="project_id" id="project_id"
                                                class="form-control select2 @error('project_id') is-invalid @enderror"
                                                required>
                                                <option value="">Pilih Project</option>
                                                @foreach ($projects as $project)
                                                    <option value="{{ $project->id }}"
                                                        data-project-file="{{ json_encode($project->Projectfile ?? []) }}"
                                                        data-project-summary="{{ json_encode($project->formatted_total_summary ?? '0') }}"
                                                        {{ old('project_id') == $project->id ? 'selected' : '' }}>
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

                                    @if (auth()->user()->roles->first()->name == 'Developer')
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label for="status_pengajuan" class="form-label required">
                                                    Status Pengajuan
                                                </label>
                                                <select name="status_pengajuan" id="status_pengajuan"
                                                    class="form-control select2" required>
                                                    <option value="in_review"
                                                        {{ old('status_pengajuan') == 'in_review' ? 'selected' : '' }}>In
                                                        Review</option>
                                                    <option value="approved"
                                                        {{ old('status_pengajuan') == 'approved' ? 'selected' : '' }}>
                                                        Approved</option>
                                                    <option value="rejected"
                                                        {{ old('status_pengajuan') == 'rejected' ? 'selected' : '' }}>
                                                        Rejected</option>
                                                </select>
                                            </div>
                                        </div>
                                    @elseif (auth()->user()->roles->first()->name == 'Owner')
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label for="status_pengajuan" class="form-label required">
                                                    Status Pengajuan
                                                </label>
                                                <select name="status_pengajuan" id="status_pengajuan"
                                                    class="form-control select2" required>
                                                    <option value="approved"
                                                        {{ old('status_pengajuan') == 'approved' ? 'selected' : '' }}>
                                                        Approved</option>
                                                    <option value="rejected"
                                                        {{ old('status_pengajuan') == 'rejected' ? 'selected' : '' }}>
                                                        Rejected</option>
                                                </select>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="review_note" class="form-label">
                                                Catatan Review
                                            </label>
                                            <textarea id="review_note" name="review_note" class="form-control @error('review_note') is-invalid @enderror"
                                                maxlength="255" rows="4" placeholder="Masukkan catatan review (maksimal 255 karakter)">{{ old('review_note') }}</textarea>
                                            @error('review_note')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                            <small class="text-muted form-text">Sisa karakter: <span
                                                    id="charCount">255</span></small>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('review') }}" class="btn btn-secondary">Kembali</a>
                                    <button class="btn btn-primary" type="submit">Simpan Review</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end row -->
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
            $(document).ready(function() {
                $('#project_id').on('change', function() {
                    const selectedOption = $(this).find('option:selected');
                    const projectFileData = selectedOption.data('project-file');
                    const projectSummaryData = selectedOption.data('project-summary');
                    const projectDetailsContainer = $('#projectDetailsContainer');

                    // Debugging
                    console.log("Project File Data:", projectFileData);
                    console.log("Project Summary Data:", projectSummaryData);

                    // Format the total summary using JavaScript's Intl.NumberFormat
                    const formatNumber = new Intl.NumberFormat(
                        'id-ID', { // 'id-ID' is for Indonesian format (with commas and periods)
                            style: 'decimal',
                            minimumFractionDigits: 2, // You can change this based on your needs
                            maximumFractionDigits: 2
                        });

                    if (projectFileData || projectSummaryData) {
                        // Project File Details
                        if (projectFileData) {
                            let fileDetailsHtml = `
                                <p><strong>File KMZ:</strong> ${projectFileData.kmz || 'N/A'}</p>
                                <p><strong>File Excel:</strong> ${projectFileData.excel || 'N/A'}</p>
                            `;
                            $('#projectFileDetails').html(fileDetailsHtml);
                        }

                        if (projectSummaryData) {
                            let totalSummary = projectSummaryData.total_summary || 0;
                            let formattedTotalSummary = formatNumber.format(totalSummary);

                            let summaryDetailsHtml = `
                <p><strong>Total Summary:</strong> ${formattedTotalSummary}</p>
            `;
                            $('#projectSummaryDetails').html(summaryDetailsHtml);
                        }

                        projectDetailsContainer.show();
                    } else {
                        projectDetailsContainer.hide();
                    }
                });

                // Trigger change event if a project is pre-selected
                $('#project_id').trigger('change');
            });
        </script>
    @endpush
@endsection
