<?php

namespace App\Http\Controllers;

use App\Http\Requests\StripePaymentSheetRequest;
use Symfony\Component\HttpFoundation\Response;

/**
 * @tags Stripe
 */
class StripeController extends BaseController
{
    /**
     * Get Payment Sheet Params
     *
     * @response array{
     *      success: bool,
     *      message: string,
     *      data: array{
     *          payment_intent: string | null,
     *          ephemeral_key: string | null,
     *          customer: string,
     *          publishable_key: string,
     *      }
     * }
     */
    public function payment_sheet(StripePaymentSheetRequest $request)
    {
        try {
            $amount = $request->input('amount') * 100;

            $stripe = new \Stripe\StripeClient([
                'api_key' => config('app.stripe.secret'),
                'stripe_version' => '2024-11-20.acacia',
            ]);

            $customer = $stripe->customers->create();
            $ephemeral_key = $stripe->ephemeralKeys->create([
                'customer' => $customer->id,
            ], [
                'stripe_version' => '2024-11-20.acacia',
            ]);

            $payment_intent = $stripe->paymentIntents->create([
                'amount' => $amount,
                'currency' => 'myr',
                'customer' => $customer->id,
            ]);

            $response = [
                'payment_intent' => $payment_intent->client_secret,
                'ephemeral_key' => $ephemeral_key->secret,
                'customer' => $customer->id,
                'publishable_key' => config('app.stripe.publishable'),
            ];

            return response()->apiSuccess('Payment sheet params fetched successfully', $response);

        } catch (\Exception $e) {
            abort(Response::HTTP_INTERNAL_SERVER_ERROR, 'Failed to fetch payment sheet params');
        }
    }
}
