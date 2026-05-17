@extends('admin.layouts.sb-admin')

@section('title', __('301 Redirects'))

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">{{ __('301 Redirects') }}</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">{{ __('301 Redirects') }}</li>
        </ol>

        <a class="btn btn-primary mb-3" href="{{ route('admin.seo-redirects.create') }}">{{ __('Create redirect') }}</a>

        @include('admin.partials.bootstrap-table-widget', [
            'tableId' => $tableId,
            'dataUrl' => $dataUrl,
            'pageSize' => 10,
            'columns' => [
                ['field' => 'id', 'title' => __('ID'), 'sortable' => true],
                [
                    'field' => 'slug_from',
                    'title' => __('From (path)'),
                    'sortable' => true,
                    'formatter' => 'adminSeoRedirectSlugFromCell',
                    'escape' => false,
                ],
                [
                    'field' => 'slug_to',
                    'title' => __('To'),
                    'sortable' => true,
                    'formatter' => 'adminSeoRedirectSlugToCell',
                    'escape' => false,
                ],
                [
                    'field' => 'is_active',
                    'title' => __('Active'),
                    'sortable' => true,
                    'formatter' => 'adminBootstrapTableBooleanIcon',
                    'escape' => false,
                ],
                ['field' => 'created_at', 'title' => __('Created at'), 'sortable' => true],
                ['field' => 'updated_at', 'title' => __('Updated at'), 'sortable' => true],
            ],
            'actionsFormatter' => 'adminSeoRedirectRowActions',
        ])
    </div>
@endsection

@push('scripts')
    <script>
        function adminSeoRedirectEscapeHtml(s) {
            return String(s)
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;');
        }

        window.adminSeoRedirectSlugFromCell = function (value) {
            return '<code>/' + adminSeoRedirectEscapeHtml(value ?? '') + '</code>';
        };

        window.adminSeoRedirectSlugToCell = function (value) {
            return '<code class="text-break">' + adminSeoRedirectEscapeHtml(value ?? '') + '</code>';
        };

        window.adminSeoRedirectRowActions = function (value, row) {
            const id = Number(row.id);
            if (!Number.isInteger(id) || id < 1) {
                return '';
            }
            const base = @json(route('admin.seo-redirects.index'));
            return (
                '<a href="' +
                base +
                '/' +
                id +
                '/edit" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a> ' +
                '<button type="button" class="btn btn-sm btn-outline-danger" onclick="adminBootstrapTableDelete(\'' +
                base +
                '/' +
                id +
                '\')"><i class="fas fa-trash"></i></button>'
            );
        };
    </script>
@endpush
