<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AvatarRequest;
use App\Http\Requests\Api\PasswordRequest;
use App\Http\Requests\Api\ProfileUpdateRequest;
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
            'user' => new UserResource($this->profileService->getProfile($request->user())),
        ], 'Profile fetched successfully');
    }

    public function update(ProfileUpdateRequest $request)
    {
        $user = $this->profileService->updateProfile($request->user(), $request->validated());

        return ApiResponse::success(['user' => new UserResource($user)], 'Profile updated successfully');
    }

    // POST /profile/avatar  (multipart/form-data, field: avatar)
    public function avatar(AvatarRequest $request)
    {
        $user = $this->profileService->updateAvatar($request->user(), $request->file('avatar'));

        return ApiResponse::success(['user' => new UserResource($user)], 'Avatar updated successfully');
    }

    // PUT /profile/password
    public function password(PasswordRequest $request)
    {
        $this->profileService->changePassword($request->user(), $request->validated('password'));

        return ApiResponse::success(null, 'Password changed successfully');
    }
}
