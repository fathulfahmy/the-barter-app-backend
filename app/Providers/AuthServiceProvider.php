<?php

namespace App\Providers;

use App\Models\BarterInvoice;
use App\Models\BarterReview;
use App\Models\BarterService;
use App\Models\BarterTransaction;
use App\Models\User;
use App\Policies\BarterInvoicePolicy;
use App\Policies\BarterReviewPolicy;
use App\Policies\BarterServicePolicy;
use App\Policies\BarterTransactionPolicy;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        BarterService::class => BarterServicePolicy::class,
        BarterTransaction::class => BarterTransactionPolicy::class,
        BarterInvoice::class => BarterInvoicePolicy::class,
        BarterReview::class => BarterReviewPolicy::class,
        User::class => UserPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
