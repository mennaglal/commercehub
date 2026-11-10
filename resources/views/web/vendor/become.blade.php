@extends('web.layouts.app')
@section('title', __('web.vendor.become_title'))
@section('page', 'become-vendor')

@section('content')
<div class="page-hero"><div class="container"><h1>{{ __('web.vendor.become_title') }}</h1><p class="text-muted mb-0">{{ __('web.vendor.become_text') }}</p></div></div>

<section class="container pb-4">
    <div class="row g-4 justify-content-center">
        <div class="col-lg-5">
            <div class="d-grid gap-3">
                @foreach([['bi-shop', 'v1'], ['bi-graph-up-arrow', 'v2'], ['bi-truck', 'v3']] as [$icon, $key])
                    <div class="feature d-flex gap-3"><div class="ico mb-0"><i class="bi {{ $icon }}"></i></div><div><h6>{{ __('web.vendor.' . $key . '_title') }}</h6><p class="text-muted small mb-0">{{ __('web.vendor.' . $key . '_text') }}</p></div></div>
                @endforeach
            </div>
        </div>

        <div class="col-lg-6">
            <form class="card-soft pad" id="become-form" enctype="multipart/form-data" novalidate>
                <div class="alert alert-danger form-errors d-none" role="alert"></div>
                <div class="mb-3"><label class="form-label" for="store_name">{{ __('web.vendor.store_name') }} *</label><input class="form-control" id="store_name" name="store_name" maxlength="255" required></div>
                <div class="mb-3"><label class="form-label" for="description">{{ __('web.vendor.description') }}</label><textarea class="form-control" id="description" name="description" rows="3" maxlength="1000"></textarea></div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6"><label class="form-label" for="logo">{{ __('web.vendor.logo') }}</label><input class="form-control" type="file" id="logo" name="logo" accept=".jpg,.jpeg,.png,.webp"></div>
                    <div class="col-md-6"><label class="form-label" for="banner">{{ __('web.vendor.banner') }}</label><input class="form-control" type="file" id="banner" name="banner" accept=".jpg,.jpeg,.png,.webp"></div>
                </div>
                <div class="alert alert-info small"><i class="bi bi-info-circle me-1"></i>{{ __('web.vendor.approval_note') }}</div>
                <button class="btn btn-brand btn-lg w-100" type="submit">{{ __('web.vendor.create_store') }}</button>
            </form>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="{{ asset('assets/web/js/pages/vendor.js') }}?v={{ config('website.version') }}"></script>
@endpush
