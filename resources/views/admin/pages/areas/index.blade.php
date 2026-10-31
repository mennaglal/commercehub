@extends('admin.layouts.app')

@section('title', __('dashboard.Areas'))

@section('content')
    @include('includes.error')
    @include('includes.success')

    <div class="col-12 my-4">
        <div class="card shadow">
            <div class="card-body">

                {{-- Header --}}
                <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                    <h5 class="mb-2">{{ __('dashboard.Areas') }}</h5>

                    <a href="{{ route('admin.areas.create') }}" class="btn btn-primary">
                        {{ __('dashboard.Add-Area') }}
                    </a>
                </div>

                {{-- Search --}}
                <form
                    method="GET"
                    action="{{ route('admin.areas.index') }}"
                    class="d-flex align-items-center flex-wrap mb-3"
                    style="gap: 10px;"
                >
                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        style="max-width: 300px;"
                        value="{{ request('search') }}"
                        placeholder="{{ __('dashboard.Search...') }}"
                    >

                    <button type="submit" class="btn btn-primary">
                        {{ __('dashboard.Search') }}
                    </button>

                    <a href="{{ route('admin.areas.index') }}" class="btn btn-secondary">
                        {{ __('dashboard.Reset') }}
                    </a>
                </form>
                {{-- Bulk Delete Form --}}
                <form
                    id="bulkDeleteAreasForm"
                    method="POST"
                    action="{{ route('admin.bulk.delete') }}"
                >
                    @csrf
                    <input type="hidden" name="model" value="areas">
                    <input type="hidden" name="ids" id="selectedAreaIds">
                </form>

                <button
                    type="button"
                    onclick="confirmBulkDeleteAreas()"
                    class="btn btn-danger mb-3"
                >
                    @lang('dashboard.delete_all')
                </button>

                {{-- Areas Table --}}
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>
                                <input
                                    type="checkbox"
                                    id="selectAllAreas"
                                    aria-label="Select all areas"
                                >
                            </th>
                            <th>#</th>
                            <th>{{ __('dashboard.Arabic name') }}</th>
                            <th>{{ __('dashboard.English name') }}</th>
                            <th>{{ __('dashboard.City') }}</th>
                            <th>{{ __('dashboard.Status') }}</th>
                            <th>{{ __('dashboard.Actions') }}</th>
                        </tr>
                        </thead>

                        <tbody>
                        @forelse($areas as $item)
                            <tr>
                                <td>
                                    <input
                                        type="checkbox"
                                        class="area-checkbox"
                                        value="{{ $item->id }}"
                                        aria-label="Select area {{ $item->name_en }}"
                                    >
                                </td>

                                <td>{{ $areas->firstItem() + $loop->index }}</td>

                                <td>{{ $item->name_ar }}</td>

                                <td>{{ $item->name_en }}</td>

                                <td>{{ $item->city?->name_en ?? '-' }}</td>

                                <td>
                                        <span class="badge badge-{{ $item->is_active ? 'success' : 'secondary' }}">
                                            {{ $item->is_active ? __('dashboard.Active') : __('dashboard.Inactive') }}
                                        </span>
                                </td>

                                <td class="text-nowrap">
                                    <a
                                        href="{{ route('admin.areas.edit', $item) }}"
                                        class="btn btn-sm btn-warning"
                                    >
                                        {{ __('dashboard.Edit') }}
                                    </a>

                                    <form
                                        action="{{ route('admin.areas.destroy', $item) }}"
                                        method="POST"
                                        class="d-inline"
                                        id="delete-areas-{{ $item->id }}"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="button"
                                            class="btn btn-sm btn-danger"
                                            onclick="confirmDeleteArea({{ $item->id }})"
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
                    {{ $areas->links('vendor.pagination.medium') }}
                </div>

            </div>
        </div>
    </div>
    <script>
        // Handle select all areas on the current page.
        document.addEventListener('DOMContentLoaded', function () {
            const selectAll = document.getElementById('selectAllAreas');
            const checkboxes = Array.from(
                document.querySelectorAll('.area-checkbox')
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

        // Confirm and submit individual area deletion.
        window.confirmDeleteArea = function (id) {
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
                    document.getElementById('delete-areas-' + id).submit();
                }
            });
        };

        // Confirm and submit bulk area deletion.
        window.confirmBulkDeleteAreas = function () {
            const selected = Array.from(
                document.querySelectorAll('.area-checkbox:checked')
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
                    document.getElementById('selectedAreaIds').value = selected.join(',');
                    document.getElementById('bulkDeleteAreasForm').submit();
                }
            });
        };
    </script>

@endsection

