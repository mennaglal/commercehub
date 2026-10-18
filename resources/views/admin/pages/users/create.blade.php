@extends('admin.layouts.app')

@section('content')
    @include('includes.error')
    @include('includes.success')

    <div class="card shadow mb-4">
        <div class="card-header">
            <h5 class="mb-0">{{ __('dashboard.users_vendors.add_user') }}</h5>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>{{ __('dashboard.Name') }}</label>
                        <input class="form-control" type="text" name="name" value="{{ old('name') }}" required>
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
                        <label>{{ __('dashboard.Image') }}</label>

                        <input
                            type="file"
                            name="avatar"
                            id="avatar"
                            class="form-control"
                            accept="image/*"
                        >

                        <div id="avatarPreviewContainer" class="mt-3" style="display: none;">
                            <img
                                id="avatarPreview"
                                src=""
                                alt="Image Preview"
                                width="200"
                                height="200"
                                class="rounded"
                                style="object-fit: fill; border: 1px solid #ddd; padding: 3px;"
                            >
                        </div>

                        @error('avatar')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                </div>
                <button class="btn btn-primary" type="submit">{{ __('dashboard.users_vendors.create') }}</button>
                <a class="btn btn-light" href="{{ route('admin.users.index') }}">{{ __('dashboard.users_vendors.cancel') }}</a>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('avatar').addEventListener('change', function (event) {
            const file = event.target.files[0];
            const preview = document.getElementById('avatarPreview');
            const container = document.getElementById('avatarPreviewContainer');

            if (file && file.type.startsWith('image/')) {
                preview.src = URL.createObjectURL(file);
                container.style.display = 'block';
            } else {
                preview.src = '';
                container.style.display = 'none';
            }
        });
    </script>

@endsection
