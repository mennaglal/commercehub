@extends('admin.layouts.app')

@section('title', __('dashboard.Sub-Categories'))

@section('content')
    @include('includes.error')
    @include('includes.success')

    <div class="col-md-12 my-4">
        <div class="card shadow">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <form method="GET" action="{{ route('admin.sub-categories.index') }}">
                            <input
                                type="text"
                                name="search"
                                class="form-control mb-2"
                                placeholder="{{ __('dashboard.Search') }}"
                                value="{{ request('search') }}">

                            <button class="btn btn-primary" type="submit">
                                <i class="fe fe-search"></i>
                                {{ __('dashboard.Search') }}
                            </button>

                            <a href="{{ route('admin.sub-categories.index') }}"
                               class="btn btn-secondary">
                                {{ __('dashboard.Reset') }}
                            </a>
                        </form>
                    </div>

                    <div class="col-md-6 text-right">
                        <a href="{{ route('admin.sub-categories.create') }}"
                           class="btn btn-primary">
                            {{ __('dashboard.Add-Sub-Category') }} +
                        </a>
                    </div>
                </div>

                {{-- Bulk delete --}}
                <form id="bulkDeleteForm"
                      method="POST"
                      action="{{ route('admin.bulk.delete') }}">
                    @csrf

                    <input type="hidden" name="model" value="sub-categories">
                    <input type="hidden" name="ids" id="selectedIds">

                    <button type="button"
                            onclick="confirmBulkDelete()"
                            class="btn btn-danger mb-3">
                        {{ __('dashboard.delete_all') }}
                    </button>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>
                                <input type="checkbox" id="selectAll">
                            </th>
                            <th>#</th>
                            <th>ID</th>
                            <th>{{ __('dashboard.English Name') }}</th>
                            <th>{{ __('dashboard.Arabic Name') }}</th>
                            <th>{{ __('dashboard.Parent Category') }}</th>
                            <th>{{ __('dashboard.Slug') }}</th>
                            <th>{{ __('dashboard.Image') }}</th>
                            <th>{{ __('dashboard.Actions') }}</th>
                        </tr>
                        </thead>

                        <tbody>
                        @forelse($subCategories as $subCategory)
                            <tr>
                                <td>
                                    <input
                                        type="checkbox"
                                        class="row-checkbox"
                                        value="{{ $subCategory->id }}">
                                </td>

                                <td>{{ $subCategories->firstItem() + $loop->index }}</td>
                                <td>{{ $subCategory->id }}</td>
                                <td>{{ $subCategory->name }}</td>
                                <td dir="rtl">{{ $subCategory->name_ar }}</td>

                                <td>
                                    {{ $subCategory->parent?->name }}
                                    -
                                    {{ $subCategory->parent?->name_ar }}
                                </td>

                                <td>{{ $subCategory->slug }}</td>

                                <td>
                                    @if($subCategory->image)
                                        <img
                                            src="{{ asset($subCategory->image) }}"
                                            width="55"
                                            height="55"
                                            style="object-fit: fill"
                                            alt="{{ __('dashboard.Subcategory Image') }}">
                                    @else
                                        -
                                    @endif
                                </td>

                                <td>
                                    <a
                                        href="{{ route('admin.sub-categories.edit', $subCategory) }}"
                                        class="text-warning mr-2"
                                        title="{{ __('dashboard.Edit-Sub-Category') }}">
                                        <i class="fe fe-edit"></i>
                                    </a>

                                    <form
                                        action="{{ route('admin.sub-categories.destroy', $subCategory) }}"
                                        method="POST"
                                        id="deleteSubCategory{{ $subCategory->id }}"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="button"
                                            class="btn btn-link text-danger p-0"
                                            title="{{ __('dashboard.Delete') }}"
                                            onclick="confirmSubCategoryDelete({{ $subCategory->id }})">
                                            <i class="fe fe-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">
                                    {{ __('dashboard.No data available') }}
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-center mt-3">
        {{ $subCategories->links('vendor.pagination.medium') }}
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.row-checkbox');

            if (selectAll) {
                selectAll.addEventListener('change', function () {
                    checkboxes.forEach(function (checkbox) {
                        checkbox.checked = selectAll.checked;
                    });
                });
            }

            checkboxes.forEach(function (checkbox) {
                checkbox.addEventListener('change', function () {
                    const checkedCount =
                        document.querySelectorAll('.row-checkbox:checked').length;

                    if (selectAll) {
                        selectAll.checked =
                            checkboxes.length > 0 &&
                            checkedCount === checkboxes.length;

                        selectAll.indeterminate =
                            checkedCount > 0 &&
                            checkedCount < checkboxes.length;
                    }
                });
            });
        });

        function confirmSubCategoryDelete(id) {
            Swal.fire({
                title: @json(__('dashboard.Are you sure?')),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: @json(__('dashboard.Yes, delete it!')),
                cancelButtonText: @json(__('dashboard.Cancelbtn'))
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('deleteSubCategory' + id).submit();
                }
            });
        }

        function confirmBulkDelete() {
            const selected = [];

            document.querySelectorAll('.row-checkbox:checked').forEach(function (checkbox) {
                selected.push(checkbox.value);
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
                cancelButtonText: @json(__('dashboard.Cancelbtn'))
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('selectedIds').value = selected.join(',');
                    document.getElementById('bulkDeleteForm').submit();
                }
            });
        }
    </script>
@endsection
