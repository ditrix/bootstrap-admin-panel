@extends('admin.layouts.sb-admin')

@section('title', __('Static pages'))

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">{{ __('Static pages') }}</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">{{ __('Static pages') }}</li>
        </ol>

        @if (session('success'))
            <div class="alert alert-success mb-3">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger mb-3">{{ session('error') }}</div>
        @endif

        <a class="btn btn-primary mb-3" href="{{ route('admin.static-pages.create') }}">{{ __('Create page') }}</a>

        @include('admin.partials.bootstrap-table-widget', [
            'tableId' => $tableId,
            'dataUrl' => $dataUrl,
            'pageSize' => 10,
            'columns' => [
                ['field' => 'id', 'title' => 'ID', 'sortable' => true],
                ['field' => 'parent_id', 'title' => 'Parent', 'sortable' => true],
                ['field' => 'code', 'title' => 'Code', 'sortable' => true],
                ['field' => 'title', 'title' => 'Title', 'sortable' => true],
                ['field' => 'slug', 'title' => 'Slug', 'sortable' => true],
                ['field' => 'sort_no', 'title' => 'Sort', 'sortable' => true],
                ['field' => 'is_active', 'title' => 'Active', 'sortable' => true],
                ['field' => 'created_at', 'title' => 'Created at', 'sortable' => true],
                ['field' => 'updated_at', 'title' => 'Updated at', 'sortable' => true],
            ],
            'actionsFormatter' => 'adminStaticPageRowActions',
        ])
    </div>
@endsection

@push('scripts')
    <script>
        window.adminStaticPageRowActions = function (value, row) {
            const base = @json(url('/adm/static-pages'));
            return (
                '<a href="' +
                base +
                '/' +
                row.id +
                '" class="btn btn-sm btn-info">show</a> ' +
                '<a href="' +
                base +
                '/' +
                row.id +
                '/edit" class="btn btn-sm btn-warning">edit</a> ' +
                '<button type="button" class="btn btn-sm btn-danger" onclick="adminBootstrapTableDelete(\'' +
                base +
                '/' +
                row.id +
                '\')">remove</button>'
            );
        };
    </script>
@endpush
