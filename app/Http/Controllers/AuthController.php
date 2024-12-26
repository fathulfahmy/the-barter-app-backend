<?php

namespace App\Http\Controllers;

use App\Http\Requests\AuthLoginRequest;
use App\Http\Requests\AuthRegisterRequest;
use App\Models\User;
use GetStream\StreamChat\Client;
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
     *          auth_token: string,
     *          chat_token: string,
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

            $request->authenticate();
            $auth_token = $user->createToken('auth-token')->plainTextToken;

            $chat_client = new Client(config('app.stream_chat.key'), config('app.stream_chat.secret'));
            $chat_token = $chat_client->createToken((string) $user->id);

            $chat_client->upsertUsers([
                [
                    'id' => (string) $user->id,
                    'name' => $user->name,
                    'role' => 'user',
                ],
            ]);

            DB::commit();

            return response()->apiSuccess('Registered successfully', [
                'auth_token' => $auth_token,
                'chat_token' => $chat_token,
                'token_type' => 'Bearer',
                'user' => auth()->user(),
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->apiError('Failed to register', $e->getMessage());
        }
    }

    /**
     * Login
     *
     * @response array{
     *      success: bool,
     *      message: string,
     *      data: array{
     *          auth_token: string,
     *          chat_token: string,
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
            $auth_token = auth()->user()->createToken('auth-token')->plainTextToken;

            $chat_client = new Client(config('app.stream_chat.key'), config('app.stream_chat.secret'));
            $chat_token = $chat_client->createToken((string) auth()->id());

            DB::commit();

            return response()->apiSuccess('Logged in successfully', [
                'auth_token' => $auth_token,
                'chat_token' => $chat_token,
                'token_type' => 'Bearer',
                'user' => auth()->user(),
            ]);

        } catch (ValidationException $e) {
            return response()->apiError('Invalid credentials', $e->getMessage());

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->apiError('Failed to login', $e->getMessage());
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

            return response()->apiError('Failed to logout', $e->getMessage());
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
            $user = auth()->user()->load('barter_services');

            return response()->apiSuccess('Fetched authenticated user successfully', $user);

        } catch (\Exception $e) {
            return response()->apiError('Failed to fetch authenticated user', $e->getMessage());
        }
    }
}
