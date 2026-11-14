<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BecomeVendorRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\VendorStoreResource;
use App\Http\Responses\ApiResponse;
use App\Services\VendorService;

class VendorController extends Controller
{
    private $vendorService;

    public function __construct(VendorService $vendorService)
    {
        $this->vendorService = $vendorService;
    }

    // POST /become-vendor : the store is created as "pending" until the admin approves it.
    public function becomeVendor(BecomeVendorRequest $request)
    {
        $data = $this->vendorService->becomeVendor($request->user(), $request->validated());

        if (!$data) {
            return ApiResponse::error('User is already a vendor', null, 409);
        }

        return ApiResponse::success([
            'user' => new UserResource($data['user']),
            'store' => new VendorStoreResource($data['store']),
        ], 'Your store was created and is waiting for approval', 201);
    }
}
