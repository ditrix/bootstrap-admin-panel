@extends('admin.layouts.sb-admin')

@section('title', 'Dashboard - SB Admin')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Dashboard</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>
        <div class="row">
            @foreach ($cards as $card)
                <div class="col-xl-3 col-md-6">
                    <div class="card bg-{{ $card['variant'] }} text-white mb-4">
                        <div class="card-body">{{ $card['label'] }}</div>
                        <div class="card-footer d-flex align-items-center justify-content-between">
                            <a class="small text-white stretched-link" href="{{ $card['href'] }}">View Details</a>
                            <div class="small text-white"><i class="fas fa-angle-right"></i></div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        <div class="row">
            <div class="col-xl-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-chart-area me-1"></i>
                        Area Chart Example
                    </div>
                    <div class="card-body"><canvas id="myAreaChart" width="100%" height="40"></canvas></div>
                </div>
            </div>
            <div class="col-xl-6">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-chart-bar me-1"></i>
                        Bar Chart Example
                    </div>
                    <div class="card-body"><canvas id="myBarChart" width="100%" height="40"></canvas></div>
                </div>
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

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js" crossorigin="anonymous"></script>
    <script src="{{ \App\Helpers\AdminHelper::maketAsset('assets/demo/chart-area-demo.js') }}"></script>
    <script src="{{ \App\Helpers\AdminHelper::maketAsset('assets/demo/chart-bar-demo.js') }}"></script>
@endpush
