<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use App\Models\Plan;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $plans = Plan::latest()->get();
        $emailTemplates = EmailTemplate::all()->keyBy('key');

        return view('settings.index', compact('plans', 'emailTemplates'));
    }

    public function update(Request $request)
    {
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

    // update plan
    public function updatePlan(Request $request, $id)
    {
        $plan = Plan::findOrFail($id);

        $data = $request->all();

        $data['features'] = $request->features
            ? array_map('trim', explode(',', $request->features))
            : [];

        $plan->update($data);

        return redirect()->back()->with('success', 'Plan berhasil diperbarui!');
    }
}
