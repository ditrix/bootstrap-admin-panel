@extends('admin.layouts.sb-admin')

@section('title', __('Add menu item'))

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">{{ __('Add menu item') }}</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.main-menu.index') }}">{{ __('Main menu') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Create') }}</li>
        </ol>
        <div class="card mb-4">
            <div class="card-body">
                <form method="post" action="{{ route('admin.main-menu.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" for="parent_id">{{ __('Parent') }}</label>
                        <select class="form-select @error('parent_id') is-invalid @enderror" id="parent_id" name="parent_id">
                            {!! $parentOptionsHtml !!}
                        </select>
                        @error('parent_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="title">{{ __('Title') }}</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required maxlength="255">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="slug">{{ __('Slug') }}</label>
                        <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug') }}" maxlength="255">
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3 form-check">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" @checked(old('is_active', true))>
                        <label class="form-check-label" for="is_active">{{ __('Active') }}</label>
                        @error('is_active')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.main-menu.index') }}">{{ __('Cancel') }}</a>
                </form>
            </div>
        </div>
    </div>
@endsection
