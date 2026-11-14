<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\VendorProductRequest;
use App\Http\Resources\VendorProductResource;
use App\Http\Responses\ApiResponse;
use App\Services\VendorProductService;
use Illuminate\Http\Request;

/**
 * The products of the logged-in vendor. The store always comes from the vendor himself (never from the request),
 * so a vendor can only work with his own products.
 * Send multipart/form-data when you upload the image (the variants are sent as variants[0][values][0]=3 ...).
 */
class ProductController extends Controller
{
    public function __construct(private VendorProductService $products)
    {
    }

    // GET /vendor/products?q=&per_page=
    public function index(Request $request)
    {
        $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        return ApiResponse::paginated(
            $this->products->paginate($request->user()->store, $request->query('q'), $request->integer('per_page', 15)),
            VendorProductResource::class,
            'Products fetched successfully'
        );
    }

    // POST /vendor/products
    public function store(VendorProductRequest $request)
    {
        $product = $this->products->create($request->user()->store, $request->validated(), $request->file('image'));

        return ApiResponse::success(new VendorProductResource($product), 'Product created successfully', 201);
    }

    // GET /vendor/products/{product}
    public function show(Request $request, string $product)
    {
        return ApiResponse::success(
            new VendorProductResource($this->products->find($request->user()->store, $product)),
            'Product fetched successfully'
        );
    }

    // PUT /vendor/products/{product}  (the whole product again, like the create)
    public function update(VendorProductRequest $request, string $product)
    {
        $updated = $this->products->update($request->user()->store, $product, $request->validated(), $request->file('image'));

        return ApiResponse::success(new VendorProductResource($updated), 'Product updated successfully');
    }

    // PATCH /vendor/products/{product}/status : show / hide the product
    public function toggleStatus(Request $request, string $product)
    {
        return ApiResponse::success(
            new VendorProductResource($this->products->toggleStatus($request->user()->store, $product)),
            'Product status updated successfully'
        );
    }

    // DELETE /vendor/products/{product}
    public function destroy(Request $request, string $product)
    {
        if (! $this->products->delete($request->user()->store, $product)) {
            return ApiResponse::error('This product cannot be deleted. Hide it instead.', null, 409);
        }

        return ApiResponse::success(null, 'Product deleted successfully');
    }
}
