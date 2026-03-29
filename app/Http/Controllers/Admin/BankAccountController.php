<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BankAccount;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class BankAccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $bankAccounts = BankAccount::latest()->paginate(10);
        return view('admin.bank-accounts.index', compact('bankAccounts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.bank-accounts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'bank_name' => ['required', 'string', 'max:100'],
            'account_holder' => ['required', 'string', 'max:100'],
            'account_number' => ['required', 'string', 'max:50', 'unique:bank_accounts,account_number'],
            'is_active' => ['boolean'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'bank_name.required' => 'Nama bank wajib diisi',
            'account_holder.required' => 'Atas nama rekening wajib diisi',
            'account_number.required' => 'Nomor rekening wajib diisi',
            'account_number.unique' => 'Nomor rekening sudah terdaftar',
        ]);

        BankAccount::create($validated);

        return redirect()->route('admin.bank-accounts.index')
            ->with('success', 'Rekening bank berhasil ditambahkan');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BankAccount $bankAccount): View
    {
        return view('admin.bank-accounts.edit', compact('bankAccount'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BankAccount $bankAccount): RedirectResponse
    {
        $validated = $request->validate([
            'bank_name' => ['required', 'string', 'max:100'],
            'account_holder' => ['required', 'string', 'max:100'],
            'account_number' => ['required', 'string', 'max:50', 'unique:bank_accounts,account_number,' . $bankAccount->id],
            'is_active' => ['boolean'],
            'notes' => ['nullable', 'string', 'max:500'],
        ], [
            'bank_name.required' => 'Nama bank wajib diisi',
            'account_holder.required' => 'Atas nama rekening wajib diisi',
            'account_number.required' => 'Nomor rekening wajib diisi',
            'account_number.unique' => 'Nomor rekening sudah terdaftar',
        ]);

        $bankAccount->update($validated);

        return redirect()->route('admin.bank-accounts.index')
            ->with('success', 'Rekening bank berhasil diperbarui');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BankAccount $bankAccount): RedirectResponse
    {
        $bankAccount->delete();

        return redirect()->route('admin.bank-accounts.index')
            ->with('success', 'Rekening bank berhasil dihapus');
    }
}
