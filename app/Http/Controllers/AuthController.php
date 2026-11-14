<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'nim_nip' => 'required|string|max:40|unique:users,nim_nip',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => [
                'required',
                'confirmed',
                Password::min(8),
            ],
        ]);

        $data['role'] = 'peminjam';

        $user = User::create($data);

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()
            ->route('borrower.dashboard')
            ->with(
                'success',
                'Pendaftaran berhasil. Selamat datang di SIPADU.'
            );
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (! Auth::attempt(
            $credentials,
            $request->boolean('remember')
        )) {
            return back()
                ->withErrors([
                    'email' => 'Email atau password tidak sesuai.',
                ])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(
            Auth::user()->role === 'admin'
                ? route('admin.dashboard')
                : route('borrower.dashboard')
        );
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()
            ->route('landing')
            ->with('success', 'Anda berhasil logout.');
    }
}