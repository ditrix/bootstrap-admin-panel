@extends('admin.layouts.sb-admin')

@section('title', __('Static pages'))

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">{{ __('Static pages') }}</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">{{ __('Static pages') }}</li>
        </ol>

        <a class="btn btn-primary mb-3" href="{{ route('admin.static-pages.create') }}">{{ __('Create page') }}</a>

        @include('admin.partials.bootstrap-table-widget', [
            'tableId' => $tableId,
            'dataUrl' => $dataUrl,
            'pageSize' => 10,
            'columns' => [
                ['field' => 'id', 'title' => 'ID', 'sortable' => true],
                ['field' => 'code', 'title' => 'Code', 'sortable' => true],
                ['field' => 'title', 'title' => 'Title', 'sortable' => true],
                ['field' => 'slug', 'title' => 'Slug', 'sortable' => true],
                ['field' => 'sort_no', 'title' => 'Sort', 'sortable' => true],
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
            'actionsFormatter' => 'adminStaticPageRowActions',
        ])
    </div>
@endsection

@push('scripts')
    <script>


//  https://fontawesome.com/icons/eye?f=classic&s=light  
// https://fontawesome.com/icons/trash?f=classic&s=light

        window.adminStaticPageRowActions = function (value, row) {
            const id = Number(row.id);
            if (!Number.isInteger(id) || id < 1) {
                return '';
            }
            const base = @json(route('admin.static-pages.index'));
            return (
                /*'<a href="' +
                base +
                '/' +
                row.id +
                '" class="btn btn-sm btn-info">show</a> ' +*/

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
