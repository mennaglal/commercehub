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
            value="{{ old('name', $subCategory->name ?? '') }}"
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
            value="{{ old('name_ar', $subCategory->name_ar ?? '') }}"
            required>
    </div>

    <div class="form-group col-md-6">
        <label for="slug">{{ __('dashboard.Slug') }}</label>

        <input
            id="slug"
            name="slug"
            class="form-control"
            value="{{ old('slug', $subCategory->slug ?? '') }}">

        <small class="text-muted">
            {{ __('dashboard.Leave blank to generate slug') }}
        </small>
    </div>

    <div class="form-group col-md-6">
        <label for="parent_id">
            {{ __('dashboard.Parent Category') }}
            <span class="text-danger">*</span>
        </label>

        <select id="parent_id" name="parent_id" class="form-control" required>
            <option value="">
                {{ __('dashboard.Select main category') }}
            </option>

            @foreach($parentCategories as $parent)
                <option
                    value="{{ $parent->id }}"
                    {{ (string) old('parent_id', $subCategory->parent_id ?? '') === (string) $parent->id ? 'selected' : '' }}>
                    {{ $parent->name }} - {{ $parent->name_ar }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group col-md-6">
        <label for="image">{{ __('dashboard.Image') }}</label>

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
                src="{{ !empty($subCategory?->image) ? asset($subCategory->image) : '' }}"
                width="200"
                height="200"
                style="object-fit: fill; {{ empty($subCategory?->image) ? 'display:none;' : '' }}"
                alt="{{ __('dashboard.Subcategory Image') }}">
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const imageInput = document.getElementById('image');
        const preview = document.getElementById('imagePreview');

        if (!imageInput || !preview) {
            return;
        }

        imageInput.addEventListener('change', function () {
            if (this.files && this.files[0]) {
                preview.src = URL.createObjectURL(this.files[0]);
                preview.style.display = 'block';
            }
        });
    });
</script>
