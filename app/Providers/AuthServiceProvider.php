<?php

namespace App\Providers;

use App\Models\BarterInvoice;
use App\Models\BarterRemark;
use App\Models\BarterReview;
use App\Models\BarterService;
use App\Models\BarterTransaction;
use App\Models\User;
use App\Models\UserReport;
use App\Policies\BarterInvoicePolicy;
use App\Policies\BarterRemarkPolicy;
use App\Policies\BarterReviewPolicy;
use App\Policies\BarterServicePolicy;
use App\Policies\BarterTransactionPolicy;
use App\Policies\UserPolicy;
use App\Policies\UserReportPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        User::class => UserPolicy::class,
        UserReport::class => UserReportPolicy::class,
        BarterService::class => BarterServicePolicy::class,
        BarterTransaction::class => BarterTransactionPolicy::class,
        BarterInvoice::class => BarterInvoicePolicy::class,
        BarterRemark::class => BarterRemarkPolicy::class,
        BarterReview::class => BarterReviewPolicy::class,
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
