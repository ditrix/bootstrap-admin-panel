@extends('admin.layouts.sb-admin')

@section('title', __('Edit banner'))

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">{{ __('Edit banner') }}</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.banners.index') }}">{{ __('Banners') }}</a></li>
            <li class="breadcrumb-item active">{{ $banner->title }}</li>
        </ol>
        <div class="card mb-4">
            <div class="card-body">
                @php
                    $bannerPreviewSrc =
                        $banner->image_path && $banner->attachmentPublicUrl()
                            ? $banner->attachmentPublicUrl()
                            : \App\Models\Banner::attachmentPlaceholderPublicUrl();
                @endphp
                <div class="mb-3">
                    <div class="form-label mb-1">{{ __('Image preview') }}</div>
                    <div class="d-flex align-items-start gap-3 flex-wrap">
                        <img
                            id="banner-form-image-preview"
                            src="{{ $bannerPreviewSrc }}"
                            alt=""
                            width="192"
                            height="128"
                            loading="lazy"
                            class="border rounded"
                            style="max-height: 8rem; width: auto; object-fit: contain;"
                            data-revert-src="{{ $bannerPreviewSrc }}"
                        >
                        @if ($banner->image_path)
                            <button
                                type="button"
                                id="banner-remove-image-btn"
                                class="btn btn-outline-danger btn-sm align-self-center"
                                data-remove-url="{{ route('admin.banners.image.destroy', $banner) }}"
                            >
                                {{ __('Remove image') }}
                            </button>
                        @endif
                    </div>
                </div>
                <form method="post" action="{{ route('admin.banners.update', $banner) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label" for="parent_id">{{ __('Parent') }}</label>
                        <select class="form-select @error('parent_id') is-invalid @enderror" id="parent_id" name="parent_id">
                            <option value="0" @selected((int) old('parent_id', $banner->parent_id) === 0)>{{ __('Root') }}</option>
                            @foreach ($parents as $parent)
                                <option value="{{ $parent->id }}" @selected((int) old('parent_id', $banner->parent_id) === $parent->id)>
                                    {{ $parent->title }} @if ($parent->code) ({{ $parent->code }}) @endif
                                </option>
                            @endforeach
                        </select>
                        @error('parent_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="code">{{ __('Code') }}</label>
                        <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code', $banner->code) }}">
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="title">{{ __('Title') }}</label>
                        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title', $banner->title) }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="sort_no">{{ __('Sort no.') }}</label>
                        <input type="number" class="form-control @error('sort_no') is-invalid @enderror" id="sort_no" name="sort_no" value="{{ old('sort_no', $banner->sort_no) }}" min="0" required>
                        @error('sort_no')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="banner-image-input">{{ __('Replace image') }}</label>
                        <input type="file" class="@error('banner_image') is-invalid @enderror" id="banner-image-input" name="banner_image" accept="image/jpeg,image/png,image/webp">
                        @error('banner_image')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3 form-check">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1" @checked(old('is_active', $banner->is_active))>
                        <label class="form-check-label" for="is_active">{{ __('Active') }}</label>
                        @error('is_active')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">{{ __('Update') }}</button>
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
            if (inputEl && window.FilePond && previewImg) {
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
            }

            const removeBtn = document.getElementById('banner-remove-image-btn');
            if (removeBtn) {
                removeBtn.addEventListener('click', async function () {
                    const rawUrl = removeBtn.getAttribute('data-remove-url') || '';
                    if (!rawUrl) {
                        return;
                    }

                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    if (!token && typeof window.adminNotify === 'function') {
                        window.adminNotify(@json(__('Missing security token for this request.')), 'danger');

                        return;
                    }

                    let response = null;
                    try {
                        response = await fetch(rawUrl, {
                            method: 'DELETE',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                Accept: 'application/json',
                                'X-CSRF-TOKEN': token ?? '',
                            },
                        });
                    } catch {
                        if (typeof window.adminNotify === 'function') {
                            window.adminNotify(@json(__('Request failed.')), 'danger');
                        }

                        return;
                    }

                    let payload = null;
                    try {
                        payload = await response.json();
                    } catch {
                        // ignore
                    }

                    const message =
                        typeof payload?.message === 'string'
                            ? payload.message
                            : response.ok
                              ? @json(__('Done.'))
                              : @json(__('Request failed.'));

                    if (typeof window.adminNotify === 'function') {
                        window.adminNotify(message, response.ok ? 'success' : 'danger');
                    }

                    if (response.ok) {
                        window.location.reload();
                    }
                });
            }
        });
    </script>
@endpush
