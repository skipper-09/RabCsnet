@extends('layout.base')
@section('tittle', $tittle)

@push('css')
    <link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}"
        rel="stylesheet" />
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
                <!-- Project Details Container -->
                <div id="projectDetailsContainer" class="row" style="display:none;">
                    <div class="col-12">
                        <div class="card border-primary shadow-lg rounded">
                            <div
                                class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">
                                    <i class="mdi mdi-information-outline me-2"></i> Project Details
                                </h5>
                                <span class="badge bg-light text-primary p-2" id="projectNameBadge"></span>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <!-- Project Information -->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <h6 class="text-primary mb-3">
                                                <i class="mdi mdi-chart-line me-2"></i> Project Information
                                            </h6>
                                            <div class="table-responsive">
                                                <table class="table table-hover">
                                                    <tbody>
                                                        <tr>
                                                            <th>Vendor</th>
                                                            <td id="projectVendor"></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Total Amount</th>
                                                            <td id="projectAmount"></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Reviewer</th>
                                                            <td id="projectReviewer"></td>
                                                        </tr>
                                                        <tr>
                                                            <th>Review Note</th>
                                                            <td id="projectReviewNote"></td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Project Files -->
                                    <div class="col-md-6">
                                        <div id="projectFileDetails" class="mb-3">
                                            <h6 class="text-primary mb-3">
                                                <i class="mdi mdi-file-document-outline me-2"></i> Project Files
                                            </h6>
                                            <div id="fileDetailsContent" class="list-group shadow-sm"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div id="projectLisence" class="mb-3">
                                            <h6 class="text-primary mb-3">
                                                <i class="mdi mdi-file-alert me-2"></i> Perijinan Project
                                            </h6>
                                            <div id="lisence" class=" d-flex flex-column"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div id="projectFileDetails" class="mb-3">
                                            <h6 class="text-primary mb-3">
                                                <i class="mdi mdi-location-enter me-2"></i> Detail Distribusi
                                            </h6>
                                            <div id="distribusi" class=" d-flex flex-column"></div>
                                        </div>
                                    </div>

                                    <!-- Project Summary -->
                                    <div class="col-md-4">
                                        <div id="projectSummaryDetails" class="mb-3">
                                            <h6 class="text-primary mb-3">
                                                <i class="mdi mdi-chart-line me-2"></i> Project Summary
                                            </h6>
                                            <div class="alert alert-info shadow-sm" id="summaryDetailsContent">
                                                No summary available
                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Modal Template untuk setiap detail proyek -->
                <div id="modalProjectDetailTemplate" class="modal" style="display: none;">
                    <div class="modal-dialog modal-fullscreen">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title mt-0" id="modalProjectTitle"></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body" id="modalProjectDetails">
                                <table id="dataTableItemDetails" class="table table-bordered dt-responsive nowrap"
                                    style="border-collapse: collapse; border-spacing: 0; width: 100%;">
                                    <thead>
                                        <tr>
                                            <th>Item Name</th>
                                            <th>Qty</th>
                                            <th>Material Cost</th>
                                            <th>Service Cost</th>
                                        </tr>
                                    </thead>
                                    <tbody id="itemDetailsBody">
                                        <!-- Data item akan dimuat disini -->
                                    </tbody>
                                </table>
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
                                                            data-project-lisence="{{ json_encode(
                                                                $project->projectlisence->map(function ($lisence) {
                                                                    return [
                                                                        'name' => $lisence->name,
                                                                        'price' => $lisence->price,
                                                                        'perijinan_file' => $lisence->perijinan_file,
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

                                        @php
                                            $statusOptions = [
                                                'Accounting' => ['in_review', 'revision'],
                                                'Owner' => ['pending', 'in_review', 'approved', 'rejected', 'revision'],
                                                'Developer' => [
                                                    'pending',
                                                    'in_review',
                                                    'approved',
                                                    'rejected',
                                                    'revision',
                                                ],
                                            ];

                                            $statusLabels = [
                                                'pending' => 'Pending',
                                                'in_review' => 'In Review',
                                                'approved' => 'Approved',
                                                'rejected' => 'Rejected',
                                                'revision' => 'Revisi',
                                            ];

                                            $userRole = auth()->user()->roles->first()->name;
                                        @endphp

                                        @if (isset($statusOptions[$userRole]))
                                            <div class="col-md-12">
                                                <div class="mb-3">
                                                    <label for="status_review" class="form-label required">
                                                        Review Status
                                                    </label>
                                                    <select name="status_review" id="status_review"
                                                        class="form-control select2" required>
                                                        @foreach ($statusOptions[$userRole] as $status)
                                                            <option value="{{ $status }}">
                                                                {{ $statusLabels[$status] }}</option>
                                                        @endforeach
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
        <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>

        <script>
            function numberFormat(value, decimals = 0, decimalSeparator = ',', thousandSeparator = '.') {
                value = parseFloat(value) || 0; // Pastikan angka valid
                value = value.toFixed(decimals); // Tetapkan jumlah desimal yang benar

                const parts = value.toString().split('.');
                parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousandSeparator);

                return 'Rp ' + parts.join(decimalSeparator); // Tambahkan "Rp" di depan hasil format
            }


            $(document).ready(function() {
                $('#project_id').change(function() {
                    let selectedOption = $(this).find(':selected');

                    let projectName = selectedOption.text();
                    let projectFile = selectedOption.data('project-file');
                    let projectSummary = selectedOption.data('project-summary') || '0';
                    let projectVendor = selectedOption.data('project-vendor') || 'Unknown';
                    let projectAmount = selectedOption.data('project-amount') || '0';
                    let projectReviewer = selectedOption.data('project-reviewer') || 'Not reviewed';
                    let projectReviewNote = selectedOption.data('project-review-note') || 'No review note';
                    let projectDetails = selectedOption.data('project-details') || [];
                    let projectLisence = selectedOption.data('project-lisence') || [];

                    // Remove any existing formatting and convert to a valid number
                    const cleanedSummary = projectSummary.replace(/\./g, '').replace(',', '.');
                    const numericSummary = parseFloat(cleanedSummary);

                    // Tampilkan project detail card
                    $('#projectDetailsContainer').fadeIn();
                    $('#projectNameBadge').text(projectName);
                    $('#summaryDetailsContent').text(numberFormat(numericSummary));
                    $('#projectVendor').text(projectVendor);
                    $('#projectAmount').text(numberFormat(projectAmount));
                    $('#projectReviewer').text(projectReviewer);
                    $('#projectReviewNote').text(projectReviewNote);





                    let fileContent = "";

                    if (projectFile && typeof projectFile === "object") {
                        let fileKMZ = projectFile.kmz ? projectFile.kmz : null;
                        let fileExcel = projectFile.excel ? projectFile.excel : null;

                        if (fileExcel) {
                            fileContent += `<a href="{{ asset('storage/files/excel/${fileExcel}') }}" download class="list-group-item list-group-item-action d-flex align-items-center">
                                    <i class="mdi mdi-file-excel me-2 fs-5 text-success"></i>
                                    <span class="file-name">Exel File</span>
                                </a>`;
                        }

                        if (fileKMZ) {
                            fileContent += `<a href="{{ asset('storage/files/kmz/${fileKMZ}') }}" download class="list-group-item list-group-item-action d-flex align-items-center">
                                    <i class="mdi mdi-map-marker-radius me-2 fs-5 text-warning"></i>
                                    <span class="file-name">Kmz File</span>
                                </a>`;
                        }
                    } else {
                        fileContent = `<div class="alert alert-warning">No files available</div>`;
                    }

                    if ($('#fileDetailsContent').length > 0) {
                        $('#fileDetailsContent').html(fileContent);
                    }


                    $('#lisence').empty(); // Kosongkan sebelumnya
                    projectLisence.forEach(function(detail, index) {
                        const formattedPrice = new Intl.NumberFormat('id-ID').format(detail.price);
                        const rupiahPrice = 'Rp ' + formattedPrice;

                        $('#lisence').append(`
                            <div class="text-uppercase alert alert-info shadow-sm">
                                <a href="{{ asset('storage/files/perijinan/${detail.perijinan_file}') }}" download="${detail.name}" class="text-dark">
                                    ${detail.name} (${rupiahPrice})
                                </a>
                            </div>
                        `);
                    });


                    //distribusi
                    $('#distribusi').empty(); // Kosongkan sebelumnya
                    projectDetails.forEach(function(detail, index) {
                        $('#distribusi').append('<div id="detail-' + index +
                            '" type="button" class=" distribusi-item text-uppercase alert alert-info shadow-sm" data-index="' +
                            index + '">' + detail.name + '</div>');
                    });

                    // Ketika item distribusi diklik, tampilkan detailnya dalam DataTable
                    $('.distribusi-item').click(function() {
                        let index = $(this).data('index'); // Ambil index unik
                        let selectedDetail = projectDetails[index]; // Ambil detail berdasarkan index

                        // Tampilkan modal dengan detail proyek
                        showModalWithDetail(projectDetails[index]);
                    });



                    function showModalWithDetail(detail) {
                        // Clear any existing content in the modal and table body before appending new rows
                        $('#itemDetailsBody').empty();
                        $('#modalProjectTitle').text(detail.name);

                        // Check if the items array is not empty
                        if (detail.items && detail.items.length > 0) {
                            let tableContent = '';
                            detail.items.forEach(item => {
                                tableContent += `
                <tr>
                    <td>${item.item_name}</td>
                    <td>${item.quantity}</td>
                    <td>${numberFormat(item.cost_material)}</td>
                    <td>${numberFormat(item.cost_service)}</td>
                    
                </tr>
            `;
                            });

                            // Add the newly created table rows to the table body
                            $('#itemDetailsBody').html(tableContent);
                        } else {
                            // If no items are available, display a message
                            $('#itemDetailsBody').html(
                                '<tr><td colspan="5">No items available for this project.</td></tr>');
                        }

                        // Destroy the existing DataTable (if any) and reinitialize with the new content
                        if ($.fn.dataTable.isDataTable('#dataTableItemDetails')) {
                            $('#dataTableItemDetails').DataTable().clear().destroy();
                        }


                        $('#dataTableItemDetails').DataTable({
                            paging: true,
                            searching: true,
                            ordering: true,
                            info: true,
                        });

                        // Show the modal after updating the content
                        $('#modalProjectDetailTemplate').fadeIn();
                    }


                    // Tutup modal
                    $('#modalProjectDetailTemplate .btn-close').click(function() {
                        if ($.fn.dataTable.isDataTable('#dataTableItemDetails')) {
                            $('#dataTableItemDetails').DataTable().clear().destroy();
                        }

                        // Clear the modal content
                        $('#itemDetailsBody').empty();
                        $('#modalProjectTitle').empty();

                        $('#modalProjectDetailTemplate').fadeOut();

                    });
                });
            });
        </script>
    @endpush
@endsection
