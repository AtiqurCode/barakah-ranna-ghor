<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Product;
use App\OrderStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $product = Product::factory()->create();

        return [
            'product_id' => $product->id,
            'email' => fake()->safeEmail(),
            'amount' => $product->price,
            'currency' => 'usd',
            'status' => OrderStatus::Pending,
            'stripe_checkout_session_id' => 'cs_test_'.fake()->unique()->regexify('[A-Za-z0-9]{24}'),
            'stripe_payment_intent_id' => null,
            'paid_at' => null,
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (): array => [
            'status' => OrderStatus::Pending,
            'paid_at' => null,
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn (): array => [
            'status' => OrderStatus::Paid,
            'stripe_payment_intent_id' => 'pi_test_'.fake()->unique()->regexify('[A-Za-z0-9]{24}'),
            'paid_at' => now(),
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (): array => [
            'status' => OrderStatus::Failed,
            'paid_at' => null,
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (): array => [
            'status' => OrderStatus::Cancelled,
            'paid_at' => null,
        ]);
    }
}
