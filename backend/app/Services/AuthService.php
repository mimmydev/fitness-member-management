<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * AuthService
 *
 * Handles all authentication business logic.
 * Controllers delegate to this service for auth operations.
 * Follows Single Responsibility Principle - only handles auth.
 */
class AuthService
{
    /**
     * Register a new user.
     *
     * Creates a User account with authentication credentials.
     * Does NOT create MemberProfile - that's handled by MemberService.
     *
     * @param array $data Validated registration data
     * @return User The newly created user
     */
    public function register(array $data): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        return $user;
    }

    /**
     * Authenticate user and create Sanctum token.
     *
     * @param array $credentials Login credentials (email, password)
     * @return array ['user' => User, 'token' => string]
     * @throws ValidationException If credentials are invalid
     */
    public function login(array $credentials): array
    {
        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $user = Auth::user();

        // Create Sanctum token for SPA authentication
        // Token name helps identify where the token was created
        $token = $user->createToken('spa-auth-token')->plainTextToken;

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

    /**
     * Logout user by revoking current access token.
     *
     * @param User $user The authenticated user
     * @return void
     */
    public function logout(User $user): void
    {
        // Revoke the current access token
        // Sanctum stores token in currentAccessToken() method
        $user->currentAccessToken()->delete();
    }

    /**
     * Logout from all devices by revoking all tokens.
     *
     * @param User $user The authenticated user
     * @return void
     */
    public function logoutAllDevices(User $user): void
    {
        // Revoke all tokens for this user
        $user->tokens()->delete();
    }

    /**
     * Get the authenticated user with relationships.
     *
     * @return User
     * @throws AuthenticationException If not authenticated
     */
    public function getAuthenticatedUser(): User
    {
        $user = Auth::user();

        if (!$user) {
            throw new AuthenticationException('Unauthenticated.');
        }

        // Eager load member profile to avoid N+1 queries
        return $user->load('memberProfile');
    }
}
