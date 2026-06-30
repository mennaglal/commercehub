@extends('admin.layouts.app')

@section('content')

    @include('includes.error')
    @include('includes.success')

    <div class="row justify-content-center">

        <div class="col-12">

            <div class="card shadow mb-4">

                <div class="card-header">
                    <h5 class="mb-0">
                        {{ __('dashboard.Edit-Admin') }}
                    </h5>
                </div>

                <div class="card-body">

                    <form method="POST"
                          action="{{ route('admin.admins.update',$admin->id) }}"
                          enctype="multipart/form-data">

                        @csrf
                        @method('PUT')

                        <div class="form-row">

                            <div class="form-group col-md-6">
                                <label for="userName">
                                    {{ __('dashboard.Name') }}
                                    <span class="text-danger">*</span>
                                </label>

                                <input
                                    id="userName"
                                    type="text"
                                    name="name"
                                    class="form-control"
                                    value="{{ old('name',$admin->name) }}"
                                    required>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="email">
                                    {{ __('dashboard.Email') }}
                                    <span class="text-danger">*</span>
                                </label>

                                <input
                                    id="email"
                                    type="email"
                                    name="email"
                                    class="form-control"
                                    value="{{ old('email',$admin->email) }}"
                                    required>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="role">
                                    {{ __('dashboard.Role') }}
                                    <span class="text-danger">*</span>
                                </label>

                                <select
                                    name="role"
                                    id="role"
                                    class="form-control"
                                    required>

                                    <option value="">
                                        {{ __('dashboard.Select Role') }}
                                    </option>

                                    @foreach($roles as $role)

                                        <option
                                            value="{{ $role->name }}"
                                            {{ old('role',$admin->roles->first()?->name) == $role->name ? 'selected' : '' }}>

                                            {{ ucfirst(str_replace('-',' ',$role->name)) }}

                                        </option>

                                    @endforeach

                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="userPassword">
                                    {{ __('dashboard.Password') }}
                                </label>

                                <input
                                    id="userPassword"
                                    type="password"
                                    name="password"
                                    class="form-control">

                                <small class="text-muted">
                                    {{ __('dashboard.Leave password blank if you do not want to change it') }}
                                </small>

                            </div>

                            <div class="form-group col-md-6">
                                <label for="password_confirmation">
                                    {{ __('dashboard.Confirm Password') }}
                                </label>

                                <input
                                    id="password_confirmation"
                                    type="password"
                                    name="password_confirmation"
                                    class="form-control">
                            </div>

                            <div class="form-group col-md-6">

                                <label for="avatar">
                                    {{ __('dashboard.Image') }}
                                </label>

                                <input
                                    id="avatar"
                                    type="file"
                                    name="avatar"
                                    class="form-control">

                                @if($admin->avatar)

                                    <div class="mt-3">

                                        <img
                                            src="{{ asset($admin->avatar) }}"
                                            width="80"
                                            height="80"
                                            class="rounded border">

                                    </div>

                                @endif

                            </div>

                        </div>

                        <div class="text-center mt-4">

                            <button type="submit" class="btn btn-primary">

                                <span class="fe fe-save mr-2"></span>

                                {{ __('dashboard.Update') }}

                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

@endsection
