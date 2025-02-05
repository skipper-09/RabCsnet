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

                                    <!-- Add this button near your project select -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <h6 class="text-primary mb-3">
                                                <i class="mdi mdi-package me-2"></i>Project Items
                                            </h6>
                                            <button type="button" class="btn btn-primary" id="viewProjectDetails" disabled>
                                                <i class="mdi mdi-eye me-1"></i>View Project Items
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Modified Modal Structure -->
                                    <div class="modal fade" id="projectDetailsModal" tabindex="-1"
                                        aria-labelledby="projectDetailsModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-xl">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="projectDetailsModalLabel">
                                                        Project Details: <span id="modalProjectNameBadge"></span>
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div id="modalProjectItems">
                                                        <!-- Project items will be dynamically inserted here -->
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Close</button>
                                                </div>
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
                                                            data-project-details="{{ json_encode(
                                                                $project->detailproject->map(function ($detail) {
                                                                    return [
                                                                        'name' => $detail->name,
                                                                        'code' => $detail->code,
                                                                        'type' => $detail->projecttype->name ?? 'N/A',
                                                                        'items' => $detail->detailitemporject->map(function ($item) {
                                                                            return [
                                                                                'item_name' => $item->item->name ?? 'N/A',
                                                                                'quantity' => $item->quantity,
                                                                                'cost_material' => $item->cost_material,
                                                                                'cost_service' => $item->cost_service,
                                                                                'service_name' => $item->service->name ?? 'N/A',
                                                                            ];
                                                                        }),
                                                                    ];
                                                                }),
                                                            ) }}"
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

                // Initialize modal and pagination settings
                const projectDetailsModal = new bootstrap.Modal(document.getElementById('projectDetailsModal'));
                const viewProjectDetailsBtn = document.getElementById('viewProjectDetails');
                const itemsPerPage = 5; // Number of items per page
                let currentPage = 1;
                let currentData = null;

                $('#project_id').on('change', function() {
                    const selectedOption = $(this).find('option:selected');
                    const projectFileData = selectedOption.data('project-file');
                    const projectSummaryData = selectedOption.data('project-summary');
                    const projectName = selectedOption.text().trim();
                    const reviewerName = selectedOption.data('project-reviewer');
                    const reviewNote = selectedOption.data('project-review-note');
                    const projectDetails = selectedOption.data('project-details');

                    // Enable/disable view button based on selection
                    viewProjectDetailsBtn.disabled = !selectedOption.val();

                    // Store the data for modal use
                    viewProjectDetailsBtn.dataset.projectData = JSON.stringify({
                        projectName,
                        projectDetails
                    });

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

                function renderPagination(totalPages, currentPage) {
                    let paginationHtml = `
        <nav aria-label="Page navigation" class="mt-3">
            <ul class="pagination justify-content-center">
                <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                    <button type="button" class="page-link" data-page="${currentPage - 1}">Previous</button>
                </li>
    `;

                    for (let i = 1; i <= totalPages; i++) {
                        paginationHtml += `
            <li class="page-item ${currentPage === i ? 'active' : ''}">
                <button type="button" class="page-link" data-page="${i}">${i}</button>
            </li>
        `;
                    }

                    paginationHtml += `
                <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                    <button type="button" class="page-link" data-page="${currentPage + 1}">Next</button>
                </li>
            </ul>
        </nav>
    `;
                    return paginationHtml;
                }

                function renderTable(items, start, end) {
                    const formatNumber = new Intl.NumberFormat('id-ID', {
                        style: 'decimal',
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    });

                    let tableHtml = `
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Item</th>
                        <th>Quantity</th>
                        <th>Material Cost</th>
                        <th>Service Cost</th>
                        <th>Service</th>
                        <th>Total Cost</th>
                    </tr>
                </thead>
                <tbody>
    `;

                    items.slice(start, end).forEach((item, index) => {
                        const materialCost = parseFloat(item.cost_material || 0);
                        const serviceCost = parseFloat(item.cost_service || 0);
                        const quantity = parseFloat(item.quantity || 0);
                        const totalCost = (materialCost + serviceCost) * quantity;

                        tableHtml += `
            <tr>
                <td class="text-center">${start + index + 1}</td>
                <td>
                    <div class="d-flex align-items-center">
                        <i class="mdi mdi-package-variant text-primary me-2"></i>
                        ${item.item_name}
                    </div>
                </td>
                <td class="text-end">${formatNumber.format(quantity)}</td>
                <td class="text-end">Rp ${formatNumber.format(materialCost)}</td>
                <td class="text-end">Rp ${formatNumber.format(serviceCost)}</td>
                <td>${item.service_name}</td>
                <td class="text-end">Rp ${formatNumber.format(totalCost)}</td>
            </tr>
        `;
                    });

                    const totalCost = items.reduce((total, item) => {
                        return total + (parseFloat(item.cost_material || 0) + parseFloat(item.cost_service ||
                            0)) * parseFloat(item.quantity || 0);
                    }, 0);

                    tableHtml += `
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th colspan="6" class="text-end">Total:</th>
                        <th class="text-end">Rp ${formatNumber.format(totalCost)}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    `;

                    return tableHtml;
                }
                // Update the event listener code to handle the pagination
                function attachPaginationHandlers(allItems, itemsPerPage) {
                    document.querySelectorAll('.pagination .page-link').forEach(button => {
                        button.addEventListener('click', function() {
                            const newPage = parseInt(this.dataset.page);
                            const totalPages = Math.ceil(allItems.length / itemsPerPage);

                            if (newPage >= 1 && newPage <= totalPages) {
                                currentPage = newPage;
                                const newStart = (currentPage - 1) * itemsPerPage;
                                const newEnd = newStart + itemsPerPage;

                                // Re-render table and pagination
                                document.getElementById('modalProjectItems').innerHTML = `
                    <div class="mb-4">
                        ${renderTable(allItems, newStart, newEnd)}
                        ${renderPagination(totalPages, currentPage)}
                    </div>
                `;

                                // Reattach event handlers to new pagination buttons
                                attachPaginationHandlers(allItems, itemsPerPage);
                            }
                        });
                    });
                }

                viewProjectDetailsBtn.addEventListener('click', function() {
                    const data = JSON.parse(this.dataset.projectData);
                    currentData = data;
                    currentPage = 1;

                    // Update modal title
                    document.getElementById('modalProjectNameBadge').textContent = data.projectName;

                    // Flatten all items from all details into a single array
                    const allItems = data.projectDetails.reduce((acc, detail) => {
                        return acc.concat(detail.items.map(item => ({
                            ...item,
                            detail_name: detail.name,
                            detail_code: detail.code,
                            detail_type: detail.type
                        })));
                    }, []);

                    // Calculate pagination
                    const totalItems = allItems.length;
                    const totalPages = Math.ceil(totalItems / itemsPerPage);
                    const start = (currentPage - 1) * itemsPerPage;
                    const end = start + itemsPerPage;

                    // Render table and pagination
                    const contentHtml = `
        <div class="mb-4">
            ${renderTable(allItems, start, end)}
            ${renderPagination(totalPages, currentPage)}
        </div>
    `;

                    document.getElementById('modalProjectItems').innerHTML = contentHtml;

                    // Attach pagination handlers
                    attachPaginationHandlers(allItems, itemsPerPage);

                    // Show the modal
                    projectDetailsModal.show();
                });

                // Trigger change event if a project is pre-selected
                $('#project_id').trigger('change');
            });
        </script>
    @endpush
@endsection
