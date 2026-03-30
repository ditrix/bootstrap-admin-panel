@extends('admin.layouts.error')

@section('title', $page->title())

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="text-center mt-4">
                    @if ($page->usesIllustration())
                        <img class="mb-4 img-error" src="{{ \App\Helpers\AdminHelper::maketAsset('assets/img/error-404-monochrome.svg') }}" alt="404" />
                    @endif
                    <p class="lead">{{ $page->lead() }}</p>
                    <a href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-arrow-left me-1"></i>
                        Return to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
