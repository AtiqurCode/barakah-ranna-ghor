<?php

use App\Actions\SyncOrderFromStripeSession;
use App\Models\Order;
use App\OrderStatus;

it('marks a pending order paid from a session-shaped object', function () {
    $order = Order::factory()->pending()->create(['email' => null]);

    $session = (object) [
        'customer_email' => 'buyer@example.com',
        'payment_intent' => 'pi_test_456',
    ];

    (new SyncOrderFromStripeSession)->markPaid($order, $session);

    expect($order->status)->toBe(OrderStatus::Paid)
        ->and($order->email)->toBe('buyer@example.com')
        ->and($order->stripe_payment_intent_id)->toBe('pi_test_456')
        ->and($order->paid_at)->not->toBeNull();
});

it('keeps the existing email when the session has none', function () {
    $order = Order::factory()->pending()->create(['email' => 'kept@example.com']);

    (new SyncOrderFromStripeSession)->markPaid($order, (object) ['payment_intent' => 'pi_test_789']);

    expect($order->fresh()->email)->toBe('kept@example.com');
});

it('does not touch an already-paid order', function () {
    $order = Order::factory()->paid()->create([
        'email' => 'original@example.com',
        'paid_at' => now()->subDay(),
    ]);
    $originalPaidAt = $order->paid_at;

    (new SyncOrderFromStripeSession)->markPaid($order, (object) [
        'customer_email' => 'different@example.com',
        'payment_intent' => 'pi_test_should_not_apply',
    ]);

    expect($order->fresh())
        ->email->toBe('original@example.com')
        ->paid_at->eq($originalPaidAt)->toBeTrue();
});

it('marks a pending order cancelled', function () {
    $order = Order::factory()->pending()->create();

    (new SyncOrderFromStripeSession)->markCancelled($order);

    expect($order->fresh()->status)->toBe(OrderStatus::Cancelled);
});

it('does not cancel an already-paid order', function () {
    $order = Order::factory()->paid()->create();

    (new SyncOrderFromStripeSession)->markCancelled($order);

    expect($order->fresh()->status)->toBe(OrderStatus::Paid);
});
