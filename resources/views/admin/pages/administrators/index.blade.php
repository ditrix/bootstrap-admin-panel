@extends('admin.layouts.sb-admin')

@section('title', __('Administrators'))

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">{{ __('Administrators') }}</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">{{ __('Administrators') }}</li>
        </ol>

        <a class="btn btn-primary mb-3" href="{{ route('admin.administrators.create') }}">{{ __('Add administrator') }}</a>

        @include('admin.partials.bootstrap-table-widget', [
            'tableId' => $tableId,
            'dataUrl' => $dataUrl,
            'pageSize' => 10,
            'columns' => [
                ['field' => 'id', 'title' => __('ID'), 'sortable' => true],
                ['field' => 'name', 'title' => __('Name'), 'sortable' => true],
                ['field' => 'email', 'title' => __('Email'), 'sortable' => true],
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
            'actionsFormatter' => 'adminAdministratorRowActions',
        ])
    </div>
@endsection

@push('scripts')
    <script>
        window.adminAdministratorRowActions = function (value, row) {
            const id = Number(row.id);
            if (!Number.isInteger(id) || id < 1) {
                return '';
            }
            const base = @json(route('admin.administrators.index'));
            const currentId = Number(@json($currentAdminId));
            const deleteDisabled =
                '<button type="button" class="btn btn-sm btn-outline-danger" disabled title="' +
                @json(__('You cannot delete your own account')) +
                '"><i class="fas fa-trash"></i></button>';
            const deleteBtn =
                '<button type="button" class="btn btn-sm btn-outline-danger" onclick="adminBootstrapTableDelete(\'' +
                base +
                '/' +
                id +
                '\')"><i class="fas fa-trash"></i></button>';
            return (
                '<a href="' +
                base +
                '/' +
                id +
                '/edit" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a> ' +
                (id === currentId ? deleteDisabled : deleteBtn)
            );
        };
    </script>
@endpush
