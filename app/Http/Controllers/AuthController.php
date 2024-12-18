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

/**
 * @tags Auth
 */
class AuthController extends BaseController
{
    /**
     * Register
     *
     * @response array{
     *      success: bool,
     *      message: string,
     *      data: array{
     *          token: string,
     *          token_type: 'Bearer',
     *          user: User,
     *      }
     * }
     */
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

            $response = [
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => auth()->user()->load('barter_services'),
            ];

            return response()->apiSuccess('Registered successfully', $response, Response::HTTP_CREATED);

        } catch (\Exception $e) {
            DB::rollBack();

            abort(Response::HTTP_INTERNAL_SERVER_ERROR, 'Failed to register');
        }
    }

    /**
     * Login
     *
     * @response array{
     *      success: bool,
     *      message: string,
     *      data: array{
     *          token: string,
     *          token_type: 'Bearer',
     *          user: User,
     *      }
     * }
     */
    public function login(AuthLoginRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $request->authenticate();
            $token = auth()->user()->createToken('auth-token')->plainTextToken;

            DB::commit();

            $response = [
                'token' => $token,
                'token_type' => 'Bearer',
                'user' => auth()->user()->load('barter_services'),
            ];

            return response()->apiSuccess('Logged in successfully', $response);

        } catch (ValidationException $e) {
            abort(Response::HTTP_INTERNAL_SERVER_ERROR, 'Invalid credentials');

        } catch (\Exception $e) {
            DB::rollBack();

            abort(Response::HTTP_INTERNAL_SERVER_ERROR, 'Failed to login');
        }
    }

    /**
     * Logout
     *
     * @response array{
     *      success: bool,
     *      message: string,
     *      data: [],
     * }
     */
    public function logout(): JsonResponse
    {
        try {
            DB::beginTransaction();

            auth()->user()->tokens()->delete();

            DB::commit();

            return response()->apiSuccess('Logged out successfully');

        } catch (\Exception $e) {
            DB::rollBack();

            abort(Response::HTTP_INTERNAL_SERVER_ERROR, 'Failed to logout');
        }
    }

    /**
     * Get Auth User
     *
     * @response array{
     *      success: bool,
     *      message: string,
     *      data: User,
     * }
     */
    public function me(): JsonResponse
    {
        try {
            $response = response()->apiSuccess(
                'Fetched authenticated user successfully',
                auth()->user()->load('barter_services'),
            );

            \Illuminate\Support\Facades\Log::debug($response);

            return $response;

        } catch (\Exception $e) {
            abort(Response::HTTP_INTERNAL_SERVER_ERROR, 'Failed to fetch authenticated user');
        }
    }
}
