<?php

namespace App\Http\Controllers;

use App\ApiResponse;
use App\Http\Requests\UserUpdateRequest;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class UserController extends BaseController
{
    public function update(UserUpdateRequest $request, $user_id)
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
                    ->usingFileName($file->getClientOriginalName())
                    ->toMediaCollection('user_avatar');
            }

            DB::commit();

            $user->refresh();

            return ApiResponse::success(
                'User updated successfully',
                200,
                $user
            );

        } catch (\Exception $e) {
            DB::rollBack();

            return ApiResponse::error(
                'Failed to update user',
                500,
                [$e->getMessage()],
            );
        }
    }
}
