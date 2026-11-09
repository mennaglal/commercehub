<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\StoreResource;
use App\Http\Responses\ApiResponse;
use App\Services\CatalogService;
use Illuminate\Http\Request;

// The PUBLIC stores. The products of a store: GET /products?store_id=ID
class StoreController extends Controller
{
    public function __construct(private CatalogService $catalog)
    {
    }

    // GET /stores?q=&per_page=
    public function index(Request $request)
    {
        $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        return ApiResponse::paginated(
            $this->catalog->stores($request->query('q'), $request->integer('per_page', 15)),
            StoreResource::class,
            'Stores fetched successfully'
        );
    }

    // GET /stores/{slug}
    public function show(string $slug)
    {
        return ApiResponse::success(new StoreResource($this->catalog->storeBySlug($slug)), 'Store fetched successfully');
    }
}
