<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SubCategoryRequest;
use App\Models\Category;
use App\Services\ImageUploader;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Throwable;

class SubCategoryController extends Controller implements HasMiddleware
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

        return view('admin.pages.sub-categories.index', compact('subCategories'));
    }

    public function create()
    {
        $parentCategories = Category::whereNull('parent_id')
            ->orderBy('name')
            ->get();

        return view('admin.pages.sub-categories.create', compact('parentCategories'));
    }

    public function store(SubCategoryRequest $request)
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
                'parent_id' => $data['parent_id'],
                'image' => $imagePath,
            ]);
        } catch (Throwable $exception) {
            $this->images->delete($imagePath);

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

    public function update(SubCategoryRequest $request, Category $sub_category)
    {
        abort_if($sub_category->parent_id === null, 404);

        $data = $request->validated();

        $oldImagePath = $sub_category->image;
        $newImagePath = $request->hasFile('image')
            ? $this->images->upload($request->file('image'), 'categories')
            : null;

        try {
            $sub_category->update([
                'name' => $data['name'],
                'name_ar' => $data['name_ar'],
                'slug' => $data['slug'],
                'parent_id' => $data['parent_id'],
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
            ->route('admin.sub-categories.index')
            ->with('success', __('messages.updated_successfully'));
    }

    public function destroy(Category $sub_category)
    {
        abort_if($sub_category->parent_id === null, 404);

        if ($sub_category->products()->exists()) {
            return back()->with('error', __('messages.category_has_products'));
        }

        $imagePath = $sub_category->image;

        $sub_category->delete();
        $this->images->delete($imagePath);

        return redirect()
            ->route('admin.sub-categories.index')
            ->with('success', __('messages.deleted_successfully'));
    }
}
