<?php

use App\Models\Order;
use App\OrderStatus;
use Illuminate\Testing\TestResponse;

beforeEach(function () {
    config(['services.stripe.webhook_secret' => 'whsec_test_secret']);
});

function stripeTestSignature(string $payload, string $secret, ?int $timestamp = null): string
{
    $timestamp ??= time();
    $signedPayload = $timestamp.'.'.$payload;

    return 't='.$timestamp.',v1='.hash_hmac('sha256', $signedPayload, $secret);
}

function postStripeWebhook(array $payload): TestResponse
{
    $json = json_encode($payload);
    $signature = stripeTestSignature($json, config('services.stripe.webhook_secret'));

    return test()->call(
        'POST',
        route('stripe.webhook'),
        [],
        [],
        [],
        [
            'HTTP_STRIPE_SIGNATURE' => $signature,
            'CONTENT_TYPE' => 'application/json',
        ],
        $json,
    );
}

function checkoutSessionPayload(Order $order, string $type, array $overrides = []): array
{
    return array_replace_recursive([
        'id' => 'evt_test_123',
        'type' => $type,
        'data' => [
            'object' => [
                'id' => $order->stripe_checkout_session_id,
                'metadata' => [
                    'order_id' => (string) $order->id,
                ],
                'customer_email' => 'buyer@example.com',
                'payment_intent' => 'pi_test_123',
            ],
        ],
    ], $overrides);
}

it('marks an order as paid from checkout.session.completed webhook', function () {
    $order = Order::factory()->pending()->create([
        'stripe_checkout_session_id' => 'cs_test_paid',
        'email' => null,
    ]);

    postStripeWebhook(checkoutSessionPayload($order, 'checkout.session.completed'))
        ->assertOk()
        ->assertSee('Webhook handled');

    $order->refresh();

    expect($order->status)->toBe(OrderStatus::Paid)
        ->and($order->email)->toBe('buyer@example.com')
        ->and($order->stripe_payment_intent_id)->toBe('pi_test_123')
        ->and($order->paid_at)->not->toBeNull();
});

it('rejects webhooks with an invalid signature', function () {
    $order = Order::factory()->pending()->create();

    $this->postJson(route('stripe.webhook'), checkoutSessionPayload($order, 'checkout.session.completed'), [
        'Stripe-Signature' => 'invalid',
    ])->assertStatus(400);

    expect($order->fresh()->status)->toBe(OrderStatus::Pending);
});

it('ignores duplicate paid webhooks', function () {
    $order = Order::factory()->paid()->create([
        'stripe_checkout_session_id' => 'cs_test_duplicate',
        'email' => 'existing@example.com',
        'paid_at' => now()->subMinute(),
    ]);

    postStripeWebhook(checkoutSessionPayload($order, 'checkout.session.completed', [
        'data' => [
            'object' => [
                'customer_email' => 'new@example.com',
            ],
        ],
    ]))->assertOk();

    $order->refresh();

    expect($order->status)->toBe(OrderStatus::Paid)
        ->and($order->email)->toBe('existing@example.com');
});

it('marks an order as cancelled from checkout.session.expired webhook', function () {
    $order = Order::factory()->pending()->create([
        'stripe_checkout_session_id' => 'cs_test_expired',
    ]);

    postStripeWebhook(checkoutSessionPayload($order, 'checkout.session.expired'))
        ->assertOk();

    expect($order->fresh()->status)->toBe(OrderStatus::Cancelled);
});
