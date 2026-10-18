@extends('admin.layouts.app')

@section('content')
    @include('includes.error')
    @include('includes.success')

    <div class="card shadow mb-4">
        <div class="card-header"><h5 class="mb-0">{{ __('dashboard.users_vendors.add_vendor') }}</h5></div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.vendors.store') }}">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>{{ __('dashboard.users_vendors.vendor_name') }}</label>
                        <input class="form-control" name="name" value="{{ old('name') }}" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>{{ __('dashboard.users_vendors.email') }}</label>
                        <input class="form-control" type="email" name="email" value="{{ old('email') }}" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>{{ __('dashboard.users_vendors.phone') }}</label>
                        <input class="form-control" name="phone" value="{{ old('phone') }}">
                    </div>
                    <div class="form-group col-md-6">
                        <label>{{ __('dashboard.users_vendors.password') }}</label>
                        <input class="form-control" type="password" name="password" required autocomplete="new-password">
                    </div>
                    <div class="form-group col-md-6">
                        <label>{{ __('dashboard.users_vendors.password_confirmation') }}</label>
                        <input class="form-control" type="password" name="password_confirmation" required autocomplete="new-password">
                    </div>
                    <div class="form-group col-md-6">
                        <label>{{ __('dashboard.users_vendors.store_name') }}</label>
                        <input class="form-control" name="store_name" value="{{ old('store_name') }}" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>{{ __('dashboard.users_vendors.store_slug') }}</label>
                        <input class="form-control" name="slug" value="{{ old('slug') }}" placeholder="{{ __('dashboard.users_vendors.slug_auto') }}">
                    </div>
                    <div class="form-group col-12">
                        <label>{{ __('dashboard.users_vendors.description') }}</label>
                        <textarea class="form-control" name="description" rows="4">{{ old('description') }}</textarea>
                    </div>
                </div>
                <button class="btn btn-primary" type="submit">{{ __('dashboard.users_vendors.create') }}</button>
                <a class="btn btn-light" href="{{ route('admin.vendors.index') }}">{{ __('dashboard.users_vendors.cancel') }}</a>
            </form>
        </div>
    </div>
@endsection
