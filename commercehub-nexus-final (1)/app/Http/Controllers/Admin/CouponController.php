<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CouponRequest;
use App\Models\Blogger;
use App\Models\Category;
use App\Models\Coupon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
class CouponController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:coupons.view', only: ['index']),
            new Middleware('permission:coupons.create', only: ['create', 'store']),
            new Middleware('permission:coupons.update', only: ['edit', 'update']),
            new Middleware('permission:coupons.delete', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $coupons = Coupon::with('blogger:id,name')
            ->withCount('categories')
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->search);

                $query->where(function ($q) use ($search) {
                    $q->where('code', 'like', "%{$search}%")
                        ->orWhereHas('blogger', function ($blogger) use ($search) {
                            $blogger->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.pages.coupons.index', compact('coupons'));
    }

    public function create()
    {
        return view('admin.pages.coupons.create', $this->formData());
    }

    public function store(CouponRequest $request)
    {
        $data = $request->validated();

        // The coupon and its categories are saved together or not at all.
        DB::transaction(function () use ($data) {
            $coupon = Coupon::create($this->attributes($data));
            $coupon->categories()->sync($data['categories']);
        });

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', __('coupons.messages.created'));
    }

    public function edit(Coupon $coupon)
    {
        $coupon->load('categories:id');

        return view('admin.pages.coupons.edit', $this->formData() + ['coupon' => $coupon]);
    }

    public function update(CouponRequest $request, Coupon $coupon)
    {
        $data = $request->validated();

        DB::transaction(function () use ($data, $coupon) {
            $coupon->update($this->attributes($data));
            $coupon->categories()->sync($data['categories']);
        });

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', __('coupons.messages.updated'));
    }
    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return redirect()
            ->route('admin.coupons.index')
            ->with('success', __('coupons.messages.deleted'));
    }
    // The coupon columns (everything except the categories).
    private function attributes(array $data): array
    {
        $attributes = Arr::except($data, ['categories']);

        // General coupon: no blogger, so no commission.
        if (empty($attributes['blogger_id'])) {
            $attributes['blogger_id'] = null;
            $attributes['commission_type'] = null;
            $attributes['commission_value'] = null;
        }

        return $attributes;
    }

    // Lists used by the form: categories as "Parent / Child" and the bloggers.
    private function formData(): array
    {
        $isArabic = app()->getLocale() === 'ar';
        $all = Category::orderBy('parent_id')->orderBy('id')->get()->keyBy('id');

        $name = fn ($category) => $isArabic ? $category->name_ar : $category->name;

        $categories = $all->map(function ($category) use ($all, $name) {
            $parent = $category->parent_id ? $all->get($category->parent_id) : null;

            return $parent ? $name($parent) . ' / ' . $name($category) : $name($category);
        })->sort()->all();   // [id => label]

        return [
            'categories' => $categories,
            'bloggers' => Blogger::orderBy('name')->get(['id', 'name']),
        ];
    }
}
