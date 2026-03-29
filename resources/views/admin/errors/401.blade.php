@extends('admin.layouts.error')

@section('title', $page->title())

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="text-center mt-4">
                    <h1 class="display-1">{{ $page->displayCode() }}</h1>
                    <p class="lead">{{ $page->lead() }}</p>
                    @if ($page->extraMessage())
                        <p>{{ $page->extraMessage() }}</p>
                    @endif
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-arrow-left me-1"></i>
                        Return to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
