@extends('web.layouts.app')
@section('title', __('web.nav.profile'))
@section('page', 'account')

@section('content')
<div class="page-hero"><div class="container"><h1>{{ __('web.account.title') }}</h1></div></div>

<section class="container pb-4">
    <div class="row g-4">
        <div class="col-lg-3">@include('web.partials.account-menu')</div>

        <div class="col-lg-9">
            <div class="card-soft pad mb-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <img class="avatar" id="avatar-img" src="{{ asset('assets/web/img/placeholder.svg') }}" alt="">
                    <div>
                        <h5 class="mb-1" id="profile-name">&nbsp;</h5>
                        <label class="btn btn-soft btn-sm mb-0" for="avatar-input"><i class="bi bi-camera me-1"></i>{{ __('web.account.change_photo') }}</label>
                        <input type="file" id="avatar-input" class="d-none" accept=".jpg,.jpeg,.png,.webp">
                    </div>
                </div>

                <form id="profile-form" novalidate>
                    <div class="alert alert-danger form-errors d-none" role="alert"></div>
                    <div class="row g-3">
                        <div class="col-md-6"><label class="form-label" for="name">{{ __('web.auth.name') }} *</label><input class="form-control" id="name" name="name" maxlength="255" required></div>
                        <div class="col-md-6"><label class="form-label" for="email">{{ __('web.auth.email') }}</label><input class="form-control" id="email" name="email" disabled></div>
                        <div class="col-md-6"><label class="form-label" for="phone">{{ __('web.auth.phone') }}</label><input class="form-control" id="phone" name="phone" maxlength="30" inputmode="tel"></div>
                        <div class="col-12"><hr class="my-1"></div>
                        <div class="col-md-4"><label class="form-label" for="country_id">{{ __('web.checkout.country') }}</label><select class="form-select" id="country_id" name="country_id"></select></div>
                        <div class="col-md-4"><label class="form-label" for="city_id">{{ __('web.checkout.city') }}</label><select class="form-select" id="city_id" name="city_id" disabled></select></div>
                        <div class="col-md-4"><label class="form-label" for="area_id">{{ __('web.checkout.area') }}</label><select class="form-select" id="area_id" name="area_id" disabled></select></div>
                    </div>
                    <button class="btn btn-brand mt-3" type="submit">{{ __('web.common.save') }}</button>
                </form>
            </div>

            <div class="card-soft pad">
                <h5 class="mb-3">{{ __('web.account.password_title') }}</h5>
                <form id="password-form" novalidate>
                    <div class="row g-3">
                        <div class="col-md-4"><label class="form-label" for="current_password">{{ __('web.account.current_password') }}</label><input class="form-control" type="password" id="current_password" name="current_password" autocomplete="current-password" required></div>
                        <div class="col-md-4"><label class="form-label" for="new_password">{{ __('web.account.new_password') }}</label><input class="form-control" type="password" id="new_password" name="password" autocomplete="new-password" required></div>
                        <div class="col-md-4"><label class="form-label" for="new_password_confirmation">{{ __('web.auth.password_confirm') }}</label><input class="form-control" type="password" id="new_password_confirmation" name="password_confirmation" autocomplete="new-password" required></div>
                    </div>
                    <button class="btn btn-brand mt-3" type="submit">{{ __('web.account.change_password') }}</button>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="{{ asset('assets/web/js/pages/account.js') }}?v={{ config('website.version') }}"></script>
@endpush
