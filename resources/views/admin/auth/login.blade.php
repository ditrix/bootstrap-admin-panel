@extends('admin.layouts.auth')

@section('title', 'Login - SB Admin')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5">
                <div class="card shadow-lg border-0 rounded-lg mt-5">
                    <div class="card-header"><h3 class="text-center font-weight-light my-4">Login</h3></div>
                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success mb-3">{{ session('status') }}</div>
                        @endif
                        <form method="post" action="{{ route('admin.login.store') }}">
                            @csrf
                            <div class="form-floating mb-3">
                                <input class="form-control @error('email') is-invalid @enderror" id="inputEmail" name="email" type="email" placeholder="name@example.com" value="{{ old('email', 'admin@mail.com') }}" autocomplete="username" required />
                                <label for="inputEmail">Email address</label>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-floating mb-3">
                                <input class="form-control @error('password') is-invalid @enderror" id="inputPassword" name="password" type="password" placeholder="Password" autocomplete="current-password" required />
                                <label for="inputPassword">Password</label>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" id="inputRememberPassword" name="remember" type="checkbox" value="1" />
                                <label class="form-check-label" for="inputRememberPassword">Remember Password</label>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                                <a class="small" href="{{ route('admin.password.request') }}">Forgot Password?</a>
                                <button type="submit" class="btn btn-primary">Login</button>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer text-center py-3">
                        <div class="small"><a href="{{ route('admin.register') }}">Need an account? Sign up!</a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
