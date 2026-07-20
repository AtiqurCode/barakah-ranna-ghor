<?php

namespace App\Http\Controllers;

use App\Actions\SyncOrderFromStripeSession;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;
use UnexpectedValueException;

class StripeWebhookController extends Controller
{
    public function __construct(private SyncOrderFromStripeSession $sync) {}

    public function __invoke(Request $request): Response
    {
        try {
            $event = Webhook::constructEvent(
                $request->getContent(),
                (string) $request->header('Stripe-Signature'),
                config('services.stripe.webhook_secret'),
            );
        } catch (UnexpectedValueException|SignatureVerificationException) {
            return response('Invalid payload', 400);
        }

        match ($event->type) {
            'checkout.session.completed' => $this->handleCheckoutCompleted($event->data->object),
            'checkout.session.expired' => $this->handleCheckoutExpired($event->data->object),
            default => null,
        };

        return response('Webhook handled', 200);
    }

    /**
     * @param  object{id: string, metadata: object{order_id?: string}, customer_email?: string|null, payment_intent?: string|null}  $session
     */
    private function handleCheckoutCompleted(object $session): void
    {
        $order = $this->findOrder($session);

        if ($order !== null) {
            $this->sync->markPaid($order, $session);
        }
    }

    /**
     * @param  object{id: string, metadata: object{order_id?: string}}  $session
     */
    private function handleCheckoutExpired(object $session): void
    {
        $order = $this->findOrder($session);

        if ($order !== null) {
            $this->sync->markCancelled($order);
        }
    }

    /**
     * @param  object{id: string, metadata: object{order_id?: string}}  $session
     */
    private function findOrder(object $session): ?Order
    {
        if (isset($session->metadata->order_id)) {
            $order = Order::query()->find($session->metadata->order_id);

            if ($order !== null) {
                return $order;
            }
        }

        return Order::query()
            ->where('stripe_checkout_session_id', $session->id)
            ->first();
    }
}
