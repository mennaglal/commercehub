@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h2 class="mb-1">{{ __('roles.permissions_title') }}</h2>
        <p class="text-muted mb-0">{{ __('roles.permissions_subtitle') }}</p>
    </div>

    <div class="alert alert-info">
        {{ __('roles.permissions_managed_from_roles') }}
    </div>

    <div class="row">
        @forelse($permissions as $module => $modulePermissions)
            <div class="col-xl-6 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-uppercase">{{ str_replace(['-', '_'], ' ', $module) }}</h5>
                        <span class="badge badge-primary">{{ $modulePermissions->count() }}</span>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead>
                                <tr>
                                    <th>{{ __('roles.permission') }}</th>
                                    <th class="text-right">{{ __('roles.assigned_roles') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($modulePermissions as $permission)
                                    <tr>
                                        <td>
                                            <strong>{{ Str::after($permission->name, '.') }}</strong>
                                            <small class="d-block text-muted">{{ $permission->name }}</small>
                                        </td>
                                        <td class="text-right"><span class="badge badge-light">{{ $permission->roles_count }}</span></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12"><div class="alert alert-secondary">{{ __('roles.no_permissions') }}</div></div>
        @endforelse
    </div>
</div>
@endsection
