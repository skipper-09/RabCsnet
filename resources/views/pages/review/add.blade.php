@extends('layout.base')
@section('tittle', $tittle)

@push('css')
    <link href="assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
@endpush

@section('content')
    @if (session('status') && session('message'))
        <div class="alert alert-{{ strtolower(session('status')) == 'success' ? 'success' : 'danger' }} alert-dismissible fade show"
            role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
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
            {{-- Project Details Section --}}
            @if ($projects->isNotEmpty())
                <div id="projectDetailsContainer" class="row" style="display:none;">
                    <div class="col-12">
                        <div class="card border-primary">
                            <div
                                class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">
                                    <i class="mdi mdi-information-outline me-2"></i>Project Details
                                </h5>
                                <span class="badge bg-light text-primary" id="projectNameBadge"></span>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div id="projectFileDetails" class="mb-3">
                                            <h6 class="text-primary mb-3">
                                                <i class="mdi mdi-file-document-outline me-2"></i>Project Files
                                            </h6>
                                            <div class="alert alert-soft-primary" role="alert" id="fileDetailsContent">
                                                No files available
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div id="projectInfo" class="mb-3">
                                            <h6 class="text-primary mb-3">
                                                <i class="mdi mdi-chart-line me-2"></i>Project Information
                                            </h6>
                                            <div class="alert alert-soft-success" role="alert" id="projectInfoContent">
                                                No information available
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div id="projectSummaryDetails" class="mb-3">
                                            <h6 class="text-primary mb-3">
                                                <i class="mdi mdi-chart-line me-2"></i>Project Summary
                                            </h6>
                                            <div class="alert alert-soft-success" role="alert" id="summaryDetailsContent">
                                                No summary available
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-info" role="alert">
                    No projects available for review at this moment.
                </div>
            @endif
            {{-- Review Form --}}
            @if ($projects->isNotEmpty())
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card">
                            <div class="card-body">
                                <form action="{{ route('review.store') }}" method="POST" id="reviewForm"
                                    class="needs-validation" novalidate>
                                    @csrf

                                    {{-- Project Selection --}}
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label for="project_id" class="form-label required">Project</label>
                                                <select name="project_id" id="project_id"
                                                    class="form-control select2 @error('project_id') is-invalid @enderror"
                                                    required>
                                                    <option value="">Pilih Project</option>
                                                    @foreach ($projects as $project)
                                                        <option value="{{ $project->id }}"
                                                            data-project-file="{{ json_encode($project->Projectfile ?? []) }}"
                                                            data-project-summary="{{ $project->formatted_total_summary ?? '0' }}"
                                                            data-project-vendor="{{ $project->vendor->name ?? 'Belum ditentukan' }}"
                                                            data-project-amount="{{ $project->amount ?? '0' }}"
                                                            data-project-reviewer="{{ $project->reviewed_by }}"
                                                            data-project-review-note="{{ $project->review_note }}"
                                                            {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                                            {{ $project->name }} - {{ $project->code }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('project_id')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Status Pengajuan for Developer and Owner --}}
                                        @if (in_array(auth()->user()->roles->first()->name, ['Developer', 'Owner']))
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label for="status_pengajuan" class="form-label required">
                                                        Review Status
                                                    </label>
                                                    <select name="status_pengajuan" id="status_pengajuan"
                                                        class="form-control select2" required>
                                                        @if (auth()->user()->roles->first()->name == 'Developer')
                                                            <option value="in_review">In Review</option>
                                                            <option value="approved">Approved</option>
                                                            <option value="rejected">Rejected</option>
                                                            <option value="revision">Revisi</option>
                                                        @else
                                                            <option value="approved">Approved</option>
                                                            <option value="rejected">Rejected</option>
                                                            <option value="revision">Revisi</option>
                                                        @endif
                                                    </select>
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Review Note --}}
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label for="review_note" class="form-label">
                                                    Review Notes
                                                </label>
                                                <textarea id="review_note" name="review_note" class="form-control @error('review_note') is-invalid @enderror"
                                                    maxlength="255" rows="4" placeholder="Enter review notes (max 255 characters)">{{ old('review_note') }}</textarea>
                                                @error('review_note')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted form-text">
                                                    Characters remaining: <span id="charCount">255</span>
                                                </small>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Form Actions --}}
                                    <div class="d-flex justify-content-between">
                                        <a href="{{ route('review') }}" class="btn btn-secondary">Back</a>
                                        <button class="btn btn-primary" type="submit">Save Review</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
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
                // Character count for review note
                $('#review_note').on('input', function() {
                    const maxLength = 255;
                    const currentLength = $(this).val().length;
                    $('#charCount').text(maxLength - currentLength);
                });

                $('#project_id').on('change', function() {
                    const selectedOption = $(this).find('option:selected');
                    const projectFileData = selectedOption.data('project-file');
                    const projectSummaryData = selectedOption.data('project-summary');
                    const projectName = selectedOption.text().trim();
                    const reviewerName = selectedOption.data('project-reviewer');
                    const reviewNote = selectedOption.data('project-review-note');

                    const projectDetailsContainer = $('#projectDetailsContainer');
                    const projectFileDetailsContainer = $('#fileDetailsContent');
                    const projectSummaryDetailsContainer = $('#summaryDetailsContent');
                    const projectInfoContainer = $('#projectInfoContent');
                    const projectNameBadge = $('#projectNameBadge');

                    // Clear previous details
                    projectFileDetailsContainer.html('No files available');
                    projectSummaryDetailsContainer.html('No summary available');
                    projectInfoContainer.html('No information available');
                    projectNameBadge.text(projectName);

                    console.log("Raw Project File Data:", projectFileData);
                    console.log("Raw Project Summary Data:", projectSummaryData);

                    // Clear previous details
                    projectFileDetailsContainer.html('No files available');
                    projectSummaryDetailsContainer.html('No summary available');
                    projectNameBadge.text(projectName);

                    // Format the total summary using JavaScript's Intl.NumberFormat
                    const formatNumber = new Intl.NumberFormat('id-ID', {
                        style: 'decimal',
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });

                    // Project File Details
                    if (projectFileData && (projectFileData.excel || projectFileData.kmz)) {
                        let fileDetailsHtml = `
        <ul class="list-unstyled mb-0">
            ${projectFileData.excel ? `
                        <li class="mb-2">
                            <i class="mdi mdi-file-excel text-success me-2"></i>
                            <strong>Excel File:</strong> 
                            <a href="{{ asset('storage/files/excel/${projectFileData.excel}') }}" download>${projectFileData.excel}</a>
                        </li>` : ''}
            ${projectFileData.kmz ? `
                        <li class="mb-2">
                            <i class="mdi mdi-map text-danger me-2"></i>
                            <strong>KMZ File:</strong> 
                            <a href="{{ asset('storage/files/kmz/${projectFileData.kmz}') }}" download>${projectFileData.kmz}</a>
                        </li>` : ''}
        </ul>
    `;
                        projectFileDetailsContainer.html(fileDetailsHtml);
                    }

                    // Project Summary Details
                    if (projectSummaryData) {
                        // Remove any existing formatting and convert to a valid number
                        const cleanedSummary = projectSummaryData.replace(/\./g, '').replace(',', '.');
                        const numericSummary = parseFloat(cleanedSummary);

                        if (!isNaN(numericSummary)) {
                            const formattedTotalSummary = formatNumber.format(numericSummary);

                            let summaryDetailsHtml = `
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span>
                                                    <i class="mdi mdi-cash me-2 text-success"></i>
                                                    <strong>Total Summary:</strong>
                                                </span>
                                                <span class="h5 mb-0 text-primary">Rp ${formattedTotalSummary}</span>
                                            </div>
                                        `;
                            projectSummaryDetailsContainer.html(summaryDetailsHtml);
                        }
                    }

                    // Ambil nilai amount langsung tanpa perlu manipulasi string
                    const numericAmount = selectedOption.data('project-amount');

                    // Pastikan nilai amount valid
                    if (!isNaN(numericAmount)) {
                        // Format angka ke dalam format yang diinginkan (contohnya format uang dengan ribuan)
                        const formattedAmount = formatNumber.format(numericAmount);

                        // Buat HTML untuk menampilkan informasi project
                        const projectInfoHtml = `
                            <ul class="list-unstyled mb-0">
                                <li class="mb-2">
                                    <i class="mdi mdi-account text-success me-2"></i>
                                    <strong>Vendor:</strong> ${selectedOption.data('project-vendor') || 'Unknown'}
                                </li>
                                <li class="mb-2">
                                    <i class="mdi mdi-cash text-primary me-2"></i>
                                    <strong>Project Amount:</strong> Rp ${formattedAmount}
                                </li>
                                <li class="mb-2">
                                    <i class="mdi mdi-account-circle text-warning me-2"></i>
                                    <strong>Reviewed By:</strong> ${reviewerName || 'Not Reviewed'}
                                </li>
                                <li class="mb-2">
                                    <i class="mdi mdi-comment-text-outline text-info me-2"></i>
                                    <strong>Review Note:</strong> ${reviewNote || 'No Review Note'}
                                </li>
                            </ul>
                        `;

                        // Update kontainer dengan HTML yang dihasilkan
                        projectInfoContainer.html(projectInfoHtml);
                    }
                    // Show container only if there's content
                    if (projectFileDetailsContainer.children().length > 0 ||
                        projectSummaryDetailsContainer.children().length > 0 ||
                        projectInfoContainer.children().length > 0) {
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
