@extends('layout.base')
@section('title', $tittle)

@push('css')
    <link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
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
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('review.update', $review->id) }}" method="POST" id="reviewForm" class="needs-validation" novalidate>
                                @csrf
                                @method('PUT')
                                
                                {{-- Project Details Section --}}
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Project</label>
                                            <input type="text" class="form-control" value="{{ $review->project->name }}" readonly>
                                            <input type="hidden" name="project_id" value="{{ $review->project->id }}">
                                        </div>
                                    </div>

                                    {{-- Project Summary Details --}}
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label class="form-label">Total Project Summary</label>
                                            <input type="text" class="form-control" 
                                                value="{{ $review->project->formatted_total_summary ?? 'N/A' }}" 
                                                readonly>
                                        </div>
                                    </div>

                                    {{-- Status Pengajuan for Developer and Owner --}}
                                    @if(in_array(auth()->user()->roles->first()->name, ['Developer', 'Owner']))
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="status_pengajuan" class="form-label required">
                                                Status Project
                                            </label>
                                            <select name="status_pengajuan" id="status_pengajuan" 
                                                class="form-control select2"
                                                {{ $canEdit ? '' : 'disabled' }}>
                                                <option value="pending" {{ $review->project->status_pengajuan == 'pending' ? 'selected' : '' }}>Pending</option>
                                                <option value="in_review" {{ $review->project->status_pengajuan == 'in_review' ? 'selected' : '' }}>In Review</option>
                                                <option value="approved" {{ $review->project->status_pengajuan == 'approved' ? 'selected' : '' }}>Approved</option>
                                                <option value="rejected" {{ $review->project->status_pengajuan == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                                <option value="revision" {{ $review->project->status_pengajuan == 'revision' ? 'selected' : '' }}>Revisi</option>
                                            </select>
                                        </div>
                                    </div>
                                    @endif
                                    
                                    {{-- Review Note --}}
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="review_note" class="form-label">
                                                Catatan Review
                                            </label>
                                            <textarea id="review_note" name="review_note" 
                                                class="form-control @error('review_note') is-invalid @enderror"
                                                maxlength="255" rows="4" 
                                                placeholder="Masukkan catatan review (maksimal 255 karakter)"
                                                {{ $canEdit ? '' : 'readonly' }}>{{ old('review_note', $review->review_note) }}</textarea>
                                            @error('review_note')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                            <small class="text-muted form-text">Sisa karakter: <span id="charCount">255</span></small>
                                        </div>
                                    </div>

                                    {{-- Review Metadata --}}
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Reviewer</label>
                                                    <input type="text" class="form-control" value="{{ $review->reviewer->name }}" readonly>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Review Date</label>
                                                    <input type="text" class="form-control" value="{{ $review->created_at->format('Y-m-d H:i:s') }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                {{-- Action Buttons --}}
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('review') }}" class="btn btn-secondary">Kembali</a>
                                    @if($canEdit)
                                        <button class="btn btn-primary" type="submit">Simpan Perubahan</button>
                                    @endif
                                </div>
                            </form>
                        </div>
                    </div>
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
                // Character count for review note
                $('#review_note').on('input', function() {
                    var maxLength = 255;
                    var currentLength = $(this).val().length;
                    var remainingChars = maxLength - currentLength;
                    $('#charCount').text(remainingChars);
                });
            });
        </script>
    @endpush
@endsection