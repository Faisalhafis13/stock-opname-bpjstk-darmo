<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Menampilkan halaman login.
     */
    public function showLogin(): View
    {
        return view('auth.login');
    }

    /**
     * Memproses login pengguna.
     */
    public function login(Request $request): RedirectResponse|JsonResponse
    {
        $credentials = $request->validate([
            'email' => [
                'required',
                'email',
            ],
            'password' => [
                'required',
                'string',
            ],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        if (! Auth::attempt($credentials)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email atau password yang dimasukkan salah.',
                    'errors' => [
                        'email' => [
                            'Email atau password yang dimasukkan salah.',
                        ],
                    ],
                ], 422);
            }

            return back()
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Email atau password yang dimasukkan salah.',
                ]);
        }

        // Mencegah session fixation setelah login berhasil.
        $request->session()->regenerate();

        $redirectUrl = redirect()->intended(route('dashboard'))->getTargetUrl();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Login berhasil. Selamat datang!',
                'redirect' => $redirectUrl,
            ]);
        }

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Mengeluarkan pengguna dari sistem.
     */

public function logout(Request $request): RedirectResponse|JsonResponse
{
    Auth::logout();

    $request->session()->invalidate();
    $request->session()->regenerateToken();

    $message = 'Anda berhasil logout.';

    if ($request->expectsJson()) {
        return response()->json([
            'success' => true,
            'message' => $message,
            'redirect' => route('login'),
        ]);
    }

    return redirect()
        ->route('login')
        ->with('success', $message);
}
}
