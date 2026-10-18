<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Throwable;

class SubCategoryController extends Controller
{
    public function index(Request $request)
    {
        $subCategories = Category::whereNotNull('parent_id')
            ->with('parent')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('name_ar', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%")
                        ->orWhereHas('parent', function ($parentQuery) use ($search) {
                            $parentQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('name_ar', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view(
            'admin.pages.sub-categories.index',
            compact('subCategories')
        );
    }

    public function create()
    {
        $parentCategories = Category::whereNull('parent_id')
            ->orderBy('name')
            ->get();

        return view(
            'admin.pages.sub-categories.create',
            compact('parentCategories')
        );
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'parent_id' => [
                'required',
                Rule::exists('categories', 'id')->whereNull('parent_id'),
            ],
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $slug = filled($validated['slug'] ?? null)
            ? Str::slug($validated['slug'])
            : Str::slug($validated['name']);

        if ($slug === '') {
            return back()->withInput()->withErrors([
                'slug' => __('dashboard.Please enter a valid English slug.'),
            ]);
        }

        if (Category::where('slug', $slug)->exists()) {
            return back()->withInput()->withErrors([
                'slug' => __('dashboard.This slug already exists.'),
            ]);
        }

        $imagePath = $this->uploadImage($request);

        try {
            Category::create([
                'name' => $validated['name'],
                'name_ar' => $validated['name_ar'],
                'slug' => $slug,
                'parent_id' => $validated['parent_id'],
                'image' => $imagePath,
            ]);
        } catch (Throwable $exception) {
            $this->deleteImage($imagePath);

            throw $exception;
        }

        return redirect()
            ->route('admin.sub-categories.index')
            ->with('success', __('messages.created_successfully'));
    }

    public function edit(Category $sub_category)
    {
        abort_if($sub_category->parent_id === null, 404);

        $parentCategories = Category::whereNull('parent_id')
            ->orderBy('name')
            ->get();

        return view('admin.pages.sub-categories.edit', [
            'subCategory' => $sub_category,
            'parentCategories' => $parentCategories,
        ]);
    }

    public function update(Request $request, Category $sub_category)
    {
        abort_if($sub_category->parent_id === null, 404);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('categories', 'slug')->ignore($sub_category->id),
            ],
            'parent_id' => [
                'required',
                Rule::exists('categories', 'id')->whereNull('parent_id'),
            ],
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $slug = filled($validated['slug'] ?? null)
            ? Str::slug($validated['slug'])
            : Str::slug($validated['name']);

        if ($slug === '') {
            return back()->withInput()->withErrors([
                'slug' => __('dashboard.Please enter a valid English slug.'),
            ]);
        }

        $slugExists = Category::where('slug', $slug)
            ->where('id', '!=', $sub_category->id)
            ->exists();

        if ($slugExists) {
            return back()->withInput()->withErrors([
                'slug' => __('dashboard.This slug already exists.'),
            ]);
        }

        $oldImagePath = $sub_category->image;
        $newImagePath = $this->uploadImage($request);

        try {
            $sub_category->update([
                'name' => $validated['name'],
                'name_ar' => $validated['name_ar'],
                'slug' => $slug,
                'parent_id' => $validated['parent_id'],
                'image' => $newImagePath ?? $oldImagePath,
            ]);
        } catch (Throwable $exception) {
            $this->deleteImage($newImagePath);

            throw $exception;
        }

        if ($newImagePath) {
            $this->deleteImage($oldImagePath);
        }

        return redirect()
            ->route('admin.sub-categories.index')
            ->with('success', __('messages.updated_successfully'));
    }

    public function destroy(Category $sub_category)
    {
        abort_if($sub_category->parent_id === null, 404);

        $imagePath = $sub_category->image;

        $sub_category->delete();

        $this->deleteImage($imagePath);

        return redirect()
            ->route('admin.sub-categories.index')
            ->with('success', __('messages.deleted_successfully'));
    }

    private function uploadImage(Request $request): ?string
    {
        if (!$request->hasFile('image')) {
            return null;
        }

        $uploadPath = public_path('uploads/categories');

        if (!File::exists($uploadPath)) {
            File::makeDirectory($uploadPath, 0755, true);
        }

        $image = $request->file('image');
        $imageName = Str::uuid() . '.' . $image->getClientOriginalExtension();

        $image->move($uploadPath, $imageName);

        return 'uploads/categories/' . $imageName;
    }

    private function deleteImage(?string $imagePath): void
    {
        if (
            $imagePath &&
            str_starts_with($imagePath, 'uploads/categories/') &&
            File::exists(public_path($imagePath))
        ) {
            File::delete(public_path($imagePath));
        }
    }
}
