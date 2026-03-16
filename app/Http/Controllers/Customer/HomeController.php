<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Menampilkan halaman utama (beranda) pelanggan.
     */
    public function index(): View
    {
        $categories = Category::where('is_active', true)
            ->withCount(['products' => fn($query) => $query->where('is_active', true)])
            ->get();

        $products = Product::with('category')
            ->where('is_active', true)
            ->latest()
            ->take(8)
            ->get();

        $cartCount = auth()->check() ? count((array) session('cart', [])) : 0;

        return view('customer.home', compact('categories', 'products', 'cartCount'));
    }

    /**
     * Menampilkan halaman Tentang Kami.
     */
    public function about(): View
    {
        return view('customer.about');
    }

    /**
     * Menampilkan halaman Kontak.
     */
    public function contact(): View
    {
        return view('customer.contact');
    }

    /**
     * Memproses form kontak dan mengarahkannya ke WhatsApp Admin.
     */
    public function submitContact(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'subject' => 'required|string|max:100',
            'message' => 'required|string|max:1000',
        ]);

        $phone = '6285290505442';

        $text = sprintf(
            "Halo, saya %s (%s).\n\nSubject: %s\nPesan: %s",
            $validated['name'],
            $validated['email'],
            $validated['subject'],
            $validated['message']
        );

        $url = "https://wa.me/{$phone}?text=" . urlencode($text);

        return redirect()->away($url);
    }
}