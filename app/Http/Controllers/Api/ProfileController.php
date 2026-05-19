<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Http\Responses\ApiResponse;
use App\Services\ProfileService;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    private $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    public function show(Request $request)
    {
        return ApiResponse::success([
            'user' => new UserResource(
                $this->profileService->getProfile($request->user())
            )
        ], 'Profile fetched successfully');
    }

    public function update(Request $request)
    {
        $user = $this->profileService->updateProfile(
            $request->user(),
            $request->all()
        );

        return ApiResponse::success([
            'user' => new UserResource($user)
        ], 'Profile updated successfully');
    }
}
