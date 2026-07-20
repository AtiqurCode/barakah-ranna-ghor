<?php

namespace App\Actions;

use App\Models\Order;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class ReconcileOrderWithStripe
{
    public function __construct(private SyncOrderFromStripeSession $sync) {}

    /**
     * Ask Stripe directly for a checkout session's real status and correct
     * the local order if it's out of sync — the fallback for when the
     * checkout.session.completed/expired webhook never arrived.
     */
    public function __invoke(Order $order): Order
    {
        if ($order->isPaid() || $order->stripe_checkout_session_id === null) {
            return $order;
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $session = Session::retrieve($order->stripe_checkout_session_id);

        match ($session->status) {
            'complete' => $this->sync->markPaid($order, $session),
            'expired' => $this->sync->markCancelled($order),
            default => null,
        };

        return $order->refresh();
    }
}
