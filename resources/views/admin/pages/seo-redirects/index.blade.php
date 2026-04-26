@extends('admin.layouts.sb-admin')

@section('title', __('301 Redirects'))

@section('content')
    <div class="container-fluid px-4">
        <h1 class="mt-4">{{ __('301 Redirects') }}</h1>
        <ol class="breadcrumb mb-4">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">{{ __('301 Redirects') }}</li>
        </ol>

        <a class="btn btn-primary mb-3" href="{{ route('admin.seo-redirects.create') }}">{{ __('Create redirect') }}</a>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="table-light">
                        <tr>
                            <th scope="col">{{ __('From (path)') }}</th>
                            <th scope="col">{{ __('To') }}</th>
                            <th scope="col" class="text-center">{{ __('Active') }}</th>
                            <th scope="col" class="text-end">{{ __('Actions') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($redirects as $r)
                            <tr>
                                <td><code>/{{ e($r->slug_from) }}</code></td>
                                <td class="text-break"><code>{{ e($r->slug_to) }}</code></td>
                                <td class="text-center">@if($r->is_active)<span class="text-success">✓</span>@else<span class="text-danger">✗</span>@endif</td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.seo-redirects.edit', $r) }}">{{ __('Edit') }}</a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" title="{{ __('Delete') }}" onclick="adminBootstrapTableDelete(@js(route('admin.seo-redirects.destroy', $r)))">
                                        <i class="fas fa-trash" aria-hidden="true"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-muted px-3 py-3">{{ __('No redirects yet.') }}</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer text-muted small">
                {{ $redirects->withQueryString()->links() }}
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <div
        id="admin-bootstrap-table-i18n"
        class="d-none"
        data-delete-confirm="{{ __('Delete this record?') }}"
        data-fallback-done="{{ __('Done.') }}"
        data-fallback-failed="{{ __('Request failed.') }}"
    ></div>
    @vite(['resources/themes/admin/assets/js/admin-bootstrap-table.js'])
@endpush
