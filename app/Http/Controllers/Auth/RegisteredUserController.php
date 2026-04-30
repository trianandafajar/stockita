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
            'name.required' => 'The name field is required.',
            'name.string' => 'The name must be a string.',
            'name.max' => 'The name may not be greater than :max characters.',

            'email.required' => 'The email field is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.unique' => 'This email is already registered.',

            'password.required' => 'The password field is required.',
            'password.confirmed' => 'The password confirmation does not match.',
            'password.min' => 'The password must be at least :min characters.',
            'password.mixed' => 'The password must contain at least one uppercase and one lowercase letter.',
            'password.numbers' => 'The password must contain at least one number.',
            'password.symbols' => 'The password must contain at least one special character or symbol.',
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

        event(new Registered($user));

        if ($plan) {
            Auth::login($user);

            $planModel = Plan::find($plan);

            session()->forget(['plan_id', 'interval']);

            if ($planModel && $planModel->price == 0) {
                return redirect("/dashboard");
            }

            return redirect("/checkout?plan={$plan}&interval={$interval}");
        }

        return redirect('login')->with('success', 'Registration successful! Welcome to the dashboard.');
    }

    private function generateUniqueSlug($name)
    {
        $slug = Str::slug($name);
        $count = Store::where('slug', 'LIKE', $slug . '%')->count();

        return $count ? "{$slug}-{$count}" : $slug;
    }
}
