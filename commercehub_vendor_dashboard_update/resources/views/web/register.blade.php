@extends('web.layouts.app')
@section('title', __('web.auth.register_title'))
@section('page', 'register')

@section('content')
<section class="container page-section">
    <div class="auth-wrap card-soft pad">
        <div class="text-center mb-4"><h1 class="h3">{{ __('web.auth.register_title') }}</h1><p class="text-muted mb-0">{{ __('web.auth.register_text') }}</p></div>
        <form id="register-form" novalidate>
            <div class="alert alert-danger form-errors d-none" role="alert"></div>
            <div class="mb-3"><label class="form-label" for="name">{{ __('web.auth.name') }} *</label><input class="form-control" id="name" name="name" maxlength="255" autocomplete="name" required></div>
            <div class="mb-3"><label class="form-label" for="email">{{ __('web.auth.email') }} *</label><input class="form-control" type="email" id="email" name="email" autocomplete="email" required></div>
            <div class="mb-3"><label class="form-label" for="phone">{{ __('web.auth.phone') }}</label><input class="form-control" id="phone" name="phone" maxlength="30" inputmode="tel" autocomplete="tel"></div>
            <div class="row g-3 mb-3">
                <div class="col-md-6"><label class="form-label" for="password">{{ __('web.auth.password') }} *</label><input class="form-control" type="password" id="password" name="password" autocomplete="new-password" required></div>
                <div class="col-md-6"><label class="form-label" for="password_confirmation">{{ __('web.auth.password_confirm') }} *</label><input class="form-control" type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password" required></div>
            </div>
            <button class="btn btn-brand w-100 btn-lg" type="submit">{{ __('web.auth.create_account') }}</button>
        </form>
        <div class="divider-text my-3">{{ __('web.common.or') }}</div>
        <a class="btn btn-soft w-100" href="{{ route('site.login') }}">{{ __('web.nav.login') }}</a>
    </div>
</section>
@endsection

@push('scripts')
<script src="{{ asset('assets/web/js/pages/auth.js') }}?v={{ config('website.version') }}"></script>
@endpush
