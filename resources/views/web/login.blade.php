@extends('web.layouts.app')
@section('title', __('web.auth.login_title'))
@section('page', 'login')

@section('content')
<section class="container page-section">
    <div class="auth-wrap card-soft pad">
        <div class="text-center mb-4"><h1 class="h3">{{ __('web.auth.login_title') }}</h1><p class="text-muted mb-0">{{ __('web.auth.login_text') }}</p></div>
        <form id="login-form" novalidate>
            <div class="alert alert-danger form-errors d-none" role="alert"></div>
            <div class="mb-3"><label class="form-label" for="email">{{ __('web.auth.email') }}</label><input class="form-control" type="email" id="email" name="email" autocomplete="email" required></div>
            <div class="mb-3"><label class="form-label" for="password">{{ __('web.auth.password') }}</label><input class="form-control" type="password" id="password" name="password" autocomplete="current-password" required></div>
            <button class="btn btn-brand w-100 btn-lg" type="submit">{{ __('web.nav.login') }}</button>
        </form>
        <div class="divider-text my-3">{{ __('web.common.or') }}</div>
        <a class="btn btn-soft w-100" href="{{ route('site.register') }}">{{ __('web.auth.create_account') }}</a>
    </div>
</section>
@endsection

@push('scripts')
<script src="{{ asset('assets/web/js/pages/auth.js') }}?v={{ config('website.version') }}"></script>
@endpush
