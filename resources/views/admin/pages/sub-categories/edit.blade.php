@extends('admin.layouts.app')

@section('title', __('dashboard.Edit-Sub-Category'))

@section('content')
    @include('includes.error')
    @include('includes.success')

    <div class="card shadow mb-4">
        <div class="card-header">
            <h5 class="mb-0">{{ __('dashboard.Edit-Sub-Category') }}</h5>
        </div>

        <div class="card-body">
            <form
                method="POST"
                action="{{ route('admin.sub-categories.update', $subCategory) }}"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                @include('admin.pages.sub-categories._form')

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary">
                        {{ __('dashboard.Update') }}
                    </button>

                    <a href="{{ route('admin.sub-categories.index') }}"
                       class="btn btn-secondary">
                        {{ __('dashboard.Cancelbtn') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
