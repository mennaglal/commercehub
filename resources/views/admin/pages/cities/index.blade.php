@extends('admin.layouts.app')

@section('title', __('dashboard.Cities'))

@section('content')
    @include('includes.error')
    @include('includes.success')

    <div class="col-12 my-4">
        <div class="card shadow">
            <div class="card-body">

                {{-- Header --}}
                <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                    <h5 class="mb-2">{{ __('dashboard.Cities') }}</h5>

                    <a href="{{ route('admin.cities.create') }}" class="btn btn-primary">
                        {{ __('dashboard.Add-City') }}
                    </a>
                </div>

                {{-- Search --}}
                <form method="GET" action="{{ route('admin.cities.index') }}" class="form-inline mb-3">
                    <input
                        type="text"
                        name="search"
                        class="form-control mr-2 mb-2"
                        value="{{ request('search') }}"
                        placeholder="{{ __('dashboard.Search...') }}"
                    >

                    <button type="submit" class="btn btn-primary mr-2 mb-2">
                        {{ __('dashboard.Search') }}
                    </button>

                    <a href="{{ route('admin.cities.index') }}" class="btn btn-secondary mb-2">
                        {{ __('dashboard.Reset') }}
                    </a>
                </form>

                {{-- Bulk Delete Form --}}
                <form
                    id="bulkDeleteCitiesForm"
                    method="POST"
                    action="{{ route('admin.bulk.delete') }}"
                >
                    @csrf
                    <input type="hidden" name="model" value="cities">
                    <input type="hidden" name="ids" id="selectedCityIds">
                </form>

                <button
                    type="button"
                    onclick="confirmBulkDeleteCities()"
                    class="btn btn-danger mb-3"
                >
                    @lang('dashboard.delete_all')
                </button>

                {{-- Cities Table --}}
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>
                                <input
                                    type="checkbox"
                                    id="selectAllCities"
                                    aria-label="Select all cities"
                                >
                            </th>
                            <th>#</th>
                            <th>{{ __('dashboard.Arabic name') }}</th>
                            <th>{{ __('dashboard.English name') }}</th>
                            <th>{{ __('dashboard.Country') }}</th>
                            <th>{{ __('dashboard.Status') }}</th>
                            <th>{{ __('dashboard.Actions') }}</th>
                        </tr>
                        </thead>

                        <tbody>
                        @forelse($cities as $item)
                            <tr>
                                <td>
                                    <input
                                        type="checkbox"
                                        class="city-checkbox"
                                        value="{{ $item->id }}"
                                        aria-label="Select city {{ $item->name_en }}"
                                    >
                                </td>

                                <td>{{ $cities->firstItem() + $loop->index }}</td>

                                <td>{{ $item->name_ar }}</td>

                                <td>{{ $item->name_en }}</td>

                                <td>{{ $item->country?->name_en ?? '-' }}</td>

                                <td>
                                        <span class="badge badge-{{ $item->is_active ? 'success' : 'secondary' }}">
                                            {{ $item->is_active ? __('dashboard.Active') : __('dashboard.Inactive') }}
                                        </span>
                                </td>

                                <td class="text-nowrap">
                                    <a
                                        href="{{ route('admin.cities.edit', $item) }}"
                                        class="btn btn-sm btn-warning"
                                    >
                                        {{ __('dashboard.Edit') }}
                                    </a>

                                    <form
                                        action="{{ route('admin.cities.destroy', $item) }}"
                                        method="POST"
                                        class="d-inline"
                                        id="delete-cities-{{ $item->id }}"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="button"
                                            class="btn btn-sm btn-danger"
                                            onclick="confirmDeleteCity({{ $item->id }})"
                                        >
                                            {{ __('dashboard.Delete') }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">
                                    {{ __('dashboard.No records found.') }}
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="d-flex justify-content-center">
                    {{ $cities->links('vendor.pagination.medium') }}
                </div>

            </div>
        </div>
    </div>
    <script>
        // Handle select all cities on the current page.
        document.addEventListener('DOMContentLoaded', function () {
            const selectAll = document.getElementById('selectAllCities');
            const checkboxes = Array.from(
                document.querySelectorAll('.city-checkbox')
            );

            if (!selectAll) {
                return;
            }

            selectAll.addEventListener('change', function () {
                checkboxes.forEach(function (checkbox) {
                    checkbox.checked = selectAll.checked;
                });

                selectAll.indeterminate = false;
            });

            checkboxes.forEach(function (checkbox) {
                checkbox.addEventListener('change', function () {
                    const checkedCount = checkboxes.filter(function (item) {
                        return item.checked;
                    }).length;

                    selectAll.checked =
                        checkboxes.length > 0 &&
                        checkedCount === checkboxes.length;

                    selectAll.indeterminate =
                        checkedCount > 0 &&
                        checkedCount < checkboxes.length;
                });
            });
        });

        // Confirm and submit individual city deletion.
        window.confirmDeleteCity = function (id) {
            Swal.fire({
                title: @json(__('dashboard.Are you sure?')),
                text: @json(__('dashboard.This record will be deleted.')),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: @json(__('dashboard.Yes, delete it!')),
                cancelButtonText: @json(__('dashboard.Cancel'))
            }).then(function (result) {
                if (result.isConfirmed) {
                    document.getElementById('delete-cities-' + id).submit();
                }
            });
        };

        // Confirm and submit bulk city deletion.
        window.confirmBulkDeleteCities = function () {
            const selected = Array.from(
                document.querySelectorAll('.city-checkbox:checked')
            ).map(function (checkbox) {
                return checkbox.value;
            });

            if (selected.length === 0) {
                Swal.fire(
                    @json(__('dashboard.Warning')),
                    @json(__('dashboard.No items selected')),
                    'warning'
                );

                return;
            }

            Swal.fire({
                title: @json(__('dashboard.Are you sure?')),
                text: @json(__('dashboard.Selected items will be deleted!')),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: @json(__('dashboard.Yes, delete them!')),
                cancelButtonText: @json(__('dashboard.Cancel'))
            }).then(function (result) {
                if (result.isConfirmed) {
                    document.getElementById('selectedCityIds').value = selected.join(',');
                    document.getElementById('bulkDeleteCitiesForm').submit();
                }
            });
        };
    </script>

@endsection

