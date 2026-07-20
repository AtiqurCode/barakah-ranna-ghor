<?php

namespace App\Actions;

use App\Models\Order;
use App\OrderStatus;
use Illuminate\Support\Carbon;

class SyncOrderFromStripeSession
{
    /**
     * Apply a completed Stripe Checkout Session onto an order. Shared by the
     * webhook (event payload) and the reconciliation paths (a freshly
     * retrieved session), so "what fields mean paid" only lives in one place.
     *
     * @param  object{customer_email?: string|null, payment_intent?: string|null}  $session
     */
    public function markPaid(Order $order, object $session): void
    {
        if ($order->isPaid()) {
            return;
        }

        $order->update([
            'status' => OrderStatus::Paid,
            'email' => $session->customer_email ?? $order->email,
            'stripe_payment_intent_id' => $session->payment_intent,
            'paid_at' => Carbon::now(),
        ]);
    }

    public function markCancelled(Order $order): void
    {
        if ($order->isPaid()) {
            return;
        }

        $order->update(['status' => OrderStatus::Cancelled]);
    }
}
