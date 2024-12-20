<?php

namespace App\Http\Controllers;

use App\Http\Requests\BarterInvoiceStoreRequest;
use App\Http\Requests\BarterInvoiceUpdateRequest;
use App\Models\BarterInvoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

/**
 * @tags Invoice
 */
class BarterInvoiceController extends BaseController
{
    /**
     * Get Invoices
     *
     * @response array{
     *      success: bool,
     *      message: string,
     *      data: Illuminate\Pagination\LengthAwarePaginator<BarterInvoice>,
     * }
     */
    public function index(): JsonResponse
    {
        try {
            $barter_invoices = BarterInvoice::with([
                'barter_transaction.barter_service',
                'barter_transaction.barter_acquirer',
                'barter_transaction.barter_provider',
            ])
                ->where('barter_acquirer_id', auth()->id())
                ->paginate(config('app.default.pagination'));

            return response()->apiSuccess('Invoices fetched successfully', $barter_invoices);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->apiError('Failed to fetch invoices', $e->getMessage());
        }
    }

    /**
     * Get Invoice
     *
     * @response array{
     *      success: bool,
     *      message: string,
     *      data: BarterInvoice,
     * }
     */
    public function show(string $barter_invoice_id): JsonResponse
    {
        try {
            $barter_invoice = BarterInvoice::with([
                'barter_transaction.barter_service',
                'barter_transaction.barter_acquirer',
                'barter_transaction.barter_provider',
            ])
                ->findOrFail($barter_invoice_id);

            return response()->apiSuccess('Invoice detail fetched successfully', $barter_invoice);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->apiError('Failed to fetch invoice detail', $e->getMessage());
        }
    }

    /**
     * Create Invoice
     *
     * @response array{
     *      success: bool,
     *      message: string,
     *      data: BarterInvoice,
     * }
     */
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

            return response()->apiSuccess('Invoice created successfully', $barter_invoice, Response::HTTP_CREATED);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->apiError('Failed to create invoice', $e->getMessage());
        }
    }

    /**
     * Update Invoice
     *
     * @response array{
     *      success: bool,
     *      message: string,
     *      data: BarterInvoice,
     * }
     */
    public function update(BarterInvoiceUpdateRequest $request, string $barter_invoice_id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $barter_invoice = BarterInvoice::findOrFail($barter_invoice_id);

            Gate::authorize('update', $barter_invoice);

            $validated = $request->validated();
            $barter_invoice->update($validated);

            if (! empty($validated['barter_service_ids'])) {
                $barter_invoice->barter_services()->sync($validated['barter_service_ids']);
            }

            DB::commit();

            $barter_invoice->refresh();

            return response()->apiSuccess('Invoice updated successfully', $barter_invoice);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->apiError('Failed to update invoice', $e->getMessage());
        }
    }

    /**
     * Delete Invoice
     *
     * @response array{
     *      success: bool,
     *      message: string,
     *      data: [],
     * }
     */
    public function destroy(string $barter_invoice_id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $barter_invoice = BarterInvoice::findOrFail($barter_invoice_id);

            Gate::authorize('delete', $barter_invoice);

            $barter_invoice->delete();

            DB::commit();

            return response()->apiSuccess('Invoice deleted successfully');

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->apiError('Failed to delete invoice', $e->getMessage());
        }
    }
}
