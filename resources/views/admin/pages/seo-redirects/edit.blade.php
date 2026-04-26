@extends('admin.layouts.sb-admin')

@section('title', __('Edit redirect'))

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">{{ __('Edit redirect') }}</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.seo-redirects.index') }}">{{ __('301 Redirects') }}</a></li>
            <li class="breadcrumb-item active">{{ $seoRedirect->slug_from }}</li>
        </ol>
        <div class="card mb-4">
            <div class="card-body">
                <form method="post" action="{{ route('admin.seo-redirects.update', $seoRedirect) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label" for="slug_from">{{ __('From (path)') }}</label>
                        <input type="text" class="form-control @error('slug_from') is-invalid @enderror" id="slug_from" name="slug_from" value="{{ old('slug_from', $seoRedirect->slug_from) }}" required>
                        @error('slug_from')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="slug_to">{{ __('To') }}</label>
                        <input type="text" class="form-control @error('slug_to') is-invalid @enderror" id="slug_to" name="slug_to" value="{{ old('slug_to', $seoRedirect->slug_to) }}" required>
                        @error('slug_to')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3 form-check">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" @checked(old('is_active', $seoRedirect->is_active))>
                        <label class="form-check-label" for="is_active">{{ __('Active') }}</label>
                    </div>
                    <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.seo-redirects.index') }}">{{ __('Cancel') }}</a>
                </form>
            </div>
        </div>
    </div>
@endsection
