@extends('admin.layouts.auth')

@section('title', 'Password Reset - SB Admin')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5">
                <div class="card shadow-lg border-0 rounded-lg mt-5">
                    <div class="card-header"><h3 class="text-center font-weight-light my-4">Password Recovery</h3></div>
                    <div class="card-body">
                        <div class="small mb-3 text-center">Enter your email address and we will send a link to reset your password.</div>
                        @if (session('status'))
                            <div class="alert alert-success mb-3">{{ session('status') }}</div>
                        @endif
                        <form method="post" action="{{ route('admin.password.email') }}">
                            @csrf
                            <div class="form-floating mb-3">
                                <input class="form-control @error('email') is-invalid @enderror" id="inputEmail" name="email" type="email" placeholder="name@example.com" value="{{ old('email') }}" required />
                                <label for="inputEmail">Email address</label>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Reset password</button>
                            </div>
                        </form>
                        <div class="text-center mt-4">
                            <a class="small" href="{{ route('admin.entry') }}">Return to login</a>
                        </div>
                    </div>
                    <div class="card-footer text-center py-3">
                        <div class="small"><a href="{{ route('admin.register') }}">Need an account? Sign up!</a></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
