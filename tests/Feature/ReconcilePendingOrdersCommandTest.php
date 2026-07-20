<?php

use App\Actions\ReconcileOrderWithStripe;
use App\Models\Order;

it('only reconciles pending orders older than the threshold with a session id', function () {
    $stuck = Order::factory()->pending()->create([
        'stripe_checkout_session_id' => 'cs_test_stuck',
        'created_at' => now()->subMinutes(30),
    ]);

    Order::factory()->pending()->create([
        'stripe_checkout_session_id' => 'cs_test_fresh',
        'created_at' => now(),
    ]);

    Order::factory()->paid()->create([
        'stripe_checkout_session_id' => 'cs_test_already_paid',
        'created_at' => now()->subMinutes(30),
    ]);

    Order::factory()->pending()->create([
        'stripe_checkout_session_id' => null,
        'created_at' => now()->subMinutes(30),
    ]);

    $this->mock(ReconcileOrderWithStripe::class)
        ->shouldReceive('__invoke')
        ->once()
        ->with(Mockery::on(fn (Order $passed) => $passed->is($stuck)))
        ->andReturnUsing(fn (Order $passed) => $passed);

    $this->artisan('orders:reconcile')
        ->expectsOutputToContain('Checked 1 pending order(s) against Stripe.')
        ->assertSuccessful();
});

it('respects a custom minutes threshold', function () {
    Order::factory()->pending()->create([
        'stripe_checkout_session_id' => 'cs_test_five_min_old',
        'created_at' => now()->subMinutes(5),
    ]);

    $this->mock(ReconcileOrderWithStripe::class)
        ->shouldReceive('__invoke')
        ->once();

    $this->artisan('orders:reconcile', ['--minutes' => 2])
        ->assertSuccessful();
});
