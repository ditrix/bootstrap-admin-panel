@extends('admin.layouts.sb-admin')

@section('title', 'Blank Page - SB Admin')

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">Blank Page</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Blank</li>
        </ol>
        <div class="card mb-4">
            <div class="card-body">
                Start adding your content here, or use this empty shell for a custom layout section.
            </div>
        </div>
    </div>
@endsection
