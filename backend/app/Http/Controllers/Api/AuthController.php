<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * AuthController
 *
 * Handles HTTP authentication requests.
 * Controllers are thin - they only handle HTTP concerns.
 * Business logic is delegated to AuthService.
 */
class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    /**
     * Register a new user.
     *
     * @param RegisterRequest $request Validated registration data
     * @return JsonResponse
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = $this->authService->register($request->validated());

        return response()->json([
            'message' => 'User registered successfully.',
            'user' => new UserResource($user),
        ], 201);
    }

    /**
     * Login user and return authentication token.
     *
     * @param LoginRequest $request Validated login credentials
     * @return JsonResponse
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login($request->validated());

        return response()->json([
            'message' => 'Login successful.',
            'user' => new UserResource($result['user']),
            'token' => $result['token'],
        ]);
    }

    /**
     * Logout user (revoke current token).
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return response()->json([
            'message' => 'Logout successful.',
        ]);
    }

    /**
     * Get authenticated user details.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function me(Request $request): JsonResponse
    {
        $user = $this->authService->getAuthenticatedUser();

        return response()->json([
            'user' => new UserResource($user),
        ]);
    }

    /**
     * Logout from all devices (revoke all tokens).
     *
     * Useful if user's device is compromised or they want to
     * forcefully log out from all sessions.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logoutAllDevices(Request $request): JsonResponse
    {
        $this->authService->logoutAllDevices($request->user());

        return response()->json([
            'message' => 'Successfully logged out from all devices.',
        ]);
    }
}
