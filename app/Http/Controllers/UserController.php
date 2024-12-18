<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

/**
 * @tags User
 */
class UserController extends BaseController
{
    /**
     * Update User
     *
     * @response array{
     *      success: bool,
     *      message: string,
     *      data: User,
     * }
     */
    public function update(UserUpdateRequest $request, string $user_id)
    {
        try {
            DB::beginTransaction();

            $user = User::findOrFail($user_id);

            Gate::authorize('update', $user);

            $validated = $request->validated();

            $user->update(Arr::except($validated, ['image']));

            $user->clearMediaCollection('user_avatar');

            if ($request->hasFile('avatar')) {
                $file = $request->file('avatar');
                $user
                    ->addMedia($file)
                    ->toMediaCollection('user_avatar');
            }

            DB::commit();

            $user->refresh();

            return response()->apiSuccess('User updated successfully', $user);

        } catch (\Exception $e) {
            DB::rollBack();

            abort(Response::HTTP_INTERNAL_SERVER_ERROR, 'Failed to update user');
        }
    }
}
