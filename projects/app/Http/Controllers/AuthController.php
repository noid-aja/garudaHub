<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLogin(Request $request)
    {
        if (Auth::check()) {
            if (app()->environment('local')) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            } else {
                return redirect()->route('dashboard.index');
            }
        }

        return response()->view('projects.login')
            ->header('Cache-Control', 'no-store, private, max-age=0');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt($data)) {
            throw ValidationException::withMessages([
                'email' => 'Email atau password salah.',
            ]);
        }

        $request->session()->regenerate();

        return redirect()->route('dashboard.index');
    }

    public function showRegister()
    {
        return view('projects.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:100', 'unique:users,email'],
            'password' => [
                'required', 'confirmed',
                Password::min(8)->letters()->numbers(),
            ],
            'role' => ['required', Rule::in(['klien', 'developer'])],
        ]);

        $user = User::create([
            'nama' => $data['nama'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role'],
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard.index')
            ->with('success', 'Registrasi berhasil.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Berhasil logout.');
    }
}
