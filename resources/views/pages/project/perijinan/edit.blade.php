@extends('layout.base')
@section('tittle', $tittle)

@push('css')
<link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/libs/bootstrap-touchspin/jquery.bootstrap-touchspin.min.css') }}" rel="stylesheet" />
<link href="{{ asset('assets/css/app.min.css') }}" id="app-style" rel="stylesheet" type="text/css" />
<link href="{{ asset('assets/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('content')
<div class="page-title-box">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-sm-6">
                <div class="page-title">
                    <h4>Edit {{ $tittle }}</h4>
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('project.detail',['id'=>$project->id],) }}">Detail Project</a></li>
                        <li class="breadcrumb-item active">Edit {{ $tittle }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid">
    <div class="page-content-wrapper">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('projectlisence.update',['id'=>$project->id,'idperijinan'=>$perijinan->id]) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="validationCustom01" class="form-label required">Nama</label>
                                        <input type="text" name="name" value="{{ $perijinan->name }}"
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
                                        <label for="validationCustom01" class="form-label required">Nominal</label>
                                        <input type="text" inputmode="numeric" name="price" value="{{ $perijinan->price }}"
                                            class="form-control @error('price') is-invalid @enderror"
                                            id="validationCustom01">
                                        @error('price')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="validationCustom01" class="form-label">File Perijinan</label>
                                        <input type="file"  name="file" 
                                            class="form-control @error('file') is-invalid @enderror"
                                            id="validationCustom01">
                                        @error('file')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="validationCustom01" class="form-label required">Catatan</label>
                                        <textarea name="note" class="form-control @error('note') is-invalid @enderror" id="" cols="20" rows="5">{{ $perijinan->note }}</textarea>
                                        @error('note')
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
<script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/libs/select2/js/select2.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/form-advanced.init.js') }}"></script>
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('assets/js/custom.js') }}"></script>

   @if (Session::has('message'))
    <script>
        Swal.fire({
            title: "{{ Session::get('status') }}",
            text: "{{ Session::get('message') }}",
            icon: "{{ Session::get('status') == 'Success' ? 'success' : 'error' }}",
            showConfirmButton: false,
            timer: 3000
        });
    </script>
@endif
@endpush
@endsection
