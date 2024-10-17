<?php

namespace App\Http\Controllers;

use App\ApiResponse;
use App\Http\Requests\AuthLoginRequest;
use App\Http\Requests\AuthRegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(AuthRegisterRequest $request): JsonResponse
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            return ApiResponse::success(
                [
                    'user' => $user
                ],
                'Registration success',
                201
            );

        } catch (\Exception $e) {
            return ApiResponse::error('Registration failed', 500);
        }
    }

    public function login(AuthLoginRequest $request): JsonResponse
    {
        try {
            $request->authenticate();
            $token = auth()->user()->createToken('auth-token')->plainTextToken;

            return ApiResponse::success(
                [
                    'token' => $token,
                    'token_type' => 'Bearer'
                ],
                'Login success'
            );
        } catch (ValidationException $e) {
            return ApiResponse::error('Invalid credentials', 401, $e->errors());
        } catch (\Exception $e) {
            return ApiResponse::error('Login failed', 500);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            auth()->user()->tokens()->delete();
            return ApiResponse::success([], 'Logout success');
        } catch (\Exception $e) {
            return ApiResponse::error('Logout failed', 500);
        }
    }
}
