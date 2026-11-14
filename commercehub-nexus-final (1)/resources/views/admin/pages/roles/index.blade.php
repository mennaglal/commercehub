@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
        <div>
            <h2 class="mb-1">{{ __('roles.title') }}</h2>
            <p class="text-muted mb-0">{{ __('roles.subtitle') }}</p>
        </div>
        @can('roles.create')
            <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
                <i class="fe fe-plus mr-1"></i> {{ __('roles.add') }}
            </a>
        @endcan
    </div>

    @include('includes.success')
    @include('includes.error')
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert"><span>&times;</span></button>
        </div>
    @endif

    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                    <tr>
                        <th>{{ __('roles.name') }}</th>
                        <th>{{ __('roles.permissions_count') }}</th>
                        <th>{{ __('roles.users_count') }}</th>
                        <th>{{ __('roles.type') }}</th>
                        <th class="text-right">{{ __('roles.actions') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($roles as $role)
                        <tr>
                            <td class="font-weight-bold">{{ $role->name }}</td>
                            <td><span class="badge badge-info">{{ $role->permissions_count }}</span></td>
                            <td><span class="badge badge-secondary">{{ $role->users_count }}</span></td>
                            <td>
                                @if($role->name === 'super-admin')
                                    <span class="badge badge-warning">{{ __('roles.system_role') }}</span>
                                @else
                                    <span class="badge badge-info">{{ __('roles.custom_role') }}</span>
                                @endif
                            </td>
                            <td class="text-right">
                                @can('roles.update')
                                    @if($role->name !== 'super-admin')
                                        <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fe fe-edit"></i> {{ __('roles.edit') }}
                                        </a>
                                    @endif
                                @endcan
                                @can('roles.delete')
                                    @if($role->name !== 'super-admin')
                                        <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="d-inline" data-confirm-delete>
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="fe fe-trash-2"></i> {{ __('roles.delete') }}
                                            </button>
                                        </form>
                                    @endif
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">{{ __('roles.empty') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-3">
        {{ $roles->links('vendor.pagination.medium') }}
    </div>
</div>
@endsection
