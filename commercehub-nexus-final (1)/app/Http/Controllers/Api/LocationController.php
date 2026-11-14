<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\AreaResource;
use App\Http\Resources\CityResource;
use App\Http\Resources\CountryResource;
use App\Http\Responses\ApiResponse;
use App\Models\City;
use App\Models\Country;

// Country -> City -> Area. Only the active ones. The area carries the shipping price.
class LocationController extends Controller
{
    // GET /countries
    public function countries()
    {
        $countries = Country::where('is_active', true)->orderBy('name_en')->get();

        return ApiResponse::success(CountryResource::collection($countries), 'Countries fetched successfully');
    }

    // GET /countries/{country}/cities
    public function cities(Country $country)
    {
        abort_unless($country->is_active, 404);

        $cities = $country->cities()->where('is_active', true)->orderBy('name_en')->get();

        return ApiResponse::success(CityResource::collection($cities), 'Cities fetched successfully');
    }

    // GET /cities/{city}/areas
    public function areas(City $city)
    {
        abort_unless($city->is_active, 404);

        $areas = $city->areas()->where('is_active', true)->orderBy('name_en')->get();

        return ApiResponse::success(AreaResource::collection($areas), 'Areas fetched successfully');
    }
}
