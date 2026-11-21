<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        $ssoMode = config('sso.sso_mode', 'dev');
        $devAccounts = $ssoMode === 'dev' ? config('sso.dev_accounts', []) : [];
        return view('auth.login', compact('ssoMode', 'devAccounts'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'nip' => 'required|string|max:20',
            'password' => 'required|string|min:6',
        ], [
            'nip.required' => 'NIP wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $user = User::where('nip', $request->nip)->where('is_active', true)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'nip' => 'NIP atau kata sandi tidak valid.',
            ])->withInput(['nip' => $request->nip]);
        }

        Auth::login($user, $request->boolean('remember'));
        $user->update(['last_login_at' => now()]);

        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar dari sistem.');
    }
}
