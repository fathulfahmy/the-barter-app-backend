<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class StripeController extends BaseController
{
    /**
     * Store a newly created payment in stripe.
     */
    public function payment_sheet(Request $request)
    {
        Log::debug('processing payment');
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
        Log::debug($amount);
        Log::debug($payment_intent);
        Log::debug($ephemeral_key);
        Log::debug($customer->id);

        return response()->json(
            [
                'success' => true,
                'message' => 'Payment processed successfully',
                'data' => [
                    'payment_intent' => $payment_intent->client_secret,
                    'ephemeral_key' => $ephemeral_key->secret,
                    'customer' => $customer->id,
                    'publishable_key' => config('app.stripe.publishable'),
                ],
            ],
            Response::HTTP_OK
        );
    }
}
