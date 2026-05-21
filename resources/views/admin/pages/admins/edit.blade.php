@extends('admin.layouts.app')
@section('content')
    @include('includes.error')
    @include('includes.success')


    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{__('dashboard.Edit Admin')}}</h5>
                </div>
                <div class="card-body">


                    <form class="forms-sample" method="POST" action="{{ route('admin.admins.update', $admin->id) }}"
                          enctype="multipart/form-data">
                        @csrf

                        <div class="form-row">

                            <div class="form-group col-md-6">
                                <label for="userName">{{__('dashboard.Name')}} <span
                                        class="text-danger">*</span></label>
                                <input id="userName" name="name" type="text" class="form-control "
                                       value="{{ old('name', $admin->name) }}" required>
                            </div>
                            <div class="form-group col-md-6">
                                <label for="email">{{__('dashboard.Email')}} <span class="text-danger">*</span></label>
                                <input id="email" name="email" type="text" class="form-control  email"
                                       value="{{ old('email', $admin->email) }}" required>
                            </div>

                        </div>

                        <div class="form-row">

                            <div class="form-group col-md-6">
                                <label for="userPassword">{{__('dashboard.Password')}} <span
                                        class="text-danger">*</span></label>
                                <input id="userPassword" name="password" type="password" class="form-control ">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="userAvatar">{{__('dashboard.Image')}}</label>
                                <input id="userAvatar" name="image" type="file" class="form-control">
                                @if($admin->avatar)
                                    <div>
                                        <img src="{{asset($admin->image)}}" style="width:50px; height:50px"/>
                                    </div>
                                @endif

                            </div>

                        </div>


                        <div class="col-auto d-flex justify-content-center mt-3">
                            <button type="submit" class="btn btn-primary">
                                <span class="fe fe-repeat fe-16 text-white mr-2"></span>{{__('dashboard.Update')}}
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
