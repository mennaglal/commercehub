<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Throwable;

class CategoryController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:categories.view', only: ['index']),
            new Middleware('permission:categories.create', only: ['create', 'store']),
            new Middleware('permission:categories.update', only: ['edit', 'update']),
            new Middleware('permission:categories.delete', only: ['destroy']),
        ];
    }
    public function index(Request $request)
    {
        $categories = Category::whereNull('parent_id')
            ->withCount('children')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('name_ar', 'like', "%{$search}%")
                        ->orWhere('slug', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.pages.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.pages.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $slug = filled($validated['slug'] ?? null)
            ? $validated['slug']
            : Str::slug($validated['name']);

        if ($slug === '') {
            return back()->withInput()->withErrors([
                'slug' => 'Please enter a valid English slug.',
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
                'parent_id' => null,
                'image' => $imagePath,
            ]);
        } catch (Throwable $exception) {
            $this->deleteImage($imagePath);

            throw $exception;
        }

        return redirect()
            ->route('admin.categories.index')
            ->with('success', __('messages.created_successfully'));
    }

    public function edit(Category $category)
    {
        abort_if($category->parent_id !== null, 404);

        return view('admin.pages.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        abort_if($category->parent_id !== null, 404);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_ar' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('categories', 'slug')->ignore($category->id),
            ],
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $slug = filled($validated['slug'] ?? null)
            ? $validated['slug']
            : Str::slug($validated['name']);

        if ($slug === '') {
            return back()->withInput()->withErrors([
                'slug' => __('dashboard.Please enter a valid English slug.'),
                ]);
        }

        $oldImagePath = $category->image;
        $newImagePath = $this->uploadImage($request);

        try {
            $category->update([
                'name' => $validated['name'],
                'name_ar' => $validated['name_ar'],
                'slug' => $slug,
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
            ->route('admin.categories.index')
            ->with('success', __('messages.updated_successfully'));
    }

    public function destroy(Category $category)
    {
        abort_if($category->parent_id !== null, 404);

        $imagePaths = Category::where('id', $category->id)
            ->orWhere('parent_id', $category->id)
            ->pluck('image')
            ->filter()
            ->all();

        $category->delete();

        foreach ($imagePaths as $imagePath) {
            $this->deleteImage($imagePath);
        }

        return redirect()
            ->route('admin.categories.index')
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
