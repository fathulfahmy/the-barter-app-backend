<?php

namespace App\Http\Controllers;

use App\ApiResponse;
use App\Http\Requests\BarterServiceStoreRequest;
use App\Http\Requests\BarterServiceUpdateRequest;
use App\Models\BarterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class BarterServiceController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        try {
            $mode = $request->input('mode');

            if (! in_array($mode, ['acquire', 'provide'])) {
                return ApiResponse::error('Invalid service mode', 400);
            }

            $query = BarterService::query()
                ->when($mode === 'acquire', function ($query) {
                    $query->with('barter_provider', 'barter_category')
                        ->whereNot('barter_provider_id', auth()->id())
                        ->where('status', 'enabled')
                        ->inRandomOrder();
                })
                ->when($mode === 'provide', function ($query) {
                    $query->with('barter_category')
                        ->where('barter_provider_id', auth()->id())
                        ->orderBy('title');
                });

            $barter_services = $query->paginate(config('app.default.pagination'));

            return ApiResponse::success(
                'Services fetched successfully',
                200,
                $barter_services,
            );

        } catch (\Exception $e) {
            return ApiResponse::error(
                'Failed to fetch services',
                500,
                [$e->getMessage()],
            );
        }
    }

    public function show($barter_service_id): JsonResponse
    {
        $barter_service = BarterService::with('barter_provider', 'barter_category')->find($barter_service_id);

        if (! isset($barter_service)) {
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
            $barter_service = BarterService::create($validated);

            DB::commit();

            return ApiResponse::success(
                'Service created successfully',
                201,
                $barter_service
            );

        } catch (\Exception $e) {
            DB::rollBack();

            return ApiResponse::error(
                'Failed to create service',
                500,
                [$e->getMessage()],
            );
        }
    }

    public function update(BarterServiceUpdateRequest $request, $barter_service_id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $barter_service = BarterService::find($barter_service_id);
            if (! isset($barter_service)) {
                throw (new \Exception('Service does not exist'));
            }

            Gate::authorize('update', $barter_service);

            $validated = $request->validated();
            $barter_service->update($validated);

            DB::commit();

            return ApiResponse::success(
                'Service updated successfully',
                200,
                $barter_service
            );

        } catch (\Exception $e) {
            DB::rollBack();

            return ApiResponse::error(
                'Failed to update service',
                500,
                [$e->getMessage()],
            );
        }
    }

    public function destroy($barter_service_id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $barter_service = BarterService::find($barter_service_id);
            if (! isset($barter_service)) {
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
