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
                    <div class="card admin-dashboard-summary-card text-body mb-4">
                        <div class="card-body">{{ $card['label'] }}</div>
                        <div class="card-footer d-flex align-items-center justify-content-between">
                            <a class="small stretched-link" href="{{ $card['href'] }}">View Details</a>
                            <div class="small text-body-secondary"><i class="fas fa-angle-right"></i></div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
