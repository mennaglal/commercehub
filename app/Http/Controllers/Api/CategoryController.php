<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Responses\ApiResponse;
use App\Services\CatalogService;

class CategoryController extends Controller
{
    public function __construct(private CatalogService $catalog)
    {
    }

    // GET /categories : the main categories, each with its subcategories
    public function index()
    {
        return ApiResponse::success(
            CategoryResource::collection($this->catalog->categories()),
            'Categories fetched successfully'
        );
    }
}
