<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\BecomeVendorRequest;
use App\Http\Resources\StoreResource;
use App\Http\Responses\ApiResponse;
use App\Services\VendorService;

class VendorController extends Controller
{
    private $vendorService;

    public function __construct(VendorService $vendorService)
    {
        $this->vendorService = $vendorService;
    }

    public function becomeVendor(BecomeVendorRequest $request)
    {

        $data = $this->vendorService->becomeVendor(
            $request->user(),
            $request->all()
        );


        if (!$data) {
            return ApiResponse::error('User is already a vendor', null, 400);
        }

        return ApiResponse::success([
            'user' => $data['user'],
            'store' => new StoreResource($data['store']),
        ], 'You are now a vendor 🎉');
    }
}
