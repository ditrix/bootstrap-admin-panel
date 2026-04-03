@extends('admin.layouts.sb-admin')

@section('title', 'Tables - SB Admin')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Tables</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Tables</li>
        </ol>
        <div class="card mb-4">
            <div class="card-body">
                Bootstrap Table loads rows from the server as JSON (<code>total</code> / <code>rows</code>). Shared helpers
                (for example <code>adminBootstrapTableDelete</code>) live in
                <code>resources/themes/admin/assets/js/admin-bootstrap-table.js</code> and are loaded via Vite with the
                <code>admin/partials/bootstrap-table-widget</code> partial. See
                <a target="_blank" href="https://bootstrap-table.com/">bootstrap-table documentation</a>.
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-table me-1"></i>
                Employees (server-side)
            </div>
            <div class="card-body">
                @include('admin.partials.bootstrap-table-widget', [
                    'tableId' => $tableId,
                    'dataUrl' => $dataUrl,
                    'pageSize' => 10,
                    'columns' => [
                        ['field' => 'id', 'title' => 'ID', 'sortable' => true],
                        ['field' => 'name', 'title' => 'Name', 'sortable' => true],
                        ['field' => 'position', 'title' => 'Position', 'sortable' => true],
                        ['field' => 'office', 'title' => 'Office', 'sortable' => true],
                        ['field' => 'age', 'title' => 'Age', 'sortable' => true],
                        ['field' => 'start_date', 'title' => 'Start date', 'sortable' => true],
                        ['field' => 'salary', 'title' => 'Salary', 'sortable' => true],
                        ['field' => 'created_at', 'title' => 'Created at', 'sortable' => true],
                        ['field' => 'updated_at', 'title' => 'Updated at', 'sortable' => true],
                    ],
                ])
            </div>
        </div>
    </div>
@endsection
