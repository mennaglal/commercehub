
@extends('admin.layouts.app')

@section('content')

    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">{{ __('pages.title') }}</h4>
                <small class="text-muted">{{ __('pages.subtitle') }}</small>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <div class="card shadow">

            <div class="card-header">
                <h5 class="mb-0">{{ __('pages.all_pages') }}</h5>
            </div>

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-bordered table-hover">

                        <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('pages.title_ar') }}</th>
                            <th>{{ __('pages.title_en') }}</th>
                            <th>{{ __('pages.slug') }}</th>
                            <th>{{ __('pages.status') }}</th>
                            <th>{{ __('pages.actions') }}</th>
                        </tr>
                        </thead>

                        <tbody>

                        @forelse($pages as $page)

                            <tr>

                                <td>{{ $page->id }}</td>

                                <td>{{ $page->title_ar }}</td>

                                <td>{{ $page->title_en }}</td>

                                <td>
                                    <code>{{ $page->slug }}</code>
                                </td>

                                <td>
                                    @if($page->is_active)
                                        <span class="badge badge-success">
                                            {{ __('pages.active') }}
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">
                                            {{ __('pages.inactive') }}
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    <a href="{{ route('admin.pages.edit', $page->id) }}"
                                       class="btn btn-sm btn-primary">

                                        <i class="fe fe-edit mr-1"></i>
                                        {{ __('pages.edit') }}

                                    </a>

                                    @if($page->is_active)
                                        <a href="{{ route('pages.show', $page->slug) }}"
                                           target="_blank"
                                           class="btn btn-sm btn-outline-info">

                                            <i class="fe fe-eye mr-1"></i>
                                            {{ __('pages.preview') }}

                                        </a>
                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="6" class="text-center">
                                    {{ __('pages.no_pages') }}
                                </td>
                            </tr>

                        @endforelse

                        </tbody>

                    </table>

                </div>

                <div class="d-flex justify-content-center">
                    {{$pages->appends(request()->query())->links('vendor.pagination.medium') }}
                </div>


            </div>

        </div>

    </div>

@endsection
