
@extends('admin.layouts.app')

@section('title', __('dashboard.Categories'))

@section('content')
    @include('includes.error')
    @include('includes.success')

    <div class="col-md-12 my-4">
        <div class="card shadow">
            <div class="card-body">
                <div class="toolbar row mb-3">
                    <div class="col-md-6">
                        <form method="GET" action="{{ route('admin.categories.index') }}" class="w-100"
                              style="max-width: 400px;">
                            <div class="form-group">
                                <input
                                    type="text"
                                    name="search"
                                    class="form-control mb-2"
                                    placeholder="{{ __('dashboard.Search') }}"
                                    value="{{ request('search') }}">

                                <div class="d-flex">
                                    <button class="btn btn-primary mr-1" type="submit">
                                        <i class="fe fe-search"></i>
                                        {{ __('dashboard.Search') }}
                                    </button>

                                    <a href="{{ route('admin.categories.index') }}"
                                       class="btn btn-secondary ml-1">
                                        <i class="fe fe-rotate-ccw"></i>
                                        {{ __('dashboard.Reset') }}
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="col-md-6 text-right">
                        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                            {{ __('dashboard.Add-Category') }} +
                        </a>
                    </div>
                </div>

                {{-- Bulk delete --}}
                <form id="bulkDeleteForm" method="POST" action="{{ route('admin.bulk.delete') }}">
                    @csrf
                    <input type="hidden" name="model" value="Category">
                    <input type="hidden" name="ids" id="selectedIds">

                    <button type="button" onclick="confirmBulkDelete()" class="btn btn-danger mb-3">
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
                            <th>{{ __('dashboard.Slug') }}</th>
                            <th>{{ __('dashboard.Subcategories Count') }}</th>
                            <th>{{ __('dashboard.Image') }}</th>
                            <th>{{ __('dashboard.Actions') }}</th>
                        </tr>
                        </thead>

                        <tbody id="categoryTableBody">
                        @forelse($categories as $category)
                            <tr>
                                <td>
                                    <input
                                        type="checkbox"
                                        class="row-checkbox"
                                        value="{{ $category->id }}">
                                </td>

                                <td>{{ $categories->firstItem() + $loop->index }}</td>
                                <td>{{ $category->id }}</td>
                                <td>{{ $category->name }}</td>
                                <td dir="rtl">{{ $category->name_ar }}</td>
                                <td>{{ $category->slug }}</td>
                                <td>{{ $category->children_count }}</td>

                                <td>
                                    @if($category->image)
                                        <img
                                            src="{{ asset($category->image) }}"
                                            width="55"
                                            height="55"
                                            style="object-fit: fill"
                                            alt="{{ $category->name }}">
                                    @else
                                        -
                                    @endif
                                </td>

                                <td>
                                    <a
                                        href="{{ route('admin.categories.edit', $category) }}"
                                        class="text-warning mr-2"
                                        title="{{ __('dashboard.Edit-Category') }}">
                                        <i class="fe fe-edit"></i>
                                    </a>

                                    <form
                                        action="{{ route('admin.categories.destroy', $category) }}"
                                        method="POST"
                                        id="deleteCategory{{ $category->id }}"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="button"
                                            class="btn btn-link text-danger p-0"
                                            title="{{ __('dashboard.Actions') }}"
                                            onclick="confirmDelete({{ $category->id }})">
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
        {{ $categories->links('vendor.pagination.medium') }}
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
                    const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;

                    if (selectAll) {
                        selectAll.checked =
                            checkboxes.length > 0 && checkedCount === checkboxes.length;

                        selectAll.indeterminate =
                            checkedCount > 0 && checkedCount < checkboxes.length;
                    }
                });
            });
        });

        function confirmDelete(id) {
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
                    document.getElementById('deleteCategory' + id).submit();
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
