@extends('backend.layout.auth.auth-app')
@section('title', 'Sign In | admin')
@section('content')
    <form method="post" action="{{route('auth.login.post')}}">
        @csrf
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="username" 
                name="email"
            placeholder="Enter email">
        </div>

        <div class="mb-3">
            {{-- <div class="float-end">
                <a href="auth-pass-reset-cover.html" class="text-muted">Forgot password?</a>
            </div> --}}
            <label class="form-label" for="password-input">Password</label>
            <div class="position-relative auth-pass-inputgroup mb-3">
                <input type="password" class="form-control pe-5 password-input" id="password-input"
                    name="password"
                 placeholder="Enter password">
                <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon" type="button" id="password-addon"><i class="ri-eye-fill align-middle"></i></button>
            </div>
        </div>

        {{-- <div class="form-check">
            <input class="form-check-input" type="checkbox" value="" id="auth-remember-check">
            <label class="form-check-label" for="auth-remember-check">Remember me</label>
        </div> --}}

        <div class="mt-4">
            <button class="btn btn-success w-100" type="submit">Sign In</button>
        </div>

        {{-- <div class="mt-4 text-center">
            <div class="signin-other-title">
                <h5 class="fs-13 mb-4 title">Sign In with</h5>
            </div>

            <div>
                <button type="button" class="btn btn-primary btn-icon waves-effect waves-light"><i class="ri-facebook-fill fs-16"></i></button>
                <button type="button" class="btn btn-danger btn-icon waves-effect waves-light"><i class="ri-google-fill fs-16"></i></button>
                <button type="button" class="btn btn-dark btn-icon waves-effect waves-light"><i class="ri-github-fill fs-16"></i></button>
                <button type="button" class="btn btn-info btn-icon waves-effect waves-light"><i class="ri-twitter-fill fs-16"></i></button>
            </div>
        </div> --}}

    </form>
@endsection


@push('srcipts-bottom')
    <!-- password-custom logi -->
    <script src="{{asset('')}}assets/js/raihan/password-toggle.js"></script>
@endpush