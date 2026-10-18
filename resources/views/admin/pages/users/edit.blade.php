@extends('admin.layouts.app')

@section('content')
    @include('includes.error')
    @include('includes.success')

    <div class="card shadow mb-4">
        <div class="card-header">
            <h5 class="mb-0">{{ __('dashboard.users_vendors.edit_user') }}</h5>
        </div>
        <div class="card-body">
            <form method="POST"
                  action="{{ route('admin.users.update', $user) }}"
                  enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>{{ __('dashboard.Image') }}</label>

                        @if($user->avatar)
                            <div class="mb-2">
                                <img
                                    src="{{ asset($user->avatar) }}"
                                    alt="{{ $user->name }}"
                                    width="80"
                                    height="80"
                                    class="rounded-circle"
                                    style="object-fit: cover;"
                                >
                            </div>
                        @endif

                        <input
                            type="file"
                            name="avatar"
                            class="form-control"
                            accept="image/*"
                        >

                        @error('avatar')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="form-group col-md-6">
                        <label>{{ __('dashboard.users_vendors.name') }}</label>
                        <input class="form-control" name="name" value="{{ old('name', $user->name) }}" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>{{ __('dashboard.users_vendors.email') }}</label>
                        <input class="form-control" type="email" name="email" value="{{ old('email', $user->email) }}" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>{{ __('dashboard.users_vendors.phone') }}</label>
                        <input class="form-control" name="phone" value="{{ old('phone', $user->phone) }}">
                    </div>
                    <div class="form-group col-md-6">
                        <label>{{ __('dashboard.users_vendors.password_optional') }}</label>
                        <input class="form-control" type="password" name="password" autocomplete="new-password">
                    </div>
                    <div class="form-group col-md-6">
                        <label>{{ __('dashboard.users_vendors.password_confirmation') }}</label>
                        <input class="form-control" type="password" name="password_confirmation" autocomplete="new-password">
                    </div>
                </div>
                <button class="btn btn-primary" type="submit">{{ __('dashboard.users_vendors.update') }}</button>
                <a class="btn btn-light" href="{{ route('admin.users.index') }}">{{ __('dashboard.users_vendors.cancel') }}</a>
            </form>
        </div>
    </div>
@endsection
