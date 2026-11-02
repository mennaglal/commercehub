@extends('admin.layouts.app')

@section('content')
    @include('includes.error')
    @include('includes.success')

    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ __('dashboard.users_vendors.users') }}</h5>
            @can('users.create')
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">{{ __('dashboard.users_vendors.add_user') }}</a>
            @endcan
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.users.index') }}" class="form-inline mb-3">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control mr-2" placeholder="{{ __('dashboard.users_vendors.search_users_placeholder') }}">
                <button class="btn btn-primary" type="submit">{{ __('dashboard.users_vendors.search') }}</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-light ml-2">{{ __('dashboard.users_vendors.reset') }}</a>
            </form>
            <div class="d-flex justify-content-between align-items-center mb-3">
                @can('users.delete')
                    <button type="button" onclick="confirmUsersBulkDelete()" class="btn btn-danger">
                        {{ __('dashboard.delete_all') }}
                    </button>
                @endcan
            </div>

            <form id="usersBulkDeleteForm" method="POST" action="{{ route('admin.bulk.delete') }}">
                @csrf
                <input type="hidden" name="model" value="customers">
                <input type="hidden" name="ids" id="usersSelectedIds">
            </form>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>
                                <input type="checkbox" id="usersSelectAll">
                            </th>
                            <th>#</th>
                            <th>{{ __('dashboard.users_vendors.name') }}</th>
                            <th>{{ __('dashboard.users_vendors.email') }}</th>
                            <th>{{ __('dashboard.users_vendors.phone') }}</th>
                            <th>{{ __('dashboard.users_vendors.status') }}</th>
                            <th>{{ __('dashboard.Country') }}</th>
                            <th>{{ __('dashboard.City') }}</th>
                            <th>{{ __('dashboard.Area') }}</th>
                            <th>{{ __('dashboard.Image') }}</th>
                            <th>{{ __('dashboard.users_vendors.actions') }}</th>
                        </tr>
                        </thead>

                        <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>
                                    <input type="checkbox" class="users-row-checkbox" value="{{ $user->id }}">
                                </td>

                                <td>{{ $users->firstItem() + $loop->index }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->phone ?: __('dashboard.users_vendors.not_available') }}</td>

                                <td>
                            <span class="badge badge-{{ $user->status ? 'success' : 'secondary' }}">
                                {{ $user->status
                                    ? __('dashboard.users_vendors.user_active')
                                    : __('dashboard.users_vendors.user_blocked') }}
                            </span>
                                </td>
                                <td>
                                    {{ $user->country
                                        ? (app()->getLocale() === 'ar'
                                            ? $user->country->name_ar
                                            : $user->country->name_en)
                                        : '-' }}
                                </td>

                                <td>
                                    {{ $user->city
                                        ? (app()->getLocale() === 'ar'
                                            ? $user->city->name_ar
                                            : $user->city->name_en)
                                        : '-' }}
                                </td>

                                <td>
                                    {{ $user->area
                                        ? (app()->getLocale() === 'ar'
                                            ? $user->area->name_ar
                                            : $user->area->name_en)
                                        : '-' }}
                                </td>
                                <td>
                                    @if($user->avatar)
                                        <img
                                            src="{{ asset($user->avatar) }}"
                                            alt="{{ $user->name }}"
                                            width="45"
                                            height="45"
                                            class="rounded-circle"
                                            style="object-fit: cover;"
                                        >
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                <td class="text-nowrap">
                                    @can('users.update')
                                        <a class="btn btn-sm btn-info"
                                           href="{{ route('admin.users.edit', $user) }}">
                                            {{ __('dashboard.users_vendors.edit') }}
                                        </a>
                                    @endcan

                                    @can('users.suspend')
                                        <form class="d-inline" method="POST"
                                              action="{{ route($user->status ? 'admin.users.block' : 'admin.users.unblock', $user) }}">
                                            @csrf
                                            <button class="btn btn-sm btn-warning" type="submit">
                                                {{ $user->status
                                                    ? __('dashboard.users_vendors.block')
                                                    : __('dashboard.users_vendors.unblock') }}
                                            </button>
                                        </form>
                                    @endcan

                                    @can('users.delete')
                                        <form class="d-inline" method="POST"
                                              action="{{ route('admin.users.destroy', $user) }}"
                                              onsubmit="return confirm(@js(__('dashboard.users_vendors.confirm_delete')))">
                                            @csrf
                                            @method('DELETE')

                                            <button class="btn btn-sm btn-danger" type="submit">
                                                {{ __('dashboard.users_vendors.delete') }}
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">
                                    {{ __('dashboard.users_vendors.no_results') }}
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>


            <div class="d-flex justify-content-center">
                {{$users->appends(request()->query())->links('vendor.pagination.medium') }}
            </div>
            <script>
                document.addEventListener('DOMContentLoaded', function () {
                    const selectAll = document.getElementById('usersSelectAll');
                    const checkboxes = document.querySelectorAll('.users-row-checkbox');

                    if (selectAll) {
                        selectAll.addEventListener('change', function () {
                            checkboxes.forEach(function (checkbox) {
                                checkbox.checked = selectAll.checked;
                            });
                        });
                    }

                    checkboxes.forEach(function (checkbox) {
                        checkbox.addEventListener('change', function () {
                            if (selectAll) {
                                selectAll.checked =
                                    checkboxes.length > 0 &&
                                    document.querySelectorAll('.users-row-checkbox:checked').length === checkboxes.length;
                            }
                        });
                    });
                });

                function confirmUsersBulkDelete() {
                    const selected = Array.from(
                        document.querySelectorAll('.users-row-checkbox:checked')
                    ).map(function (checkbox) {
                        return checkbox.value;
                    });

                    if (selected.length === 0) {
                        Swal.fire(
                            "{{__('dashboard.Warning')}}",
                            "{{__('dashboard.No items selected')}}",
                            "warning"
                        );
                        return;
                    }

                    Swal.fire({
                        title: "{{__('dashboard.Are you sure?')}}",
                        text: "{{__('dashboard.Selected items will be deleted!')}}",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#d33",
                        cancelButtonColor: "#3085d6",
                        confirmButtonText: "{{__('dashboard.Yes, delete them!')}}",
                        cancelButtonText: "{{__('dashboard.Cancelbtn')}}"
                    }).then(function (result) {
                        if (result.isConfirmed) {
                            document.getElementById('usersSelectedIds').value = selected.join(',');
                            document.getElementById('usersBulkDeleteForm').submit();
                        }
                    });
                }
            </script>

        </div>
    </div>
@endsection
