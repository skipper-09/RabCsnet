@extends('layout.base')
@section('tittle', $tittle)

@push('css')
    <link href="assets/libs/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
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
                        <h4>Tambah {{ $tittle }}</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('tasks.assign') }}">{{ $tittle }}</a></li>
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
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('tasks.assign.store') }}" method="POST" enctype="multipart/form-data"
                                class="needs-validation" novalidate>
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="validationCustom01" class="form-label required">
                                                Task
                                            </label>
                                            <select name="task_id"
                                                class="form-control select2 @error('task_id') is-invalid @enderror"
                                                aria-label="Default select example">
                                                <option selected>Pilih Task</option>
                                                @foreach ($tasks as $task)
                                                    <option value="{{ $task->id }}">{{ $task->title }}</option>
                                                @endforeach
                                            </select>
                                            @error('task_id')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="validationCustom01" class="form-label required">
                                                Vendor
                                            </label>
                                            <select name="vendor_id"
                                                class="form-control select2 @error('vendor_id') is-invalid @enderror"
                                                aria-label="Default select example">
                                                <option selected>Pilih Vendor</option>
                                                @foreach ($vendors as $vendor)
                                                    <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('vendor_id')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="finish_date" class="form-label required">Estimasi Selesai</label>
                                            <input type="date"
                                                class="form-control @error('finish_date') is-invalid @enderror"
                                                id="finish_date" name="finish_date" value="{{ old('finish_date') }}">
                                            @error('finish_date')
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
    @endpush
@endsection
