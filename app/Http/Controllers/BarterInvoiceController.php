<?php

namespace App\Http\Controllers;

use App\ApiResponse;
use App\Http\Requests\BarterInvoiceStoreRequest;
use App\Http\Requests\BarterInvoiceUpdateRequest;
use App\Models\BarterInvoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class BarterInvoiceController extends BaseController
{
    public function index(): JsonResponse
    {
        $barter_invoices = BarterInvoice::with('barter_transaction.barter_service', 'barter_transaction.barter_acquirer', 'barter_services')
            ->where('barter_acquirer_id', auth()->id())
            ->paginate(config('app.default.pagination'));

        return ApiResponse::success(
            'Invoices fetched successfully',
            200,
            $barter_invoices,
        );
    }

    public function show($barter_invoice_id): JsonResponse
    {
        $barter_invoice = BarterInvoice::with('barter_transaction.barter_service', 'barter_transaction.barter_acquirer', 'barter_transaction.barter_provider', 'barter_services')->find($barter_invoice_id);

        if (! isset($barter_invoice)) {
            throw (new \Exception('Invoice does not exist'));
        }

        return ApiResponse::success(
            'Invoice detail fetched successfully',
            200,
            $barter_invoice,
        );
    }

    public function store(BarterInvoiceStoreRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $validated = $request->validated();
            $validated['barter_acquirer_id'] = auth()->id();
            $barter_invoice = BarterInvoice::create($validated);

            if (! empty($validated['barter_service_ids'])) {
                $barter_invoice->barter_services()->attach($validated['barter_service_ids']);
            }

            DB::commit();

            return ApiResponse::success('Invoice created successfully', 201);

        } catch (\Exception $e) {
            DB::rollBack();

            return ApiResponse::error(
                'Failed to create invoice',
                500,
                [$e->getMessage()],
            );
        }
    }

    public function update(BarterInvoiceUpdateRequest $request, $barter_invoice_id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $barter_invoice = BarterInvoice::find($barter_invoice_id);
            if (! isset($barter_invoice)) {
                throw (new \Exception('Invoice does not exist'));
            }

            Gate::authorize('update', $barter_invoice);

            $validated = $request->validated();
            $barter_invoice->update($validated);

            if (! empty($validated['barter_service_ids'])) {
                $barter_invoice->barter_services()->sync($validated['barter_service_ids']);
            }

            DB::commit();

            return ApiResponse::success('Invoice updated successfully', 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return ApiResponse::error(
                'Failed to update invoice',
                500,
                [$e->getMessage()],
            );
        }
    }

    public function destroy($barter_invoice_id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $barter_invoice = BarterInvoice::find($barter_invoice_id);
            if (! isset($barter_invoice)) {
                throw (new \Exception('Invoice does not exist'));
            }

            Gate::authorize('delete', $barter_invoice);

            $barter_invoice->delete();

            DB::commit();

            return ApiResponse::success('Invoice deleted successfully', 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return ApiResponse::error(
                'Failed to delete invoice',
                500,
                [$e->getMessage()],
            );
        }
    }
}
