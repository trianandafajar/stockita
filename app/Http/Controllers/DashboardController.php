<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view dashboard stats')->only(['index']);
    }

    public function index(Request $request)
    {
        $storeId = Auth::user()->store->id;
        $warehouseIds = Auth::user()->store->warehouse->pluck('id');

        $today = Carbon::today();
        $yesterday = Carbon::yesterday();

        $transactions = Transaction::where('store_id', $storeId)->where('is_active', true)
            ->whereBetween('created_at', [
                $today->copy()->subDays(30)->startOfDay(),
                $today->copy()->endOfDay(),
            ])
            ->get()
            ->groupBy(fn($t) => Carbon::parse($t->created_at)->format('Y-m-d'));

        $todayData = $transactions[$today->format('Y-m-d')] ?? collect();
        $yesterdayData = $transactions[$yesterday->format('Y-m-d')] ?? collect();

        $totalOrder = $transactions->flatten()->count();

        $todayCount = $todayData->count();
        $yesterdayCount = $yesterdayData->count();

        if ($yesterdayCount == 0) {
            $percentOrder = $todayCount > 0 ? 100 : 0;
        } else {
            $percentOrder = (($todayCount - $yesterdayCount) / $yesterdayCount) * 100;
        }

        // revenue
        $todayRevenue = (float) $todayData->sum('total');
        $yesterdayRevenue = (float) $yesterdayData->sum('total');

        if ($yesterdayRevenue == 0) {
            $percentRevenue = $todayRevenue > 0 ? 100 : 0;
        } else {
            $percentRevenue = (($todayRevenue - $yesterdayRevenue) / $yesterdayRevenue) * 100;
        }

        $formatPercent = function ($value) {
            if ($value > 100) {
                return '100%+';
            }
            if ($value < -100) {
                return '-100%';
            }

            return round($value, 1) . '%';
        };

        $percentOrderLabel = $formatPercent($percentOrder);
        $percentRevenueLabel = $formatPercent($percentRevenue);

        // stok
        $stocks = Stock::whereIn('warehouse_id', $warehouseIds)->get();

        $totalStock = $stocks->sum('qty');

        $todayStock = $stocks->filter(
            fn($s) => Carbon::parse($s->updated_at)->isToday()
        )->sum('qty');

        $yesterdayStock = $stocks->filter(
            fn($s) => Carbon::parse($s->updated_at)->isYesterday()
        )->sum('qty');

        if ($yesterdayStock == 0) {
            $percentStock = $todayStock > 0 ? 100 : 0;
        } else {
            $percentStock = (($todayStock - $yesterdayStock) / $yesterdayStock) * 100;
        }

        $percentStockLabel = $formatPercent($percentStock);

        $lowStock = $stocks->filter(fn($s) => $s->qty <= 5 && $s->qty > 0);

        $lowStockCount = $lowStock->count();

        $todayLow = $lowStock->filter(
            fn($s) => Carbon::parse($s->updated_at)->isToday()
        )->count();

        $yesterdayLow = $lowStock->filter(
            fn($s) => Carbon::parse($s->updated_at)->isYesterday()
        )->count();

        if ($yesterdayLow == 0) {
            $percentLow = $todayLow > 0 ? 100 : 0;
        } else {
            $percentLow = (($todayLow - $yesterdayLow) / $yesterdayLow) * 100;
        }

        $percentLowLabel = $formatPercent($percentLow);

        // revenue order
        $range = $request->get('range', 7);

        $chartLabels = [];
        $chartRevenue = [];
        $chartOrders = [];

        for ($i = $range - 1; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i)->format('Y-m-d');
            $formatted = Carbon::parse($date)->format('d M');

            $dayData = $transactions[$date] ?? collect();

            $chartLabels[] = $formatted;
            $chartRevenue[] = $dayData->sum('total');
            $chartOrders[] = $dayData->count();
        }

        // top produk
        $topProducts = DB::table('transaction_items')
            ->where('store_id', $storeId)
            ->where('is_active', true)
            ->select('products.name', DB::raw('SUM(transaction_items.qty) as total_sold'))
            ->join('products', 'transaction_items.product_id', '=', 'products.id')
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_sold')
            ->limit(5)
            ->get();

        if ($topProducts->isEmpty()) {
            $labels = ['Belum ada data'];
            $data = [1];
        } else {
            $labels = $topProducts->pluck('name');
            $data = $topProducts->pluck('total_sold');
        }

        return view('dashboard', compact(
            'totalOrder',
            'todayCount',
            'yesterdayCount',
            'percentOrder',
            'percentOrderLabel',

            'todayRevenue',
            'yesterdayRevenue',
            'percentRevenue',
            'percentRevenueLabel',

            'totalStock',
            'todayStock',
            'yesterdayStock',
            'percentStock',
            'percentStockLabel',

            'lowStockCount',
            'percentLow',
            'percentLowLabel',

            'chartLabels',
            'chartRevenue',
            'chartOrders',
            'range',

            'labels',
            'data'
        ));
    }
}
