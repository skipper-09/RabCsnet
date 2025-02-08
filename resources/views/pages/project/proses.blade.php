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
                        <h4>Proses {{ $tittle }}</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('project') }}">{{ $tittle }}</a></li>
                            <li class="breadcrumb-item active">Proses {{ $tittle }}</li>
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
                            <form action="{{ route('project.prosesdata', ['id' => $id_project]) }}" method="POST"
                                enctype="multipart/form-data" class="">
                                @csrf
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="validationCustom01" class="form-label required">File Excel</label>
                                            <input type="file" name="excel"
                                                class="form-control @error('excel') is-invalid @enderror"
                                                id="validationCustom01">
                                            @error('excel')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror

                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="validationCustom01" class="form-label required">File Kmz</label>
                                            <input type="file" name="kmz"
                                                class="form-control @error('kmz') is-invalid @enderror"
                                                id="validationCustom01">
                                            @error('kmz')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror

                                        </div>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>No</th>
                                                    <th>Distribusi</th>
                                                    <th>Total Biaya Material</th>
                                                    <th>Total Biaya Service</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($details as $index => $detail)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td>{{ $detail['distribusi'] }}</td>
                                                        <td>{{ number_format($detail['total_material'], 0, ',', '.') }}
                                                        </td>
                                                        <td>{{ number_format($detail['total_service'], 0, ',', '.') }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th colspan="2" class="text-end">Total Semua Distribusi:</th>
                                                    <th>{{ number_format($details->sum('total_material'), 0, ',', '.') }}
                                                    </th>
                                                    <th>{{ number_format($details->sum('total_service'), 0, ',', '.') }}
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th colspan="2" class="text-end">Total:</th>
                                                    <th colspan="2">
                                                        {{ number_format($details->sum('total'), 0, ',', '.') }}
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th colspan="2" class="text-end">Backup {{ $backup_rate }}%:</th>
                                                    <th colspan="2">
                                                        {{ number_format($details->sum('backup'), 0, ',', '.') }}</th>
                                                </tr>
                                                <tr>
                                                    <th colspan="2" class="text-end">Total + Backup:</th>
                                                    <th colspan="2">
                                                        {{ number_format($details->sum('total_with_backup'), 0, ',', '.') }}
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th colspan="2" class="text-end">Ppn {{ $ppn_rate }}%:</th>
                                                    <th colspan="2">
                                                        {{ number_format($details->sum('ppn'), 0, ',', '.') }}</th>
                                                </tr>
                                                <tr>
                                                    <th colspan="2" class="text-end">Total + Ppn:</th>
                                                    <th colspan="2">
                                                        {{ number_format($details->sum('total_with_ppn'), 0, ',', '.') }}
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th colspan="2" class="text-end">Total + Ppn + Backup:</th>
                                                    <th colspan="2">
                                                        {{ number_format($details->sum('total_with_ppn_backup'), 0, ',', '.') }}
                                                    </th>
                                                </tr>
                                            </tfoot>
                                            <input type="hidden" name="total_material"
                                                value="{{ $details->sum('total_material') }}" id="total_material">
                                            <input type="hidden" name="total_service"
                                                value="{{ $details->sum('total_service') }}" id="total_service">
                                            <input type="hidden" name="total" value="{{ $details->sum('total') }}"
                                                id="total">
                                            <input type="hidden" name="ppn" value="{{ $details->sum('ppn') }}"
                                                id="ppn">
                                            <input type="hidden" name="total_with_ppn"
                                                value="{{ $details->sum('total_with_ppn') }}" id="total_with_ppn">

                                        </table>
                                    </div>
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
