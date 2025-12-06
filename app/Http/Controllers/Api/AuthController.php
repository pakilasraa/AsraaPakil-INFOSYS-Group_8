<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user
     * POST /api/auth/register
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            // expect: password_confirmation field from frontend
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'username' => $validated['email'], // temporary rule: username = email
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'last_login' => null,
            'last_password_change' => null,
        ]);



        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'user' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ],
            'token' => $token,
        ], 201);
    }

    /**
     * Login user
     * POST /api/auth/login
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        // optional: delete previous tokens (para 1 device only)
        // $user->tokens()->delete();

        $token = $user->createToken('api_token')->plainTextToken;

        return response()->json([
            'user' => [
                'id'    => $user->id,
                'name'  => $user->name,
                'email' => $user->email,
            ],
            'token' => $token,
        ]);
    }

    /**
     * Get current authenticated user
     * GET /api/auth/me
     */
    public function me(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'id'    => $user->id,
            'name'  => $user->name,
            'email' => $user->email,
        ]);
    }

    /**
     * Logout (revoke current token)
     * POST /api/auth/logout
     */
    public function logout(Request $request)
    {
        $user = $request->user();

        // Delete only current access token
        $user->currentAccessToken()->delete();

        // kung gusto mo delete lahat:
        // $user->tokens()->delete();

        return response()->json([
            'message' => 'Logged out successfully',
        ]);
    }
}
