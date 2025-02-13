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
    <!-- start page title -->
    <div class="page-title-box">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <div class="page-title">
                        <h4>Edit {{ $tittle }}</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('review') }}">Project Review</a></li>
                            <li class="breadcrumb-item active">Edit Project Review</li>
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
                <!-- Project Details Card -->
                <div class="col-lg-7">
                    <div class="card">
                        <div class="card-body">
                            <div id="projectDetailsContainer">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <h5>Project Name</h5>
                                        <div class="badge badge-soft-primary font-size-14" id="projectNameBadge">
                                            {{ $project->name }}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <h5>Total Summary</h5>
                                        <div id="summaryDetailsContent">Rp
                                            {{ number_format($project->summary->total_summary, 0, ',', '.') }}</div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <h5>Vendor</h5>
                                        <div id="projectVendor">{{ $project->vendor->name ?? 'Unknown' }}</div>
                                    </div>
                                    <div class="col-md-6">
                                        <h5>Project Amount</h5>
                                        <div id="projectAmount">Rp {{ number_format($project->amount, 0, ',', '.') }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Project Files -->
                                <div class="mb-4">
                                    <h5>Project Files</h5>
                                    <div class="list-group" id="fileDetailsContent">
                                        @if ($project->Projectfile)
                                            @if ($project->Projectfile->excel)
                                                <a href="{{ asset('storage/files/excel/' . $project->Projectfile->excel) }}"
                                                    download
                                                    class="list-group-item list-group-item-action d-flex align-items-center">
                                                    <i class="mdi mdi-file-excel me-2 fs-5 text-success"></i>
                                                    <span class="file-name">Excel File</span>
                                                </a>
                                            @endif
                                            @if ($project->Projectfile->kmz)
                                                <a href="{{ asset('storage/files/kmz/' . $project->Projectfile->kmz) }}"
                                                    download
                                                    class="list-group-item list-group-item-action d-flex align-items-center">
                                                    <i class="mdi mdi-map-marker-radius me-2 fs-5 text-warning"></i>
                                                    <span class="file-name">KMZ File</span>
                                                </a>
                                            @endif
                                        @else
                                            <div class="alert alert-warning">No files available</div>
                                        @endif
                                    </div>
                                </div>

                                <!-- License Section -->
                                <div class="mb-4">
                                    <h5>Licenses</h5>
                                    <div id="lisence">
                                        @foreach ($project->projectlisence ?? [] as $license)
                                            <div class="text-uppercase alert alert-info shadow-sm">
                                                <a href="{{ asset('storage/files/perijinan/' . $license->perijinan_file) }}"
                                                    download="{{ $license->name }}" class="text-dark">
                                                    {{ $license->name }} (Rp
                                                    {{ number_format($license->price, 0, ',', '.') }})
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <!-- Distribution Section -->
                                <div class="mb-4">
                                    <h5>Distribution</h5>
                                    <div id="distribusi" class="row g-3">
                                        @foreach ($project->detailproject ?? [] as $index => $detail)
                                            <div class="col-md-4 col-sm-6">
                                                <div id="detail-{{ $index }}"
                                                    class="distribusi-item text-uppercase alert alert-info shadow-sm h-100 d-flex align-items-center justify-content-between"
                                                    data-index="{{ $index }}" role="button">
                                                    <span>{{ $detail->name }}</span>
                                                    <span class="badge bg-primary rounded-pill">
                                                        {{ $detail->detailitemporject->count() ?? 0 }}
                                                    </span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Review Form Card -->
                <div class="col-lg-5">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title mb-4">Review Details</h4>
                            <form action="{{ route('review.update', $review->id) }}" method="POST" id="reviewForm"
                                class="needs-validation" novalidate>
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label for="review_note" class="form-label">
                                        Catatan Review
                                    </label>
                                    <textarea id="review_note" name="review_note" class="form-control @error('review_note') is-invalid @enderror"
                                        maxlength="255" rows="4" placeholder="Masukkan catatan review (maksimal 255 karakter)">{{ old('review_note', $review->review_note) }}</textarea>
                                    @error('review_note')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>

                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('review') }}" class="btn btn-secondary">Kembali</a>
                                    <button class="btn btn-primary" type="submit">Simpan Perubahan</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for Distribution Details -->
    <div class="modal fade" id="modalProjectDetailTemplate" tabindex="-1" aria-labelledby="modalProjectDetailLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalProjectDetailLabel">Project Detail: <span
                            id="modalProjectTitle"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table id="dataTableItemDetails" class="table table-hover" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>Item Name</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-end">Material Cost</th>
                                    <th class="text-end">Service Cost</th>
                                    <th class="text-end">Total Cost Material</th>
                                    <th class="text-end">Total Cost Service</th>
                                </tr>
                            </thead>
                            <tbody id="itemDetailsBody">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
        <script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
        <script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
        <script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
        <script src="{{ asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>

        <script>
            $(document).ready(function() {
                // Initialize DataTable instance
                let itemDetailsTable;

                // Function to safely access nested object properties
                function safeGet(obj, path, defaultValue = 'N/A') {
                    try {
                        return path.split('.').reduce((current, key) =>
                            current && current[key] !== undefined ? current[key] : defaultValue,
                            obj);
                    } catch (e) {
                        return defaultValue;
                    }
                }

                // Function to format numbers
                function numberFormat(number) {
                    return new Intl.NumberFormat('id-ID', {
                        style: 'currency',
                        currency: 'IDR',
                        minimumFractionDigits: 0,
                        maximumFractionDigits: 0
                    }).format(number);
                }

                // Handle clicking on distribution items
                $('.distribusi-item').click(function() {
                    const index = $(this).data('index');
                    const details = {!! json_encode($project->detailproject ?? []) !!};

                    if (details[index]) {
                        showModalWithDetail(details[index]);
                    }
                });

                function showModalWithDetail(detail) {
                    // Clear previous content
                    $('#itemDetailsBody').empty();
                    $('#modalProjectTitle').text(detail.name);

                    // Generate table content
                    if (detail.detailitemporject && detail.detailitemporject.length > 0) {
                        const tableContent = detail.detailitemporject.map(i => {
                            // Safely access the item name and other properties
                            const itemName = safeGet(i, 'item.name');
                            const quantity = safeGet(i, 'quantity', 0);
                            const costMaterial = safeGet(i, 'cost_material', 0);
                            const costService = safeGet(i, 'cost_service', 0);

                            return `
                    <tr>
                        <td>${itemName}</td>
                        <td class="text-center">${quantity}</td>
                        <td class="text-end">${numberFormat(costMaterial)}</td>
                        <td class="text-end">${numberFormat(costService)}</td>
                        <td class="text-end">${numberFormat(costMaterial * quantity)}</td>
                        <td class="text-end">${numberFormat(costService * quantity)}</td>
                    </tr>
                `;
                        }).join('');

                        $('#itemDetailsBody').html(tableContent);
                    } else {
                        $('#itemDetailsBody').html(
                            '<tr><td colspan="6" class="text-center">No items available for this project.</td></tr>'
                        );
                    }

                    // Destroy existing DataTable if it exists
                    if (itemDetailsTable) {
                        itemDetailsTable.destroy();
                    }

                    // Initialize new DataTable
                    itemDetailsTable = $('#dataTableItemDetails').DataTable({
                        paging: true,
                        searching: true,
                        ordering: true,
                        info: true,
                        responsive: true,
                        language: {
                            search: "Search:",
                            lengthMenu: "Show _MENU_ entries",
                            info: "Showing _START_ to _END_ of _TOTAL_ entries",
                            infoEmpty: "Showing 0 to 0 of 0 entries",
                            infoFiltered: "(filtered from _MAX_ total entries)",
                            emptyTable: "No data available"
                        }
                    });

                    // Show modal
                    $('#modalProjectDetailTemplate').modal('show');
                }

                // Handle modal close
                $('#modalProjectDetailTemplate').on('hidden.bs.modal', function() {
                    if (itemDetailsTable) {
                        itemDetailsTable.destroy();
                        itemDetailsTable = null;
                    }
                    $('#itemDetailsBody').empty();
                    $('#modalProjectTitle').empty();
                });
            });
        </script>
    @endpush
@endsection
