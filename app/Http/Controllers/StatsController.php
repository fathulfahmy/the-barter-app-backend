<?php

namespace App\Http\Controllers;

use App\Models\BarterService;
use App\Models\BarterTransaction;

class StatsController extends Controller
{
    /**
     * Get Monthly Transactions Group By Status
     *
     * @response array{
     *      success: bool,
     *      message: string,
     *      data: array,
     * }
     */
    public function barter_transactions_monthly_group_by_status()
    {
        try {
            $start_date = now()->startOfMonth()->startofday();
            $end_date = now()->endOfDay();

            $barter_transactions = BarterTransaction::query()
                ->where(function ($query) {
                    $query->where('barter_provider_id', auth()->id())
                        ->orWhere('barter_acquirer_id', auth()->id());
                })
                ->whereBetween('updated_at', [$start_date, $end_date])
                ->get();

            $dates = collect(range($start_date->day, $end_date->day))
                ->map(function ($day) use ($start_date) {

                    $date = $start_date->copy()->addDays($day - 1)->endOfDay();

                    return [
                        'date' => $date,
                        'label' => $date->format('j M Y'),
                    ];
                });

            $stats = [
                $dates->map(function ($date) use ($barter_transactions) {
                    $count = $barter_transactions
                        ->where('updated_at', '<=', $date['date'])
                        ->whereIn('status', ['accepted', 'awaiting_completed'])
                        ->count();

                    return [
                        'value' => $count,
                        'label' => $date['label'],
                    ];
                })->values()->toArray(),

                $dates->map(function ($date) use ($barter_transactions) {
                    $count = $barter_transactions
                        ->where('updated_at', '<=', $date['date'])
                        ->whereIn('status', ['completed'])
                        ->count();

                    return [
                        'value' => $count,
                        'label' => $date['label'],
                    ];
                })->values()->toArray(),
            ];

            return response()->apiSuccess('Monthly transactions stats fetched successfully', $stats);

        } catch (\Exception $e) {
            return response()->apiError('Failed to fetch monthly transactions stats', $e->getMessage());
        }
    }

    /**
     * Get Monthly Trending Services
     *
     * @response array{
     *      success: bool,
     *      message: string,
     *      data: array,
     * }
     */
    public function barter_services_monthly_trending()
    {
        $start_date = now()->startOfMonth()->startofday();
        $end_date = now()->endOfDay();

        try {
            $stats = BarterService::query()
                ->where('barter_provider_id', auth()->id())
                ->withCount([
                    'barter_transactions as barter_transactions_count' => function ($query) {
                        $query->where('status', 'completed');
                    },
                    'barter_invoices as barter_invoices_count' => function ($query) use ($start_date, $end_date) {
                        $query
                            ->whereBetween('created_at', [$start_date, $end_date])
                            ->whereHas('barter_transaction', function ($query) {
                                $query->where('status', 'completed');
                            });
                    },
                ])
                ->take(5)
                ->get()
                ->map(function ($barter_service) {
                    $value = $barter_service->barter_transactions_count + $barter_service->barter_invoices_count;

                    if ($value <= 0) {
                        return null;
                    }

                    return [
                        'value' => $value,
                        'label' => $barter_service->title,
                    ];
                })
                ->filter()
                ->sortByDesc('value')
                ->values()
                ->toArray();

            if (empty($stats)) {
                $stats = [
                    [
                        'value' => 0,
                        'label' => '',
                    ],
                ];
            }

            return response()->apiSuccess('Monthly trending services stats fetched successfully', $stats);
        } catch (\Exception $e) {
            return response()->apiError('Failed to fetch monthly trending services stats', $e->getMessage());
        }
    }

    /**
     * Get Tab Bar Badges
     *
     * @response array{
     *      success: bool,
     *      message: string,
     *      data: array,
     * }
     */
    public function tab_bar_badges()
    {
        try {
            $pending_barter_transactions = 0;

            $pending_barter_transactions = BarterTransaction::query()
                ->where('barter_provider_id', auth()->id())
                ->where('status', 'pending')
                ->count();

            $stats = [
                'pending_barter_transactions' => $pending_barter_transactions,
            ];

            return response()->apiSuccess('Fetched tab bar badges successfully', $stats);

        } catch (\Exception $e) {
            return response()->apiError('Failed to fetch tab bar badges', $e->getMessage());
        }
    }
}
