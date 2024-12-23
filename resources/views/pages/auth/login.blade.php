@extends('layout.auth')
@section('tittle',$tittle)
@section('content')
<div class="home-center">
    <div class="home-desc-center">

        <div class="container">

            {{-- <div class="home-btn"><a href="/" class="text-white router-link-active"><i
                        class="fas fa-home h2"></i></a></div> --}}
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card">
                        <div class="card-body">
                            <div class="px-2 py-3">
                                <div class="text-center">
                                    <a href="{{ route('dashboard') }}">
                                        <img src="{{ asset('assets/images/logo-dark.png') }}" height="22" alt="logo">
                                    </a>
                                    <h5 class="text-primary mb-2 mt-4">Welcome Back !</h5>
                                    <p class="text-muted">Sign in to continue to Morvin.</p>
                                </div>
                                <form class="form-horizontal mt-4 pt-2" method="POST" action="{{ route('auth.signin') }}" enctype="multipart/form-data">
                                    @csrf

                                    <div class="mb-3">
                                        <label for="username">Username</label>
                                        <input type="text" name="username" class="form-control" id="username" required
                                            placeholder="Enter username">
                                    </div>

                                    <div class="mb-3">
                                        <label for="userpassword">Password</label>
                                        <input type="password" name="password" class="form-control" id="userpassword" required
                                            placeholder="Enter password">
                                    </div>

                                    <div class="mb-3">
                                            <div class="form-check">
                                                <input type="checkbox" name="remember_me" class="form-check-input"
                                                    id="customControlInline">
                                                <label class="form-label"
                                                    for="customControlInline">Remember me</label>
                                            </div>
                                    </div>

                                    <div>
                                        <button class="btn btn-primary w-100 waves-effect waves-light"
                                            type="submit">Log In</button>
                                    </div>

                                    <div class="mt-4 text-center">
                                        <a href="auth-recoverpw.html" class="text-muted"><i class="mdi mdi-lock me-1"></i> Forgot your password?</a>
                                    </div>
                                </form>   
                            </div>
                        </div>
                    </div>

                    {{-- <div class="mt-5 text-center text-white">
                        <p>Don't have an account ?<a href="auth-register.html" class="fw-bold text-white"> Register</a> </p>
                        <p>© <script>document.write(new Date().getFullYear())</script> Morvin. Crafted with <i class="mdi mdi-heart text-danger"></i> by Themesdesign</p>
                    </div> --}}
                </div>
            </div>

        </div>


    </div>
    <!-- End Log In page -->
</div>
@endsection