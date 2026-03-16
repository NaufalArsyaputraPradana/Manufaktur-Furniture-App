<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login.
     */
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectUser(Auth::user());
        }

        return view('auth.login');
    }

    /**
     * Proses autentikasi login.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ], [
            'email.required'    => 'Alamat email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();

            if (!$user->is_active) {
                Auth::logout();
                return back()->with('error', 'Akun Anda telah dinonaktifkan. Silakan hubungi administrator.');
            }

            $request->session()->regenerate();

            return $this->redirectUser($user);
        }

        return back()
            ->withErrors(['email' => 'Kredensial yang Anda berikan tidak cocok dengan data kami.'])
            ->withInput($request->only('email'))
            ->with('error', 'Login gagal! Silakan periksa kembali email dan password Anda.');
    }

    /**
     * Tampilkan halaman registrasi.
     */
    public function showRegister(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectUser(Auth::user());
        }

        return view('auth.register');
    }

    /**
     * Proses pendaftaran user baru.
     */
    public function register(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone'    => ['required', 'string', 'max:20'],
            'address'  => ['nullable', 'string', 'max:500'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->letters()->numbers()
            ],
        ], [
            'name.required'      => 'Nama lengkap wajib diisi.',
            'email.unique'       => 'Email ini sudah terdaftar di sistem kami.',
            'password.min'       => 'Password minimal 8 karakter dengan kombinasi huruf dan angka.',
            'password.confirmed' => 'Konfirmasi password tidak sesuai.',
            'phone.required'     => 'Nomor telepon wajib diisi.',
        ]);

        $customerRole = Role::where('name', 'customer')->first();

        if (!$customerRole) {
            return back()->withInput()->with('error', 'Pendaftaran gagal karena konfigurasi sistem belum lengkap (Role tidak ditemukan).');
        }

        try {
            $user = User::create([
                'name'      => $validated['name'],
                'email'     => $validated['email'],
                'password'  => Hash::make($validated['password']),
                'phone'     => $validated['phone'],
                'address'   => $validated['address'] ?? null,
                'role_id'   => $customerRole->id,
                'is_active' => true,
            ]);

            Auth::login($user);

            return redirect()->route('home')
                ->with('success', "Selamat bergabung, {$user->name}! Akun Anda berhasil dibuat.");

        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Terjadi masalah saat membuat akun. Silakan coba beberapa saat lagi.');
        }
    }

    /**
     * Proses logout user.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah berhasil keluar dari sistem.');
    }

    /**
     * Helper: Pengalihan halaman berdasarkan peran (Role) user.
     */
    protected function redirectUser($user): RedirectResponse
    {
        $role = $user->role?->name ?? 'customer';

        return match ($role) {
            'admin', 'staff'    => redirect()->intended(route('admin.dashboard'))
                                    ->with('success', 'Selamat datang di Panel Administrasi.'),
            'production_staff'  => redirect()->intended(route('production.dashboard'))
                                    ->with('success', 'Selamat bekerja! Status produksi siap dikelola.'),
            default             => redirect()->intended(route('home'))
                                    ->with('success', 'Login berhasil. Selamat berbelanja!'),
        };
    }
}