<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\SubscriptionsImport;
use App\Models\Plan;
use App\Models\Store;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class SubscriptionController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage subscription')->only(['index', 'create', 'store', 'toggle', 'destroy']);
    }

    public function index(Request $request)
    {
        $subscriptions = Subscription::with(['user', 'plan'])
            ->when($request->search, function ($q) use ($request) {
                $search = $request->search;

                $q->where(function ($query) use ($search) {
                    $query
                        ->orWhereHas('user', function ($q) use ($search) {
                            $q->where('name', 'like', "%$search%");
                        })->orWhereHas('user', function ($q) use ($search) {
                            $q->where('email', 'like', "%$search%");
                        });
                });
            })
            ->when($request->status, function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->when($request->interval, function ($q) use ($request) {
                $q->where('interval', $request->interval);
            })
            ->when($request->type, function ($q) use ($request) {
                $q->where('plan_id', $request->type);
            })

            ->latest()
            ->paginate(10)
            ->withQueryString();

        $plans = Plan::all();
        $stores = Store::all();

        return view('admin.subscription.index', compact('subscriptions', 'stores', 'plans'));
    }

    public function create()
    {
        $plans = Plan::all();
        $owners = User::role('owner')
            ->whereDoesntHave('subscription')
            ->get();


        return view('admin.subscription.create', compact('owners', 'plans'));
    }

    public function store(Request $request)
    {
        $isAdmin = auth()->user()->hasRole('admin');
        $prefix = $isAdmin ? '/admin' : '';

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'plan_id' => 'required|exists:plans,id',
            'interval' => 'required|in:monthly,yearly',
        ]);

        $user = User::findOrFail($request->user_id);
        $plan = Plan::findOrFail($request->plan_id);

        $user->subscription()->create([
            'plan_id' => $plan->id,
            'interval' => $request->interval,
            'status' => 'active',
            'started_at' => now(),
            'current_period_end' => $request->interval === 'yearly'
                ? now()->addYear()
                : now()->addMonth(),
        ]);

        $user = User::find($request->user_id);

        $subscription = $user->subscription;

        $user->syncAllLimits();

        logActivity('CREATE', $subscription, [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'plan' => $plan->name,
            'interval' => $request->interval,
            'status' => 'active',
        ]);

        return redirect($prefix . '/subscriptions')
            ->with('success', 'Langganan berhasil dibuat!');
    }

    public function update(Request $request, $id)
    {
        $subscription = Subscription::findOrFail($id);
        $user = $subscription->user->id;

        $oldPlan = $subscription->plan;
        $newPlan = Plan::find($request->plan_id);

        $isUpgrade = $newPlan->price > $oldPlan->price;
        $isDowngrade = $newPlan->price < $oldPlan->price;

        $before = [
            'plan' => $oldPlan->name,
            'interval' => $subscription->interval,
        ];

        $subscription->update([
            'plan_id' => $request->plan_id,
            'interval' => $request->interval,
            'started_at' => now(),
            'current_period_end' => $request->interval === 'yearly'
                ? now()->addYear()
                : now()->addMonth(),

        ]);

        $user = User::find($user);
        $user->syncAllLimits();

        $actionType = 'UPDATE';

        if ($isUpgrade) {
            $actionType = 'UPGRADE_SUBSCRIPTION';
        } elseif ($isDowngrade) {
            $actionType = 'DOWNGRADE_SUBSCRIPTION';
        }

        // 🔥 LOG UPDATE SUBSCRIPTION
        logActivity($actionType, $subscription, [
            'user_id' => $subscription->user_id,
            'before' => $before,
            'after' => [
                'plan' => $newPlan->name,
                'interval' => $request->interval,
            ],
        ]);

        if ($isUpgrade) {
            $message = 'Berhasil upgrade paket!';
        } elseif ($isDowngrade) {
            $message = 'Berhasil downgrade paket!';
        } else {
            $message = 'Langganan berhasil diperbarui!';
        }

        return back()->with('success', $message);
    }

    public function toggle($id)
    {
        $subscription = Subscription::findOrFail($id);

        $oldStatus = $subscription->status;

        if ($subscription->status === 'active') {
            $subscription->update([
                'status' => 'cancelled'
            ]);
        } else {
            $subscription->update([
                'status' => 'active'
            ]);
        }

        logActivity('TOGGLE_SUBSCRIPTION', $subscription, [
            'user_id' => $subscription->user_id,
            'before' => $oldStatus,
            'after' => $subscription->status,
        ]);

        return back()->with('success', 'Status langganan berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $subscription = Subscription::findOrFail($id);

        $data = [
            'user_id' => $subscription->user_id,
            'plan' => $subscription->plan->name,
            'interval' => $subscription->interval,
        ];

        $subscription->delete();

        logActivity('DELETE', $subscription, $data);

        return redirect()->back()->with('success', 'Data langganan berhasil dihapus!');
    }

    // import
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls,csv',
            'store_id' => 'required'
        ], [
            'file.required' => 'File wajib diupload!',
            'file.mimes' => 'Format file harus Excel (.xlsx, .xls, .csv)',
            'store_id.required' => 'Toko harus dipilih!'
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator, 'import')
                ->withInput();
        }

        Excel::import(new SubscriptionsImport, $request->file('file'));

        return back()->with('success', 'Import berhasil!');
    }

    public function template()
    {
        return Excel::download(new class implements
            \Maatwebsite\Excel\Concerns\FromArray,
            \Maatwebsite\Excel\Concerns\WithHeadings
        {
            public function headings(): array
            {
                return [
                    'nama',
                    'email',
                    'password',
                    'nama_toko',
                    'alamat',
                    'plan',
                    'interval'
                ];
            }

            public function array(): array
            {
                return [
                    [
                        'Budi Santoso',
                        'budi@email.com',
                        'password123',
                        'Toko Budi',
                        'Jl. Contoh No.1',
                        'Business',
                        'monthly'
                    ]
                ];
            }
        }, 'template-import.xlsx');
    }
}
