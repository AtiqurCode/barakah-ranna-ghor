<?php

namespace App\Actions;

use App\Models\Order;
use App\Models\Product;
use App\OrderStatus;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class CreateStripeCheckoutSession
{
    public function __invoke(Product $product): string
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $currency = config('services.stripe.currency', 'usd');

        $order = Order::query()->create([
            'product_id' => $product->id,
            'amount' => $product->price,
            'currency' => $currency,
            'status' => OrderStatus::Pending,
        ]);

        $session = Session::create([
            'mode' => 'payment',
            'line_items' => [[
                'price_data' => [
                    'currency' => $currency,
                    'product_data' => [
                        'name' => (string) $product->translate('name'),
                    ],
                    'unit_amount' => $product->price * 100,
                ],
                'quantity' => 1,
            ]],
            'metadata' => [
                'order_id' => (string) $order->id,
            ],
            'success_url' => route('checkout.success').'?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('checkout.cancel').'?session_id={CHECKOUT_SESSION_ID}',
        ]);

        $order->update([
            'stripe_checkout_session_id' => $session->id,
        ]);

        return $session->url;
    }
}
