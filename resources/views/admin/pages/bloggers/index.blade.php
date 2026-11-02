{{-- Change 'layouts.admin' / 'content' to your real admin layout names. --}}
@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">


    <div class="card shadow mb-4">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h4 class="mb-0">{{ __('bloggers.title') }}</h4>
            <a href="{{ route('admin.bloggers.create') }}" class="btn btn-primary">{{ __('bloggers.add') }}</a>
        </div>

        <div class="card-body">

            {{-- Search --}}
            <form action="{{ route('admin.bloggers.index') }}" method="GET" class="mb-4">
                <div class="row">
                    <div class="col-md-8 mb-2">
                        <input
                            type="text"
                            name="search"
                            class="form-control"
                            placeholder="{{ __('dashboard.Search...') }}"
                            value="{{ request('search') }}"
                        >
                    </div>

                    <div class="col-md-4 mb-2 d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fe fe-search"></i>
                            {{ __('dashboard.Search') }}
                        </button>

                        @if(request()->filled('search'))
                            <a href="{{ route('admin.bloggers.index') }}" class="btn btn-light">{{ __('dashboard.Reset') }}</a>
                        @endif
                    </div>
                </div>
            </form>

            {{-- Bulk Delete Form --}}
            <form
                method="POST"
                action="{{ route('admin.bulk.delete') }}"
                data-bulk-delete
                data-selection-group="bloggers"
            >
                @csrf

                <input type="hidden" name="model" value="bloggers">
                <input type="hidden" name="ids">

                <button type="submit" class="btn btn-danger mb-3">
                    @lang('dashboard.delete_all')
                </button>
            </form>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                    <tr>
                        <th>
                            <input type="checkbox" data-select-all="bloggers">
                        </th>
                        <th>{{ __('bloggers.columns.name') }}</th>
                        <th>{{ __('bloggers.columns.phone') }}</th>
                        <th>{{ __('bloggers.columns.email') }}</th>
                        <th>{{ __('bloggers.columns.coupons') }}</th>
                        <th>{{ __('bloggers.columns.status') }}</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($bloggers as $blogger)
                        <tr>
                            <td>
                                <input
                                    type="checkbox"
                                    data-select-item="bloggers"
                                    value="{{ $blogger->id }}"
                                >
                            </td>
                            <td>{{ $blogger->name }}</td>
                            <td>{{ $blogger->phone ?? '-' }}</td>
                            <td>{{ $blogger->email ?? '-' }}</td>
                            <td>{{ $blogger->coupons_count }}</td>
                            <td>
                                <span class="badge bg-{{ $blogger->status ? 'success' : 'secondary' }}">
                                    {{ $blogger->status ? __('bloggers.active') : __('bloggers.inactive') }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.bloggers.show', $blogger) }}" class="btn btn-sm btn-outline-secondary">{{ __('orders.fields.view') }}</a>
                                <a href="{{ route('admin.bloggers.edit', $blogger) }}" class="btn btn-sm btn-outline-primary">{{ __('bloggers.edit') }}</a>
                                <form
                                    action="{{ route('admin.bloggers.destroy', $blogger) }}"
                                    method="POST"
                                    class="d-inline"
                                    data-confirm-delete
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit" class="btn btn-sm btn-danger">
                                        {{ __('dashboard.Delete') }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted py-4">{{ __('bloggers.empty') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Pagination --}}
    <div class="d-flex justify-content-center">
        {{ $bloggers->links('vendor.pagination.medium') }}
    </div>
</div>
@endsection
