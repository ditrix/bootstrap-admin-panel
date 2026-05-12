@extends('admin.layouts.sb-admin')

@section('title', __('Banners'))

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">{{ __('Banners') }}</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">{{ __('Banners') }}</li>
        </ol>

        <a class="btn btn-primary mb-3" href="{{ route('admin.banners.create') }}">{{ __('Create banner') }}</a>

        @include('admin.partials.bootstrap-table-widget', [
            'tableId' => $tableId,
            'dataUrl' => $dataUrl,
            'pageSize' => 10,
            'columns' => [
                ['field' => 'id', 'title' => 'ID', 'sortable' => true],
                ['field' => 'code', 'title' => 'Code', 'sortable' => true],
                [
                    'field' => 'preview_url',
                    'title' => 'Preview',
                    'sortable' => false,
                    'formatter' => 'adminBannerBootstrapTablePreview',
                    'escape' => false,
                ],
                ['field' => 'title', 'title' => 'Title', 'sortable' => true],
                [
                    'field' => 'is_active',
                    'title' => 'Active',
                    'sortable' => true,
                    'formatter' => 'adminBootstrapTableBooleanIcon',
                    'escape' => false,
                ],
                ['field' => 'created_at', 'title' => 'Created at', 'sortable' => true],
                ['field' => 'updated_at', 'title' => 'Updated at', 'sortable' => true],
            ],
            'actionsFormatter' => 'adminBannerRowActions',
        ])
    </div>
@endsection

@push('scripts')
    <script>
        function adminBootstrapTableEscapeHtmlAttr(raw) {
            return String(raw)
                .replace(/&/g, '&amp;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');
        }

        window.adminBannerBootstrapTablePreview = function (_value, row) {
            const u = typeof row.preview_url === 'string' ? row.preview_url : '';
            if (!u) {
                return '';
            }
            return (
                '<img src="' +
                adminBootstrapTableEscapeHtmlAttr(u) +
                '" class="border rounded" width="72" height="48" loading="lazy" alt="">'
            );
        };

        window.adminBannerRowActions = function (_value, row) {
            const id = Number(row.id);
            if (!Number.isInteger(id) || id < 1) {
                return '';
            }
            const base = @json(route('admin.banners.index'));
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
