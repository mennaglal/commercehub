<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CurrencyResource;
use App\Http\Responses\ApiResponse;
use App\Models\Currency;

class CurrencyController extends Controller
{
    // GET /currencies : the active currencies. All the prices of the API are in the base currency (EGP):
    // the app converts them with rate_to_egp and decimal_places.
    public function index()
    {
        $currencies = Currency::where('is_active', true)->whereNotNull('rate_to_egp')->orderBy('code')->get();

        return ApiResponse::success(CurrencyResource::collection($currencies), 'Currencies fetched successfully');
    }
}
