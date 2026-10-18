
@extends('admin.layouts.app')

@section('title', __('dashboard.Edit-Currency'))

@section('content')
    @include('includes.error')
    @include('includes.success')

    <div class="row justify-content-center">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header">
                    <h5 class="mb-0">{{ __('dashboard.Edit-Currency') }}</h5>
                </div>

                <div class="card-body">
                    <form
                        method="POST"
                        action="{{ route('admin.currencies.update', $currency) }}"
                    >
                        @csrf
                        @method('PUT')

                        @php($isEdit = true)

                        @include('admin.pages.currencies._form')
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
