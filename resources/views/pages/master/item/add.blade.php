@extends('layout.base')
@section('tittle', $tittle)

@push('css')
    <link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />

    <style>
        #additional-details-section {
            display: none;
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
                            <form action="{{ route('item.store') }}" method="POST" enctype="multipart/form-data"
                                class="needs-validation" novalidate>
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="validationCustom01" class="form-label required">Name</label>
                                            <input type="text" name="name"
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
                                            <label for="validationCustom01" class="form-label">Material
                                                Price</label>
                                            <input type="text" inputmode="numeric" name="material_price"
                                                class="form-control @error('material_price') is-invalid @enderror"
                                                id="validationCustom01">
                                            @error('material_price')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="validationCustom01" class="form-label required">
                                                Item Type
                                            </label>
                                            <select name="type_id"
                                                class="form-control select2 @error('type_id') is-invalid @enderror"
                                                aria-label="Default select example">
                                                <option selected>Select Item Type</option>
                                                @foreach ($types as $type)
                                                    <option value="{{ $type->id }}">{{ $type->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('type_id')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="validationCustom01" class="form-label required">
                                                Item Unit
                                            </label>
                                            <select name="unit_id"
                                                class="form-control select2 @error('unit_id') is-invalid @enderror"
                                                aria-label="Default select example">
                                                <option selected>Select Item Unit</option>
                                                @foreach ($units as $unit)
                                                    <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('unit_id')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="validationCustom01" class="form-label">
                                                Description
                                            </label>
                                            <textarea id="textarea" name="description" class="form-control @error('description') is-invalid @enderror"
                                                maxlength="225" rows="3" placeholder="Enter Description"></textarea>
                                            @error('description')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="my-3">
                                        <!-- New Conditional Input Section -->
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">Apakah anda membutuhkan Jasa
                                                    juga?</label>
                                                <select id="additional-details-select" name="has_additional_details"
                                                    class="form-control">
                                                    <option value="no">Tidak</option>
                                                    <option value="yes">Ya</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Conditional Additional Details Section -->
                                        <div id="additional-details-section">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="service_name" class="form-label">Nama Jasa</label>
                                                        <input type="text" name="service_name" id="service_name"
                                                            class="form-control @error('service_name') is-invalid @enderror"
                                                            maxlength="255" placeholder="Masukkan Nama Jasa">
                                                        @error('service_name')
                                                            <div class="invalid-feedback">
                                                                {{ $message }}
                                                            </div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label for="service_price" class="form-label">Harga Jasa</label>
                                                        <input type="number" name="service_price" id="service_price"
                                                            class="form-control @error('service_price') is-invalid @enderror"
                                                            placeholder="Masukkan Harga Jasa">
                                                        @error('service_price')
                                                            <div class="invalid-feedback">
                                                                {{ $message }}
                                                            </div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
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
            $(document).ready(function() {
                // Conditional Details Section Toggle
                $('#additional-details-select').change(function() {
                    if ($(this).val() === 'yes') {
                        $('#additional-details-section').show();
                    } else {
                        $('#additional-details-section').hide();
                        // Clear input values when hidden
                        $('#additional-details-section input, #additional-details-section textarea').val('');
                    }
                });
            });
        </script>
    @endpush
@endsection
