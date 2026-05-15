@extends('admin.layouts.sb-admin')

@section('title', __('Permissions'))

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">{{ __('Permissions') }}</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ $adminHomeUrl }}">Dashboard</a></li>
            <li class="breadcrumb-item active">{{ __('Permissions') }}</li>
        </ol>

        @include('admin.partials.bootstrap-table-widget', [
            'tableId' => $tableId,
            'dataUrl' => $dataUrl,
            'pagination' => false,
            'search' => false,
            'serverSidePagination' => false,
            'pageSize' => 10,
            'columns' => [
                ['field' => 'id', 'title' => __('ID')],
                ['field' => 'name', 'title' => __('Name')],
                ['field' => 'guard_name', 'title' => __('Guard')],
                ['field' => 'created_at', 'title' => __('Created at')],
                ['field' => 'updated_at', 'title' => __('Updated at')],
            ],
            'actionsFormatter' => '',
        ])
    </div>
@endsection
