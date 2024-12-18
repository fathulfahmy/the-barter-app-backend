<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthLoginRequest;
use App\Http\Requests\AuthRegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

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

            $request->authenticate();
            $token = $user->createToken('auth-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Registered successfully',
                'data' => [
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'user' => auth()->user()->load('barter_services'),
                ],
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            DB::rollBack();

            abort(Response::HTTP_INTERNAL_SERVER_ERROR, 'Failed to register');
        }
    }

    public function login(AuthLoginRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $request->authenticate();
            $token = auth()->user()->createToken('auth-token')->plainTextToken;

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Logged in successfully',
                'data' => [
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'user' => auth()->user()->load('barter_services'),
                ],
            ], Response::HTTP_OK);

        } catch (ValidationException $e) {
            abort(Response::HTTP_INTERNAL_SERVER_ERROR, 'Invalid credentials');

        } catch (\Exception $e) {
            DB::rollBack();

            abort(Response::HTTP_INTERNAL_SERVER_ERROR, 'Failed to login');
        }
    }

    public function logout(): JsonResponse
    {
        try {
            DB::beginTransaction();

            auth()->user()->tokens()->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully',
                'data' => [],
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            DB::rollBack();

            abort(Response::HTTP_INTERNAL_SERVER_ERROR, 'Failed to logout');
        }
    }

    public function me(): JsonResponse
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'Fetched authenticated user successfully',
                'data' => auth()->user()->load('barter_services'),
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            abort(Response::HTTP_INTERNAL_SERVER_ERROR, 'Failed to fetch authenticated user');
        }
    }
}
