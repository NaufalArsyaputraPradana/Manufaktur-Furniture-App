<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ProfileController extends Controller
{
    /**
     * Menampilkan halaman profil pengguna.
     */
    public function index(): View
    {
        $user = auth()->user()->load(['role', 'orders']);

        return view('customer.profile.index', compact('user'));
    }

    /**
     * Memperbarui informasi dasar profil.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'email'   => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone'   => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
        ], [
            'name.required'  => 'Nama lengkap wajib diisi.',
            'name.max'       => 'Nama lengkap maksimal 255 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
            'email.unique'   => 'Email sudah digunakan oleh akun lain.',
            'phone.max'      => 'Nomor telepon maksimal 20 karakter.',
            'address.max'    => 'Alamat maksimal 500 karakter.',
        ]);

        $user->update($validated);

        return back()->with('success', 'Profil Anda berhasil diperbarui! ✅');
    }

    /**
     * Memperbarui password pengguna.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.required'         => 'Password saat ini wajib diisi.',
            'current_password.current_password' => 'Password saat ini tidak sesuai. Silakan coba lagi.',
            'password.required'                 => 'Password baru wajib diisi.',
            'password.confirmed'                => 'Konfirmasi password baru tidak sesuai.',
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Password Anda berhasil diubah! 🔐 Pastikan Anda mengingatnya.');
    }

    /**
     * Menghapus akun pengguna secara permanen.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ], [
            'password.required'         => 'Password wajib diisi untuk konfirmasi penghapusan.',
            'password.current_password' => 'Password tidak sesuai. Silakan coba lagi.',
        ]);

        $user = $request->user();

        $hasPendingOrders = $user->orders()
            ->whereIn('status', ['pending', 'confirmed', 'in_production'])
            ->exists();

        if ($hasPendingOrders) {
            return back()->with('error', 'Tidak dapat menghapus akun! Anda masih memiliki pesanan yang sedang diproses. Harap tunggu hingga pesanan selesai.');
        }

        $userName = $user->name;

        auth()->logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')
            ->with('success', "Akun {$userName} telah dihapus. Terima kasih telah menjadi bagian dari kami. 👋");
    }
}