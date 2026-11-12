<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\VendorProductRequest;
use App\Models\Category;
use App\Models\VariantType;
use App\Services\VendorProductService;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(private VendorProductService $products) {}

    public function index(Request $request)
    {
        $store = $request->user()->store;
        $products = $this->products->paginate($store, trim((string) $request->query('search')), 15);
        return view('vendor-dashboard.products.index', compact('products'));
    }

    public function create()
    {
        return view('vendor-dashboard.products.form', $this->formData() + ['product' => null, 'formAction' => route('vendor.products.store'), 'formMethod' => 'POST']);
    }

    public function store(VendorProductRequest $request)
    {
        $this->products->create($request->user()->store, $request->validated(), $request->file('image'));
        return redirect()->route('vendor.products.index')->with('success', __('messages.created_successfully'));
    }

    public function edit(Request $request, int $product)
    {
        $item = $this->products->find($request->user()->store, $product);
        return view('vendor-dashboard.products.form', $this->formData() + ['product' => $item, 'formAction' => route('vendor.products.update', $item->id), 'formMethod' => 'PUT']);
    }

    public function update(VendorProductRequest $request, int $product)
    {
        $this->products->update($request->user()->store, $product, $request->validated(), $request->file('image'));
        return redirect()->route('vendor.products.index')->with('success', __('messages.updated_successfully'));
    }

    public function destroy(Request $request, int $product)
    {
        if (! $this->products->delete($request->user()->store, $product)) {
            return back()->with('error', __('products.delete_failed'));
        }
        return back()->with('success', __('messages.deleted_successfully'));
    }

    public function toggleStatus(Request $request, int $product)
    {
        $this->products->toggleStatus($request->user()->store, $product);
        return back()->with('success', __('messages.updated_successfully'));
    }

    private function formData(): array
    {
        return [
            'parentCategories' => Category::whereNull('parent_id')->orderBy('name')->get(),
            'categories' => Category::whereNotNull('parent_id')->orderBy('name')->get(),
            'variantTypes' => VariantType::with(['values' => fn ($q) => $q->where('status', true)->orderBy('value')])->where('status', true)->orderBy('name')->get(),
        ];
    }
}
