<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Resources\ProductResource;
use App\Http\Responses\ApiResponse;
use App\Services\ProductService;

class ProductController extends Controller
{
    private $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function store(StoreProductRequest $request)
    {
        $product = $this->productService->create(
            $request->user(),
            $request->all()
        );

        if (!$product) {
            return ApiResponse::error(
                'You must be a vendor first',
                null,
                403
            );
        }

        return ApiResponse::success(
            new ProductResource($product),
            'Product created successfully',
            201
        );
    }
}
