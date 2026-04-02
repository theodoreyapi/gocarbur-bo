<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Garage;
use App\Models\PaymentTransaction;
use App\Models\Station;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    /** GET /admin/dashboard/overview */
    public function overview(): JsonResponse
    {
        $today  = now()->startOfDay();
        $month  = now()->startOfMonth();
        $prev   = now()->subMonth()->startOfMonth();

        return response()->json([
            'success' => true,
            'data'    => [
                'users' => [
                    'total'       => User::count(),
                    'premium'     => User::where('subscription_type','premium')->count(),
                    'new_today'   => User::where('created_at','>=',$today)->count(),
                    'new_month'   => User::where('created_at','>=',$month)->count(),
                ],
                'stations' => [
                    'total'    => Station::count(),
                    'verified' => Station::where('is_verified',true)->count(),
                    'pro'      => Station::whereIn('subscription_type',['pro','premium'])->count(),
                ],
                'garages' => [
                    'total'    => Garage::count(),
                    'verified' => Garage::where('is_verified',true)->count(),
                    'pro'      => Garage::whereIn('subscription_type',['pro','premium'])->count(),
                ],
                'revenue' => [
                    'this_month' => PaymentTransaction::where('status','success')
                        ->where('paid_at','>=',$month)->sum('amount'),
                    'last_month' => PaymentTransaction::where('status','success')
                        ->whereBetween('paid_at',[$prev, $month])->sum('amount'),
                    'total'      => PaymentTransaction::where('status','success')->sum('amount'),
                ],
                'subscriptions' => [
                    'active' => Subscription::where('status','active')
                        ->where('expires_at','>=',now())->count(),
                ],
            ],
        ]);
    }

    /** GET /admin/dashboard/revenue */
    public function revenue(Request $request): JsonResponse
    {
        $months = (int) $request->input('months', 12);

        $revenue = PaymentTransaction::where('status','success')
            ->where('paid_at','>=', now()->subMonths($months))
            ->selectRaw('
                YEAR(paid_at)  AS year,
                MONTH(paid_at) AS month,
                SUM(amount)    AS total,
                COUNT(*)       AS transactions
            ')
            ->groupByRaw('YEAR(paid_at), MONTH(paid_at)')
            ->orderByRaw('YEAR(paid_at), MONTH(paid_at)')
            ->get();

        return response()->json(['success' => true, 'data' => $revenue]);
    }

    /** GET /admin/dashboard/growth */
    public function growth(): JsonResponse
    {
        $growth = User::selectRaw('DATE(created_at) AS date, COUNT(*) AS new_users')
            ->where('created_at','>=', now()->subDays(30))
            ->groupByRaw('DATE(created_at)')
            ->orderBy('date')
            ->get();

        return response()->json(['success' => true, 'data' => $growth]);
    }

    /** GET /admin/dashboard/activity */
    public function activity(): JsonResponse
    {
        $logs = \App\Models\ActivityLog::with('causer')
            ->latest('occurred_at')
            ->limit(50)
            ->get();

        return response()->json(['success' => true, 'data' => $logs]);
    }
}
