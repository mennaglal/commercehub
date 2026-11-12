<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use App\Http\Responses\ApiResponse;
use App\Models\VariantType;

class VariantTypeController extends Controller
{
    // GET /vendor/variant-types : the option types (Color, Size...) and their values the vendor can use in his products.
    // The ids of the values are what the product form sends in variants[i][values][].
    public function index()
    {
        $types = VariantType::where('status', true)
            ->with(['values' => fn ($values) => $values->where('status', true)->orderBy('id')])
            ->orderBy('id')
            ->get();

        return ApiResponse::success($types->map(fn ($type) => [
            'id' => $type->id,
            'name' => $type->name,
            'input_type' => $type->input_type,   // text | color
            'values' => $type->values->map(fn ($value) => [
                'id' => $value->id,
                'value' => $value->value,
                'color_code' => $value->color_code,
            ])->values(),
        ])->values(), 'Variant types fetched successfully');
    }
}
