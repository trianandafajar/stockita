<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DemoLoginController extends Controller
{
    public function login(Request $request)
    {
        $emails = [
            'admin' => 'admin@demo.com',
            'owner' => 'owner@demo.com',
            'buyer' => 'buyer@demo.com',
        ];

        $role = $request->input('role');

        // Validation for the requested role
        if (!array_key_exists($role, $emails)) {
            return back()->with('error', 'Invalid role selected.');
        }

        $user = User::where('email', $emails[$role])->first();

        // Check if the demo user exists in the database
        if (!$user) {
            return back()->with('error', 'Demo user not found.');
        }

        Auth::login($user);

        // Redirect based on role
        return redirect()->intended(match ($role) {
            'admin' => '/admin/dashboard',
            'owner' => '/dashboard',
            'buyer' => '/buyer/dashboard',
            default => '/dashboard',
        });
    }
}
