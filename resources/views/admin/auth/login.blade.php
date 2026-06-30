@extends('admin.layouts.auth')

@section('title', __('auth.form.login'))

@section('content')

    <div class="login-card">

        <div class="login-header">

            <h2>{{ __('auth.form.welcome') }}</h2>

            <p>{{ __('auth.form.login_subtitle') }}</p>

        </div>

        <form method="POST" action="{{ route('admin.login.submit') }}">

            @csrf

            <div class="input-group">

                <label>{{ __('auth.form.email') }}</label>

                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="{{ __('auth.form.email') }}">

                @error('email')
                <small class="text-error">{{ $message }}</small>
                @enderror

            </div>

            <div class="input-group">

                <label>{{ __('auth.form.password') }}</label>

                <input
                    type="password"
                    name="password"
                    placeholder="{{ __('auth.form.password') }}">

                @error('password')
                <small class="text-error">{{ $message }}</small>
                @enderror

            </div>

            <div class="remember">

                <label>

                    <input type="checkbox" name="remember">

                    {{ __('auth.form.remember_me') }}

                </label>

            </div>

            <button type="submit" class="btn-login">

                {{ __('auth.form.login') }}

            </button>

        </form>

    </div>
@endsection
