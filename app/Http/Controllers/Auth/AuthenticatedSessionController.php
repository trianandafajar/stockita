<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\Subscription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        if ($request->remember) {
            Cookie::queue('remember_email', $request->email, 60 * 24 * 30);
            Cookie::queue('remember_password', $request->password, 60 * 24 * 30);
        }

        $user = auth()->user();

        if ($user->hasRole('owner')) {
            $expired = Subscription::where('user_id', $user->id)
                ->where('status', 'active')
                ->where('current_period_end', '<', now())
                ->first();

            if ($expired) {
                $expired->update([
                    'status' => 'expired',
                ]);

                $user->deactivateAllData();
            }
        }

        if ($user->hasRole('admin')) {
            return redirect()->intended('/admin/dashboard');
        }

        if ($user->hasRole('buyer')) {
            return redirect()->intended('/buyer/dashboard');
        }

        return redirect()->intended('/dashboard');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
