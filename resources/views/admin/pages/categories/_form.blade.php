
<div class="form-row">
    <div class="form-group col-md-6">
        <label for="name">
            {{ __('dashboard.English Name') }}
            <span class="text-danger">*</span>
        </label>

        <input
            id="name"
            name="name"
            class="form-control"
            value="{{ old('name', $category->name ?? '') }}"
            required>
    </div>

    <div class="form-group col-md-6">
        <label for="name_ar">
            {{ __('dashboard.Arabic Name') }}
            <span class="text-danger">*</span>
        </label>

        <input
            id="name_ar"
            name="name_ar"
            class="form-control"
            dir="rtl"
            value="{{ old('name_ar', $category->name_ar ?? '') }}"
            required>
    </div>

    <div class="form-group col-md-6">
        <label for="slug">{{ __('dashboard.Slug') }}</label>

        <input
            id="slug"
            name="slug"
            class="form-control"
            value="{{ old('slug', $category->slug ?? '') }}">

        <small class="text-muted">
            {{ __('dashboard.Leave blank to generate slug') }}
        </small>
    </div>

    <div class="form-group col-md-6">
        <label for="image">{{ __('dashboard.Category Image') }}</label>

        <input
            id="image"
            name="image"
            type="file"
            class="form-control"
            accept=".jpg,.jpeg,.png,.webp">

        <small class="text-muted">
            {{ __('dashboard.Maximum image size') }}
        </small>

        <div class="mt-3">
            <img
                id="imagePreview"
                src="{{ !empty($category?->image) ? asset($category->image) : '' }}"
                width="200"
                height="200"
                style="object-fit: fill; {{ empty($category?->image) ? 'display:none;' : '' }}"
                alt="{{ __('dashboard.Category Image') }}">
        </div>
    </div>
</div>

<script>
    document.getElementById('image').addEventListener('change', function () {
        const preview = document.getElementById('imagePreview');

        if (this.files && this.files[0]) {
            preview.src = URL.createObjectURL(this.files[0]);
            preview.style.display = 'block';
        }
    });
</script>
