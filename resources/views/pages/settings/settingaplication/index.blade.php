@extends('layout.base')

@section('tittle', $tittle)

@push('css')
    <link href="{{ asset('assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}" rel="stylesheet"/>
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
                            <form action="{{ route('aplication.update') }}" method="POST" enctype="multipart/form-data"
                                novalidate>
                                @csrf
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="validationCustom01" class="form-label">Nama Aplikasi</label>
                                            <input type="text" name="name"
                                                class="form-control @error('name') is-invalid @enderror"
                                                id="validationCustom01" value="{{ $setting->name }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>


                                    <!-- Dropzone File Upload -->

                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="dropzone" class="form-label">Logo Perusahaan</label>
                                            <div>
                                                @if ($setting->logo)
                                                    <img id="previewImage" src="{{ asset('storage/' . $setting->logo) }}"
                                                        alt="Preview Logo" class="img-thumbnail mb-3"
                                                        style="max-height: 150px;">
                                                @else
                                                    <img id="previewImage" src="#" alt="Preview Logo"
                                                        class="img-thumbnail mb-3"
                                                        style="display: none; max-height: 150px;">
                                                @endif
                                            </div>
                                            <input type="file" name="logo" class="form-control" id="logoInput">
                                        </div>
                                    </div>


                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="validationCustom01" class="form-label">Description</label>
                                            <textarea id="textarea" name="description" class="form-control @error('description') is-invalid @enderror"
                                                maxlength="225" rows="3" placeholder="Enter Description">{{ $setting->description }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>


                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="validationCustom01" class="form-label">Ppn</label>
                                            <input type="text" inputmode="numeric" name="ppn"
                                                class="form-control @error('ppn') is-invalid @enderror"
                                                id="validationCustom01" value="{{ $setting->ppn }}" required>
                                            @error('ppn')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="validationCustom01" class="form-label">Nilai Lebih</label>
                                            <input type="text" inputmode="numeric" name="backup"
                                                class="form-control @error('backup') is-invalid @enderror"
                                                id="validationCustom01" value="{{ $setting->backup }}" required>
                                            @error('backup')
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
            document.getElementById('logoInput').addEventListener('change', function(e) {
                const file = e.target.files[0];
                const previewImage = document.getElementById('previewImage');

                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(event) {
                        previewImage.src = event.target.result;
                        previewImage.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                } else {
                    previewImage.style.display = 'none';
                }
            });
        </script>
    @endpush
@endsection
