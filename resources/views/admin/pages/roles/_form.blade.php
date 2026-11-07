@php
    $role = $role ?? null;
    $selectedPermissions = old('permissions', $selectedPermissions ?? []);
@endphp

<div class="card shadow mb-4">
    <div class="card-header">
        <h5 class="mb-0">{{ __('roles.role_information') }}</h5>
    </div>
    <div class="card-body">
        <div class="form-group mb-0">
            <label for="name">{{ __('roles.name') }} <span class="text-danger">*</span></label>
            <input type="text" name="name" id="name" value="{{ old('name', $role?->name) }}"
                   class="form-control @error('name') is-invalid @enderror" maxlength="100" required
                   pattern="[A-Za-z0-9_-]+" autocomplete="off">
            <small class="form-text text-muted">{{ __('roles.name_hint') }}</small>
            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
        <div>
            <h5 class="mb-1">{{ __('roles.permissions') }}</h5>
            <small class="text-muted">{{ __('roles.permissions_hint') }}</small>
        </div>
        <div class="mt-2 mt-md-0">
            <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllPermissions">{{ __('roles.select_all') }}</button>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="clearAllPermissions">{{ __('roles.clear_all') }}</button>
        </div>
    </div>
    <div class="card-body">
        @error('permissions') <div class="alert alert-danger">{{ $message }}</div> @enderror
        @foreach($permissions as $module => $modulePermissions)
            <div class="border rounded p-3 mb-3 permission-module">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="mb-0 text-primary text-uppercase">{{ str_replace(['-', '_'], ' ', $module) }}</h6>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input module-toggle" id="module-{{ $loop->index }}" data-module="{{ $loop->index }}">
                        <label class="form-check-label" for="module-{{ $loop->index }}">{{ __('roles.select_module') }}</label>
                    </div>
                </div>
                <div class="row">
                    @foreach($modulePermissions as $permission)
                        <div class="col-md-4 col-lg-3 mb-2">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input permission-checkbox module-{{ $loop->parent->index }}"
                                       id="permission-{{ $permission->id }}" name="permissions[]"
                                       value="{{ $permission->name }}"
                                       @checked(in_array($permission->name, $selectedPermissions, true))>
                                <label class="custom-control-label" for="permission-{{ $permission->id }}">
                                    {{ Str::after($permission->name, '.') }}
                                    <small class="d-block text-muted">{{ $permission->name }}</small>
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>

<div class="d-flex justify-content-end mb-4">
    <a href="{{ route('admin.roles.index') }}" class="btn btn-light mr-2">{{ __('roles.cancel') }}</a>
    <button type="submit" class="btn btn-primary"><i class="fe fe-save mr-1"></i> {{ __('roles.save') }}</button>
</div>

<script>
    document.getElementById('selectAllPermissions')?.addEventListener('click', function () {
        document.querySelectorAll('.permission-checkbox').forEach(input => input.checked = true);
        document.querySelectorAll('.module-toggle').forEach(input => input.checked = true);
    });

    document.getElementById('clearAllPermissions')?.addEventListener('click', function () {
        document.querySelectorAll('.permission-checkbox, .module-toggle').forEach(input => input.checked = false);
    });

    document.querySelectorAll('.module-toggle').forEach(toggle => {
        toggle.addEventListener('change', function () {
            document.querySelectorAll('.module-' + this.dataset.module).forEach(input => input.checked = this.checked);
        });
    });

    document.querySelectorAll('.permission-checkbox').forEach(input => {
        input.addEventListener('change', function () {
            const moduleClass = Array.from(this.classList).find(className => className.startsWith('module-'));
            if (!moduleClass) return;
            const toggle = document.querySelector('.module-toggle[data-module="' + moduleClass.replace('module-', '') + '"]');
            const group = document.querySelectorAll('.' + moduleClass);
            if (toggle) toggle.checked = Array.from(group).every(permission => permission.checked);
        });
    });
</script>
