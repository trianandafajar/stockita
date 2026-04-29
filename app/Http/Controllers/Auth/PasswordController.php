<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validateWithBag('updatePassword', [
            'current_password' => ['required', 'current_password'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
            ],
        ], [
            'password.required' => 'Password wajib diisi.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
            'password.min' => 'Password minimal :min karakter.',
            'password.mixed' => 'Password harus mengandung minimal 1 huruf besar dan 1 huruf kecil.',
            'password.numbers' => 'Password harus mengandung minimal 1 angka.',
            'password.symbols' => 'Password harus mengandung minimal 1 karakter unik/simbol.',
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        Auth::logoutOtherDevices($validated['password']);

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'Password berhasil diperbarui. Silakan login kembali.');
    }
}
