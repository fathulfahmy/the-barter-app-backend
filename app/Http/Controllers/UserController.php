<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class UserController extends BaseController
{
    /**
     * Update the specified user in storage.
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

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => $user,
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            DB::rollBack();

            abort(Response::HTTP_INTERNAL_SERVER_ERROR, 'Failed to update user');
        }
    }
}
