@extends('admin.layouts.auth')

@section('title', 'Set new password - SB Admin')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5">
                <div class="card shadow-lg border-0 rounded-lg mt-5">
                    <div class="card-header"><h3 class="text-center font-weight-light my-4">Reset password</h3></div>
                    <div class="card-body">
                        <form method="post" action="{{ route('admin.password.update') }}">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}" />
                            <div class="form-floating mb-3">
                                <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email', $email) }}" required autocomplete="username" />
                                <label for="email">Email</label>
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-floating mb-3">
                                <input class="form-control @error('password') is-invalid @enderror" id="password" name="password" type="password" required autocomplete="new-password" />
                                <label for="password">New password</label>
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="form-floating mb-3">
                                <input class="form-control" id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" />
                                <label for="password_confirmation">Confirm password</label>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Save password</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
