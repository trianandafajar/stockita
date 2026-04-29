<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Plan;
use App\Models\Store;
use App\Models\Subscription;
use App\Models\User;
use App\Notifications\NewOwnerNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(Request $request): View
    {
        session([
            'plan_id' => $request->plan,
            'interval' => $request->interval,
        ]);

        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'name.string' => 'Nama harus berupa teks.',
            'name.max' => 'Nama maksimal :max karakter.',

            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Email tidak valid.',
            'email.unique' => 'Email sudah terdaftar.',

            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
            'password.min' => 'Password minimal :min karakter.',
            'password.mixed' => 'Password harus mengandung minimal 1 huruf besar dan 1 huruf kecil.',
            'password.numbers' => 'Password harus mengandung minimal 1 angka.',
            'password.symbols' => 'Password harus mengandung minimal 1 karakter unik/simbol.',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole('owner');

        if ($user->hasRole('owner')) {
            Store::create([
                'name' => $user->name . "'s Store",
                'email' => $request->email,
                'owner_id' => $user->id,
                'slug' => $this->generateUniqueSlug($user->name . '-store'),
            ]);

            $admins = User::role('admin')->get();

            foreach ($admins as $admin) {
                $admin->notify(new NewOwnerNotification($user));
            }
        }

        $plan = session('plan_id');
        $interval = session('interval');

        if ($plan) {

            Auth::login($user);

            $planModel = Plan::find($plan);

            session()->forget(['plan_id', 'interval']);

            if ($planModel && $planModel->price == 0) {
                return redirect("/dashboard");
            }

            return redirect("/checkout?plan={$plan}&interval={$interval}");
        }

        return redirect("/dashboard");

        event(new Registered($user));

        return redirect(route('login'))->with('success', 'Registrasi berhasil silahkan login!');
    }

    private function generateUniqueSlug($name)
    {
        $slug = Str::slug($name);
        $count = Store::where('slug', 'LIKE', $slug . '%')->count();

        return $count ? "{$slug}-{$count}" : $slug;
    }
}
