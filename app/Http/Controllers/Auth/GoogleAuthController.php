<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable $e) {
            return redirect()->route('login')->with('error', 'Login Google gagal. Silakan coba lagi.');
        }

        $role = Role::where('name', 'customer')->first();
        if (!$role) {
            return redirect()->route('login')->with('error', 'Konfigurasi role customer belum tersedia.');
        }

        $email = $googleUser->getEmail();
        if (!$email) {
            return redirect()->route('login')->with('error', 'Akun Google tidak memiliki email. Gunakan metode login lain.');
        }

        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $email)
            ->first();

        if ($user) {
            if (!$user->google_id) {
                $user->update([
                    'google_id' => $googleUser->getId(),
                    'avatar'    => $googleUser->getAvatar(),
                ]);
            }
        } else {
            $user = User::create([
                'name'      => $googleUser->getName() ?: 'Pelanggan',
                'email'     => $email,
                'google_id' => $googleUser->getId(),
                'avatar'    => $googleUser->getAvatar(),
                'password'  => Hash::make(Str::random(48)),
                'role_id'   => $role->id,
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
        }

        if (!$user->is_active) {
            return redirect()->route('login')->with('error', 'Akun Anda telah dinonaktifkan.');
        }

        Auth::login($user, true);
        request()->session()->regenerate();

        return redirect()->intended(route('home'))->with('success', 'Anda masuk dengan Google.');
    }
}
