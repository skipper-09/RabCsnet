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
                        <h4>Add {{ $tittle }}</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('role') }}">{{ $tittle }}</a></li>
                            <li class="breadcrumb-item active">Add {{ $tittle }}</li>
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
                            <form action="{{ route('role.store') }}" method="POST" enctype="multipart/form-data"
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
                                    <div class="form-group col-12 col-md-12">
                                        <div class="mb-3">
                                            <div class="d-flex align-content-start justify-content-start mb-2">
                                                <button id="select-all-btn" type="button"
                                                    class="btn btn-sm btn-primary mr-2" onclick="toggleSelectAll()">Select
                                                    All</button>
                                            </div>
                                            <label class="form-label" for="permissions">
                                                Permissions
                                            </label>
                                            <div class="row mx-4">
                                                @foreach ($permission as $perm)
                                                    <div class="col-4 col-md-4">
                                                        <div class="form-check">
                                                            <input class="form-check-input permission-checkbox"
                                                                type="checkbox" name="permissions[]"
                                                                id="permission_{{ $perm->id }}"
                                                                value="{{ $perm->name }}">
                                                            <label class="form-check-label"
                                                                for="permission_{{ $perm->id }}">
                                                                {{ $perm->name }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
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
            let allSelected = false;

            function toggleSelectAll() {
                const checkboxes = document.querySelectorAll('.permission-checkbox');
                allSelected = !allSelected;

                checkboxes.forEach((checkbox) => {
                    checkbox.checked = allSelected;
                });

                const selectAllBtn = document.getElementById('select-all-btn');
                selectAllBtn.textContent = allSelected ? 'Unselect All' : 'Select All';
            }

            function checkIfAllSelected() {
                const checkboxes = document.querySelectorAll('.permission-checkbox');
                const allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);

                allSelected = allChecked;
                const selectAllBtn = document.getElementById('select-all-btn');
                selectAllBtn.textContent = allChecked ? 'Unselect All' : 'Select All';
            }

            document.addEventListener('DOMContentLoaded', function() {
                checkIfAllSelected();
            });
        </script>
    @endpush
@endsection
