<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view dashboard stats')->only(['index']);
    }

    public function index(Request $request)
    {
        // card data
        $totalUsers = User::count();
        $totalStores = Store::count();
        $totalTransactions = Transaction::count();
        $totalRevenue = Transaction::where('status', 'paid')->sum('total');

        $range = $request->range ?? 7;

        $start = now()->subDays($range - 1);
        $end = now();

        // chart 
        $rawData = Transaction::selectRaw('DATE(created_at) as date, SUM(total) as revenue, COUNT(*) as orders')
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('date')
            ->pluck('revenue', 'date');

        $orderData = Transaction::selectRaw('DATE(created_at) as date, COUNT(*) as orders')
            ->whereBetween('created_at', [$start, $end])
            ->groupBy('date')
            ->pluck('orders', 'date');

        $period = CarbonPeriod::create($start, $end);

        $chartLabels = [];
        $chartRevenue = [];
        $chartOrders = [];

        foreach ($period as $date) {
            $d = $date->format('Y-m-d');

            $chartLabels[] = $date->format('d M');
            $chartRevenue[] = $rawData[$d] ?? 0;
            $chartOrders[] = $orderData[$d] ?? 0;
        }

        $todayRevenue = Transaction::where('status', 'paid')
            ->whereDate('created_at', today())
            ->sum('total');

        $yesterdayRevenue = Transaction::where('status', 'paid')
            ->whereDate('created_at', today()->subDay())
            ->sum('total');

        if ($yesterdayRevenue > 0) {
            $revenueGrowth = (($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100;
        } else {
            $revenueGrowth = $todayRevenue > 0 ? 100 : 0;
        }

        // top store
        $topStores = Transaction::selectRaw('store_id, SUM(total) as revenue')
            ->where('status', 'paid')
            ->groupBy('store_id')
            ->with('store')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();

        // transaksi terakkhir
        $latestTransactions = Transaction::with('store')
            ->latest()
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'totalStores',
            'totalTransactions',
            'totalRevenue',
            'chartLabels',
            'chartRevenue',
            'chartOrders',
            'range',
            'topStores',
            'latestTransactions',
            'revenueGrowth',
            'todayRevenue',
            'yesterdayRevenue'
        ));
    }
}
