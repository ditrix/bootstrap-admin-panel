@extends('admin.layouts.sb-admin')

@section('title', $staticPage->title)

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">{{ $staticPage->title }}</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.static-pages.index') }}">{{ __('Static pages') }}</a></li>
            <li class="breadcrumb-item active">{{ $staticPage->code }}</li>
        </ol>
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>{{ __('Details') }}</span>
                <div>
                    <a class="btn btn-sm btn-warning" href="{{ route('admin.static-pages.edit', $staticPage) }}">{{ __('Edit') }}</a>
                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('admin.static-pages.index') }}">{{ __('Back to list') }}</a>
                </div>
            </div>
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-3">ID</dt>
                    <dd class="col-sm-9">{{ $staticPage->id }}</dd>
                    <dt class="col-sm-3">{{ __('Parent ID') }}</dt>
                    <dd class="col-sm-9">{{ $staticPage->parent_id }}</dd>
                    <dt class="col-sm-3">{{ __('Code') }}</dt>
                    <dd class="col-sm-9">{{ $staticPage->code }}</dd>
                    <dt class="col-sm-3">{{ __('Slug') }}</dt>
                    <dd class="col-sm-9">{{ $staticPage->slug }}</dd>
                    <dt class="col-sm-3">{{ __('Sort no.') }}</dt>
                    <dd class="col-sm-9">{{ $staticPage->sort_no }}</dd>
                    <dt class="col-sm-3">{{ __('Active') }}</dt>
                    <dd class="col-sm-9">{{ $staticPage->is_active ? __('Yes') : __('No') }}</dd>
                    <dt class="col-sm-3">{{ __('Description') }}</dt>
                    <dd class="col-sm-9">{!! nl2br(e($staticPage->description ?? '')) !!}</dd>
                    <dt class="col-sm-3">{{ __('Content') }}</dt>
                    <dd class="col-sm-9">{!! $staticPage->content !!}</dd>
                </dl>
            </div>
        </div>
    </div>
@endsection
