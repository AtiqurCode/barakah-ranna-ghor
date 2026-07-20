<?php

use App\Actions\CreateStripeCheckoutSession;
use App\Actions\ReconcileOrderWithStripe;
use App\Models\Order;
use App\Models\Product;
use App\OrderStatus;

it('redirects guest to stripe checkout', function () {
    $product = Product::factory()->create(['price' => 25]);

    $this->mock(CreateStripeCheckoutSession::class)
        ->shouldReceive('__invoke')
        ->once()
        ->with(Mockery::on(fn (Product $passed) => $passed->is($product)))
        ->andReturn('https://checkout.stripe.com/c/pay/cs_test_123');

    $this->post(route('checkout.store', $product))
        ->assertRedirect('https://checkout.stripe.com/c/pay/cs_test_123');
});

it('creates a pending order when checkout begins', function () {
    $product = Product::factory()->create(['price' => 25]);

    $this->mock(CreateStripeCheckoutSession::class)
        ->shouldReceive('__invoke')
        ->once()
        ->andReturnUsing(function (Product $passed) {
            Order::query()->create([
                'product_id' => $passed->id,
                'amount' => $passed->price,
                'currency' => 'usd',
                'status' => OrderStatus::Pending,
                'stripe_checkout_session_id' => 'cs_test_123',
            ]);

            return 'https://checkout.stripe.com/c/pay/cs_test_123';
        });

    $this->post(route('checkout.store', $product))->assertRedirect();

    $this->assertDatabaseHas('orders', [
        'product_id' => $product->id,
        'amount' => 25,
        'currency' => 'usd',
        'status' => OrderStatus::Pending->value,
        'stripe_checkout_session_id' => 'cs_test_123',
    ]);
});

it('shows checkout success page for a session', function () {
    $order = Order::factory()->pending()->create([
        'stripe_checkout_session_id' => 'cs_test_success',
    ]);

    $this->mock(ReconcileOrderWithStripe::class)
        ->shouldReceive('__invoke')
        ->once()
        ->with(Mockery::on(fn (Order $passed) => $passed->is($order)))
        ->andReturnUsing(fn (Order $passed) => $passed);

    $this->get(route('checkout.success', ['session_id' => 'cs_test_success']))
        ->assertOk()
        ->assertSee('Thank you for your order')
        ->assertSee((string) $order->id);
});

it('shows checkout cancel page for a session', function () {
    $order = Order::factory()->pending()->create([
        'stripe_checkout_session_id' => 'cs_test_cancel',
    ]);

    $this->get(route('checkout.cancel', ['session_id' => 'cs_test_cancel']))
        ->assertOk()
        ->assertSee('Checkout cancelled')
        ->assertSee($order->product->translate('name'));
});
