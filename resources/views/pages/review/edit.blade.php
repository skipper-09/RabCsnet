@extends('layout.base')
@section('tittle', $tittle)

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
                            <li class="breadcrumb-item"><a href="{{ route('review') }}">{{ $tittle }}</a></li>
                            <li class="breadcrumb-item active">Edit {{ $tittle }}</li>
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
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="project_id" class="form-label required">
                                                Project
                                            </label>
                                            <select name="project_id" id="project_id" 
                                                class="form-control select2 @error('project_id') is-invalid @enderror"
                                                required disabled>
                                                <option value="{{ $review->project->id }}" selected>
                                                    {{ $review->project->name }}
                                                </option>
                                            </select>
                                            <input type="hidden" name="project_id" value="{{ $review->project->id }}">
                                            @error('project_id')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>

                                    @if(auth()->user()->roles->first()->name == 'Developer')
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="status_pengajuan" class="form-label required">
                                                Status Project
                                            </label>
                                            <select name="status_pengajuan" id="status_pengajuan" 
                                                class="form-control select2"
                                                required>
                                                <option value="in_review" {{ $review->project->status_pengajuan == 'in_review' ? 'selected' : '' }}>In Review</option>
                                                <option value="approved" {{ $review->project->status_pengajuan == 'approved' ? 'selected' : '' }}>Approved</option>
                                            </select>
                                        </div>
                                    </div>
                                    @endif
                                    
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="review_note" class="form-label">
                                                Catatan Review
                                            </label>
                                            <textarea id="review_note" name="review_note" 
                                                class="form-control @error('review_note') is-invalid @enderror"
                                                maxlength="255" rows="4" 
                                                placeholder="Masukkan catatan review (maksimal 255 karakter)">{{ old('review_note', $review->review_note) }}</textarea>
                                            @error('review_note')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                            <small class="text-muted form-text">Sisa karakter: <span id="charCount">255</span></small>
                                        </div>
                                    </div>
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