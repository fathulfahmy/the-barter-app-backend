<?php

namespace App\Http\Controllers;

use App\Http\Requests\BarterInvoiceStoreRequest;
use App\Http\Requests\BarterInvoiceUpdateRequest;
use App\Models\BarterInvoice;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class BarterInvoiceController extends BaseController
{
    /**
     * Display a listing of the barter invoice.
     */
    public function index(): JsonResponse
    {
        try {
            $barter_invoices = BarterInvoice::with('barter_transaction.barter_service', 'barter_transaction.barter_acquirer', 'barter_services')
                ->where('barter_acquirer_id', auth()->id())
                ->paginate(config('app.default.pagination'));

            return response()->json([
                'success' => true,
                'message' => 'Invoices fetched successfully',
                'data' => $barter_invoices,
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            DB::rollBack();

            abort(Response::HTTP_INTERNAL_SERVER_ERROR, 'Failed to fetch invoices');
        }
    }

    /**
     * Display the specified barter invoice.
     */
    public function show(string $barter_invoice_id): JsonResponse
    {
        try {
            $barter_invoice = BarterInvoice::with('barter_transaction.barter_service', 'barter_transaction.barter_acquirer', 'barter_transaction.barter_provider', 'barter_services')
                ->findOrFail($barter_invoice_id);

            return response()->json([
                'success' => true,
                'message' => 'Invoice detail fetched successfully',
                'data' => $barter_invoice,
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            DB::rollBack();

            abort(Response::HTTP_INTERNAL_SERVER_ERROR, 'Failed to fetch invoice detail');
        }
    }

    /**
     * Store a newly created barter invoice in storage.
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

            return response()->json([
                'success' => true,
                'message' => 'Invoice created successfully',
                'data' => $barter_invoice,
            ], Response::HTTP_CREATED);

        } catch (\Exception $e) {
            DB::rollBack();

            abort(Response::HTTP_INTERNAL_SERVER_ERROR, 'Failed to create invoice');
        }
    }

    /**
     * Update the specified barter invoice in storage.
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

            return response()->json([
                'success' => true,
                'message' => 'Invoice updated successfully',
                'data' => $barter_invoice,
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            DB::rollBack();

            abort(Response::HTTP_INTERNAL_SERVER_ERROR, 'Failed to update invoice');
        }
    }

    /**
     * Remove the specified barter invoice from storage.
     */
    public function destroy(string $barter_invoice_id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $barter_invoice = BarterInvoice::findOrFail($barter_invoice_id);

            Gate::authorize('delete', $barter_invoice);

            $barter_invoice->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Invoice deleted successfully',
                'data' => [],
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            DB::rollBack();

            abort(Response::HTTP_INTERNAL_SERVER_ERROR, 'Failed to delete invoice');
        }
    }
}
