@extends('admin.layouts.sb-admin')

@section('title', __('Edit category'))

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">{{ __('Edit category') }}</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.category-tree.index') }}">{{ __('Category Tree') }}</a></li>
            <li class="breadcrumb-item active">{{ $categoryTree->title }}</li>
        </ol>
        <div class="card mb-4">
            <div class="card-body">
                <form method="post" action="{{ route('admin.category-tree.update', $categoryTree) }}">
                    @csrf
                    @method('PUT')
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
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $categoryTree->title) }}" required maxlength="255">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="slug">{{ __('Slug') }}</label>
                        <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug" value="{{ old('slug', $categoryTree->slug) }}" maxlength="255">
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="description">{{ __('Description') }}</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $categoryTree->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="ct-content">{{ __('Content') }}</label>
                        <textarea class="form-control @error('content') is-invalid @enderror" id="ct-content" name="content" rows="8">{{ old('content', $categoryTree->content) }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3 form-check">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" @checked(old('is_active', $categoryTree->is_active))>
                        <label class="form-check-label" for="is_active">{{ __('Active') }}</label>
                        @error('is_active')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
                    <a class="btn btn-outline-secondary" href="{{ route('admin.category-tree.index') }}">{{ __('Cancel') }}</a>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jodit@4.12.2/es5/jodit.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <script src="https://cdn.jsdelivr.net/npm/jodit@4.12.2/es5/jodit.min.js" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        if (typeof Jodit !== 'undefined') {
            Jodit.make('#ct-content', {
                language: 'ru',
                height: 400,
                enableDragAndDropFileToEditor: true,
                uploader: { insertImageAsBase64URI: true },
            });
        }
    </script>
@endpush
