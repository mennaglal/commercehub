<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdateStoreRequest;
use App\Http\Resources\VendorStoreResource;
use App\Http\Responses\ApiResponse;
use App\Services\VendorService;
use Illuminate\Http\Request;

// The store of the logged-in vendor (also available while the store is still "pending").
class StoreController extends Controller
{
    public function __construct(private VendorService $vendors)
    {
    }

    // GET /vendor/store
    public function show(Request $request)
    {
        return ApiResponse::success(new VendorStoreResource($request->user()->store), 'Store fetched successfully');
    }

    // PUT /vendor/store  (multipart/form-data when a logo or a banner is uploaded)
    public function update(UpdateStoreRequest $request)
    {
        $store = $this->vendors->updateStore($request->user()->store, $request->validated());

        return ApiResponse::success(new VendorStoreResource($store), 'Store updated successfully');
    }
}
