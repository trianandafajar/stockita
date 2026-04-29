<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
     public function __construct()
    {
        $this->middleware('permission:view own orders')->only(['index', 'show']);
    }

    public function index(Request $request)
    {
        $userId = Auth::id();
        $customerId = Customer::where('user_id', $userId)->first()->id;

        $query = Transaction::with('items.product')
            ->where('customer_id',$customerId);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->start && $request->end) {
            $query->whereBetween('created_at', [
                $request->start,
                $request->end,
            ]);
        }

        $orders = $query->latest()->paginate(10);

        return view('buyer.orders.index', compact('orders'));
    }

    public function show($id)
    {
        $userId = Auth::id();
        $customerId = Customer::where('user_id', $userId)->first()->id;
        
        $order = Transaction::with(['items.product'])
            ->where('customer_id', $customerId)
            ->findOrFail($id);

        return view('buyer.orders.show', compact('order'));
    }
}
