@extends('admin.layouts.sb-admin')

@section('title', __('Add administrator'))

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">{{ __('Add administrator') }}</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.administrators.index') }}">{{ __('Administrators') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Create') }}</li>
        </ol>
        <div class="card mb-4">
            <div class="card-body">
                <form method="post" action="{{ route('admin.administrators.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" for="name">{{ __('Name') }}</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required autocomplete="name">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="email">{{ __('Email') }}</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autocomplete="email">
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @include('admin.partials.admin-password-fields', ['requirePassword' => true])
                    <div class="mb-3 form-check">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" @checked(old('is_active', true))>
                        <label class="form-check-label" for="is_active">{{ __('Active') }}</label>
                    </div>
                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.administrators.index') }}">{{ __('Cancel') }}</a>
                </form>
            </div>
        </div>
    </div>
@endsection
