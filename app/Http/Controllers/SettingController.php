<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use App\Models\Plan;
use App\Models\Setting;
use App\Models\Store;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view settings')->only(['index']);
        $this->middleware('permission:edit store')->only(['updateStore']);
    }


    private function generateUniqueSlug($name)
    {
        $slug = Str::slug($name);
        $count = Store::where('slug', 'LIKE', $slug . '%')->count();

        return $count ? "{$slug}-{$count}" : $slug;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $owner = $user->store->owner;
        $store = $user->store;

        $subscription = Subscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->first();

        $plan = $subscription ? Plan::find($subscription->plan_id) : null;

        return view('settings.index', compact('subscription', 'plan', 'owner', 'store'));
    }

    public function update(Request $request)
    {
        // STORE
        // if ($request->has('store')) {
        //         Store::updateOrCreate(
        //             ['name' => $request->

        //         );
        // }

        // APP (kamu belum handle ini ❗)
        if ($request->has('app')) {
            foreach ($request->app as $key => $value) {
                Setting::updateOrCreate(
                    ['key' => 'app.' . $key],
                    ['value' => $value]
                );
            }
        }

        // email
        if ($request->has('email')) {
            foreach ($request->email as $key => $value) {
                Setting::updateOrCreate(
                    ['key' => 'email.' . $key],
                    ['value' => $value]
                );
            }
        }

        cache()->forget('settings');

        return back()->with('success', 'Setting berhasil disimpan');
    }

    public function updateStore(Request $request, $id)
    {
        $data = $request->store;

        Store::updateOrCreate(
            ['id' => $id],

            [
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'slug' => $this->generateUniqueSlug($data['name']),
                'address' => $data['address'],
            ]
        );

        return redirect()->back()->with('success', 'Informasi toko berhasil diperbarui!');
    }

    public function updateEmail(Request $request, $key)
    {
        EmailTemplate::updateOrCreate(
            ['key' => $key],
            [
                'subject' => $request->subject,
                'body' => $request->body,
            ]
        );

        return back()->with('success', 'Template berhasil disimpan');
    }
}
