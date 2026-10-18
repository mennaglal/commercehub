
@extends('admin.layouts.app')

@section('title', __('dashboard.Edit-Category'))

@section('content')
    @include('includes.error')
    @include('includes.success')

    <div class="card shadow mb-4">
        <div class="card-header">
            <h5 class="mb-0">{{ __('dashboard.Edit-Category') }}</h5>
        </div>

        <div class="card-body">
            <form
                method="POST"
                action="{{ route('admin.categories.update', $category) }}"
                enctype="multipart/form-data">
                @csrf
                @method('PUT')

                @include('admin.pages.categories._form')

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary">
                        {{ __('dashboard.Update') }}
                    </button>

                    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                        {{ __('dashboard.Cancelbtn') }}
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
