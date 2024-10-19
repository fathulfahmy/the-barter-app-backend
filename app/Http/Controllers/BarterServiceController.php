<?php

namespace App\Http\Controllers;
use App\ApiResponse;
use App\Http\Requests\BarterServiceStoreRequest;
use App\Http\Requests\BarterServiceUpdateRequest;
use App\Models\BarterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class BarterServiceController extends BaseController
{
    public function acquire(): JsonResponse
    {
        $barter_services = BarterService::with('barter_provider', 'barter_category')
            ->paginate(10);

        return ApiResponse::success(
            'Services fetched successfully',
            200,
            $barter_services,
        );
    }

    public function provide(): JsonResponse
    {
        $barter_services = BarterService::with('barter_category')
            ->where('barter_provider_id', auth()->id())
            ->paginate(10);

        return ApiResponse::success(
            'Services fetched successfully',
            200,
            $barter_services,
        );
    }

    public function show($id): JsonResponse
    {
        $barter_service = BarterService::with('barter_provider', 'barter_category')->find($id);

        if (!isset($barter_service)) {
            throw (new \Exception('Service does not exist'));
        }


        return ApiResponse::success(
            'Service detail fetched successfully',
            200,
            $barter_service,
        );
    }

    public function store(BarterServiceStoreRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $validated = $request->validated();
            $validated['barter_provider_id'] = auth()->id();
            BarterService::create($validated);

            DB::commit();

            return ApiResponse::success('Service created successfully', 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::error(
                'Failed to create service',
                500,
                [$e->getMessage()],
            );
        }
    }

    public function update(BarterServiceUpdateRequest $request, $id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $barter_service = BarterService::find($id);
            if (!isset($barter_service)) {
                throw (new \Exception('Service does not exist'));
            }

            Gate::authorize('update', $barter_service);

            $validated = $request->validated();
            $barter_service->update($validated);

            DB::commit();

            return ApiResponse::success('Service updated successfully', 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::error(
                'Failed to update service',
                500,
                [$e->getMessage()],
            );
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $barter_service = BarterService::find($id);
            if (!isset($barter_service)) {
                throw (new \Exception('Service does not exist'));
            }

            Gate::authorize('delete', $barter_service);

            $barter_service->delete();

            DB::commit();

            return ApiResponse::success('Service deleted successfully', 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::error(
                'Failed to delete service',
                500,
                [$e->getMessage()],
            );
        }
    }
}
