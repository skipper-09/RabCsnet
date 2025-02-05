@extends('layout.base')
@section('tittle', $tittle)

@push('css')
    <link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />

    <style>
        .image-preview {
            max-width: 200px;
            max-height: 200px;
            margin-top: 10px;
        }

        .preview-container {
            margin-top: 10px;
        }
    </style>
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
                            <li class="breadcrumb-item"><a href="{{ route('user') }}">{{ $tittle }}</a></li>
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
                            <form action="{{ route('user.update', ['id' => $user->id]) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="picture" class="form-label">Gambar</label>
                                            <input type="file" name="picture" id="picture"
                                                class="form-control @error('picture') is-invalid @enderror" accept="image/*"
                                                onchange="previewImage(this)">
                                            <small class="text-muted">Format yang diterima: JPEG, PNG, JPG, GIF. Ukuran
                                                maksimal: 2MB</small>
                                            @error('picture')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="preview-container mt-2">
                                                <img id="imagePreview"
                                                    src="{{ $user->picture ? asset('storage/images/user/' . $user->picture) : asset('images/placeholder.png') }}"
                                                    alt="Preview" class="image-preview"
                                                    style="max-width: 200px; max-height: 200px; display: {{ $user->picture ? 'block' : 'none' }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="username" class="form-label">Username</label>
                                            <input type="text" name="username"
                                                class="form-control @error('username') is-invalid @enderror"
                                                value="{{ old('username', $user->username) }}" id="username">
                                            @error('username')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Nama</label>
                                            <input type="text" name="name"
                                                class="form-control @error('name') is-invalid @enderror"
                                                value="{{ old('name', $user->name) }}" id="name">
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email</label>
                                            <input type="email" name="email"
                                                class="form-control @error('email') is-invalid @enderror"
                                                value="{{ old('email', $user->email) }}" id="email">
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="password" class="form-label">Password</label>
                                            <input type="password" name="password"
                                                class="form-control @error('password') is-invalid @enderror" id="password">
                                            <small class="text-muted">Kosongkan jika tidak ingin mengubah password</small>
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                                            <input type="password" name="password_confirmation"
                                                class="form-control @error('password_confirmation') is-invalid @enderror"
                                                id="password_confirmation">
                                            @error('password_confirmation')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="is_block" class="form-label">User Access</label>
                                            <select name="is_block"
                                                class="form-select @error('is_block') is-invalid @enderror"
                                                id="is_block">
                                                <option value="0"
                                                    {{ old('is_block', $user->is_block) == 0 ? 'selected' : '' }}>Not
                                                    Blocked</option>
                                                <option value="1"
                                                    {{ old('is_block', $user->is_block) == 1 ? 'selected' : '' }}>Blocked
                                                </option>
                                            </select>
                                            @error('is_block')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="role" class="form-label">Role User</label>
                                            <select name="role" class="form-select @error('role') is-invalid @enderror"
                                                id="role">
                                                <option value="">Pilih Role User</option>
                                                @foreach ($role as $item)
                                                    <option value="{{ $item->name }}"
                                                        {{ old('role', $user->hasRole($item->name) ? $item->name : '') == $item->name ? 'selected' : '' }}>
                                                        {{ $item->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('role')
                                                <div class="invalid-feedback">{{ $message }}</div>
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
            <!-- end row -->

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
            // Image preview function
            function previewImage(input) {
                const preview = document.getElementById('imagePreview');
                if (input.files && input.files[0]) {
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        preview.src = e.target.result;
                        preview.style.display = 'block';
                    }

                    reader.readAsDataURL(input.files[0]);
                } else {
                    // Don't reset the preview if no new file is selected
                    if (!preview.src.includes('storage/images/user')) {
                        preview.src = '#';
                        preview.style.display = 'none';
                    }
                }
            }
        </script>
    @endpush
@endsection
