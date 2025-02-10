@extends('layout.base')

@section('tittle', $tittle)

@push('css')
<link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
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
                        <li class="breadcrumb-item active">Project</li>
                        <li class="breadcrumb-item active">{{ $tittle }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Upload ATP File for Project: {{ $project->name }}</div>

                <div class="card-body">
                    <form id="uploadForm" action="{{ route('project.store-atp', $project->id) }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="file" class="form-label required">ATP File</label>
                                <input type="file" class="form-control @error('file') is-invalid @enderror" id="file"
                                    name="file" required accept=".zip,.rar">
                                <small class="form-text text-muted">
                                    Allowed file types: ZIP, RAR (Max 150MB)
                                </small>
                                @error('file')
                                <div class="invalid-feedback">
                                    {{ $message }}
                                </div>
                                @enderror
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Upload ATP File</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('js')
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
{{-- custom swetaert --}}
<script src="{{ asset('assets/js/custom.js') }}"></script>
<script>
    $("form").submit(function(event){
        event.preventDefault();

        Swal.fire({
            title: 'Uploading...',
            text: 'Please wait while your file is being uploaded.',
            icon: 'info',
            showConfirmButton: false,
            allowOutsideClick: false, 
            didOpen: () => {
                Swal.showLoading();
            }
        });

        var formData = new FormData($(this)[0]);
        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.fire({
                    title: 'Success!',
                    text: 'Your ATP file has been uploaded successfully.',
                    icon: 'success',
                    showConfirmButton: true
                }).then(function() {
                    window.location.href = "{{ route('project') }}";
                });
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    title: 'Error!',
                    text: 'There was an error uploading the file.',
                    icon: 'error',
                    showConfirmButton: true
                });
            }
        });
    });
</script>
@endpush