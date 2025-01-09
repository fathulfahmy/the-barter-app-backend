<?php

namespace App\Observers;

use App\Models\BarterReview;
use App\Models\BarterTransaction;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;

class BarterReviewObserver implements ShouldHandleEventsAfterCommit
{
    /**
     * Handle the BarterReview "created" event.
     */
    public function created(BarterReview $barter_review): void
    {
        BarterReview::withoutEvents(function () use ($barter_review) {

            $barter_transaction = BarterTransaction::query()
                ->with('barter_invoice.barter_services')
                ->find($barter_review->barter_transaction_id);

            if (! $barter_transaction) {
                return;
            }

            $is_user_acquirer = $barter_review->author_id == $barter_transaction->barter_acquirer_id;

            if ($is_user_acquirer) {
                $barter_service = $barter_transaction->barter_service;
                if ($barter_service) {
                    $barter_review->update([
                        'barter_service_id' => $barter_service->id,
                    ]);
                }
            } else {
                $barter_services = $barter_transaction->barter_invoice->barter_services ?? [];
                foreach ($barter_services as $i => $barter_service) {
                    if ($i === 0) {
                        $barter_review->update([
                            'barter_service_id' => $barter_service->id,
                        ]);
                    } else {
                        BarterReview::create([
                            'barter_transaction_id' => $barter_review->barter_transaction_id,
                            'barter_service_id' => $barter_service->id,
                            'author_id' => $barter_review->author_id,
                            'rating' => $barter_review->rating,
                            'description' => $barter_review->description,
                        ]);
                    }
                }
            }

        });
    }

    /**
     * Handle the BarterReview "updated" event.
     */
    public function updated(BarterReview $barter_review): void
    {
        BarterReview::withoutEvents(function () use ($barter_review) {

            BarterReview::query()
                ->where('author_id', $barter_review->author_id)
                ->where('barter_transaction_id', $barter_review->barter_transaction_id)
                ->update([
                    'rating' => $barter_review->rating,
                    'description' => $barter_review->description,
                ]);

        });
    }

    /**
     * Handle the BarterReview "deleted" event.
     */
    public function deleted(BarterReview $barterReview): void
    {
        //
    }

    /**
     * Handle the BarterReview "restored" event.
     */
    public function restored(BarterReview $barterReview): void
    {
        //
    }

    /**
     * Handle the BarterReview "force deleted" event.
     */
    public function forceDeleted(BarterReview $barterReview): void
    {
        //
    }
}
