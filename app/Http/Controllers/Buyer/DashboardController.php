<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;


class DashboardController extends Controller
{
     public function __construct()
    {
        $this->middleware('permission:view order history')->only(['index']);
    }

    public function index()
    {
        $userId = Auth::id();

        $customerId = Customer::where('user_id', $userId)->first()->id;

        $totalOrders = Transaction::where('customer_id', $customerId)->count();

        $totalSpent = Transaction::where('customer_id', $customerId)
            ->sum('total');

        $lastOrder = Transaction::where('customer_id', $customerId)
            ->latest()
            ->first();

        $recentOrders = Transaction::where('customer_id', $customerId)
            ->latest()
            ->take(5)
            ->get();

        return view('buyer.dashboard', compact(
            'totalOrders',
            'totalSpent',
            'lastOrder',
            'recentOrders'
        ));
    }
}
