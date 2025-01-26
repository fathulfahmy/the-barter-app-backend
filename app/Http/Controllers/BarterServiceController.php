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
            $search = $request->input('search');
            $categories = $request->input('categories', []);

            $query = BarterService::query()
                ->when(! empty($search), function ($query) use ($search) {
                    $query->where(function ($query) use ($search) {
                        $query->where('title', 'like', "%{$search}%")
                            ->orWhere('description', 'like', "%{$search}%")
                            ->orWhere('min_price', 'like', "%{$search}%")
                            ->orWhere('max_price', 'like', "%{$search}%")
                            ->orWhere('price_unit', 'like', "%{$search}%")
                            ->orWhereHas('barter_provider', function ($query) use ($search) {
                                $query->where('name', 'like', "%{$search}%");
                            })
                            ->orWhereHas('barter_category', function ($query) use ($search) {
                                $query->where('name', 'like', "%{$search}%");
                            });
                    });
                })
                ->when(! empty($categories), function ($query) use ($categories) {
                    $query->whereIn('barter_category_id', $categories);
                })
                ->when($mode === 'acquire', function ($query) {
                    $query->with(['barter_provider'])
                        ->whereNot('barter_provider_id', auth()->id())
                        ->where('status', 'enabled')
                        ->inRandomOrder();
                })
                ->when($mode === 'provide', function ($query) {
                    $query
                        ->where('barter_provider_id', auth()->id())
                        ->orderBy('title');
                });

            $barter_services = $query->paginate(config('app.default.pagination'));

            return response()->apiSuccess('Services fetched successfully', $barter_services);

        } catch (\Exception $e) {
            return response()->apiError('Failed to fetch services', $e->getMessage());
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
            $barter_service = BarterService::with([
                'barter_provider',
            ])
                ->findOrFail($barter_service_id);

            return response()->apiSuccess('Service detail fetched successfully', $barter_service);

        } catch (\Exception $e) {
            return response()->apiError('Failed to fetch services', $e->getMessage());
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

            return response()->apiSuccess('Service created successfully', $barter_service, Response::HTTP_CREATED);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->apiError('Failed to create service', $e->getMessage());
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

            if ($request->hasFile('images')) {

                $barter_service->clearMediaCollection('barter_service_images');

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

            return response()->apiError('Failed to update service', $e->getMessage());
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

            $ongoing_transactions = $barter_service->barter_transactions()
                ->whereIn('status', ['accepted', 'awaiting_completed'])
                ->exists();

            if ($ongoing_transactions) {
                return response()->apiError('Failed to delete service with ongoing barters');
            }

            Gate::authorize('delete', $barter_service);

            $barter_service->delete();

            DB::commit();

            return response()->apiSuccess('Service deleted successfully');

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->apiError('Failed to delete service', $e->getMessage());
        }
    }
}
