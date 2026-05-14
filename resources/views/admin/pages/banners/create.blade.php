@extends('admin.layouts.sb-admin')

@section('title', __('Create banner'))

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">{{ __('Create banner') }}</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.banners.index') }}">{{ __('Banners') }}</a></li>
            <li class="breadcrumb-item active">{{ __('Create') }}</li>
        </ol>
        <div class="card mb-4">
            <div class="card-body">
                <form method="post" action="{{ route('admin.banners.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" for="code">{{ __('Code') }}</label>
                        <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code') }}">
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="title">{{ __('Title') }}</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="sort_no">{{ __('Sort no.') }}</label>
                        <input type="number" class="form-control @error('sort_no') is-invalid @enderror" id="sort_no" name="sort_no" value="{{ old('sort_no', 0) }}" min="0" required>
                        @error('sort_no')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <div class="form-label mb-1">{{ __('Image preview') }}</div>
                        <img
                            id="banner-form-image-preview"
                            src="{{ \App\Models\Banner::attachmentPlaceholderPublicUrl() }}"
                            alt=""
                            width="192"
                            height="128"
                            loading="lazy"
                            class="border rounded d-block"
                            style="max-height: 8rem; width: auto; object-fit: contain;"
                            data-revert-src="{{ \App\Models\Banner::attachmentPlaceholderPublicUrl() }}"
                        >
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="banner-image-input">{{ __('Image') }}</label>
                        <input type="file" class="@error('banner_image') is-invalid @enderror" id="banner-image-input" name="banner_image" accept="image/jpeg,image/png,image/webp">
                        @error('banner_image')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
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
                    <a class="btn btn-outline-secondary" href="{{ route('admin.banners.index') }}">{{ __('Cancel') }}</a>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/filepond@4.32.5/dist/filepond.min.css">
    <script src="https://cdn.jsdelivr.net/npm/filepond@4.32.5/dist/filepond.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const inputEl = document.getElementById('banner-image-input');
            const previewImg = document.getElementById('banner-form-image-preview');
            if (!inputEl || !window.FilePond || !previewImg) {
                return;
            }

            let blobUrl = null;
            const revertSrc = previewImg.getAttribute('data-revert-src') || previewImg.src;

            function clearBlobUrl() {
                if (blobUrl) {
                    URL.revokeObjectURL(blobUrl);
                    blobUrl = null;
                }
            }

            function setPreviewToRevert() {
                clearBlobUrl();
                previewImg.src = revertSrc;
            }

            const pond = window.FilePond.create(inputEl, {
                credits: false,
                name: inputEl.name || 'banner_image',
                allowMultiple: false,
                instantUpload: false,
                storeAsFile: true,
                acceptedFileTypes: ['image/jpeg', 'image/png', 'image/webp'],
                labelIdle: @json(__('Drag & Drop your image or Browse')),
            });

            pond.on('addfile', (_err, item) => {
                const file = item?.file;
                if (!(file instanceof File)) {
                    return;
                }
                clearBlobUrl();
                blobUrl = URL.createObjectURL(file);
                previewImg.src = blobUrl;
            });

            pond.on('removefile', () => {
                setPreviewToRevert();
            });
        });
    </script>
@endpush
