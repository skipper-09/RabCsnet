@extends('layout.base')
@section('tittle', "Edit $tittle")

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
                            <li class="breadcrumb-item"><a href="{{ route('project') }}">{{ $tittle }}</a></li>
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
                            <form
                                action="{{ route('projectdetail.update', ['id' => $detailproject->project_id, 'iddetail' => $detailproject->id]) }}"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="validationCustom01" class="form-label required">Nama</label>
                                            <input type="text" name="name"
                                                value="{{ old('name', $detailproject->name) }}"
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
                                                    <option value="{{ $type->id }}"
                                                        {{ $detailproject->type_project_id == $type->id ? 'selected' : '' }}>
                                                        {{ $type->name }}
                                                    </option>
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
                                                maxlength="225" rows="3">{{ old('description', $detailproject->description) }}</textarea>
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
                                                        <th>Material</th>
                                                        <th>Jasa</th>
                                                        <th>Jumlah</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($projectDetails as $index => $detail)
                                                        <tr>
                                                            <th scope="row">{{ $index + 1 }}</th>
                                                            <td>
                                                                <select name="item_id[]" class="form-control select2 w-100">
                                                                    <option selected>Pilih Item</option>
                                                                    @foreach ($item as $unit)
                                                                        <option value="{{ $unit->id }}"
                                                                            {{ $detail->item_id == $unit->id ? 'selected' : '' }}
                                                                            data-material-price="{{ $unit->material_price }}"
                                                                            data-service-price="{{ $unit->service_price }}">
                                                                            {{ $unit->name }}
                                                                        </option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td>
                                                                {{-- <input type="hidden" name="material[]" value="off"> --}}
                                                                <input
                                                                    class="form-check-input permission-checkbox material-checkbox"
                                                                    type="checkbox" name="material[]" value="on"
                                                                    @checked($detail->cost_material != 0)>
                                                                <span class="material-price" style="display:none;">Rp
                                                                    0</span>
                                                            </td>

                                                            <td>
                                                                {{-- <input type="hidden" name="service_id[]" value="off"> --}}
                                                                <input
                                                                    class="form-check-input permission-checkbox service-checkbox"
                                                                    type="checkbox" name="service_id[]" value="on"
                                                                    @checked($detail->cost_service != 0)>
                                                                <span class="service-price" style="display:none;">Rp
                                                                    0</span>
                                                            </td>

                                                            <td>
                                                                <input type="text" name="quantity[]"
                                                                    value="{{ $detail->quantity }}" class="form-control"
                                                                    inputmode="numeric">
                                                            </td>
                                                            <td>
                                                                <button type="button"
                                                                    class="btn btn-danger btn-sm delete-btn">
                                                                    Delete
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
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

                function numberFormat(value, decimals = 0, decimalSeparator = ',', thousandSeparator = '.') {
                    value = parseFloat(value) || 0; // Pastikan angka valid
                    value = value.toFixed(decimals); // Tetapkan jumlah desimal yang benar

                    const parts = value.toString().split('.');
                    parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, thousandSeparator);

                    return 'Rp ' + parts.join(decimalSeparator); // Tambahkan "Rp" di depan hasil format
                }

                function updatePrice(row) {
                    const selectedItem = $(row).find('select[name="item_id[]"] option:selected');
                    const materialPrice = selectedItem.data('material-price') || 0;
                    const servicePrice = selectedItem.data('service-price') || 0;

                    const materialChecked = $(row).find('.material-checkbox').prop('checked');
                    const serviceChecked = $(row).find('.service-checkbox').prop('checked');

                    if (materialChecked) {
                        $(row).find('.material-price').show().text(numberFormat(materialPrice.toLocaleString()));
                    } else {
                        $(row).find('.material-price').hide();
                    }

                    if (serviceChecked) {
                        $(row).find('.service-price').show().text(numberFormat(servicePrice.toLocaleString()));
                    } else {
                        $(row).find('.service-price').hide();
                    }
                }
                // Tambah baris baru
                $('#addRow').click(function() {
                    const tableBody = $('#myTable tbody');
                    const rowIndex = tableBody.children('tr').length + 1;
                    const newRow = `
                    <tr>
                        <th scope="row">${rowIndex}</th>
                        <td>
                            <select name="item_id[]" class="form-control select2 w-100">
                    <option selected>Pilih Item</option>
                    @foreach ($item as $unit)
                    <option value="{{ $unit->id }}" data-material-price="{{ $unit->material_price }}" data-service-price="{{ $unit->service_price }}">
                        {{ $unit->name }}
                    </option>
                    @endforeach
                </select>
                        </td>
                        
                         <td>
                            <input class="form-check-input permission-checkbox material-checkbox"
                                    type="checkbox" name="material[]" >
                            <span class="material-price" style="display:none;">Rp 0</span> 
                        </td>
                                                    
                        <td>
                            <input class="form-check-input permission-checkbox service-checkbox"
                                    type="checkbox" name="service_id[]" >
                            <span class="service-price" style="display:none;">Rp 0</span>
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

                $('#myTable').on('change', '.material-checkbox, .service-checkbox', function() {
                    const row = $(this).closest('tr');
                    updatePrice(row);
                });

                // Ketika select item berubah, perbarui harga
                $('#myTable').on('change', 'select[name="item_id[]"]', function() {
                    const row = $(this).closest('tr');
                    updatePrice(row);
                });

                // Inisialisasi harga untuk baris yang sudah ada
                $('#myTable tbody tr').each(function() {
                    updatePrice($(this));
                });

                $('.select2').select2();


                //sumbit button handler

                $("form").on("submit", function() {
                    $("input[type=checkbox]").each(function() {
                        if (!$(this).prop("checked")) {
                            $(this).after(
                                $('<input>').attr({
                                    type: 'hidden',
                                    name: $(this).attr('name'),
                                    value: 'off'
                                })
                            );
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
