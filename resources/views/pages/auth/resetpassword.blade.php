@extends('layout.auth')

@section('tittle', $tittle)

@section('content')
    <div class="home-center">
        <div class="home-desc-center">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card shadow-sm">
                            <div class="card-body text-center py-5">
                                <div>
                                    <img src="{{ asset('storage/' . getAppLogo()) }}" alt="Logo" height="50">
                                </div>
                                <h4 class="text-danger mt-4">Reset Password</h4>
                                <p class="text-muted">Untuk mereset password Anda, harap hubungi admin kami.</p>

                                <div class="mt-4">
                                    <a href="mailto:teamIT@myinternusa.com" class="btn btn-primary">
                                        Hubungi Admin
                                    </a>
                                </div>

                                <div class="mt-3">
                                    <a href="{{ route('login') }}" class="text-muted">
                                        <i class="mdi mdi-arrow-left"></i> Kembali ke Login
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <p class="text-muted mb-0">&copy; {{ date('Y') }} {{ getAppName() }}. All Rights Reserved.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
