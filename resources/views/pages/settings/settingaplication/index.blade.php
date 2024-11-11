@extends('layout.base')

@section('tittle', $tittle)

@push('css')
<!-- DataTables CSS -->
<link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet"
    type="text/css" />
<link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/libs/dropzone/min/dropzone.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />

<!-- Custom Dropzone CSS -->
<style>
    #dropzoneArea {
        border: 2px dashed #6c757d;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        background-color: #f8f9fa;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    #dropzoneArea:hover {
        background-color: #e2e6ea;
    }

    #dropzoneArea .dz-message {
        font-size: 16px;
        color: #495057;
        font-weight: 500;
    }
</style>
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
                        <li class="breadcrumb-item active">Settings</li>
                        <li class="breadcrumb-item active">{{ $tittle }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="page-content-wrapper">
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('aplication.update', ['id' => $app->id]) }}" method="POST"
                            enctype="multipart/form-data" novalidate>
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="validationCustom01" class="form-label">Nama Aplikasi</label>
                                        <input type="text" name="name" class="form-control" id="validationCustom01"
                                            value="{{ $app->name }}" required>
                                        <div class="valid-feedback">Looks good!</div>
                                    </div>
                                </div>


                                <!-- Dropzone File Upload -->

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="dropzone" class="form-label">Logo Perusahaan</label>
                                        <input type="file" name="logo" class="form-control" id="">
                                        {{-- <div id="dropzoneArea" class="dropzone">
                                            <div class="dz-message needsclick">
                                                Drag & drop your file here or click to upload.
                                            </div>
                                        </div> --}}
                                    </div>
                                </div>



                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="validationCustom01" class="form-label">Description</label>
                                        <textarea id="textarea" name="description" class="form-control" maxlength="225"
                                            rows="3" placeholder="Enter Description">{{ $app->description }}</textarea>
                                        <div class="valid-feedback">Looks good!</div>
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
    </div>
</div>

@push('js')
<script src="{{ asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('assets/js/custom.js') }}"></script>
<script src="{{ asset('assets/libs/dropzone/min/dropzone.min.js') }}"></script>

<script>
    Dropzone.autoDiscover = false;
            var dropzoneArea = new Dropzone("#dropzoneArea", {
                url: "{{ route('aplication.update',['id'=>$app->id]) }}",  // Route specifically for image upload
                maxFiles: 1,
                acceptedFiles: ".jpeg,.jpg,.png,.webp",
                addRemoveLinks: true,
                dictDefaultMessage: "Drop file here or click to upload",
                autoProcessQueue: true,  // Automatically upload files when added
                init: function () {
                    this.on("maxfilesexceeded", function (file) {
                        this.removeAllFiles();
                        this.addFile(file);
                    });
        
                    this.on("success", function (file, response) {
                        // Add response (e.g., filename) to hidden input for form submission
                        document.getElementById('dropzoneArea').value = response.filename;
                    });
                    
                    // Display existing file if exists
                    var existingFile = "{{ $app->logo }}";
                    if (existingFile) {
                        var mockFile = { name: "Current Logo", size: 12345, type: 'image/jpeg' };
                        this.emit("addedfile", mockFile);
                        this.emit("thumbnail", mockFile, "{{ asset('/storage/logo/' . $app->logo) }}");
                        this.emit("complete", mockFile);
                        this.files.push(mockFile);
                    }
                }
            });
</script>

@endpush
@endsection