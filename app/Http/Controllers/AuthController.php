<?php

namespace App\Http\Controllers;

use App\ApiResponse;
use App\Http\Requests\AuthLoginRequest;
use App\Http\Requests\AuthRegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends BaseController
{
    public function register(AuthRegisterRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            DB::commit();

            return ApiResponse::success('Registered successfully', 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::error(
                'Failed to register',
                500,
                [$e->getMessage()]
            );
        }
    }

    public function login(AuthLoginRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $request->authenticate();
            $token = auth()->user()->createToken('auth-token')->plainTextToken;

            DB::commit();

            return ApiResponse::success('Logged in successfully', 200, [
                'token' => $token,
                'token_type' => 'Bearer'
            ]);

        } catch (ValidationException $e) {
            return ApiResponse::error(
                'Invalid credentials',
                401,
                $e->errors()
            );

        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::error(
                'Failed to login',
                500,
                [$e->getMessage()]
            );
        }
    }

    public function logout(): JsonResponse
    {
        try {
            DB::beginTransaction();

            auth()->user()->tokens()->delete();

            DB::commit();

            return ApiResponse::success('Logged out successfully', 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::error(
                'Failed to logout',
                500,
                [$e->getMessage()]
            );
        }
    }
}
