<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Models\Category;
use App\Models\Product;
use App\Services\ImageUploader;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
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

    public function __construct(private ImageUploader $images) {}

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

    public function store(CategoryRequest $request)
    {
        $data = $request->validated();

        $imagePath = $request->hasFile('image')
            ? $this->images->upload($request->file('image'), 'categories')
            : null;

        try {
            Category::create([
                'name' => $data['name'],
                'name_ar' => $data['name_ar'],
                'slug' => $data['slug'],
                'parent_id' => null,
                'image' => $imagePath,
            ]);
        } catch (Throwable $exception) {
            $this->images->delete($imagePath);

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

    public function update(CategoryRequest $request, Category $category)
    {
        abort_if($category->parent_id !== null, 404);

        $data = $request->validated();

        $oldImagePath = $category->image;
        $newImagePath = $request->hasFile('image')
            ? $this->images->upload($request->file('image'), 'categories')
            : null;

        try {
            $category->update([
                'name' => $data['name'],
                'name_ar' => $data['name_ar'],
                'slug' => $data['slug'],
                'image' => $newImagePath ?? $oldImagePath,
            ]);
        } catch (Throwable $exception) {
            $this->images->delete($newImagePath);

            throw $exception;
        }

        if ($newImagePath) {
            $this->images->delete($oldImagePath);
        }

        return redirect()
            ->route('admin.categories.index')
            ->with('success', __('messages.updated_successfully'));
    }

    public function destroy(Category $category)
    {
        abort_if($category->parent_id !== null, 404);

        $categoryIds = $category->children()->pluck('id')->push($category->id);

        // Deleting a category must never silently delete the products inside it.
        if (Product::whereIn('category_id', $categoryIds)->exists()) {
            return back()->with('error', __('messages.category_has_products'));
        }

        $imagePaths = Category::whereIn('id', $categoryIds)
            ->pluck('image')
            ->filter()
            ->all();

        $category->delete();

        foreach ($imagePaths as $imagePath) {
            $this->images->delete($imagePath);
        }

        return redirect()
            ->route('admin.categories.index')
            ->with('success', __('messages.deleted_successfully'));
    }
}
