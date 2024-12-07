@extends('layout.base')

@section('tittle', $tittle)

@section('content')
    <div class="page-title-box">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-sm-6">
                    <div class="page-title">
                        <h4>{{ $tittle }}</h4>
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Project</li>
                            <li class="breadcrumb-item active">{{ $tittle }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Upload ATP File for Project: {{ $project->name }}</div>

                    <div class="card-body">
                        <form action="{{ route('project.store-atp', $project->id) }}" method="POST"
                            enctype="multipart/form-data">
                            @csrf

                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="file" class="form-label required">ATP File</label>
                                    <input type="file" class="form-control @error('file') is-invalid @enderror"
                                        id="file" name="file" required accept=".pdf,.doc,.docx,.xls,.xlsx">
                                    <small class="form-text text-muted">
                                        Allowed file types: PDF, DOC, DOCX, XLS, XLSX (Max 10MB)
                                    </small>
                                    @error('file')
                                        <div class="invalid-feedback">
                                            {{ $message }}
                                        </div>
                                    @enderror
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">Upload ATP File</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
