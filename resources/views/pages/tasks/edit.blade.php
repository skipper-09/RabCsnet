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
                        <h4>Edit {{ $tittle }}</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('tasks') }}">{{ $tittle }}</a></li>
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
                            <form action="{{ route('tasks.update', ['id' => $tasks->id]) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                @method('PUT')
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="mb-3">
                                            <label for="validationCustom01" class="form-label required">Judul</label>
                                            <input type="text" name="title" value="{{ $tasks->title }}"
                                                class="form-control @error('title') is-invalid @enderror"
                                                id="validationCustom01">
                                            @error('title')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="validationCustom01" class="form-label">
                                                Sub Tugas
                                            </label>
                                            <select name="parent_id"
                                                class="form-control select2 @error('parent_id') is-invalid @enderror"
                                                id="parent_id">
                                                <option value="">Pilih Sub Tugas (Opsional)</option>
                                                @foreach ($parentTasks as $parentTask)
                                                    <option value="{{ $parentTask->id }}"
                                                        {{ old('parent_id', $tasks->parent_id) == $parentTask->id ? 'selected' : '' }}>
                                                        {{ $parentTask->title }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('parent_id')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="validationCustom01" class="form-label required">
                                                Project
                                            </label>
                                            <select name="project_id"
                                                class="form-control select2 @error('project_id') is-invalid @enderror"
                                                aria-label="Default select example">
                                                <option selected>Pilih Project</option>
                                                @foreach ($projects as $project)
                                                    <option value="{{ $project->id }}"
                                                        {{ old('project_id', $tasks->project_id) == $project->id ? 'selected' : '' }}>
                                                        {{ $project->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('project_id')
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
                                            <label for="start_date" class="form-label required">Tanggal Mulai</label>
                                            <input type="date"
                                                class="form-control @error('start_date') is-invalid @enderror"
                                                id="start_date" name="start_date"
                                                value="{{ old('start_date', $tasks->start_date) }}">
                                            @error('start_date')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="end_date" class="form-label required">Tanggal Selesai</label>
                                            <input type="date"
                                                class="form-control @error('end_date') is-invalid @enderror" id="end_date"
                                                name="end_date" value="{{ old('end_date', $tasks->end_date) }}">
                                            @error('end_date')
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
                                                Status
                                            </label>
                                            <select name="status"
                                                class="form-control select2 @error('status') is-invalid @enderror"
                                                aria-label="Default select example">
                                                <option value="">Pilih Status</option>
                                                <option value="pending"
                                                    {{ old('status', $tasks->status) == 'pending' ? 'selected' : '' }}>
                                                    Pending
                                                </option>
                                                <option value="in_progres"
                                                    {{ old('status', $tasks->status) == 'in_progres' ? 'selected' : '' }}>
                                                    In Progress
                                                </option>
                                                <option value="complated"
                                                    {{ old('status', $tasks->status) == 'complated' ? 'selected' : '' }}>
                                                    Completed
                                                </option>
                                                <option value="canceled"
                                                    {{ old('status', $tasks->status) == 'canceled' ? 'selected' : '' }}>
                                                    Canceled
                                                </option>
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
                                                Prioritas
                                            </label>
                                            <select name="priority"
                                                class="form-control select2 @error('priority') is-invalid @enderror"
                                                aria-label="Default select example">
                                                <option selected value="">Pilih Prioritas</option>
                                                <option value="low"
                                                    {{ old('priority', $tasks->priority) == 'low' ? 'selected' : '' }}>
                                                    Low
                                                </option>
                                                <option value="medium"
                                                    {{ old('priority', $tasks->priority) == 'medium' ? 'selected' : '' }}>
                                                    Medium
                                                </option>
                                                <option value="high"
                                                    {{ old('priority', $tasks->priority) == 'high' ? 'selected' : '' }}>
                                                    High
                                                </option>
                                            </select>
                                            @error('priority')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label for="validationCustom01" class="form-label">
                                            Deskripsi
                                        </label>
                                        <textarea id="textarea" name="description" class="form-control @error('description') is-invalid @enderror"
                                            maxlength="225" rows="3" placeholder="Enter Description">{{ old('description', $tasks->description) }}</textarea>
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
