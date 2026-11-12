<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Http\Responses\ApiResponse;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(RegisterRequest $request)
    {
        $data = $this->authService->register($request->validated());

        return ApiResponse::success([
            'user' => new UserResource($data['user']),
            'token' => $data['token'],
        ], 'Registered successfully', 201);
    }

    public function login(LoginRequest $request)
    {
        $data = $this->authService->login($request->validated());

        if (!$data) {
            return ApiResponse::error('Invalid credentials', null, 401);
        }

        return ApiResponse::success([
            'user' => new UserResource($data['user']),
            'token' => $data['token'],
        ], 'Login successful');
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return ApiResponse::success(null, 'Logged out successfully');
    }
}
