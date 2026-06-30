@extends('admin.layouts.app')
@section('content')
    @include('includes.error')
    @include('includes.success')



    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{__('dashboard.Add-Admin')}}</h5>
                </div>
                <div class="card-body">


                    <form class="forms-sample" method="POST" action="{{route('admin.admins.store')}}"
                          enctype="multipart/form-data">
                        @csrf

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="userName">{{__('dashboard.Name')}} <span class="text-danger">*</span>
                                </label>
                                <input id="userName" name="name" type="text"
                                       value="{{old('name')}}" class="form-control " required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="email">{{__('dashboard.Email')}} <span class="text-danger">*</span></label>
                                <input id="email" name="email" type="email" class="form-control email"
                                       value="{{ old('email') }}" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="role">
                                    {{ __('dashboard.Role') }}
                                    <span class="text-danger">*</span>
                                </label>

                                <select name="role" id="role" class="form-control" required>
                                    <option value="">
                                        {{ __('dashboard.Select Role') }}
                                    </option>

                                    @foreach($roles as $role)
                                        <option
                                            value="{{ $role->name }}"
                                            {{ old('role') == $role->name ? 'selected' : '' }}>
                                            {{ ucfirst(str_replace('-', ' ', $role->name)) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="userPassword">
                                    {{ __('dashboard.Password') }}
                                    <span class="text-danger">*</span>
                                </label>

                                <input
                                    id="userPassword"
                                    name="password"
                                    type="password"
                                    class="form-control"
                                    required>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="password_confirmation">
                                    {{ __('dashboard.Confirm Password') }}
                                    <span class="text-danger">*</span>
                                </label>

                                <input
                                    id="password_confirmation"
                                    name="password_confirmation"
                                    type="password"
                                    class="form-control"
                                    required>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="userAvatar">{{__('dashboard.Image')}}</label>
                                <input id="userAvatar" name="avatar" type="file" class="form-control">
                            </div>

                        </div>

                        <div class="col-auto d-flex justify-content-center mt-3">
                            <button type="submit" class="btn btn-primary">
                                <span class="fe fe-plus-circle fe-16 mr-2"></span>{{__('dashboard.Create')}}
                            </button>
                        </div>


                    </form>

                </div>
            </div>
        </div>
    </div>
    </div>
    </div>

@endsection
