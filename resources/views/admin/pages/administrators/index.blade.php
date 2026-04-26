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

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="table-light">
                        <tr>
                            <th scope="col">{{ __('Name') }}</th>
                            <th scope="col">{{ __('Email') }}</th>
                            <th scope="col" class="text-center">{{ __('Active') }}</th>
                            <th scope="col" class="text-end">{{ __('Actions') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse ($administrators as $user)
                            <tr>
                                <td>{{ e($user->name) }}</td>
                                <td>{{ e($user->email) }}</td>
                                <td class="text-center">@if($user->is_active)<span class="text-success">✓</span>@else<span class="text-danger">✗</span>@endif</td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.administrators.edit', $user) }}">{{ __('Edit') }}</a>
                                    @if (Auth::guard('admin')->id() === (int) $user->id)
                                        <button type="button" class="btn btn-sm btn-outline-danger" disabled title="{{ __('You cannot delete your own account') }}">
                                            <i class="fas fa-trash" aria-hidden="true"></i>
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-sm btn-outline-danger" title="{{ __('Delete') }}" onclick="adminBootstrapTableDelete(@js(route('admin.administrators.destroy', $user)))">
                                            <i class="fas fa-trash" aria-hidden="true"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-muted px-3 py-3">{{ __('No administrators.') }}</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer text-muted small">
                {{ $administrators->withQueryString()->links() }}
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
