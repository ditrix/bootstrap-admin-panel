@extends('admin.layouts.sb-admin')

@section('title', __('Edit static page'))

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">{{ __('Edit static page') }}</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.static-pages.index') }}">{{ __('Static pages') }}</a></li>
            <li class="breadcrumb-item active">{{ $staticPage->title }}</li>
        </ol>
        <div class="card mb-4">
            <div class="card-body">
                <form method="post" action="{{ route('admin.static-pages.update', $staticPage) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label" for="parent_id">{{ __('Parent') }}</label>
                        <select class="form-select @error('parent_id') is-invalid @enderror" id="parent_id" name="parent_id">
                            <option value="0" @selected((int) old('parent_id', $staticPage->parent_id) === 0)>{{ __('Root') }}</option>
                            @foreach ($parents as $parent)
                                <option value="{{ $parent->id }}" @selected((int) old('parent_id', $staticPage->parent_id) === $parent->id)>
                                    {{ $parent->title }} ({{ $parent->code }})
                                </option>
                            @endforeach
                        </select>
                        @error('parent_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="code">{{ __('Code') }}</label>
                        <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $staticPage->code) }}" required>
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="title">{{ __('Title') }}</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $staticPage->title) }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="slug">{{ __('Slug') }}</label>
                        <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug', $staticPage->slug) }}" required>
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="description">{{ __('Description') }}</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $staticPage->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="content">{{ __('Content') }}</label>
                        <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content" rows="8">{{ old('content', $staticPage->content) }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="sort_no">{{ __('Sort no.') }}</label>
                        <input type="number" class="form-control @error('sort_no') is-invalid @enderror" id="sort_no" name="sort_no" value="{{ old('sort_no', $staticPage->sort_no) }}" min="0" required>
                        @error('sort_no')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3 form-check">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" @checked(old('is_active', $staticPage->is_active))>
                        <label class="form-check-label" for="is_active">{{ __('Active') }}</label>
                        @error('is_active')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.static-pages.index') }}">{{ __('Cancel') }}</a>
                </form>
            </div>
        </div>
    </div>
@endsection
