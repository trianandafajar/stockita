<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        // $googleUser = Socialite::driver('google')->user();

        $googleUser = Socialite::driver('google')->stateless()->user();

        $user = User::where('email', $googleUser->email)->first();

        if (! $user) {
            $user = User::create([
                'name' => $googleUser->name,
                'email' => $googleUser->email,
                'email_verified_at' => now(),
                'password' => bcrypt('password123'),
            ]);

            $user->assignRole('owner');
        }

        if ($user->hasRole('owner')) {
            Store::create([
               'name' => $user->name."'s Store",
                'email' => $googleUser->email,
                'owner_id' => $user->id,
                'slug' => $this->generateUniqueSlug($user->name.'-store'),
            ]);
        }

        if ($user) {
            Subscription::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'plan_id' => 1,
                    'interval' => 'monthly',
                    'status' => 'active',
                    'current_period_end' => now()->addDays(30),
                ]
            );
        }

        Auth::login($user);

        return redirect('/dashboard');
    }

    private function generateUniqueSlug($name)
    {
        $slug = Str::slug($name);
        $count = Store::where('slug', 'LIKE', $slug.'%')->count();

        return $count ? "{$slug}-{$count}" : $slug;
    }
}
