<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ProductIndexRequest;
use App\Http\Resources\ProductDetailResource;
use App\Http\Resources\ProductResource;
use App\Http\Responses\ApiResponse;
use App\Services\CatalogService;

// The PUBLIC catalog. (The vendor products are in Api\Vendor\ProductController.)
class ProductController extends Controller
{
    public function __construct(private CatalogService $catalog)
    {
    }

    // GET /products?q=&category_id=&store_id=&min_price=&max_price=&on_sale=&in_stock=&sort=&per_page=
    public function index(ProductIndexRequest $request)
    {
        return ApiResponse::paginated(
            $this->catalog->products($request->validated()),
            ProductResource::class,
            'Products fetched successfully'
        );
    }

    // GET /products/{slug}
    public function show(string $slug)
    {
        return ApiResponse::success(
            new ProductDetailResource($this->catalog->productBySlug($slug)),
            'Product fetched successfully'
        );
    }
}
