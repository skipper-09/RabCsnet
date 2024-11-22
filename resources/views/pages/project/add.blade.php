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
                            <li class="breadcrumb-item"><a href="{{ route('item') }}">{{ $tittle }}</a></li>
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
                            <form action="{{ route('project.store') }}" method="POST" enctype="multipart/form-data"
                                class="">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="validationCustom01" class="form-label required">Nama</label>
                                            <input type="text" name="name" value="{{ old('name') }}"
                                                class="form-control @error('name') is-invalid @enderror"
                                                id="validationCustom01">
                                            @error('name')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror

                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="validationCustom01" class="form-label required">
                                                Perusahaan
                                            </label>
                                            <select name="company_id"
                                                class="form-control  @error('company_id') is-invalid @enderror select2"
                                                aria-label="Default select example">
                                                <option selected value="">Pilih Perusahaan</option>
                                                @foreach ($company as $item)
                                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('company_id')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    {{-- <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="validationCustom01" class="form-label required">
                                                Penanggung Jawab
                                            </label>
                                            <select name="responsible_person"
                                                class="form-control  @error('responsible_person') is-invalid @enderror select2"
                                                aria-label="Default select example">
                                                <option selected value="">Pilih Penanggung Jawab</option>
                                                @foreach ($user as $item)
                                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('responsible_person')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="validationCustom01" class="form-label required">Tanggal Mulai Project</label>
                                            <input type="date" name="start_date" 
                                                class="form-control @error('start_date') is-invalid @enderror"
                                                id="validationCustom01">
                                            @error('start_date')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="validationCustom01" class="form-label required">Finsih Project</label>
                                            <input type="date" name="end_date" 
                                                class="form-control @error('end_date') is-invalid @enderror"
                                                id="validationCustom01">
                                            @error('end_date')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="validationCustom01" class="form-label required">
                                                Status
                                            </label>
                                            <select name="status"
                                                class="form-control select2 @error('status') is-invalid @enderror"
                                                aria-label="Default select example">
                                                <option selected value="">Pilih Status</option>
                                                <option value="1">Aktif</option>
                                                <option value="0">Tidak Aktif</option>
                                            </select>
                                            @error('status')
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
                                                class="form-control  @error('vendor_id') is-invalid @enderror select2"
                                                aria-label="Default select example">
                                                <option selected value="">Pilih Vendor</option>
                                                @foreach ($vendor as $item)
                                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('vendor_id')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div> --}}

                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="validationCustom01" class="form-label required">
                                            Deskripsi
                                        </label>
                                        <textarea id="textarea" name="description" class="form-control @error('description') is-invalid @enderror" maxlength="225"
                                            rows="3"></textarea>
                                        @error('description')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
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
