<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Services\VendorOrderService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(private VendorOrderService $orders)
    {
    }

    // GET /vendor/dashboard
    public function index(Request $request)
    {
        return ApiResponse::success($this->orders->dashboard($request->user()->store), 'Dashboard fetched successfully');
    }
}
