<?php

namespace App\Policies;

use App\Models\Payment;
use App\Models\User;

class PaymentPolicy
{
    /**
     * Determine if the user can view any payments
     */
    public function viewAny(User $user): bool
    {
        return $user->role?->name === 'admin';
    }

    /**
     * Determine if the user can view a specific payment
     */
    public function view(User $user, Payment $payment): bool
    {
        // Admin can view all payments
        if ($user->role?->name === 'admin') {
            return true;
        }

        // Customer can view their own order's payment
        if ($user->role?->name === 'customer') {
            return $payment->order?->user_id === $user->id;
        }

        return false;
    }

    /**
     * Determine if the user can verify a payment
     */
    public function verify(User $user, Payment $payment): bool
    {
        return $user->role?->name === 'admin';
    }

    /**
     * Determine if the user can reject a payment
     */
    public function reject(User $user, Payment $payment): bool
    {
        return $user->role?->name === 'admin';
    }

    /**
     * Determine if the user can confirm final payment
     */
    public function confirmFinalPayment(User $user, Payment $payment): bool
    {
        return $user->role?->name === 'admin';
    }

    /**
     * Determine if the user can confirm final payment (alias)
     */
    public function confirmFinal(User $user, Payment $payment): bool
    {
        return $user->role?->name === 'admin';
    }

    /**
     * Determine if the user can upload payment proof
     */
    public function uploadProof(User $user, Payment $payment): bool
    {
        // Customer can upload proof for their own payment
        if ($user->role?->name === 'customer') {
            return $payment->order?->user_id === $user->id;
        }

        // Admin can upload proof for any payment
        return $user->role?->name === 'admin';
    }
}
