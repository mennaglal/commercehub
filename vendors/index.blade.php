@extends('admin.layouts.app')

@section('content')
    @include('includes.error')
    @include('includes.success')

    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ __('dashboard.users_vendors.vendors') }}</h5>
            @can('vendors.create')
                <a href="{{ route('admin.vendors.create') }}" class="btn btn-primary">{{ __('dashboard.users_vendors.add_vendor') }}</a>
            @endcan
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.vendors.index') }}" class="form-inline mb-3">
                <input name="search" value="{{ request('search') }}" class="form-control mr-2" placeholder="{{ __('dashboard.users_vendors.search_vendors_placeholder') }}">
                <button class="btn btn-primary" type="submit">{{ __('dashboard.users_vendors.search') }}</button>
                <a href="{{ route('admin.vendors.index') }}" class="btn btn-light ml-2">{{ __('dashboard.users_vendors.reset') }}</a>
            </form>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('dashboard.users_vendors.vendor_name') }}</th>
                            <th>{{ __('dashboard.users_vendors.email') }}</th>
                            <th>{{ __('dashboard.users_vendors.store_name') }}</th>
                            <th>{{ __('dashboard.users_vendors.approval_status') }}</th>
                            <th>{{ __('dashboard.users_vendors.store_status') }}</th>
                            <th>{{ __('dashboard.users_vendors.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vendors as $vendor)
                            @php($approvalStatus = $vendor->store?->approval_status)
                            <tr>
                                <td>{{ $vendors->firstItem() + $loop->index }}</td>
                                <td>{{ $vendor->name }}</td>
                                <td>{{ $vendor->email }}</td>
                                <td>{{ $vendor->store?->name ?? __('dashboard.users_vendors.not_available') }}</td>
                                <td>
                                    @if(!$vendor->store)
                                        {{ __('dashboard.users_vendors.missing_store') }}
                                    @else
                                        {{ __('dashboard.users_vendors.' . ($approvalStatus ?: 'pending')) }}
                                    @endif
                                </td>
                                <td>
                                    {{ $vendor->store ? ($vendor->store->status ? __('dashboard.users_vendors.active') : __('dashboard.users_vendors.inactive')) : __('dashboard.users_vendors.not_available') }}
                                </td>
                                <td class="text-nowrap">
                                    @can('vendors.update')
                                        <a href="{{ route('admin.vendors.edit', $vendor) }}" class="btn btn-sm btn-info">{{ __('dashboard.users_vendors.edit') }}</a>
                                    @endcan
                                    @if($vendor->store && $approvalStatus !== 'approved')
                                        @can('vendors.approve')
                                            <form class="d-inline" method="POST" action="{{ route('admin.vendors.approve', $vendor) }}">
                                                @csrf
                                                <button class="btn btn-sm btn-success" type="submit">{{ __('dashboard.users_vendors.approve') }}</button>
                                            </form>
                                        @endcan
                                    @endif
                                    @if($vendor->store && $approvalStatus !== 'rejected')
                                        @can('vendors.reject')
                                            <form class="d-inline" method="POST" action="{{ route('admin.vendors.reject', $vendor) }}">
                                                @csrf
                                                <button class="btn btn-sm btn-warning" type="submit">{{ __('dashboard.users_vendors.reject') }}</button>
                                            </form>
                                        @endcan
                                    @endif
                                    @can('vendors.suspend')
                                        <form class="d-inline" method="POST" action="{{ route('admin.vendors.suspend', $vendor) }}">
                                            @csrf
                                            <button class="btn btn-sm btn-secondary" type="submit">{{ __('dashboard.users_vendors.suspend') }}</button>
                                        </form>
                                    @endcan
                                    @can('vendors.delete')
                                        <form class="d-inline" method="POST" action="{{ route('admin.vendors.destroy', $vendor) }}" onsubmit="return confirm(@js(__('dashboard.users_vendors.confirm_delete_vendor'))) ">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger" type="submit">{{ __('dashboard.users_vendors.delete') }}</button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center">{{ __('dashboard.users_vendors.no_results') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $vendors->links() }}
        </div>
    </div>
@endsection
