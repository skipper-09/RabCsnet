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
                        <h4>Tambah {{ $tittle }}</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a
                                    href="{{ route('project.detail', ['id' => $project->id]) }}">{{ $tittle }}</a>
                            </li>
                            <li class="breadcrumb-item active">Tambah {{ $tittle }}</li>
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
                            <form action="{{ route('projectdetail.store', ['id' => $project->id]) }}" method="POST"
                                enctype="multipart/form-data">
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
                                                Tipe Projek
                                            </label>
                                            <select name="type_id"
                                                class="form-control select2 @error('type_id') is-invalid @enderror"
                                                aria-label="Default select example">
                                                <option value="">Pilih Tipe Projek</option>
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
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="validationCustom01" class="form-label required">Deskripsi</label>
                                            <textarea id="textarea" name="description" class="form-control @error('description') is-invalid @enderror"
                                                maxlength="225" rows="3"></textarea>
                                            @error('description')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <button type="button" class="btn btn-primary btn-sm mb-3" id="addRow">Add
                                            Row</button>
                                    </div>

                                    <div class="col-lg-12">
                                        <div class="table-responsive">
                                            <table class="table" id="myTable">
                                                <thead>
                                                    <tr>
                                                        <th>No</th>
                                                        <th>Item</th>
                                                        <th>Jasa</th>
                                                        <th>Jumlah</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <th scope="row">1</th>
                                                        <td>
                                                            <select name="item_id[]" class="form-control select2">
                                                                <option selected>Pilih Item Unit</option>
                                                                @foreach ($item as $unit)
                                                                    <option value="{{ $unit->id }}">
                                                                        {{ $unit->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                       
                                                        <td>
                                                            <select name="service_id[]" class="form-control select2">
                                                                <option value="">Pilih Jasa</option>
                                                                @foreach ($service as $srv)
                                                                    <option value="{{ $srv->id }}">
                                                                        {{ $srv->name }} -
                                                                        Rp. {{ number_format($srv->price, 0, ',', '.') }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                        </td>
                                                        <td>
                                                            <input type="text" name="quantity[]" class="form-control"
                                                                inputmode="numeric">
                                                        </td>
                                                        <td>
                                                            <button type="button" class="btn btn-danger btn-sm delete-btn">
                                                                Delete
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
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
        <script>
            $(document).ready(function() {
                // Tambah baris baru
                $('#addRow').click(function() {
                    const tableBody = $('#myTable tbody');
                    const rowIndex = tableBody.children('tr').length + 1;
                    const newRow = `
                    <tr>
                        <th scope="row">${rowIndex}</th>
                        <td>
                            <select name="item_id[]" class="form-control select2">
                                <option selected>Pilih Item Unit</option>
                                @foreach ($item as $unit)
                                <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                                @endforeach
                            </select>
                        </td>
                       
                        <td>
                            <select name="service_id[]" class="form-control select2">
                                <option value="">Pilih Jasa</option>
                                @foreach ($service as $srv)
                                <option value="{{ $srv->id }}">
                                    {{ $srv->name }} - 
                                    Rp. {{ number_format($srv->price, 0, ',', '.') }}
                                </option>
                                @endforeach
                            </select>
                        </td>
                         <td>
                            <input type="text" name="quantity[]" class="form-control" inputmode="numeric">
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm delete-btn">Delete</button>
                        </td>
                    </tr>`;
                    tableBody.append(newRow);
                    tableBody.find('.select2').select2();
                });

                // Hapus baris
                $('#myTable').on('click', '.delete-btn', function() {
                    $(this).closest('tr').remove();
                    updateRowNumbers();
                });

                // Update nomor baris
                function updateRowNumbers() {
                    $('#myTable tbody tr').each(function(index) {
                        $(this).find('th').text(index + 1);
                    });
                }

                $('.select2').select2();
            });
        </script>
    @endpush
@endsection
