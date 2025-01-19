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
            $start_date = now()->startOfMonth();
            $end_date = now();

            $transactions = BarterTransaction::query()
                ->select(['created_at', 'status'])
                ->where(function ($query) {
                    $authId = auth()->id();
                    $query->where('barter_provider_id', $authId)
                        ->orWhere('barter_acquirer_id', $authId);
                })
                ->whereBetween('created_at', [$start_date, $end_date])
                ->get()
                ->groupBy(function ($item) {
                    return $item->created_at->toDateString();
                })
                ->map(function ($group) {
                    return $group->groupBy('status')->map->count();
                });

            $dates = collect(range($start_date->day, $end_date->day))
                ->map(function ($day) use ($start_date) {
                    $date = $start_date->copy()->addDays($day - 1);

                    return [
                        'date' => $date->format('Y-m-d'),
                        'label' => $date->format('j M Y'),
                    ];
                });

            $stats = [
                $dates->map(function ($date) use ($transactions) {
                    $accepted = $transactions[$date['date']]['accepted'] ?? 0;
                    $awaiting = $transactions[$date['date']]['awaiting_completed'] ?? 0;

                    return [
                        'value' => $accepted + $awaiting,
                        'label' => $date['label'],
                    ];
                })->values()->toArray(),

                $dates->map(function ($date) use ($transactions) {
                    $completed = $transactions[$date['date']]['completed'] ?? 0;

                    return [
                        'value' => $completed,
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
        try {
            $stats = BarterService::query()
                ->select(['id', 'title'])
                ->where('barter_provider_id', auth()->id())
                ->withCount([
                    'barter_transactions as barter_transactions_count',
                    'barter_invoices as barter_invoices_count' => function ($query) {
                        $query->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
                    },
                ])
                ->take(5)
                ->get()
                ->map(function ($barter_service) {
                    return [
                        'value' => $barter_service->barter_transactions_count + $barter_service->barter_invoices_count,
                        'label' => $barter_service->title,
                    ];
                })
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
}
