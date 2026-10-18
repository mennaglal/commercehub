
@extends('admin.layouts.app')

@section('title', __('dashboard.Currencies'))

@section('content')
    @include('includes.error')
    @include('includes.success')

    <div class="col-12 my-4">
        <div class="card shadow">
            <div class="card-body">

                {{-- Header --}}
                <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                    <h5 class="mb-2">{{ __('dashboard.Currencies') }}</h5>

                    <a href="{{ route('admin.currencies.create') }}" class="btn btn-primary">
                        {{ __('dashboard.Add-Currency') }}
                    </a>
                </div>

                {{-- Search --}}
                <form method="GET" action="{{ route('admin.currencies.index') }}" class="form-inline mb-3">
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

                    <a href="{{ route('admin.currencies.index') }}" class="btn btn-secondary mb-2">
                        {{ __('dashboard.Reset') }}
                    </a>
                </form>

                {{-- Bulk Delete Form --}}
                <form
                    id="bulkDeleteCurrenciesForm"
                    method="POST"
                    action="{{ route('admin.bulk.delete') }}"
                >
                    @csrf
                    <input type="hidden" name="model" value="Currency">
                    <input type="hidden" name="ids" id="selectedCurrencyIds">
                </form>

                <button
                    type="button"
                    onclick="confirmBulkDeleteCurrencies()"
                    class="btn btn-danger mb-3"
                >
                    @lang('dashboard.delete_all')
                </button>

                {{-- Currencies Table --}}
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>
                                <input
                                    type="checkbox"
                                    id="selectAllCurrencies"
                                    aria-label="Select all currencies"
                                >
                            </th>
                            <th>#</th>
                            <th>{{ __('dashboard.Arabic name') }}</th>
                            <th>{{ __('dashboard.English name') }}</th>
                            <th>{{ __('dashboard.Code') }}</th>
                            <th>{{ __('dashboard.Symbol') }}</th>
                            <th>{{ __('dashboard.Decimal places') }}</th>
                            <th>{{ __('dashboard.Exchange rate to EGP') }}</th>
                            <th>{{ __('dashboard.Countries') }}</th>
                            <th>{{ __('dashboard.Status') }}</th>
                            <th>{{ __('dashboard.Actions') }}</th>
                        </tr>
                        </thead>

                        <tbody>
                        @forelse($currencies as $item)
                            <tr>
                                <td>
                                    <input
                                        type="checkbox"
                                        class="currency-checkbox"
                                        value="{{ $item->id }}"
                                        aria-label="Select currency {{ $item->code }}"
                                        @if ($item->code === 'EGP') disabled @endif
                                    >
                                </td>

                                <td>{{ $currencies->firstItem() + $loop->index }}</td>

                                <td>{{ $item->name_ar }}</td>

                                <td>{{ $item->name_en }}</td>

                                <td>{{ $item->code }}</td>

                                <td>{{ $item->symbol }}</td>

                                <td>{{ $item->decimal_places }}</td>

                                <td>
                                    @if ($item->rate_to_egp !== null)
                                        {{ number_format((float) $item->rate_to_egp, 8, '.', '') }}
                                    @else
                                        <span class="text-danger">
                                                {{ __('dashboard.Not set') }}
                                            </span>
                                    @endif

                                    @if ($item->code === 'EGP')
                                        <span class="badge badge-info">Base</span>
                                    @endif
                                </td>

                                <td>{{ $item->countries_count }}</td>

                                <td>
                                        <span class="badge badge-{{ $item->is_active ? 'success' : 'secondary' }}">
                                            {{ $item->is_active ? __('dashboard.Active') : __('dashboard.Inactive') }}
                                        </span>
                                </td>

                                <td class="text-nowrap">
                                    <a
                                        href="{{ route('admin.currencies.edit', $item) }}"
                                        class="btn btn-sm btn-warning"
                                    >
                                        {{ __('dashboard.Edit') }}
                                    </a>

                                    <form
                                        action="{{ route('admin.currencies.destroy', $item) }}"
                                        method="POST"
                                        class="d-inline"
                                        id="delete-currencies-{{ $item->id }}"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="button"
                                            class="btn btn-sm btn-danger"
                                            onclick="confirmDeleteCurrency({{ $item->id }})"
                                        >
                                            {{ __('dashboard.Delete') }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center">
                                    {{ __('dashboard.No records found.') }}
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="d-flex justify-content-center">
                    {{ $currencies->links('vendor.pagination.medium') }}
                </div>

            </div>
        </div>
    </div>
    <script>
        // Handle select all currencies on the current page.
        document.addEventListener('DOMContentLoaded', function () {
            const selectAll = document.getElementById('selectAllCurrencies');
            const checkboxes = Array.from(
                document.querySelectorAll('.currency-checkbox:not(:disabled)')
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

        // Confirm and submit individual currency deletion.
        window.confirmDeleteCurrency = function (id) {
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
                    document.getElementById('delete-currencies-' + id).submit();
                }
            });
        };

        // Confirm and submit bulk currency deletion.
        window.confirmBulkDeleteCurrencies = function () {
            const selected = Array.from(
                document.querySelectorAll('.currency-checkbox:checked:not(:disabled)')
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
                    document.getElementById('selectedCurrencyIds').value = selected.join(',');
                    document.getElementById('bulkDeleteCurrenciesForm').submit();
                }
            });
        };
    </script>

@endsection

