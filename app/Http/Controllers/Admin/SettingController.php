<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    /**
     * Tampilkan halaman pengaturan sistem.
     */
    public function index(): View
    {
        $settings = [
            'site_name' => Setting::get('site_name', config('app.name', 'UD Bisa Furniture')),
            'site_email' => Setting::get('site_email', config('mail.from.address')),
            'site_phone' => Setting::get('site_phone', ''),
            'site_address' => Setting::get('site_address', ''),
            'currency' => Setting::get('currency', 'IDR'),
            'timezone' => Setting::get('timezone', config('app.timezone', 'Asia/Jakarta')),
            'items_per_page' => (int) Setting::get('items_per_page', 10),
            'enable_notifications' => (bool) Setting::get('enable_notifications', true),
            'enable_email_notifications' => (bool) Setting::get('enable_email_notifications', false),
            'maintenance_mode' => (bool) Setting::get('maintenance_mode', false),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Perbarui pengaturan sistem.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'site_name' => ['required', 'string', 'max:255'],
            'site_email' => ['required', 'email', 'max:255'],
            'site_phone' => ['nullable', 'string', 'max:50'],
            'site_address' => ['nullable', 'string', 'max:1000'],
            'currency' => ['required', 'string', 'in:IDR,USD'],
            'timezone' => ['required', 'string', 'max:100'],
            'items_per_page' => ['required', 'integer', 'min:5', 'max:100'],
            'enable_notifications' => ['nullable', 'boolean'],
            'enable_email_notifications' => ['nullable', 'boolean'],
            'maintenance_mode' => ['nullable', 'boolean'],
        ]);

        $bool = fn(string $key): bool => $request->boolean($key);

        Setting::set('site_name', $validated['site_name']);
        Setting::set('site_email', $validated['site_email']);
        Setting::set('site_phone', $validated['site_phone'] ?? '');
        Setting::set('site_address', $validated['site_address'] ?? '');
        Setting::set('currency', $validated['currency']);
        Setting::set('timezone', $validated['timezone']);
        Setting::set('items_per_page', (int) $validated['items_per_page']);
        Setting::set('enable_notifications', $bool('enable_notifications'));
        Setting::set('enable_email_notifications', $bool('enable_email_notifications'));
        Setting::set('maintenance_mode', $bool('maintenance_mode'));

        return redirect()->route('admin.settings.index')
            ->with('success', 'Pengaturan sistem berhasil diperbarui.');
    }
}