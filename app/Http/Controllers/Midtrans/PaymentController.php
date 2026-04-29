<?php

namespace App\Http\Controllers\Midtrans;

use App\Http\Controllers\Controller;
use App\Models\MidtransTransaction;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;

class PaymentController extends Controller
{
    public function index()
    {
        $plans = Plan::all();
        $user = auth()->user();
        $organization = $user->organization;

        $subscription = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        return view('subscription.index', compact('plans', 'subscription', 'organization'));
    }

    public function pay(Request $request)
    {
        $user = auth()->user();

        $plan = Plan::findOrFail($request->plan_id);
        $interval = $request->interval;

        if ($plan->price == 0) {
            Subscription::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'plan_id' => $plan->id,
                    'interval' => 'monthly',
                    'status' => 'active',
                    'current_period_end' => now()->addDays(30),
                ]
            );

            $user = User::find($user->id);
            $user->syncAllLimits();
            return response()->json(['free' => true]);
        }

        $amount = $interval === 'yearly'
            ? $plan->yearly_price
            : $plan->price;

        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = false;
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $orderId = 'ORDER-' . uniqid();

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $amount,
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email,
            ],
        ];

        $snapToken = Snap::getSnapToken($params);

        MidtransTransaction::create([
            'user_id' => $user->id,
            'order_id' => $orderId,
            'plan_id' => $plan->id,
            'interval' => $interval,
            'gross_amount' => $amount,
            'status' => 'pending',
            'snap_token' => $snapToken,
        ]);

        return response()->json([
            'snap_token' => $snapToken,
        ]);
    }

    public function upgrade(Request $request)
    {
        $user = auth()->user();

        $plan = Plan::findOrFail($request->plan_id);
        $interval = $request->interval;

        if ($plan->price == 0) {
            Subscription::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'plan_id' => $plan->id,
                    'interval' => 'monthly',
                    'status' => 'active',
                    'current_period_end' => now()->addDays(30),
                ]
            );

            $user = User::find($user->id);
            $user->syncAllLimits();

            return response()->json(['free' => true]);
        }

        $amount = $interval === 'yearly'
            ? $plan->yearly_price
            : $plan->price;

        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = false;
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $orderId = 'ORDER-' . uniqid();

        $params = [
            'transaction_details' => [
                'order_id' => $orderId,
                'gross_amount' => $amount,
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email,
            ],
        ];

        $snapToken = Snap::getSnapToken($params);

        MidtransTransaction::updateOrCreate([
            'user_id' => $user->id,
            'order_id' => $orderId,
            'plan_id' => $plan->id,
            'interval' => $interval,
            'gross_amount' => $amount,
            'status' => 'pending',
            'snap_token' => $snapToken,
        ]);

        return response()->json([
            'snap_token' => $snapToken,
        ]);
    }

    public function webhook(Request $request)
    {
        Log::info('MIDTRANS WEBHOOK', $request->all());

        $orderId = $request->order_id;
        $status = $request->transaction_status;

        $transaction = MidtransTransaction::where('order_id', $orderId)->first();

        if (! $transaction) {
            return response()->json(['message' => 'Not found'], 404);
        }

        if ($status == 'settlement' || $status == 'capture') {

            $transaction->update([
                'status' => 'paid',
            ]);

            $plan = Plan::find($transaction->plan_id);

            Subscription::updateOrCreate(
                ['user_id' => $transaction->user_id],
                [
                    'plan_id' => $plan->id,
                    'interval' => $transaction->interval,
                    'status' => 'active',
                    'current_period_end' => now()->addDays(
                        $transaction->interval === 'yearly' ? 365 : 30
                    ),
                ]
            );
            $user = User::find($transaction->user_id);
            $user->syncAllLimits();
        } elseif ($status == 'pending') {

            $transaction->update([
                'status' => 'pending',
            ]);
        } elseif ($status == 'expire') {

            $transaction->update([
                'status' => 'expired',
            ]);
        } elseif ($status == 'cancel') {

            $transaction->update([
                'status' => 'cancelled',
            ]);
        }

        return response()->json(['success' => true]);
    }

    public function checkout(Request $request)
    {
        $plan = Plan::findOrFail($request->plan);

        return view('checkout', [
            'plan' => $plan,
            'interval' => $request->interval ?? 'monthly',
        ]);
    }
}
