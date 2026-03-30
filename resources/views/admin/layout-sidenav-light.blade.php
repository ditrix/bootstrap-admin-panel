@extends('admin.layouts.sb-admin', ['sidenavClass' => 'sb-sidenav-light'])

@section('title', 'Sidenav Light - SB Admin')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Sidenav Light</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Sidenav Light</li>
        </ol>
        <div class="card mb-4">
            <div class="card-body">
                This page demonstrates the light sidenav variation. The
                <code>.sb-sidenav-light</code>
                class can be applied to the sidebar for a lighter appearance.
            </div>
        </div>
    </div>
@endsection
