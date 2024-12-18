<?php

namespace App\Http\Controllers;

use App\Http\Requests\BarterServiceStoreRequest;
use App\Http\Requests\BarterServiceUpdateRequest;
use App\Models\BarterService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

/**
 * @tags Service
 */
class BarterServiceController extends BaseController
{
    /**
     * Get Services
     *
     * @response array{
     *      success: bool,
     *      message: string,
     *      data: Illuminate\Pagination\LengthAwarePaginator<BarterService>,
     * }
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $mode = $request->input('mode');

            if (! in_array($mode, ['acquire', 'provide'])) {
                abort(Response::HTTP_BAD_REQUEST, 'Invalid service mode');
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

            return response()->apiSuccess('Services fetched successfully', $barter_services);

        } catch (\Exception $e) {
            abort(Response::HTTP_INTERNAL_SERVER_ERROR, 'Failed to fetch services');
        }
    }

    /**
     * Get Service
     *
     * @response array{
     *      success: bool,
     *      message: string,
     *      data: BarterService,
     * }
     */
    public function show(string $barter_service_id): JsonResponse
    {
        try {
            $barter_service = BarterService::with('barter_provider', 'barter_category')
                ->findOrFail($barter_service_id);

            return response()->apiSuccess('Service detail fetched successfully', $barter_service);

        } catch (\Exception $e) {
            abort(Response::HTTP_INTERNAL_SERVER_ERROR, 'Failed to fetch services');
        }
    }

    /**
     * Create Service
     *
     * @response array{
     *      success: bool,
     *      message: string,
     *      data: BarterService,
     * }
     */
    public function store(BarterServiceStoreRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $validated = $request->validated();
            $validated['barter_provider_id'] = auth()->id();
            $barter_service = BarterService::create(Arr::except($validated, ['images']));

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $barter_service
                        ->addMedia($file)
                        ->toMediaCollection('barter_service_images');
                }
            }
            DB::commit();

            return response()->apiSuccess('Service created successfully', $barter_service);

        } catch (\Exception $e) {
            DB::rollBack();

            abort(Response::HTTP_INTERNAL_SERVER_ERROR, 'Failed to create service');
        }
    }

    /**
     * Update Service
     *
     * @response array{
     *      success: bool,
     *      message: string,
     *      data: BarterService,
     * }
     */
    public function update(BarterServiceUpdateRequest $request, string $barter_service_id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $barter_service = BarterService::findOrFail($barter_service_id);

            Gate::authorize('update', $barter_service);

            $validated = $request->validated();

            $barter_service->update(Arr::except($validated, ['images']));

            $barter_service->clearMediaCollection('barter_service_images');

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $barter_service
                        ->addMedia($file)
                        ->toMediaCollection('barter_service_images');
                }
            }

            DB::commit();

            $barter_service->refresh();

            return response()->apiSuccess('Service updated successfully', $barter_service);

        } catch (\Exception $e) {
            DB::rollBack();

            abort(Response::HTTP_INTERNAL_SERVER_ERROR, 'Failed to update service');
        }
    }

    /**
     * Delete Service
     *
     * @response array{
     *      success: bool,
     *      message: string,
     *      data: [],
     * }
     */
    public function destroy(string $barter_service_id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $barter_service = BarterService::findOrFail($barter_service_id);

            Gate::authorize('delete', $barter_service);

            $barter_service->delete();

            DB::commit();

            return response()->apiSuccess('Service deleted successfully');

        } catch (\Exception $e) {
            DB::rollBack();

            abort(Response::HTTP_INTERNAL_SERVER_ERROR, 'Failed to delete service');
        }
    }
}
