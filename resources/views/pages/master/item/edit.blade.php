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
                        <li class="breadcrumb-item"><a href="{{ route('item') }}">{{ $tittle }}</a></li>
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
                        <form action="{{ route('item.update', ['id' => $item->id]) }}" method="POST"
                            enctype="multipart/form-data" class="needs-validation" novalidate>
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="name" class="form-label required">Name</label>
                                        <input type="text" name="name" value="{{ old('name', $item->name) }}"
                                            class="form-control @error('name') is-invalid @enderror" id="name">
                                        @error('name')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="material_price" class="form-label required">Material Price</label>
                                        <input type="text" inputmode="numeric" name="material_price"
                                            value="{{ number_format( $item->material_price,'0','','') }}"
                                            class="form-control @error('material_price') is-invalid @enderror"
                                            id="material_price">
                                        @error('material_price')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="validationCustom01" class="form-label required">Service
                                            Price</label>
                                        <input type="text" inputmode="numeric" name="service_price"
                                            value="{{ number_format( $item->service_price,'0','','') }}"
                                            class="form-control @error('service_price') is-invalid @enderror"
                                            id="validationCustom01">
                                        @error('service_price')
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
                                        <label for="type_id" class="form-label required">Item Type</label>
                                        <select name="type_id" id="type_id"
                                            class="form-control select2 @error('type_id') is-invalid @enderror">
                                            @foreach ($types as $type)
                                            <option value="{{ $type->id }}" {{ old('type_id', $item->type_id) ==
                                                $type->id ? 'selected' : '' }}>
                                                {{ $type->name }}</option>
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
                                        <label for="unit_id" class="form-label required">Item Unit</label>
                                        <select name="unit_id" id="unit_id"
                                            class="form-control select2 @error('unit_id') is-invalid @enderror">
                                            @foreach ($units as $unit)
                                            <option value="{{ $unit->id }}" {{ old('unit_id', $item->unit_id) ==
                                                $unit->id ? 'selected' : '' }}>
                                                {{ $unit->name }}</option>
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
                                        <label for="description" class="form-label">Description</label>
                                        <textarea id="description" name="description"
                                            class="form-control @error('description') is-invalid @enderror"
                                            maxlength="225" rows="3"
                                            placeholder="Enter Description">{{ old('description', $item->description) }}</textarea>
                                        @error('description')
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
<script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('assets/libs/metismenu/metisMenu.min.js') }}"></script>
<script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
<script src="{{ asset('assets/libs/parsleyjs/parsley.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/form-validation.init.js') }}"></script>
<script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/form-advanced.init.js') }}"></script>
@endpush
@endsection